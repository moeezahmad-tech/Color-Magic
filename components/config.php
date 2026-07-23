<?php
/**
 * Environment-aware base path configuration.
 *
 * $base is empty ('') on production and '/ColorMagic' on local dev.
 * Use it as a prefix for ALL URLs: href, src, canonical, og:url, etc.
 *
 * Usage:
 *   <script src="<?= $base ?>/assets/js/app.js"></script>
 *   <a href="<?= $base ?>/palettes">Explore</a>
 */
/** @var string $base Environment-aware base path: empty on production, '/ColorMagic' on local dev. */
$base = (isset($_SERVER['HTTP_HOST']) && preg_match('/^(localhost|127\.0\.0\.1)(:\d+)?$/', $_SERVER['HTTP_HOST']))
    ? '/ColorMagic'
    : '';
