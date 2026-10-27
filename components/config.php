<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Environment-aware base path configuration.
 *
 * $base is empty ('') on production and '/ColorMagic' on local dev (Apache).
 * When using PHP's built-in server (router.php), $base is empty because the
 * router serves from the project root.
 *
 * Usage:
 *   <script src="<?= $base ?>/assets/js/app.js"></script>
 *   <a href="<?= $base ?>/palettes">Explore</a>
 */
/** @var string $base Environment-aware base path: empty on production or built-in server, '/ColorMagic' on local Apache. */
$isBuiltinServer = php_sapi_name() === 'cli-server';
$base = (!$isBuiltinServer && isset($_SERVER['HTTP_HOST']) && preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $_SERVER['HTTP_HOST']))
    ? '/ColorMagic'
    : '';

/**
 * Helper to safely parse a .env file into key-value pairs without INI parser restrictions.
 */
if (!function_exists('cm_read_env_file')) {
    function cm_read_env_file(string $filePath): array {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [];
        }
        $data = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val, " \t\n\r\0\x0B\"'");
                $data[$key] = $val;
            }
        }
        return $data;
    }
}

// Load environment configuration:
// - On production: .env.colormagic is located in the parent directory (__DIR__ . '/../../.env.colormagic')
// - In local dev: both .env.colormagic and .env.colormagic.local exist in project root; .env.colormagic.local takes precedence.
$cmBaseCandidates = [
    __DIR__ . '/../../.env.colormagic', // Production parent directory
    __DIR__ . '/../.env.colormagic',    // Project root
    __DIR__ . '/../../.env',           // Fallback
    __DIR__ . '/../.env',              // Fallback
    __DIR__ . '/../.env.api.colormagic' // Legacy fallback
];

$cmLocalCandidates = [
    __DIR__ . '/../../.env.colormagic.local', // Local parent directory
    __DIR__ . '/../.env.colormagic.local',    // Local project root
    __DIR__ . '/../.env.local'                // Legacy fallback
];

$cmEnv = [];

// 1. Load base/production configuration
foreach ($cmBaseCandidates as $file) {
    if (file_exists($file) && is_readable($file)) {
        $cmEnv = array_merge($cmEnv, cm_read_env_file($file));
        break;
    }
}

// 2. Overlay local development configuration (if present)
foreach ($cmLocalCandidates as $file) {
    if (file_exists($file) && is_readable($file)) {
        $cmEnv = array_merge($cmEnv, cm_read_env_file($file));
        break;
    }
}

// 3. Populate process environment and $_ENV
foreach ($cmEnv as $k => $v) {
    if (getenv($k) === false) {
        putenv("{$k}={$v}");
    }
    if (!isset($_ENV[$k])) {
        $_ENV[$k] = $v;
    }
}

// Extract API_BASE_URL
$apiBaseUrl = $cmEnv['API_BASE_URL'] ?? ($cmEnv['API_URL'] ?? ($cmEnv['COLORMAGIC_API_URL'] ?? ''));
if (empty($apiBaseUrl)) {
    // Default to production API URL for all environments (local & live)
    $apiBaseUrl = 'https://colormagic-api.techkreative.com';
}

if (!headers_sent()) {
    header('Link: <' . $base . '/assets/images/logo.png>; rel="icon"; type="image/png"');
}

/**
 * Dynamic asset versioning helper to prevent stale browser & CDN cache.
 * Returns file modification time or current timestamp.
 */
if (!function_exists('cm_asset_v')) {
    function cm_asset_v(string $relPath): string {
        $fullPath = dirname(__DIR__) . '/' . ltrim($relPath, '/\\');
        if (file_exists($fullPath)) {
            return (string) filemtime($fullPath);
        }
        return (string) time();
    }
}

