<?php
header('X-Robots-Tag: noindex, nofollow', true);
header('Cache-Control: private, no-cache, no-store, must-revalidate');
require_once __DIR__ . '/../components/config.php';

$appId = getenv('PINTEREST_APP_ID') ?: ($_ENV['PINTEREST_APP_ID'] ?? '1622531');

if (empty($appId)) {
    $adminEnv = __DIR__ . '/../.env.colormagic.admin';
    if (file_exists($adminEnv)) {
        foreach (file($adminEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
            $l = trim($l);
            if (str_starts_with($l, 'PINTEREST_APP_ID=')) {
                $appId = trim(substr($l, strlen('PINTEREST_APP_ID=')));
            }
        }
    }
}

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'colormagic.techkreative.com';
$redirectUri = $protocol . '://' . $host . ($base ?: '') . '/auth/pinterest-callback';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['return'])) {
    $_SESSION['auth_return'] = $_GET['return'];
}

$scopes = 'user_accounts:read,boards:read,pins:read';
$authUrl = "https://www.pinterest.com/oauth/?client_id=" . urlencode($appId) .
           "&redirect_uri=" . urlencode($redirectUri) .
           "&response_type=code" .
           "&scope=" . urlencode($scopes);

header('Location: ' . $authUrl);
exit();

