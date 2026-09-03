<?php

namespace ColorMagic\Controllers;

use ColorMagic\Repositories\PaletteRepository;
use ColorMagic\Services\ResponseHelper;

/**
 * Palette Controller
 * Endpoints for curated color palettes, search queries, and community submissions.
 */
class PaletteController extends BaseController
{
    private PaletteRepository $repository;

    public function __construct(?PaletteRepository $repository = null)
    {
        $this->repository = $repository ?? new PaletteRepository();
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
        $q     = $this->getStringParam('q');

        $page  = $this->getIntParam('page', 1, 1);
        $limit = isset($_GET['limit']) ? $this->getIntParam('limit', 50, 1, 200) : (isset($_GET['page']) ? 50 : 0);

        // Bulk full fetch if no filter and no limit
        if ($limit === 0 && $style === '' && $q === '') {
            $all = $this->repository->all();
            ResponseHelper::success($all, ['total' => count($all)]);
            return;
        }

        $effectiveLimit = $limit > 0 ? $limit : 50;
        $result = $this->repository->search($q, $style, $page, $effectiveLimit);

        $meta = [];
        if ($style !== '') $meta['style'] = $style;
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
     * Get single palette by ID
     */
    public function getById(string $id): void
    {
        $palette = $this->repository->findById($id);

        if (!$palette) {
            ResponseHelper::error("Palette not found for ID '{$id}'", 404);
            return;
        }

        ResponseHelper::success($palette);
    }

    /**
     * Submit community palette (Future User Dashboard & Monetization feature)
     */
    public function submit(): void
    {
        $body = $this->getJsonBody();

        if (empty($body['colors']) || !is_array($body['colors']) || count($body['colors']) < 2) {
            ResponseHelper::error("Palette must contain at least 2 colors", 422);
            return;
        }

        $cleanColors = [];
        foreach ($body['colors'] as $c) {
            $hex = '#' . strtoupper(ltrim(trim((string)$c), '#'));
            if (preg_match('/^#[0-9A-F]{3,8}$/i', $hex)) {
                $cleanColors[] = $hex;
            }
        }

        if (count($cleanColors) < 2) {
            ResponseHelper::error("Invalid hex color codes provided", 422);
            return;
        }

        $created = $this->repository->submitUserPalette([
            'user_id' => $body['user_id'] ?? null,
            'name'    => $body['name'] ?? 'Custom Palette',
            'style'   => $body['style'] ?? 'Custom',
            'colors'  => $cleanColors
        ]);

        ResponseHelper::success($created, ['message' => 'Palette submitted successfully for review'], 201);
    }
}
