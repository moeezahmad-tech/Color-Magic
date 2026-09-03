<?php

namespace ColorMagic\Controllers;

use ColorMagic\Repositories\ColorRepository;
use ColorMagic\Services\ResponseHelper;

/**
 * Colors REST Controller (PHP 7.0+ Compatible)
 */
class ColorController extends BaseController
{
    private $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository ?? new ColorRepository();
    }

    /**
     * GET /v2/colors - List, search, paginate or return dict format
     */
    public function index()
    {
        $q      = (string)$this->getQuery('q', '');
        $page   = (int)$this->getQuery('page', 1);
        $limit  = (int)$this->getQuery('limit', 50);
        $format = (string)$this->getQuery('format', 'list');

        // Check if user requested dictionary format (format=dict)
        if ($format === 'dict') {
            $dict = $this->repository->all(true);
            ResponseHelper::success($dict, 200, ['total' => count($dict), 'format' => 'dictionary']);
            return;
        }

        // Support query by hex in query param (?hex=123524)
        $hex = (string)$this->getQuery('hex', '');
        if ($hex !== '') {
            $this->getByHex($hex);
            return;
        }

        // Support query by slug in query param (?slug=phthalo-green)
        $slug = (string)$this->getQuery('slug', '');
        if ($slug !== '') {
            $this->getBySlug($slug);
            return;
        }

        $result = $this->repository->search($q, $page, $limit);
        ResponseHelper::success($result['items'], 200, [
            'total' => $result['total'],
            'page'  => $result['page'],
            'limit' => $result['limit']
        ]);
    }

    /**
     * GET /v2/colors/{hex} - Lookup single color by Hex code
     */
    public function getByHex(string $hex)
    {
        $color = $this->repository->findByHex($hex);
        if (!$color) {
            ResponseHelper::error("Color not found for hex code '#{$hex}'", 404);
            return;
        }

        ResponseHelper::success($color);
    }

    /**
     * GET /v2/colors/slug/{slug} - Lookup single color by name slug
     */
    public function getBySlug(string $slug)
    {
        $color = $this->repository->findBySlug($slug);
        if (!$color) {
            ResponseHelper::error("Color not found for slug '{$slug}'", 404);
            return;
        }

        ResponseHelper::success($color);
    }
}
