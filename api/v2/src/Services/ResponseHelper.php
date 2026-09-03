<?php

namespace ColorMagic\Services;

/**
 * Ultra-fast HTTP JSON Response & Performance Engine
 * Supports ETag 304 caching, execution timing, CORS, Gzip compression, and uniform formatting.
 */
class ResponseHelper
{
    private static float $startTime = 0.0;

    /**
     * Start execution timer
     */
    public static function startTimer(): void
    {
        self::$startTime = microtime(true);
    }

    /**
     * Get elapsed execution time in milliseconds
     */
    public static function getElapsedTimeMs(): float
    {
        if (self::$startTime <= 0.0) {
            return 0.0;
        }
        return round((microtime(true) - self::$startTime) * 1000, 2);
    }

    /**
     * Send JSON response with ETags and caching
     */
    public static function json(array $data, int $statusCode = 200, array $extraHeaders = []): void
    {
        // Safe Header Emission
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Content-Type: application/json; charset=UTF-8');

            $elapsedMs = self::getElapsedTimeMs();
            header("X-Response-Time: {$elapsedMs}ms");
            header("X-Powered-By: ColorMagic-V2-Engine");

            foreach ($extraHeaders as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        // Generate JSON payload
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // HTTP ETag & 304 Not Modified validation for GET requests
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && $statusCode === 200) {
            $etag = '"' . md5($json) . '"';
            if (!headers_sent()) {
                header("ETag: {$etag}");
                header("Cache-Control: public, max-age=86400, stale-while-revalidate=604800");
            }

            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
                if (!headers_sent()) {
                    http_response_code(304);
                }
                exit;
            }
        }

        if (!headers_sent()) {
            http_response_code($statusCode);
        }

        // Gzip compression if supported and beneficial
        if (
            strlen($json) > 1024 &&
            !headers_sent() &&
            extension_loaded('zlib') &&
            isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
            str_contains($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
        ) {
            ob_start('ob_gzhandler');
        }

        echo $json;
        exit;
    }

    /**
     * Standard success response
     */
    public static function success(mixed $data, array $meta = [], int $statusCode = 200): void
    {
        $response = [
            'status' => 'success',
            'data'   => $data
        ];

        if (!empty($meta)) {
            $response = array_merge($response, $meta);
        }

        self::json($response, $statusCode);
    }

    /**
     * Standard paginated response
     */
    public static function paginated(array $items, int $total, int $page, int $limit, array $extraMeta = []): void
    {
        $totalPages = $limit > 0 ? (int)ceil($total / $limit) : 1;

        $response = array_merge([
            'status'      => 'success',
            'page'        => $page,
            'limit'       => $limit,
            'total'       => $total,
            'total_pages' => $totalPages,
            'data'        => $items
        ], $extraMeta);

        self::json($response, 200);
    }

    /**
     * Standard error response
     */
    public static function error(string $message, int $statusCode = 400, array $errors = []): void
    {
        $response = [
            'status'  => 'error',
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        self::json($response, $statusCode);
    }
}
