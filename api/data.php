<?php
/**
 * Color Magic - Secure Dataset API Gateway
 * Protects datasets from direct document browsing, external scraping/hotlinking, and unauthorized access.
 */

// 1. Allowed JSON datasets (Strict Whitelist)
$whitelist = [
    'gradients.json'   => __DIR__ . '/../data/gradients.json',
    'palettes.json'    => __DIR__ . '/../data/palettes.json',
    'color-names.json' => __DIR__ . '/../data/color-names.json',
    'avatars.json'     => __DIR__ . '/../data/avatars.json',
];

$requestedFile = isset($_GET['file']) ? strtolower(basename((string)$_GET['file'])) : '';

// Block unknown or non-whitelisted files (e.g. pinterest_boards.json, internal files)
if (!isset($whitelist[$requestedFile]) || !file_exists($whitelist[$requestedFile])) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    echo json_encode([
        'error' => 'Resource not found or restricted.',
        'code'  => 404
    ]);
    exit;
}

$targetPath = $whitelist[$requestedFile];

// 2. Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Referrer-Policy: strict-origin-when-cross-origin');

// 3. Security Inspection: Block direct document navigation (e.g. typing raw URL in browser address bar)
$secFetchDest = strtolower($_SERVER['HTTP_SEC_FETCH_DEST'] ?? '');
$secFetchMode = strtolower($_SERVER['HTTP_SEC_FETCH_MODE'] ?? '');
if ($secFetchDest === 'document' || $secFetchMode === 'navigate') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error'   => 'Direct document access to raw datasets is restricted.',
        'message' => 'Please explore colors, palettes, and gradients through the Color Magic website interface.',
        'code'    => 403
    ]);
    exit;
}

// 4. Security Inspection: Block cross-site fetch / hotlinking from external websites
$secFetchSite = strtolower($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '');
if ($secFetchSite === 'cross-site') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => 'Unauthorized cross-origin request. Hotlinking is prohibited.',
        'code'  => 403
    ]);
    exit;
}

// 5. Origin & Referer Verification
$httpHost = strtolower($_SERVER['HTTP_HOST'] ?? '');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? strtolower((string)$_SERVER['HTTP_ORIGIN']) : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? strtolower((string)$_SERVER['HTTP_REFERER']) : '';

function cmIsAllowedHost(string $host): bool {
    return $host === 'colormagic.techkreative.com'
        || $host === 'localhost'
        || str_starts_with($host, 'localhost:')
        || $host === '127.0.0.1'
        || str_starts_with($host, '127.0.0.1:')
        || str_ends_with($host, '.techkreative.com');
}

if ($origin !== '') {
    $originHost = parse_url($origin, PHP_URL_HOST);
    $originPort = parse_url($origin, PHP_URL_PORT);
    $originFull = $originHost . ($originPort ? ':' . $originPort : '');
    if (!$originHost || !cmIsAllowedHost($originFull)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Cross-origin resource access forbidden.',
            'code'  => 403
        ]);
        exit;
    }
    header('Access-Control-Allow-Origin: ' . htmlspecialchars($origin, ENT_QUOTES, 'UTF-8'));
}

if ($referer !== '') {
    $refererHost = parse_url($referer, PHP_URL_HOST);
    $refererPort = parse_url($referer, PHP_URL_PORT);
    $refererFull = $refererHost . ($refererPort ? ':' . $refererPort : '');
    if ($refererHost && !cmIsAllowedHost($refererFull)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Hotlinking from external referer is prohibited.',
            'code'  => 403
        ]);
        exit;
    }
}

// 6. Rate Limiting Protection (Anti-Scraping / Anti-DoS)
// Track requests in session: max 120 dataset requests per 60 seconds
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$now = time();
$rateWindow = 60;
$rateLimit = 120;
if (!isset($_SESSION['cm_api_rate']) || !is_array($_SESSION['cm_api_rate'])) {
    $_SESSION['cm_api_rate'] = ['start' => $now, 'count' => 0];
}
if ($now - $_SESSION['cm_api_rate']['start'] > $rateWindow) {
    $_SESSION['cm_api_rate'] = ['start' => $now, 'count' => 1];
} else {
    $_SESSION['cm_api_rate']['count']++;
    if ($_SESSION['cm_api_rate']['count'] > $rateLimit) {
        http_response_code(429);
        header('Retry-After: 60');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Rate limit exceeded. Too many dataset requests.',
            'code'  => 429
        ]);
        exit;
    }
}

// 7. Caching and ETag Support
$fileMtime = filemtime($targetPath);
$fileSize = filesize($targetPath);
$etag = sprintf('"%x-%x"', $fileMtime, $fileSize);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=3600, stale-while-revalidate=86400');
header('ETag: ' . $etag);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileMtime) . ' GMT');

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}

// 8. Output content
readfile($targetPath);
exit;

