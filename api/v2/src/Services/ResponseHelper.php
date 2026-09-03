<?php

namespace ColorMagic\Services;

use ColorMagic\Config\Env;

/**
 * Standardized REST API Response Formatter (PHP 7.0+ Compatible)
 */
class ResponseHelper
{
    private static $startTime = 0.0;

    /**
     * Start high-resolution execution timer
     */
    public static function startTimer(): void
    {
        self::$startTime = microtime(true);
    }

    /**
     * Calculate execution latency
     */
    public static function getLatencyMs(): float
    {
        if (self::$startTime <= 0.0) {
            return 0.0;
        }
        return round((microtime(true) - self::$startTime) * 1000, 2);
    }

    /**
     * Send standardized JSON success response with ETag and CORS
     */
    public static function success($data, int $statusCode = 200, array $meta = []): void
    {
        self::sendHeaders($statusCode);

        $response = [
            'status'     => 'success',
            'latency_ms' => self::getLatencyMs(),
        ];

        if (!empty($meta)) {
            foreach ($meta as $k => $v) {
                $response[$k] = $v;
            }
        }

        $response['data'] = $data;

        $json = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        // ETag 304 handling
        $etag = '"' . md5($json) . '"';
        header("ETag: {$etag}");

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            exit;
        }

        echo $json;
        exit;
    }

    /**
     * Send standardized JSON error response
     */
    public static function error(string $message, int $statusCode = 400, array $extra = []): void
    {
        self::sendHeaders($statusCode);

        $response = [
            'status'     => 'error',
            'message'    => $message,
            'latency_ms' => self::getLatencyMs(),
        ];

        if (!empty($extra)) {
            $response['details'] = $extra;
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send standard HTTP headers
     */
    private static function sendHeaders(int $statusCode = 200): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        $cacheTtl = (int)Env::get('CACHE_TTL', 86400);
        if ($cacheTtl > 0) {
            header("Cache-Control: public, max-age={$cacheTtl}, stale-while-revalidate=604800");
        }
    }
}
