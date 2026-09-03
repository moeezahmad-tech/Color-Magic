<?php

namespace ColorMagic\Controllers;

use ColorMagic\Repositories\GradientRepository;
use ColorMagic\Services\ResponseHelper;

/**
 * Gradient Controller
 * Endpoints for CSS gradient queries, style filtering, and ID lookups.
 */
class GradientController extends BaseController
{
    private GradientRepository $repository;

    public function __construct(?GradientRepository $repository = null)
    {
        $this->repository = $repository ?? new GradientRepository();
    }

    /**
     * Handle index route: list, search, or ID query param
     */
    public function index(): void
    {
        $id = $this->getStringParam('id');
        if ($id !== '') {
            $this->getById($id);
            return;
        }

        $style = $this->getStringParam('style');
        $type  = $this->getStringParam('type');
        $q     = $this->getStringParam('q');

        $page  = $this->getIntParam('page', 1, 1);
        $limit = isset($_GET['limit']) ? $this->getIntParam('limit', 50, 1, 200) : (isset($_GET['page']) ? 50 : 0);

        // Bulk full fetch if no filter and no limit
        if ($limit === 0 && $style === '' && $type === '' && $q === '') {
            $all = $this->repository->all();
            ResponseHelper::success($all, ['total' => count($all)]);
            return;
        }

        $effectiveLimit = $limit > 0 ? $limit : 50;
        $result = $this->repository->search($q, $style, $type, $page, $effectiveLimit);

        $meta = [];
        if ($style !== '') $meta['style'] = $style;
        if ($type !== '')  $meta['type']  = $type;
        if ($q !== '')     $meta['query'] = $q;

        ResponseHelper::paginated(
            $result['items'],
            $result['total'],
            $result['page'],
            $result['limit'],
            $meta
        );
    }

    /**
     * Get single gradient by ID
     */
    public function getById(string $id): void
    {
        $gradient = $this->repository->findById($id);

        if (!$gradient) {
            ResponseHelper::error("Gradient not found for ID '{$id}'", 404);
            return;
        }

        ResponseHelper::success($gradient);
    }
}
