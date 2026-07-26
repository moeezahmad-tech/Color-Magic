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
