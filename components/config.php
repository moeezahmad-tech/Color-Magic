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

// Load API_BASE_URL from .env configuration
$apiBaseUrl = '';
$envFileCandidates = [
    __DIR__ . '/../.env',
    __DIR__ . '/../.env.local',
    __DIR__ . '/../.env.api.colormagic',
    __DIR__ . '/../.env.colormagic'
];

foreach ($envFileCandidates as $envFile) {
    if (file_exists($envFile) && is_readable($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') continue;
                if (strpos($line, '=') !== false) {
                    list($envKey, $envVal) = explode('=', $line, 2);
                    $envKey = trim($envKey);
                    $envVal = trim($envVal, " \t\n\r\0\x0B\"'");
                    if (in_array($envKey, ['API_BASE_URL', 'API_URL', 'COLORMAGIC_API_URL'], true) && empty($apiBaseUrl)) {
                        $apiBaseUrl = $envVal;
                    }
                }
            }
        }
    }
}

if (empty($apiBaseUrl)) {
    // Default to production API URL for all environments (local & live)
    $apiBaseUrl = 'https://colormagic-api.techkreative.com';
}

if (!headers_sent()) {
    header('Link: <' . $base . '/assets/images/logo.png>; rel="icon"; type="image/png"');
}
