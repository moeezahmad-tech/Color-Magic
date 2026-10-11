<?php
/**
 * Dynamic XML Sitemap Generator for Color Magic
 * Generates all sitemap links dynamically from live datasets and pages.
 * Supports:
 *   - /sitemap.php or /sitemap.xml         -> Complete dynamic sitemap with all URLs
 *   - /sitemap.php?type=index              -> Master sitemap index
 *   - /sitemap.php?type=pages              -> Dynamic pages sitemap
 *   - /sitemap.php?type=colors             -> Dynamic colors sitemap (with image tags)
 *   - /sitemap.php?type=palettes           -> Dynamic palettes sitemap
 *   - /sitemap.php?type=gradients          -> Dynamic gradients sitemap
 */

declare(strict_types=1);

// Set XML headers
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
          (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
          (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http';

$httpHost = (string)($_SERVER['HTTP_HOST'] ?? 'colormagic.techkreative.com');
$baseUrl = (strpos($httpHost, 'localhost') !== false || strpos($httpHost, '127.0.0.1') !== false)
    ? $scheme . '://' . $httpHost
    : 'https://colormagic.techkreative.com';

$type = strtolower(trim((string)($_GET['type'] ?? 'all')));

function xmlEscape(string $str): string
{
    return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function slugify(string $name): string
{
    return trim(strtolower((string)preg_replace('/[^a-zA-Z0-9]+/i', '-', $name)), '-');
}

// ── SITEMAP INDEX MODE ────────────────────────────────────────────────────────
if ($type === 'index') {
    $now = date('Y-m-d');
    $pagesMod = file_exists(__DIR__ . '/pages/index.php') ? date('Y-m-d', filemtime(__DIR__ . '/pages/index.php')) : $now;
    $colorsMod = file_exists(__DIR__ . '/data/color-names.json') ? date('Y-m-d', filemtime(__DIR__ . '/data/color-names.json')) : $now;
    $palettesMod = file_exists(__DIR__ . '/data/palettes.json') ? date('Y-m-d', filemtime(__DIR__ . '/data/palettes.json')) : $now;
    $gradientsMod = file_exists(__DIR__ . '/data/gradients.json') ? date('Y-m-d', filemtime(__DIR__ . '/data/gradients.json')) : $now;

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    $sections = [
        ['loc' => $baseUrl . '/sitemaps/pages.xml', 'lastmod' => $pagesMod],
        ['loc' => $baseUrl . '/sitemaps/colors.xml', 'lastmod' => $colorsMod],
        ['loc' => $baseUrl . '/sitemaps/palettes.xml', 'lastmod' => $palettesMod],
        ['loc' => $baseUrl . '/sitemaps/gradients.xml', 'lastmod' => $gradientsMod],
    ];

    foreach ($sections as $s) {
        echo '  <sitemap>' . "\n";
        echo '    <loc>' . xmlEscape($s['loc']) . '</loc>' . "\n";
        echo '    <lastmod>' . xmlEscape($s['lastmod']) . '</lastmod>' . "\n";
        echo '  </sitemap>' . "\n";
    }

    echo '</sitemapindex>' . "\n";
    exit;
}

// ── URLSET SITEMAP MODE ───────────────────────────────────────────────────────
$includePages = in_array($type, ['all', '', 'pages'], true);
$includeColors = in_array($type, ['all', '', 'colors'], true);
$includePalettes = in_array($type, ['all', '', 'palettes'], true);
$includeGradients = in_array($type, ['all', '', 'gradients'], true);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
if ($includeColors) {
    echo "\n        xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\"";
}
echo '>' . "\n";

// 1. Static Pages
if ($includePages) {
    $staticPages = [
        ['path' => '',                    'priority' => '1.0', 'freq' => 'weekly',  'file' => 'index.php'],
        ['path' => 'palettes',            'priority' => '0.9', 'freq' => 'weekly',  'file' => 'palettes.php'],
        ['path' => 'gradients',           'priority' => '0.9', 'freq' => 'weekly',  'file' => 'gradients.php'],
        ['path' => 'generate-palette',    'priority' => '0.9', 'freq' => 'weekly',  'file' => 'generate-palette.php'],
        ['path' => 'palette-from-image',  'priority' => '0.9', 'freq' => 'weekly',  'file' => 'palette-from-image.php'],
        ['path' => 'find-color',          'priority' => '0.9', 'freq' => 'weekly',  'file' => 'find-color.php'],
        ['path' => 'hex-to-color-name',   'priority' => '0.8', 'freq' => 'monthly', 'file' => 'hex-to-color-name.php'],
        ['path' => 'hex-to-rgb',          'priority' => '0.8', 'freq' => 'monthly', 'file' => 'hex-to-rgb.php'],
        ['path' => 'what-color-is',       'priority' => '0.8', 'freq' => 'monthly', 'file' => 'what-color-is.php'],
        ['path' => 'dark-color-finder',   'priority' => '0.8', 'freq' => 'monthly', 'file' => 'dark-color-finder.php'],
        ['path' => 'brand-color-lookup',  'priority' => '0.8', 'freq' => 'monthly', 'file' => 'brand-color-lookup.php'],
        ['path' => 'open-source',         'priority' => '0.8', 'freq' => 'monthly', 'file' => 'open-source.php'],
    ];

    foreach ($staticPages as $page) {
        $filePath = __DIR__ . '/pages/' . $page['file'];
        $lastmod = file_exists($filePath) ? date('Y-m-d', filemtime($filePath)) : date('Y-m-d');
        $url = $baseUrl . '/' . ($page['path'] !== '' ? $page['path'] : '');

        echo "  <url>\n";
        echo "    <loc>" . xmlEscape($url) . "</loc>\n";
        echo "    <lastmod>{$lastmod}</lastmod>\n";
        echo "    <changefreq>{$page['freq']}</changefreq>\n";
        echo "    <priority>{$page['priority']}</priority>\n";
        echo "  </url>\n";
    }
}

// 2. Palettes
if ($includePalettes) {
    $palettesFile = __DIR__ . '/data/palettes.json';
    if (file_exists($palettesFile)) {
        $palettesData = json_decode((string)file_get_contents($palettesFile), true);
        $lastmod = date('Y-m-d', filemtime($palettesFile));

        if (is_array($palettesData)) {
            // Count names for duplicate suffixing
            $nameCount = [];
            foreach ($palettesData as $p) {
                $name = (string)($p['name'] ?? '');
                $s = slugify($name);
                $nameCount[$s] = ($nameCount[$s] ?? 0) + 1;
            }

            foreach ($palettesData as $palette) {
                $name = (string)($palette['name'] ?? '');
                $id = (string)($palette['id'] ?? '');
                $s = slugify($name);
                if (($nameCount[$s] ?? 0) > 1) {
                    $s .= '-' . str_replace('palette_', '', $id);
                }
                if ($s === '') {
                    $s = $id;
                }
                $loc = $baseUrl . '/palette/' . $s . '/';

                echo "  <url>\n";
                echo "    <loc>" . xmlEscape($loc) . "</loc>\n";
                echo "    <lastmod>{$lastmod}</lastmod>\n";
                echo "    <changefreq>monthly</changefreq>\n";
                echo "    <priority>0.8</priority>\n";
                echo "  </url>\n";
            }
        }
    }
}

// 3. Gradients
if ($includeGradients) {
    $gradientsFile = __DIR__ . '/data/gradients.json';
    if (file_exists($gradientsFile)) {
        $gradientsData = json_decode((string)file_get_contents($gradientsFile), true);
        $lastmod = date('Y-m-d', filemtime($gradientsFile));

        if (is_array($gradientsData)) {
            foreach ($gradientsData as $gradient) {
                $id = (string)($gradient['id'] ?? '');
                if ($id === '') continue;
                $loc = $baseUrl . '/gradient/' . $id . '/';

                echo "  <url>\n";
                echo "    <loc>" . xmlEscape($loc) . "</loc>\n";
                echo "    <lastmod>{$lastmod}</lastmod>\n";
                echo "    <changefreq>monthly</changefreq>\n";
                echo "    <priority>0.6</priority>\n";
                echo "  </url>\n";
            }
        }
    }
}

// 4. Colors
if ($includeColors) {
    $colorsFile = __DIR__ . '/data/color-names.json';
    if (file_exists($colorsFile)) {
        $colorsData = json_decode((string)file_get_contents($colorsFile), true);
        $lastmod = date('Y-m-d', filemtime($colorsFile));

        if (is_array($colorsData)) {
            foreach ($colorsData as $hexKey => $entry) {
                $cleanHex = strtoupper((string)(is_array($entry) ? ($entry['hex'] ?? $hexKey) : $hexKey));
                $name = (string)(is_array($entry) ? ($entry['name'] ?? $cleanHex) : $entry);
                $slug = (string)(is_array($entry) ? ($entry['slug'] ?? slugify($name)) : slugify($name));

                $colorUrl = ($slug !== '')
                    ? $baseUrl . '/color/' . $slug . '/'
                    : $baseUrl . '/color/' . strtolower($cleanHex) . '/';

                $imgUrl = $baseUrl . '/colors/' . strtolower($cleanHex) . '.webp';
                $title = "{$name} (#{$cleanHex})";
                $caption = "Color swatch and palette preview for hex code #{$cleanHex} - {$name}.";

                echo "  <url>\n";
                echo "    <loc>" . xmlEscape($colorUrl) . "</loc>\n";
                echo "    <lastmod>{$lastmod}</lastmod>\n";
                echo "    <changefreq>monthly</changefreq>\n";
                echo "    <priority>0.8</priority>\n";
                echo "    <image:image>\n";
                echo "      <image:loc>" . xmlEscape($imgUrl) . "</image:loc>\n";
                echo "      <image:title>" . xmlEscape($title) . "</image:title>\n";
                echo "      <image:caption>" . xmlEscape($caption) . "</image:caption>\n";
                echo "    </image:image>\n";
                echo "  </url>\n";
            }
        }
    }
}

echo "</urlset>\n";
