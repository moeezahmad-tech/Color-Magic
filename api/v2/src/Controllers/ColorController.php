<?php

namespace ColorMagic\Controllers;

use ColorMagic\Repositories\ColorRepository;
use ColorMagic\Services\ResponseHelper;

/**
 * Color Controller
 * Fast endpoints for color lookup by hex/slug, query search, and pagination.
 */
class ColorController extends BaseController
{
    private ColorRepository $repository;

    public function __construct(?ColorRepository $repository = null)
    {
        $this->repository = $repository ?? new ColorRepository();
    }

    /**
     * Handle index route: list, search, or query param lookup
     */
    public function index(): void
    {
        $hex  = $this->getStringParam('hex');
        $slug = $this->getStringParam('slug');

        if ($hex !== '') {
            $this->getByHex($hex);
            return;
        }

        if ($slug !== '') {
            $this->getBySlug($slug);
            return;
        }

        $q     = $this->getStringParam('q');
        $page  = $this->getIntParam('page', 1, 1);
        $limit = isset($_GET['limit']) ? $this->getIntParam('limit', 50, 1, 200) : (isset($_GET['page']) ? 50 : 0);

        // If no limit or page specified, return all (backward compatible / bulk consumption)
        if ($limit === 0 && $q === '') {
            $format = $this->getStringParam('format');
            $all = $this->repository->all($format === 'dict');
            ResponseHelper::success($all, ['total' => count($all)]);
            return;
        }

        $effectiveLimit = $limit > 0 ? $limit : 50;
        $result = $this->repository->search($q, $page, $effectiveLimit);

        ResponseHelper::paginated(
            $result['items'],
            $result['total'],
            $result['page'],
            $result['limit'],
            ['query' => $q]
        );
    }

    /**
     * Get single color by Hex code
     */
    public function getByHex(string $hex): void
    {
        $color = $this->repository->findByHex($hex);

        if (!$color) {
            $clean = strtoupper(ltrim(trim($hex), '#'));
            ResponseHelper::error("Color not found for hex #{$clean}", 404);
            return;
        }

        ResponseHelper::success($color);
    }

    /**
     * Get single color by Slug
     */
    public function getBySlug(string $slug): void
    {
        $color = $this->repository->findBySlug($slug);

        if (!$color) {
            ResponseHelper::error("Color not found for slug '{$slug}'", 404);
            return;
        }

        ResponseHelper::success($color);
    }
}
