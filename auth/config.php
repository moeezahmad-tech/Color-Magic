<?php
require_once __DIR__ . '/../components/config.php';

// Base and Local candidate files:
// - On production: .env.colormagic is located in the parent directory (__DIR__ . '/../../.env.colormagic')
// - In local dev: both .env.colormagic and .env.colormagic.local exist; .env.colormagic.local takes precedence.
$env = !empty($cmEnv) ? $cmEnv : [];

if (empty($env)) {
    $baseCandidates = [
        __DIR__ . '/../../.env.colormagic', // Production parent directory
        __DIR__ . '/../.env.colormagic',    // Project root
        __DIR__ . '/../../.env',           // Fallback
        __DIR__ . '/../.env'               // Fallback
    ];

    $localCandidates = [
        __DIR__ . '/../../.env.colormagic.local',
        __DIR__ . '/../.env.colormagic.local',
        __DIR__ . '/../.env.local'
    ];

    foreach ($baseCandidates as $candidate) {
        if (file_exists($candidate) && is_readable($candidate)) {
            $parsed = function_exists('cm_read_env_file') ? cm_read_env_file($candidate) : @parse_ini_file($candidate);
            if ($parsed) {
                $env = array_merge($env, $parsed);
                break;
            }
        }
    }

    foreach ($localCandidates as $candidate) {
        if (file_exists($candidate) && is_readable($candidate)) {
            $parsed = function_exists('cm_read_env_file') ? cm_read_env_file($candidate) : @parse_ini_file($candidate);
            if ($parsed) {
                $env = array_merge($env, $parsed);
                break;
            }
        }
    }
}

// Support legacy and project-specific key names.
$clientId = $env['CLIENT_ID_COLORMAGIC']
    ?? ($env['CLINET_ID_COLORMAGIC'] ?? ($env['CLIENT_ID'] ?? ($env['CLINET_ID'] ?? '')));
$clientSecret = $env['CLIENT_SECRET_COLORMAGIC'] ?? ($env['CLIENT_SECRET'] ?? '');

// Dynamically determine protocol (supports reverse proxy & Cloudflare)
$protocol = (
    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
) ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'colormagic.techkreative.com';

define('GOOGLE_CLIENT_ID', $clientId);
define('GOOGLE_CLIENT_SECRET', $clientSecret);
define('GOOGLE_REDIRECT_URI', $protocol . '://' . $host . $base . '/auth/google-callback.php');

function getGoogleAuthUrl() {
    $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online',
        'prompt' => 'select_account',
    ];
    return $authUrl . '?' . http_build_query($params);
}
