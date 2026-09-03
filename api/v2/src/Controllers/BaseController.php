<?php

namespace ColorMagic\Controllers;

/**
 * Base Controller (PHP 7.0+ Compatible)
 */
abstract class BaseController
{
    /**
     * Parse JSON body from request
     */
    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Get validated query parameter
     */
    protected function getQuery(string $key, $default = null)
    {
        return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
    }
}
