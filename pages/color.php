<?php
include '../components/config.php';
/** @var string $base */

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function normalizeHex(string $hex): string
{
    return strtoupper(ltrim(trim($hex), '#'));
}

function hexToRgb(string $hex): array
{
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2)),
    ];
}

function hexToHsl(string $hex): array
{
    $rgb = hexToRgb($hex);
    $r = $rgb['r'] / 255;
    $g = $rgb['g'] / 255;
    $b = $rgb['b'] / 255;

    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $l = ($max + $min) / 2;

    if ($max === $min) {
        return ['h' => 0, 's' => 0, 'l' => (int) round($l * 100)];
    }

    $d = $max - $min;
    $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

    if ($max === $r) {
        $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
    } elseif ($max === $g) {
        $h = ($b - $r) / $d + 2;
    } else {
        $h = ($r - $g) / $d + 4;
    }

    $h /= 6;

    return [
        'h' => (int) round($h * 360),
        's' => (int) round($s * 100),
        'l' => (int) round($l * 100),
    ];
}

function relativeLuminance(string $hex): float
{
    $rgb = hexToRgb($hex);
    $channels = [$rgb['r'], $rgb['g'], $rgb['b']];
    $linear = array_map(static function (int $v): float {
        $x = $v / 255;
        return $x <= 0.03928 ? ($x / 12.92) : pow(($x + 0.055) / 1.055, 2.4);
    }, $channels);

    return ($linear[0] * 0.2126) + ($linear[1] * 0.7152) + ($linear[2] * 0.0722);
}

function bestContrastText(string $hex): string
{
    return relativeLuminance($hex) > 0.179 ? '#111111' : '#FFFFFF';
}

function colorDistanceSq(string $hexA, string $hexB): int
{
    $a = hexToRgb($hexA);
    $b = hexToRgb($hexB);
    $dr = $a['r'] - $b['r'];
    $dg = $a['g'] - $b['g'];
    $db = $a['b'] - $b['b'];
    return ($dr * $dr) + ($dg * $dg) + ($db * $db);
}

function mixColor(array $base, array $mix, float $weight): string {
    $r = (int) round($base['r'] * $weight + $mix['r'] * (1 - $weight));
    $g = (int) round($base['g'] * $weight + $mix['g'] * (1 - $weight));
    $b = (int) round($base['b'] * $weight + $mix['b'] * (1 - $weight));
    return sprintf('%02x%02x%02x', $r, $g, $b);
}

$hexParam = isset($_GET['hex']) ? (string) $_GET['hex'] : '';
$slugParam = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$normalizedHex = normalizeHex($hexParam);
$isValidHex = (bool) preg_match('/^[A-F0-9]{6}$/', $normalizedHex);
$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$isProduction = $host === 'colormagic.techkreative.com';

if (!$isValidHex) {
    $normalizedHex = '000000';
}

$hexWithHash = '#' . $normalizedHex;

$colorNames = [];
$colorNamesPath = __DIR__ . '/../data/color-names.json';
if (is_file($colorNamesPath)) {
    $decoded = json_decode((string) file_get_contents($colorNamesPath), true);
    if (is_array($decoded)) {
        $colorNames = $decoded;
    }
}

// If accessed via slug (?slug=midnight-blue), look up the hex from the slug
if ($slugParam !== '' && $hexParam === '') {
    $slugMap = [];
    foreach ($colorNames as $entry) {
        if (is_array($entry) && isset($entry['slug'], $entry['hex'])) {
            $slugMap[$entry['slug']] = $entry['hex'];
        }
    }
    if (isset($slugMap[$slugParam])) {
        $normalizedHex = strtoupper($slugMap[$slugParam]);
        $isValidHex = true;
        $hexWithHash = '#' . $normalizedHex;
    } else {
        // Unknown slug — fall back to 404-like redirect
        header('HTTP/1.1 404 Not Found');
        exit;
    }
}

$paletteData = [];
$palettePath = __DIR__ . '/../data/palettes.json';
if (is_file($palettePath)) {
    $decoded = json_decode((string) file_get_contents($palettePath), true);
    if (is_array($decoded)) {
        $paletteData = $decoded;
    }
}

// Keys in color-names.json are uppercase hex without '#'
$hexNameEntry = $colorNames[strtoupper($normalizedHex)] ?? null;
$hexName = is_array($hexNameEntry) ? ($hexNameEntry['name'] ?? ('Hex ' . $hexWithHash)) : ('Hex ' . $hexWithHash);
$colorSlug = is_array($hexNameEntry) ? ($hexNameEntry['slug'] ?? null) : null;

// If accessed via hex URL and this color has a named slug, 301 redirect to the slug URL
if ($slugParam === '' && $colorSlug !== null) {
    $scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $httpHost    = (string) ($_SERVER['HTTP_HOST'] ?? 'colormagic.techkreative.com');
    $scriptName  = (string) ($_SERVER['SCRIPT_NAME'] ?? '/color.php');
    $basePath    = $base;
    $slugUrl     = $scheme . '://' . $httpHost . $basePath . '/color/' . $colorSlug . '/';
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $slugUrl);
    exit;
}
$rgb = hexToRgb($normalizedHex);
$hsl = hexToHsl($normalizedHex);
$contrast = bestContrastText($normalizedHex);

$shades = [];
for ($i = 1; $i <= 10; $i++) {
    $shades[] = mixColor($rgb, ['r'=>0,'g'=>0,'b'=>0], $i / 10);
}

$tints = [];
for ($i = 10; $i >= 1; $i--) {
    $tints[] = mixColor($rgb, ['r'=>255,'g'=>255,'b'=>255], $i / 10);
}

$tones = [];
for ($i = 1; $i <= 10; $i++) {
    $tones[] = mixColor($rgb, ['r'=>128,'g'=>128,'b'=>128], $i / 10);
}

$darkestShade = '#' . $shades[0];
$lightestTint = '#' . $tints[9];
$leastSaturatedTone = '#' . $tones[0];

$exactPalettes = [];
$nearPalettes = [];

foreach ($paletteData as $palette) {
    if (!is_array($palette) || !isset($palette['colors']) || !is_array($palette['colors'])) {
        continue;
    }

    $paletteHexes = [];
    foreach ($palette['colors'] as $paletteHex) {
        if (!is_string($paletteHex)) {
            continue;
        }
        $n = normalizeHex($paletteHex);
        if ((bool) preg_match('/^[A-F0-9]{6}$/', $n)) {
            $paletteHexes[] = $n;
        }
    }

    if ($paletteHexes === []) {
        continue;
    }

    $palette['colors'] = array_map(
        static function (string $h): string {
            return '#' . $h;
        },
        $paletteHexes
    );

    if (in_array($normalizedHex, $paletteHexes, true)) {
        $exactPalettes[] = $palette;
        continue;
    }

    $closest = PHP_INT_MAX;
    foreach ($paletteHexes as $pHex) {
        $distance = colorDistanceSq($normalizedHex, $pHex);
        if ($distance < $closest) {
            $closest = $distance;
        }
    }

    $palette['_distance'] = $closest;
    $nearPalettes[] = $palette;
}

usort(
    $nearPalettes,
    static function (array $a, array $b): int {
        return ($a['_distance'] ?? PHP_INT_MAX) <=> ($b['_distance'] ?? PHP_INT_MAX);
    }
);

$relatedPalettes = $exactPalettes;
foreach ($nearPalettes as $nearPalette) {
    if (count($relatedPalettes) >= 12) {
        break;
    }
    $relatedPalettes[] = $nearPalette;
}

$styles = [];
foreach ($relatedPalettes as $palette) {
    if (!empty($palette['style']) && is_string($palette['style'])) {
        $styles[] = $palette['style'];
    }
}
$styles = array_values(array_unique($styles));
$styleSnippet = $styles !== [] ? implode(', ', array_slice($styles, 0, 3)) : 'curated';

$hasColorName = isset($colorNames[strtoupper($normalizedHex)]) && is_array($colorNames[strtoupper($normalizedHex)]);
$metaTitle = $hasColorName
    ? $hexName . ' ' . $hexWithHash . ' Color Details, RGB, HSL and Related Palettes | Color Magic'
    : $hexName . ' Color Details, RGB, HSL and Related Palettes | Color Magic';
$metaDescription = 'Explore ' . $hexName . ' (' . $hexWithHash . ') with RGB(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ') and HSL(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%). Discover ' . count($relatedPalettes) . ' related ' . $styleSnippet . ' color palettes from our colors library.';

$baseUrl = 'https://colormagic.techkreative.com';
$canonicalUrl = $colorSlug !== null
    ? $baseUrl . '/color/' . $colorSlug . '/'
    : $baseUrl . '/color/' . strtolower($normalizedHex) . '/';
$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/color.php');
$basePath = $base;

$assetBase = $basePath;
$homePageUrl = $baseUrl . '/';
$openSourceUrl = $assetBase . '/open-source';
$manifestUrl = $assetBase . '/manifest.json';
$faviconUrl = $assetBase . '/assets/images/logo.png';
$logoUrl = $assetBase . '/assets/images/logo.png';
$siteStylesUrl = $assetBase . '/assets/css/style.css?v=1.0';
$tailwindConfigUrl = $assetBase . '/assets/js/tailwind-config.js';
$colorRouteBase = $assetBase . '/color/';
$dynamicColorImageUrl = $baseUrl . '/colors/' . strtolower($normalizedHex) . '.webp';
$ogImageUrl = $baseUrl . '/assets/og-preview.png';

// WCAG Contrast Ratios
function contrastRatio(float $l1, float $l2): float {
    $lighter = max($l1, $l2);
    $darker = min($l1, $l2);
    return ($lighter + 0.05) / ($darker + 0.05);
}

$colorLuminance = relativeLuminance($normalizedHex);
$whiteLuminance = 1.0;
$blackLuminance = 0.0;
$contrastWithWhite = round(contrastRatio($colorLuminance, $whiteLuminance), 2);
$contrastWithBlack = round(contrastRatio($colorLuminance, $blackLuminance), 2);

$wcagAANormal = 4.5;
$wcagAALarge = 3.0;
$wcagAAA = 7.0;

$whitePassesAA = $contrastWithWhite >= $wcagAANormal;
$whitePassesAALarge = $contrastWithWhite >= $wcagAALarge;
$whitePassesAAA = $contrastWithWhite >= $wcagAAA;
$blackPassesAA = $contrastWithBlack >= $wcagAANormal;
$blackPassesAALarge = $contrastWithBlack >= $wcagAALarge;
$blackPassesAAA = $contrastWithBlack >= $wcagAAA;

// Color Harmonies (based on HSL hue rotation)
$hue = $hsl['h'];
$complementaryHue = ($hue + 180) % 360;
$analogous1 = ($hue + 30) % 360;
$analogous2 = ($hue + 330) % 360;
$triadic1 = ($hue + 120) % 360;
$triadic2 = ($hue + 240) % 360;

function hslToHex(int $h, int $s, int $l): string {
    $s /= 100; $l /= 100;
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - $c / 2;
    if ($h < 60) { $r=$c; $g=$x; $b=0; }
    elseif ($h < 120) { $r=$x; $g=$c; $b=0; }
    elseif ($h < 180) { $r=0; $g=$c; $b=$x; }
    elseif ($h < 240) { $r=0; $g=$x; $b=$c; }
    elseif ($h < 300) { $r=$x; $g=0; $b=$c; }
    else { $r=$c; $g=0; $b=$x; }
    return sprintf('%02X%02X%02X', (int)round(($r+$m)*255), (int)round(($g+$m)*255), (int)round(($b+$m)*255));
}

$complementaryHex = '#' . hslToHex($complementaryHue, $hsl['s'], $hsl['l']);
$analogous1Hex = '#' . hslToHex($analogous1, $hsl['s'], $hsl['l']);
$analogous2Hex = '#' . hslToHex($analogous2, $hsl['s'], $hsl['l']);
$triadic1Hex = '#' . hslToHex($triadic1, $hsl['s'], $hsl['l']);
$triadic2Hex = '#' . hslToHex($triadic2, $hsl['s'], $hsl['l']);

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Hex ' . $hexWithHash . ' Color Palette & Harmonies',
    'url' => $canonicalUrl,
    'description' => $metaDescription,
    'primaryImageOfPage' => $dynamicColorImageUrl,
    'image' => $dynamicColorImageUrl,
    'mainEntity' => [
        '@type' => 'DefinedTerm',
        'name' => $hexName,
        'identifier' => $hexWithHash,
        'description' => 'RGB(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ') | HSL(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%)',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo e($metaTitle); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>" />
    <meta name="keywords" content="color palette generator, hex code details, rgb to hsl, designer tools, TechKreative" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="<?php echo $isValidHex ? 'index, follow' : 'noindex, follow'; ?>" />

    <?php if ($isProduction): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-537L4MR968');
    </script>
    <?php endif; ?>

    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo e($metaTitle); ?>" />
    <meta property="og:description" content="<?php echo e($metaDescription); ?>" />
    <meta property="og:url" content="<?php echo e($canonicalUrl); ?>" />
    <meta property="og:image" content="<?php echo e($dynamicColorImageUrl); ?>" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" content="<?php echo e($metaTitle); ?>" />
    <meta property="twitter:description" content="<?php echo e($metaDescription); ?>" />
    <meta property="twitter:image" content="<?php echo e($dynamicColorImageUrl); ?>" />

    <link rel="manifest" href="<?php echo e($manifestUrl); ?>" />
    <link rel="icon" type="image/png" href="<?php echo e($faviconUrl); ?>" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?php echo e($siteStylesUrl); ?>" />
    <script id="tailwind-config" src="<?php echo e($tailwindConfigUrl); ?>"></script>

    <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

    <style>
        .copy-feedback {
            transition: all 0.25s ease;
        }

        .copy-feedback.show {
            opacity: 1;
            transform: translateY(0);
        }

        .sb-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            transition: all 0.25s ease;
        }
        .sb-btn .sb-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .sb-btn:hover .sb-icon { transform: scale(1.12); }
        .sb-btn.sb-inactive { color: #475569; background: transparent; }
        .sb-btn.sb-inactive:hover { background: #fdf2f8; color: #ec4899; }
        .sb-btn.sb-inactive .sb-icon { background: #fdf2f8; color: #ec4899; }
        .sb-btn.sb-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            box-shadow: 0 8px 24px -6px rgba(236, 72, 153, 0.45);
        }
        .sb-btn.sb-active .sb-icon { background: rgba(255, 255, 255, 0.18); color: #fff; }
        .sb-btn.sb-active:hover { box-shadow: 0 12px 28px -6px rgba(124, 58, 237, 0.45); transform: translateY(-1px); }
        .dark .sb-btn.sb-inactive { color: #94a3b8; }
        .dark .sb-btn.sb-inactive:hover { background: #1e293b; color: #ec4899; }
        .dark .sb-btn.sb-inactive .sb-icon { background: #1e293b; color: #ec4899; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .animate-fadeIn { animation: fadeIn 0.2s ease-out; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include '../components/navbar.php'; ?>
    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="<?php echo e($homePageUrl); ?>" class="flex items-center gap-2 text-primary">
                <img src="<?php echo e($logoUrl); ?>" alt="Color Magic Logo" class="h-8 w-8 object-contain" />
                <span class="text-xl font-bold tracking-tight"><span
                        class="text-slate-900 dark:text-white">Color</span><span
                        class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span></span>
            </a>
            <button id="closeMobileMenuBtn"
                class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                aria-label="Close menu">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        <div class="space-y-2">
            <a href="<?php echo e($homePageUrl); ?>" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-house-door"></i></span>
                <div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-palette"></i></span>
                <div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse collections</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-rainbow"></i></span>
                <div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS gradients</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-eyedropper"></i></span>
                <div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp; info</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-stars"></i></span>
                <div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color schemes</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/favorites" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-heart-fill"></i></span>
                <div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors, palettes &amp; gradients</span></div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?php echo e($openSourceUrl); ?>" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-github"></i></span>
                <div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on GitHub</span></div>
            </a>
        </div>
    </div>

    <main class="w-full max-w-7xl mx-auto pt-24 px-6 py-8 md:py-10">
        <div class="mb-6">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                <a class="hover:text-primary transition-colors" href="<?php echo e($homePageUrl); ?>">Home</a>
                <span class="mx-2">/</span>
                <span>Color <?php echo e($hexWithHash); ?></span>
            </p>
        </div>

        <section class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="grid grid-cols-1 lg:grid-cols-[420px,1fr]">
                <div class="relative min-h-[280px] md:min-h-[360px] p-6 flex items-end overflow-hidden" style="background-color: <?php echo e($hexWithHash); ?>;">
                    <img
                        src="<?php echo e($dynamicColorImageUrl); ?>"
                        alt="Color palette preview for hex code <?php echo e(strtolower($normalizedHex)); ?> featuring complementary design rules"
                        width="600"
                        height="600"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="eager"
                        fetchpriority="high" />
                    <div class="relative z-10 px-4 py-3 rounded-xl backdrop-blur-sm" style="background: rgba(15, 23, 42, 0.25); color: <?php echo e($contrast); ?>;">
                        <p class="text-sm uppercase tracking-widest opacity-85">Selected Color</p>
                        <p class="text-3xl font-bold"><?php echo e($hexWithHash); ?></p>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-3"><?php echo e($hexName); ?> Color Details</h1>
                    <h2 class="text-xl md:text-2xl font-semibold tracking-tight mb-3">Explore Hex Code Details with the Color Magic Design Tool</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-base md:text-lg mb-6">
                        Complete color profile for <?php echo e($hexWithHash); ?> with copy-ready values and related palettes.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">Hex</p>
                            <p class="font-mono font-bold text-lg mb-3"><?php echo e($hexWithHash); ?></p>
                            <button class="copy-btn px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-all hover:opacity-95 shadow-lg shadow-primary/20" data-copy="<?php echo e($hexWithHash); ?>">Copy HEX</button>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">RGB</p>
                            <p class="font-mono font-bold text-lg mb-3"><?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?></p>
                            <button class="copy-btn px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-all hover:opacity-95 shadow-lg shadow-primary/20" data-copy="rgb(<?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?>)">Copy RGB</button>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">HSL</p>
                            <p class="font-mono font-bold text-lg mb-3"><?php echo e((string) $hsl['h']); ?>, <?php echo e((string) $hsl['s']); ?>%, <?php echo e((string) $hsl['l']); ?>%</p>
                            <button class="copy-btn px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-all hover:opacity-95 shadow-lg shadow-primary/20" data-copy="hsl(<?php echo e((string) $hsl['h']); ?>, <?php echo e((string) $hsl['s']); ?>%, <?php echo e((string) $hsl['l']); ?>%)">Copy HSL</button>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">Best Contrast</p>
                            <p class="font-semibold text-lg"><?php echo $contrast === '#FFFFFF' ? 'White text' : 'Dark text'; ?></p>
                            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400"><?php echo e((string) count($relatedPalettes)); ?> related palettes found</p>
                        </div>
                    </div>
                    <p id="copyFeedback" class="copy-feedback opacity-0 -translate-y-1 mt-4 text-sm font-semibold text-emerald-600">Copied to clipboard</p>
                </div>
            </div>
        </section>

        <section class="mt-12" aria-labelledby="shades-tints-title">
            <h2 id="shades-tints-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Shades and Tints of <?php echo e($hexWithHash); ?></h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A shade is achieved by adding black to any pure hue, while a tint is created by mixing white to any pure hue. 
                <span class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($darkestShade); ?></span> is the darkest color, while 
                <span class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($lightestTint); ?></span> is the lightest one.
            </p>

            <div class="mb-8">
                <div class="flex h-24 md:h-32 rounded-2xl overflow-hidden shadow-sm">
                    <?php foreach ($shades as $shadeHex): ?>
                        <a href="<?php echo e($colorRouteBase . $shadeHex . '/'); ?>" class="flex-1 transition-opacity hover:opacity-90 relative group" style="background-color: #<?php echo e($shadeHex); ?>;" aria-label="View shade <?php echo e('#' . $shadeHex); ?>">
                            <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 text-white text-xs font-mono font-bold backdrop-blur-sm transition-opacity">#<?php echo e($shadeHex); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="text-center text-sm text-slate-500 mt-2 font-medium">Shade Color Variation</p>
            </div>

            <div class="mb-10 lg:mb-12">
                <div class="flex h-24 md:h-32 rounded-2xl overflow-hidden shadow-sm">
                    <?php foreach ($tints as $tintHex): ?>
                        <a href="<?php echo e($colorRouteBase . $tintHex . '/'); ?>" class="flex-1 transition-opacity hover:opacity-90 relative group" style="background-color: #<?php echo e($tintHex); ?>;" aria-label="View tint <?php echo e('#' . $tintHex); ?>">
                            <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 text-white text-xs font-mono font-bold backdrop-blur-sm transition-opacity">#<?php echo e($tintHex); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="text-center text-sm text-slate-500 mt-2 font-medium">Tint Color Variation</p>
            </div>
        </section>

        <section class="mt-8 md:mt-12 mb-12 lg:mb-16" aria-labelledby="tones-title">
            <h2 id="tones-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Tones of <?php echo e($hexWithHash); ?></h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A tone is produced by adding gray to any pure hue. In this case, 
                <span class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($leastSaturatedTone); ?></span> is the less saturated color, while 
                <span class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($hexWithHash); ?></span> is the most saturated one.
            </p>

            <div>
                <div class="flex h-24 md:h-32 rounded-2xl overflow-hidden shadow-sm">
                    <?php foreach ($tones as $toneHex): ?>
                        <a href="<?php echo e($colorRouteBase . $toneHex . '/'); ?>" class="flex-1 transition-opacity hover:opacity-90 relative group" style="background-color: #<?php echo e($toneHex); ?>;" aria-label="View tone <?php echo e('#' . $toneHex); ?>">
                            <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 text-white text-xs font-mono font-bold backdrop-blur-sm transition-opacity">#<?php echo e($toneHex); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="text-center text-sm text-slate-500 mt-2 font-medium">Tone Color Variation</p>
            </div>
        </section>

        <!-- Color Harmonies Section -->
        <section class="mt-8 md:mt-12" aria-labelledby="harmonies-title">
            <h2 id="harmonies-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Color Harmonies</h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                Color harmonies derived from <?php echo e($hexWithHash); ?> using standard color theory rotation rules on the HSL color wheel.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-white dark:bg-slate-900">
                    <p class="text-xs uppercase tracking-widest text-slate-500 mb-2">Complementary</p>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e($colorRouteBase . strtolower(ltrim($complementaryHex, '#')) . '/'); ?>" class="w-12 h-12 rounded-lg shadow-sm block" style="background-color: <?php echo e($complementaryHex); ?>;"></a>
                        <span class="font-mono font-bold"><?php echo e($complementaryHex); ?></span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-white dark:bg-slate-900">
                    <p class="text-xs uppercase tracking-widest text-slate-500 mb-2">Analogous</p>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e($colorRouteBase . strtolower(ltrim($analogous1Hex, '#')) . '/'); ?>" class="w-12 h-12 rounded-lg shadow-sm block" style="background-color: <?php echo e($analogous1Hex); ?>;"></a>
                        <a href="<?php echo e($colorRouteBase . strtolower(ltrim($analogous2Hex, '#')) . '/'); ?>" class="w-12 h-12 rounded-lg shadow-sm block" style="background-color: <?php echo e($analogous2Hex); ?>;"></a>
                        <span class="font-mono text-sm"><?php echo e($analogous1Hex); ?>, <?php echo e($analogous2Hex); ?></span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-white dark:bg-slate-900">
                    <p class="text-xs uppercase tracking-widest text-slate-500 mb-2">Triadic</p>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo e($colorRouteBase . strtolower(ltrim($triadic1Hex, '#')) . '/'); ?>" class="w-12 h-12 rounded-lg shadow-sm block" style="background-color: <?php echo e($triadic1Hex); ?>;"></a>
                        <a href="<?php echo e($colorRouteBase . strtolower(ltrim($triadic2Hex, '#')) . '/'); ?>" class="w-12 h-12 rounded-lg shadow-sm block" style="background-color: <?php echo e($triadic2Hex); ?>;"></a>
                        <span class="font-mono text-sm"><?php echo e($triadic1Hex); ?>, <?php echo e($triadic2Hex); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- WCAG Accessibility Contrast Section -->
        <section class="mt-8 md:mt-12" aria-labelledby="wcag-title">
            <h2 id="wcag-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-4">WCAG Contrast Accessibility</h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                Contrast ratio analysis of <?php echo e($hexWithHash); ?> against white and black backgrounds per WCAG 2.1 guidelines.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5 bg-white dark:bg-slate-900">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-white border border-slate-200"></div>
                        <div>
                            <p class="font-bold">On White Background</p>
                            <p class="text-sm text-slate-500">Ratio: <span class="font-mono font-bold"><?php echo $contrastWithWhite; ?>:1</span></p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span>AA Normal Text (4.5:1)</span>
                            <span class="font-bold <?php echo $whitePassesAA ? 'text-emerald-600' : 'text-red-500'; ?>"><?php echo $whitePassesAA ? '✓ Pass' : '✗ Fail'; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>AA Large Text (3:1)</span>
                            <span class="font-bold <?php echo $whitePassesAALarge ? 'text-emerald-600' : 'text-red-500'; ?>"><?php echo $whitePassesAALarge ? '✓ Pass' : '✗ Fail'; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>AAA Normal Text (7:1)</span>
                            <span class="font-bold <?php echo $whitePassesAAA ? 'text-emerald-600' : 'text-red-500'; ?>"><?php echo $whitePassesAAA ? '✓ Pass' : '✗ Fail'; ?></span>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-5 bg-white dark:bg-slate-900">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-black"></div>
                        <div>
                            <p class="font-bold">On Black Background</p>
                            <p class="text-sm text-slate-500">Ratio: <span class="font-mono font-bold"><?php echo $contrastWithBlack; ?>:1</span></p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span>AA Normal Text (4.5:1)</span>
                            <span class="font-bold <?php echo $blackPassesAA ? 'text-emerald-600' : 'text-red-500'; ?>"><?php echo $blackPassesAA ? '✓ Pass' : '✗ Fail'; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>AA Large Text (3:1)</span>
                            <span class="font-bold <?php echo $blackPassesAALarge ? 'text-emerald-600' : 'text-red-500'; ?>"><?php echo $blackPassesAALarge ? '✓ Pass' : '✗ Fail'; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>AAA Normal Text (7:1)</span>
                            <span class="font-bold <?php echo $blackPassesAAA ? 'text-emerald-600' : 'text-red-500'; ?>"><?php echo $blackPassesAAA ? '✓ Pass' : '✗ Fail'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-400">Note: Full WCAG validation requires manual testing with assistive technologies and expert accessibility review.</p>
        </section>

        <section class="mt-8" aria-labelledby="related-palettes-title">
            <h2 id="related-palettes-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-5">
                Related Color Palettes
            </h2>

            <?php if ($relatedPalettes === []): ?>
                <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900">
                    No related palettes found in the current library.
                </div>
            <?php else: ?>

                <!-- Rendered by JS using the shared createPaletteCard() component -->
                <div id="relatedPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8"></div>

                <script>
                    window._relatedPalettes = <?php echo json_encode(array_values($relatedPalettes), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                </script>

            <?php endif; ?>

        </section>

        <!-- Related Tools -->
        <section class="mt-8 md:mt-12 mb-8" aria-labelledby="related-tools-title">
            <h2 id="related-tools-title" class="text-xl md:text-2xl font-bold tracking-tight mb-4">Related Color Tools</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="<?php echo e($assetBase); ?>/hex-to-rgb" class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <i class="bi bi-sliders text-secondary text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Hex to RGB Converter</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Convert this color to RGB instantly</p>
                    </div>
                </a>
                <a href="<?php echo e($assetBase); ?>/find-color" class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <i class="bi bi-eyedropper text-emerald-500 text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Find Color</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Look up any hex code details</p>
                    </div>
                </a>
                <a href="<?php echo e($assetBase); ?>/generate-palette" class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <i class="bi bi-stars text-pink-500 text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Generate Palette</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Create schemes from this color</p>
                    </div>
                </a>
            </div>
        </section>



    </main>
    <?php include "../components/footer.php"; ?>

    <!-- Shared palette component (same as explore page) -->
    <script>
        // Base paths must be set before component scripts read them
        window.CM_COLOR_BASE   = '<?php echo e($colorRouteBase); ?>';
        window.CM_PALETTE_BASE = '<?php echo e($assetBase); ?>/palette/';
    </script>
    <script src="<?php echo e($assetBase); ?>/assets/js/utils.js"></script>
    <script src="<?php echo e($assetBase); ?>/assets/js/services/favorites.js"></script>
    <script src="<?php echo e($assetBase); ?>/assets/js/components/palette-card.js"></script>

    <script>
        // ── Copy HEX / RGB / HSL buttons ─────────────────────────────────────
        var copyFeedback = document.getElementById('copyFeedback');

        document.querySelectorAll('.copy-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var text = button.getAttribute('data-copy') || '';
                navigator.clipboard.writeText(text).then(function () {
                    if (copyFeedback) {
                        copyFeedback.classList.add('show');
                        copyFeedback.textContent = text + ' copied';
                        setTimeout(function () { copyFeedback.classList.remove('show'); }, 1200);
                    }
                }).catch(function () {
                    if (copyFeedback) {
                        copyFeedback.classList.add('show');
                        copyFeedback.textContent = 'Copy failed. Please copy manually.';
                        setTimeout(function () { copyFeedback.classList.remove('show'); }, 1500);
                    }
                });
            });
        });

        // ── Render related palettes using the shared createPaletteCard() ──────
        (function renderRelatedPalettes() {
            var grid = document.getElementById('relatedPalettesGrid');
            var data = window._relatedPalettes;
            if (!grid || !data || !window.ColorMagic || !window.ColorMagic.createPaletteCard) return;

            window.ColorMagic.markDuplicateSlugs(data);

            var fragment = document.createDocumentFragment();
            data.forEach(function (palette) {
                fragment.appendChild(window.ColorMagic.createPaletteCard(palette));
            });
            grid.appendChild(fragment);
        })();

        // ── Delegated handlers for related palette cards ──────────────────────
        document.addEventListener('click', function (e) {

            // Copy entire palette
            var copyBtn = e.target.closest('.copy-palette-btn');
            if (copyBtn) {
                var colors    = copyBtn.getAttribute('data-colors') || '';
                var icon      = copyBtn.querySelector('i');
                var origClass = icon ? icon.className : '';
                navigator.clipboard.writeText(colors).then(function () {
                    if (icon) icon.className = 'bi bi-check-circle-fill text-xl';
                    copyBtn.classList.add('text-green-500');
                    setTimeout(function () {
                        if (icon) icon.className = origClass;
                        copyBtn.classList.remove('text-green-500');
                    }, 2000);
                }).catch(function () {
                    if (icon) { icon.className = 'bi bi-x-circle text-xl'; setTimeout(function () { icon.className = origClass; }, 2000); }
                });
                return;
            }

            // Copy single swatch HEX
            var hexBtn = e.target.closest('.swatch-copy-hex');
            if (hexBtn) {
                var hexVal   = hexBtn.getAttribute('data-hex') || '';
                var hIcon    = hexBtn.querySelector('i');
                var origHTML = hexBtn.innerHTML;
                navigator.clipboard.writeText(hexVal).then(function () {
                    if (hIcon) hIcon.className = 'bi bi-check-circle-fill';
                    hexBtn.classList.add('text-green-600');
                    setTimeout(function () { hexBtn.innerHTML = origHTML; hexBtn.classList.remove('text-green-600'); }, 1500);
                }).catch(function () {
                    hexBtn.innerHTML = '<i class="bi bi-x-circle"></i>';
                    setTimeout(function () { hexBtn.innerHTML = origHTML; }, 1500);
                });
                return;
            }

            // Favorite single color
            var favColorBtn = e.target.closest('.swatch-fav-color');
            if (favColorBtn && window.ColorMagic && window.ColorMagic.ColorFavorites) {
                var favHex  = favColorBtn.getAttribute('data-hex') || '';
                var added   = window.ColorMagic.ColorFavorites.toggleFavorite(favHex);
                var fIcon   = favColorBtn.querySelector('i');
                if (fIcon) fIcon.className = added ? 'bi bi-heart-fill text-red-500' : 'bi bi-heart';
                favColorBtn.title = added ? 'Remove from favorites' : 'Add to favorites';
                return;
            }

            // Favorite palette toggle
            var favPaletteBtn = e.target.closest('.favorite-btn');
            if (favPaletteBtn && window.ColorMagic && window.ColorMagic.Favorites) {
                var paletteId = favPaletteBtn.getAttribute('data-palette-id');
                window.ColorMagic.Favorites.toggleFavorite(paletteId);
                window.ColorMagic.Favorites.updateFavoriteButton(favPaletteBtn, paletteId);
                return;
            }
        });
    </script>
    <script>
        // Mobile menu toggle
        (function() {
            const openBtn = document.getElementById('mobileMenuBtn');
            const closeBtn = document.getElementById('closeMobileMenuBtn');
            const overlay = document.getElementById('mobileMenuOverlay');
            if (openBtn && closeBtn && overlay) {
                openBtn.addEventListener('click', function() { overlay.style.display = 'flex'; });
                closeBtn.addEventListener('click', function() { overlay.style.display = 'none'; });
            }
        })();
    </script>
</body>
</html>