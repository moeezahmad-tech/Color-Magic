<?php
require_once __DIR__ . '/../components/config.php';

$isLocalHost = isset($_SERVER['HTTP_HOST']) && preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $_SERVER['HTTP_HOST']);
$envFile = $isLocalHost
    ? __DIR__ . '/../.env.colormagic'
    : __DIR__ . '/../../.env.colormagic';

$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

// Support legacy and project-specific key names.
$clientId = $env['CLIENT_ID_COLORMAGIC']
    ?? ($env['CLINET_ID_COLORMAGIC'] ?? ($env['CLIENT_ID'] ?? ($env['CLINET_ID'] ?? '')));
$clientSecret = $env['CLIENT_SECRET_COLORMAGIC'] ?? ($env['CLIENT_SECRET'] ?? '');

// Dynamically determine protocol
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

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
