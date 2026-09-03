<?php
/**
 * ColorMagic REST API V2 Front Controller
 * File: api/v2.php
 * Base URL: https://colormagic-api.techkreative.com/v2/
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '1');

// PHP 7.0 - 7.4 compatibility polyfills
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle === '' || strncmp((string)$haystack, (string)$needle, strlen((string)$needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return (string)$needle === '' || substr((string)$haystack, -strlen((string)$needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return (string)$needle === '' || strpos((string)$haystack, (string)$needle) !== false;
    }
}

// Autoloader for ColorMagic namespace
spl_autoload_register(function ($class) {
    $prefix = 'ColorMagic\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $relativeFile = str_replace('\\', '/', $relative) . '.php';

    $candidates = [
        __DIR__ . '/v2/src/' . $relativeFile,
        __DIR__ . '/src/' . $relativeFile,
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Require ResponseHelper eagerly
if (file_exists(__DIR__ . '/v2/src/Services/ResponseHelper.php')) {
    require_once __DIR__ . '/v2/src/Services/ResponseHelper.php';
}

use ColorMagic\Services\ResponseHelper;
use ColorMagic\Controllers\ColorController;
use ColorMagic\Controllers\GradientController;
use ColorMagic\Controllers\PaletteController;
use ColorMagic\Controllers\HealthController;

// Start micro-timer
ResponseHelper::startTimer();

// Handle HTTP OPTIONS Preflight
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    http_response_code(200);
    exit;
}

try {
    // Parse Request Path
    $requestUri = (string)($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

    // Normalize path by stripping script subfolder and v2 prefixes
    $path = preg_replace('#^/(ColorMagic/api/v2|ColorMagic/api|api/v2|api|v2\.php|v2)#i', '', $path);
    $path = '/' . trim($path, '/');
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    // Route matching
    // Health / Root
    if ($path === '/' || $path === '/health' || $path === '/index.php' || $path === '/v2') {
        (new HealthController())->check();
        exit;
    }

    // Colors
    // GET /colors/slug/{slug}
    if (preg_match('#^/colors/slug/([^/]+)$#i', $path, $matches)) {
        (new ColorController())->getBySlug($matches[1]);
        exit;
    }

    // GET /colors/{hex} (e.g. /colors/FF0000 or /colors/123524 or /color/ff0000)
    if (preg_match('#^/(?:colors|color)/([0-9A-Fa-f]{3,8})$#i', $path, $matches)) {
        (new ColorController())->getByHex($matches[1]);
        exit;
    }

    // GET /colors or /colors/
    if ($path === '/colors' || $path === '/color') {
        (new ColorController())->index();
        exit;
    }

    // Gradients
    // GET /gradients/{id} (e.g. /gradients/gradient_1)
    if (preg_match('#^/(?:gradients|gradient)/([^/]+)$#i', $path, $matches)) {
        (new GradientController())->getById($matches[1]);
        exit;
    }

    // GET /gradients
    if ($path === '/gradients' || $path === '/gradient') {
        (new GradientController())->index();
        exit;
    }

    // Palettes
    // POST /palettes (Community submission)
    if (($path === '/palettes' || $path === '/palette') && $method === 'POST') {
        (new PaletteController())->submit();
        exit;
    }

    // GET /palettes/{id} (e.g. /palettes/palette_1)
    if (preg_match('#^/(?:palettes|palette)/([^/]+)$#i', $path, $matches)) {
        (new PaletteController())->getById($matches[1]);
        exit;
    }

    // GET /palettes
    if ($path === '/palettes' || $path === '/palette') {
        (new PaletteController())->index();
        exit;
    }

    // 404 Route Not Found
    ResponseHelper::error("Endpoint not found: {$path}", 404, [
        'available_routes' => [
            'GET /v2/health',
            'GET /v2/colors',
            'GET /v2/colors/{hex}',
            'GET /v2/colors/slug/{slug}',
            'GET /v2/gradients',
            'GET /v2/gradients/{id}',
            'GET /v2/palettes',
            'GET /v2/palettes/{id}',
            'POST /v2/palettes'
        ]
    ]);

} catch (\Throwable $e) {
    ResponseHelper::error("Internal Server Error: " . $e->getMessage(), 500, [
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
