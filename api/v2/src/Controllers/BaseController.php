<?php

namespace ColorMagic\Controllers;

use ColorMagic\Services\ResponseHelper;

/**
 * Base Controller
 * Common request parameter extraction and validation.
 */
abstract class BaseController
{
    /**
     * Get integer parameter from $_GET
     */
    protected function getIntParam(string $key, int $default = 0, int $min = 0, int $max = PHP_INT_MAX): int
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        $val = (int)$_GET[$key];
        return max($min, min($max, $val));
    }

    /**
     * Get trimmed string parameter from $_GET
     */
    protected function getStringParam(string $key, string $default = ''): string
    {
        return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
    }

    /**
     * Parse JSON request body
     */
    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}
