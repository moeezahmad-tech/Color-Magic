<?php

namespace ColorMagic\Controllers;

use ColorMagic\Repositories\GradientRepository;
use ColorMagic\Services\ResponseHelper;

/**
 * Gradient REST Controller (PHP 7.0+ Compatible)
 */
class GradientController extends BaseController
{
    private $repository;

    public function __construct($repository = null)
    {
        $this->repository = $repository ?? new GradientRepository();
    }

    /**
     * GET /v2/gradients - List, search, and filter CSS gradients
     */
    public function index(): void
    {
        $id = (string)$this->getQuery('id', '');
        if ($id !== '') {
            $this->getById($id);
            return;
        }

        $q     = (string)$this->getQuery('q', '');
        $style = $this->getQuery('style');
        $type  = $this->getQuery('type');
        $page  = (int)$this->getQuery('page', 1);
        $limit = (int)$this->getQuery('limit', 50);

        $result = $this->repository->search($q, $style, $type, $page, $limit);
        ResponseHelper::success($result['items'], 200, [
            'total' => $result['total'],
            'page'  => $result['page'],
            'limit' => $result['limit']
        ]);
    }

    /**
     * GET /v2/gradients/{id} - Lookup single gradient by ID
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
