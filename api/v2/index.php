<?php
/**
 * ColorMagic REST API V2 Entry Point & Router
 * Base URL: https://api.colormagic.techkreative.com/v2/
 */

declare(strict_types=1);

// Autoloader for ColorMagic namespace
spl_autoload_register(static function (string $class): void {
    $prefix = 'ColorMagic\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

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

    // Normalize path by stripping script subfolder prefixes
    $path = preg_replace('#^/(ColorMagic/api/v2|ColorMagic/api|api/v2|api|v2)#i', '', $path);
    $path = '/' . trim($path, '/');
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    // Route matching
    // Health / Root
    if ($path === '/' || $path === '/health') {
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

} catch (Exception $e) {
    ResponseHelper::error("Internal Server Error: " . $e->getMessage(), 500);
}
