<?php
header('X-Robots-Tag: noindex, nofollow', true);
header('Cache-Control: private, no-cache, no-store, must-revalidate');
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/UserDb.php';
require_once __DIR__ . '/AdminAuth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$appId = getenv('PINTEREST_APP_ID') ?: ($_ENV['PINTEREST_APP_ID'] ?? '1622531');
$appSecret = getenv('PINTEREST_APP_SECRET') ?: ($_ENV['PINTEREST_APP_SECRET'] ?? '');

if (empty($appSecret)) {
    $adminEnv = __DIR__ . '/../.env.colormagic.admin';
    if (file_exists($adminEnv)) {
        foreach (file($adminEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
            $l = trim($l);
            if (str_starts_with($l, 'PINTEREST_APP_SECRET=')) {
                $appSecret = trim(substr($l, strlen('PINTEREST_APP_SECRET=')));
            }
            if (str_starts_with($l, 'PINTEREST_APP_ID=') && empty($appId)) {
                $appId = trim(substr($l, strlen('PINTEREST_APP_ID=')));
            }
        }
    }
}

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'colormagic.techkreative.com';
$redirectUri = $protocol . '://' . $host . ($base ?: '') . '/auth/pinterest-callback';

if (!empty($_GET['code'])) {
    $code = trim($_GET['code']);
    
    // Exchange code for token
    $tokenUrl = 'https://api.pinterest.com/v5/oauth/token';
    $basicAuth = base64_encode("{$appId}:{$appSecret}");
    
    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type'   => 'authorization_code',
        'code'         => $code,
        'redirect_uri' => $redirectUri
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $basicAuth,
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $tokenRes = curl_exec($ch);
    curl_close($ch);
    
    $tokenData = json_decode($tokenRes, true) ?: [];
    $accessToken = $tokenData['access_token'] ?? null;
    
    if ($accessToken) {
        // Fetch user profile from Pinterest
        $userUrl = 'https://api.pinterest.com/v5/user_account';
        $ch2 = curl_init($userUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $userRes = curl_exec($ch2);
        curl_close($ch2);
        
        $pinterestUser = json_decode($userRes, true) ?: [];
        
        if (!empty($pinterestUser['id']) || !empty($pinterestUser['username'])) {
            $username = $pinterestUser['username'] ?? ('pinterest_' . ($pinterestUser['id'] ?? 'user'));
            $name = $pinterestUser['business_name'] ?? ($pinterestUser['username'] ?? 'Pinterest User');
            $email = $username . '@pinterest.user';
            $picture = $pinterestUser['profile_image'] ?? null;
            
            $existing = UserDb::getUserByEmail($email);
            $avatar = $existing['avatar'] ?? '1.webp';
            
            $_SESSION['user'] = [
                'id'       => $pinterestUser['id'] ?? $username,
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'picture'  => $picture,
                'provider' => 'pinterest',
                'avatar'   => $avatar
            ];
            
            UserDb::upsertUser([
                'email'    => $email,
                'name'     => $name,
                'username' => $username,
                'avatar'   => $avatar,
                'website'  => $pinterestUser['website_url'] ?? '',
                'bio'      => $pinterestUser['about'] ?? ''
            ]);
            
            $return = $_SESSION['auth_return'] ?? ($base . '/profile');
            unset($_SESSION['auth_return']);
            header('Location: ' . $return);
            exit();
        }
    }
}

header('Location: ' . ($base ?: '') . '/login?error=auth_failed&provider=pinterest');
exit();

