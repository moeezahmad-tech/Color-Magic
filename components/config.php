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

if (!headers_sent()) {
    header('Link: <' . $base . '/assets/images/logo.png>; rel="icon"; type="image/png"');
}
?>
<script>window.CM_BASE_PATH = "<?= $base ?>";</script>
<?php

/**
 * Renders an inline <script> tag containing preloaded JSON datasets ('gradients', 'palettes', 'color-names').
 * Prevents frontend JavaScript from making HTTP fetch() requests to static JSON files,
 * eliminating extra server hits and unwanted Google Analytics / server logging entries.
 *
 * @param array $keys Array of data keys to inline, e.g. ['gradients', 'palettes']
 */
function renderInlineData(array $keys): void {
    // Data is fetched via direct HTTP fetch() to https://api.colormagic.techkreative.com/
}

