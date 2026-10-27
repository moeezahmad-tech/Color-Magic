<?php
/**
 * Color Magic - Local Development Router for PHP Built-in Server
 * Usage: php -S localhost:8080 router.php
 */

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$uriPath = parse_url($rawUri, PHP_URL_PATH) ?? '/';
$uriPath = rawurldecode($uriPath);
$path = trim($uriPath, '/');

// Intercept sitemap requests to generate dynamically
if ($path === 'sitemap.xml' || $path === 'sitemap.php') {
    $targetType = $_GET['type'] ?? 'all';
    dispatch(__DIR__ . '/sitemap.php', ['type' => $targetType]);
}
if (preg_match('#^sitemaps/([a-zA-Z0-9_-]+)\.xml$#i', $path, $matches)) {
    dispatch(__DIR__ . '/sitemap.php', ['type' => $matches[1]]);
}

// 1. Serve physical static files directly, or execute physical PHP scripts directly
$filePath = __DIR__ . '/' . $path;
if ($path !== '' && file_exists($filePath) && !is_dir($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        dispatch($filePath);
    }
    // Static asset (CSS, JS, images, fonts, txt, etc.) -> let PHP CLI server stream it
    return false;
}

// 2. Legacy API data compatibility -> /data/*.json
if (preg_match('#^api/([a-zA-Z0-9_-]+\.json)$#i', $path, $matches)) {
    $targetData = __DIR__ . '/data/' . $matches[1];
    if (file_exists($targetData)) {
        header('Content-Type: application/json; charset=utf-8');
        readfile($targetData);
        exit;
    }
}

// 3. Stale Service Worker requests from browser cache
if ($path === 'sw.js') {
    header('Content-Type: application/javascript; charset=utf-8');
    echo '// No active service worker';
    exit;
}

/**
 * Dispatch request to target PHP file with correct execution context and working directory.
 */
function dispatch(string $file, array $queryParams = []): void
{
    foreach ($queryParams as $key => $val) {
        $_GET[$key] = $val;
    }
    $_SERVER['SCRIPT_FILENAME'] = $file;
    $_SERVER['SCRIPT_NAME'] = '/' . ltrim(str_replace(__DIR__, '', $file), '/\\');
    chdir(dirname($file));
    require $file;
    exit;
}

// 4. Homepage
if ($path === '' || $path === 'index.php') {
    dispatch(__DIR__ . '/pages/index.php');
}

// 5. Dynamic Routes
// Hex route: /color/3498db/ or /color/3498db
if (preg_match('#^color/([0-9a-fA-F]{6})/?$#', $path, $matches)) {
    dispatch(__DIR__ . '/pages/color.php', ['hex' => strtoupper($matches[1])]);
}

// Named Color Slug route: /color/midnight-blue/ or /color/midnight-blue
if (preg_match('#^color/([a-zA-Z][a-zA-Z0-9-]*)/?$#', $path, $matches)) {
    dispatch(__DIR__ . '/pages/color.php', ['slug' => strtolower($matches[1])]);
}

// Palette route: /palette/sunset-vibes/
if (preg_match('#^palette/([a-zA-Z0-9_-]+)/?$#', $path, $matches)) {
    dispatch(__DIR__ . '/pages/palette.php', ['slug' => $matches[1]]);
}

// Gradient route: /gradient/gradient_1/
if (preg_match('#^gradient/([a-zA-Z0-9_-]+)/?$#', $path, $matches)) {
    dispatch(__DIR__ . '/pages/gradient.php', ['id' => $matches[1]]);
}

// Dynamic Color Swatch Image: /colors/3498db.webp
if (preg_match('#^colors/([0-9a-fA-F]{6})\.webp$#i', $path, $matches)) {
    dispatch(__DIR__ . '/pages/colorImage.php', ['hex' => strtoupper($matches[1])]);
}

// 6. Auth Routes (support both extensionless and .php)
$cleanPath = preg_replace('/\.php$/i', '', $path);

$authRoutes = [
    'auth/google-login'    => __DIR__ . '/auth/google-login.php',
    'auth/google-callback' => __DIR__ . '/auth/google-callback.php',
    'auth/logout'          => __DIR__ . '/auth/logout.php',
    'auth/delete-account'  => __DIR__ . '/auth/delete-account.php',
    'auth/favorites'       => __DIR__ . '/auth/favorites.php',
    'auth/admin-auth'      => __DIR__ . '/auth/admin-auth.php',
];
if (isset($authRoutes[$path]) || isset($authRoutes[$cleanPath])) {
    dispatch($authRoutes[$path] ?? $authRoutes[$cleanPath]);
}

// 7. Static Pages (support both extensionless and .php)
$staticPages = [
    'admin'              => __DIR__ . '/pages/admin.php',
    'palettes'           => __DIR__ . '/pages/palettes.php',
    'gradients'          => __DIR__ . '/pages/gradients.php',
    'generate-palette'   => __DIR__ . '/pages/generate-palette.php',
    'palette-from-image' => __DIR__ . '/pages/palette-from-image.php',
    'find-color'         => __DIR__ . '/pages/find-color.php',
    'profile'            => __DIR__ . '/pages/profile.php',
    'login'              => __DIR__ . '/pages/login.php',
    'open-source'        => __DIR__ . '/pages/open-source.php',
    'hex-to-color-name'  => __DIR__ . '/pages/hex-to-color-name.php',
    'hex-to-rgb'         => __DIR__ . '/pages/hex-to-rgb.php',
    'what-color-is'      => __DIR__ . '/pages/what-color-is.php',
    'dark-color-finder'  => __DIR__ . '/pages/dark-color-finder.php',
    'brand-color-lookup' => __DIR__ . '/pages/brand-color-lookup.php',
    'about'              => __DIR__ . '/pages/about.php',
    'contact'            => __DIR__ . '/pages/contact.php',
    'privacy-policy'     => __DIR__ . '/pages/privacy-policy.php',
    'terms-and-conditions' => __DIR__ . '/pages/terms-and-conditions.php',
    'terms'              => __DIR__ . '/pages/terms-and-conditions.php',
    'cookie-policy'      => __DIR__ . '/pages/cookie-policy.php',
];
if (isset($staticPages[$path]) || isset($staticPages[$cleanPath])) {
    dispatch($staticPages[$path] ?? $staticPages[$cleanPath]);
}

// Also handle direct access to pages/xxx or pages/xxx.php
if (preg_match('#^pages/([a-zA-Z0-9_-]+)(\.php)?$#', $path, $matches)) {
    $target = __DIR__ . '/pages/' . $matches[1] . '.php';
    if (file_exists($target)) {
        dispatch($target);
    }
}

// 8. 404 Fallback
http_response_code(404);
echo "404 Not Found: " . htmlspecialchars($uriPath);
