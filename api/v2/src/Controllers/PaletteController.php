<?php

namespace ColorMagic\Controllers;

use ColorMagic\Repositories\PaletteRepository;
use ColorMagic\Services\ResponseHelper;

/**
 * Palette REST Controller (PHP 7.0+ Compatible)
 */
class PaletteController extends BaseController
{
    private $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository ?? new PaletteRepository();
    }

    /**
     * GET /v2/palettes - List, search, and filter curated palettes
     */
    public function index()
    {
        $id = (string)$this->getQuery('id', '');
        if ($id !== '') {
            $this->getById($id);
            return;
        }

        $q     = (string)$this->getQuery('q', '');
        $style = $this->getQuery('style');
        $page  = (int)$this->getQuery('page', 1);
        $limit = (int)$this->getQuery('limit', 50);

        $result = $this->repository->search($q, $style, $page, $limit);
        ResponseHelper::success($result['items'], 200, [
            'total' => $result['total'],
            'page'  => $result['page'],
            'limit' => $result['limit']
        ]);
    }

    /**
     * GET /v2/palettes/{id} - Lookup single palette by ID
     */
    public function getById(string $id)
    {
        $palette = $this->repository->findById($id);
        if (!$palette) {
            ResponseHelper::error("Palette not found for ID '{$id}'", 404);
            return;
        }

        ResponseHelper::success($palette);
    }

    /**
     * POST /v2/palettes - Submit community palette
     */
    public function submit()
    {
        $body = $this->getJsonBody();

        if (empty($body['name']) || empty($body['colors']) || !is_array($body['colors'])) {
            ResponseHelper::error("Missing required fields: 'name' and 'colors' (array of hex strings)", 422);
            return;
        }

        // Validate hex colors
        $cleanedColors = [];
        foreach ($body['colors'] as $c) {
            $hex = ltrim(trim((string)$c), '#');
            if (preg_match('/^[0-9A-Fa-f]{3,8}$/', $hex)) {
                $cleanedColors[] = '#' . strtoupper($hex);
            }
        }

        if (count($cleanedColors) < 2) {
            ResponseHelper::error("A palette must contain at least 2 valid hex color codes", 422);
            return;
        }

        $submission = $this->repository->submitUserPalette([
            'user_id' => $body['user_id'] ?? null,
            'name'    => (string)$body['name'],
            'style'   => (string)($body['style'] ?? 'Custom'),
            'colors'  => $cleanedColors
        ]);

        ResponseHelper::success($submission, 201, ['message' => 'Palette submitted successfully for review']);
    }
}
