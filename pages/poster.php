<?php
/**
 * pages/poster.php
 * Dynamic, high-performance poster image streaming & on-demand caching.
 *
 * Routes:
 *   /posters/palettes/{slug}.jpg
 *   /posters/gradients/{id}.jpg
 *   /posters/colors/{slug}.jpg
 */

declare(strict_types=1);

require_once __DIR__ . '/../services/PosterCacheService.php';

$category = strtolower(trim((string)($_GET['category'] ?? 'palettes')));
$idOrSlug = trim((string)($_GET['id'] ?? ''));

if (empty($idOrSlug)) {
    http_response_code(400);
    echo "Missing required poster identifier.";
    exit;
}

// Strip potential .jpg extension if passed in query param
$idOrSlug = preg_replace('/\.jpg$/i', '', $idOrSlug);

$targetFile = null;

// Map category to folder name
$folderCategory = match($category) {
    'palettes', 'palette' => 'palettes',
    'gradients', 'gradient' => 'gradients',
    'colors', 'color' => 'colors',
    default => $category
};

// 1. Fast path: check if cached image already exists on disk and is valid
$cachedFile = __DIR__ . '/../assets/generated/posters/' . $folderCategory . '/' . $idOrSlug . '.jpg';
if (file_exists($cachedFile) && filesize($cachedFile) > 1000) {
    $targetFile = $cachedFile;
}

if (!$targetFile) {
    if ($category === 'palettes' || $category === 'palette') {
        $palettesFile = __DIR__ . '/../data/palettes.json';
        $palettes = file_exists($palettesFile) ? json_decode((string)file_get_contents($palettesFile), true) : [];
        $matched = null;

        if (is_array($palettes)) {
            foreach ($palettes as $p) {
                $nameSlug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower((string)($p['name'] ?? ''))), '-');
                $pId = (string)($p['id'] ?? '');
                if ($nameSlug === strtolower($idOrSlug) || $pId === $idOrSlug) {
                    $matched = $p;
                    break;
                }
            }
        }

        if ($matched) {
            $matched['slug'] = $idOrSlug;
            $targetFile = PosterCacheService::getPalettePoster($matched);
        }
    } elseif ($category === 'gradients' || $category === 'gradient') {
        $gradientsFile = __DIR__ . '/../data/gradients.json';
        $gradients = file_exists($gradientsFile) ? json_decode((string)file_get_contents($gradientsFile), true) : [];
        $matched = null;

        if (is_array($gradients)) {
            foreach ($gradients as $g) {
                $gId = (string)($g['id'] ?? '');
                $nameSlug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower((string)($g['name'] ?? ''))), '-');
                if ($gId === $idOrSlug || $nameSlug === strtolower($idOrSlug)) {
                    $matched = $g;
                    break;
                }
            }
        }

        if ($matched) {
            $matched['id'] = $idOrSlug;
            $targetFile = PosterCacheService::getGradientPoster($matched);
        }
    } elseif ($category === 'colors' || $category === 'color') {
        $colorsFile = __DIR__ . '/../data/color-names.json';
        $colors = file_exists($colorsFile) ? json_decode((string)file_get_contents($colorsFile), true) : [];
        $matched = null;

        $cleanSearch = strtolower(ltrim($idOrSlug, '#'));
        if (is_array($colors)) {
            foreach ($colors as $key => $c) {
                $h = strtolower((string)($c['hex'] ?? $key));
                $s = strtolower((string)($c['slug'] ?? ''));
                if ($h === $cleanSearch || $s === $cleanSearch) {
                    $matched = $c;
                    break;
                }
            }
        }

        // Fallback: If not found in named colors, support arbitrary hex colors (3 to 8 hex chars)
        if (!$matched && preg_match('/^[a-f0-9]{3,8}$/i', $cleanSearch)) {
            $hex = (strlen($cleanSearch) === 3)
                ? $cleanSearch[0].$cleanSearch[0].$cleanSearch[1].$cleanSearch[1].$cleanSearch[2].$cleanSearch[2]
                : substr($cleanSearch, 0, 6);
            $matched = [
                'hex'  => strtoupper($hex),
                'name' => '#' . strtoupper($hex),
                'slug' => strtolower($cleanSearch)
            ];
        }

        if ($matched) {
            $matched['slug'] = $idOrSlug;
            $targetFile = PosterCacheService::getColorPoster($matched);
        }
    }
}

if (!$targetFile || !file_exists($targetFile)) {
    http_response_code(404);
    echo "Poster not found.";
    exit;
}

// Stream the JPEG with strong browser and CDN caching
$mtime = filemtime($targetFile);
$etag = '"' . md5($targetFile . $mtime) . '"';

header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($targetFile));
header('Cache-Control: public, max-age=31536000, immutable');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
header('ETag: ' . $etag);

// Support 304 Not Modified
if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime)) {
    http_response_code(304);
    exit;
}

readfile($targetFile);
exit;

