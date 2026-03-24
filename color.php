<?php

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

$hexParam = isset($_GET['hex']) ? (string) $_GET['hex'] : '';
$normalizedHex = normalizeHex($hexParam);
$isValidHex = (bool) preg_match('/^[A-F0-9]{6}$/', $normalizedHex);

if (!$isValidHex) {
    $normalizedHex = '000000';
}

$hexWithHash = '#' . $normalizedHex;

$colorNames = [];
$colorNamesPath = __DIR__ . '/data/color-names.json';
if (is_file($colorNamesPath)) {
    $decoded = json_decode((string) file_get_contents($colorNamesPath), true);
    if (is_array($decoded)) {
        $colorNames = $decoded;
    }
}

$paletteData = [];
$palettePath = __DIR__ . '/data/colors.json';
if (is_file($palettePath)) {
    $decoded = json_decode((string) file_get_contents($palettePath), true);
    if (is_array($decoded)) {
        $paletteData = $decoded;
    }
}

$hexName = $colorNames[strtolower($normalizedHex)] ?? ('Hex ' . $hexWithHash);
$rgb = hexToRgb($normalizedHex);
$hsl = hexToHsl($normalizedHex);
$contrast = bestContrastText($normalizedHex);

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

$metaTitle = $hexName . ' ' . $hexWithHash . ' Color Details, RGB, HSL and Related Palettes | Color Magic';
$metaDescription = 'Explore ' . $hexName . ' (' . $hexWithHash . ') with RGB(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ') and HSL(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%). Discover ' . count($relatedPalettes) . ' related ' . $styleSnippet . ' color palettes from our colors library.';

$baseUrl = 'https://colormagic.techkreative.com';
$canonicalUrl = $baseUrl . '/color/' . strtolower($normalizedHex) . '/';
$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/color.php');
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($basePath === '/') {
    $basePath = '';
}

$assetBase = $basePath;
$homePageUrl = $assetBase . '/';
$openSourceUrl = $assetBase . '/open-source.html';
$manifestUrl = $assetBase . '/manifest.json';
$faviconUrl = $assetBase . '/images/roundlogo.png';
$logoUrl = $assetBase . '/images/logo.png';
$siteStylesUrl = $assetBase . '/style/style.css?v=1.0';
$tailwindConfigUrl = $assetBase . '/js/tailwind-config.js';
$colorRouteBase = $assetBase . '/color/';
$ogImageUrl = $baseUrl . '/images/full-logo.png';

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $metaTitle,
    'description' => $metaDescription,
    'url' => $canonicalUrl,
    'mainEntity' => [
        '@type' => 'DefinedTerm',
        'name' => $hexName,
        'identifier' => $hexWithHash,
    ],
];
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo e($metaTitle); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>" />
    <meta name="keywords" content="<?php echo e($hexWithHash); ?>, <?php echo e($hexName); ?>, color hex, color palettes, rgb, hsl, color details" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="<?php echo $isValidHex ? 'index, follow' : 'noindex, follow'; ?>" />

    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo e($metaTitle); ?>" />
    <meta property="og:description" content="<?php echo e($metaDescription); ?>" />
    <meta property="og:url" content="<?php echo e($canonicalUrl); ?>" />
    <meta property="og:image" content="<?php echo e($ogImageUrl); ?>" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" content="<?php echo e($metaTitle); ?>" />
    <meta property="twitter:description" content="<?php echo e($metaDescription); ?>" />
    <meta property="twitter:image" content="<?php echo e($ogImageUrl); ?>" />

    <link rel="manifest" href="<?php echo e($manifestUrl); ?>" />
    <link rel="icon" type="image/png" href="<?php echo e($faviconUrl); ?>" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <header
        class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
        <div class="max-w-[1440px] mx-auto px-6 h-16 flex items-center justify-between gap-8">
            <a href="<?php echo e($homePageUrl); ?>" class="flex items-center gap-2 text-primary hover:opacity-80 transition-opacity">
                <img src="<?php echo e($logoUrl); ?>" alt="Color Magic Logo" class="h-8 w-8 object-contain">
                <h2 class="text-xl font-bold tracking-tight">Color Magic</h2>
            </a>

            <div class="flex items-center gap-3">
                <a href="<?php echo e($homePageUrl); ?>"
                    class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <i class="bi bi-house-door"></i>
                    <span>Home</span>
                </a>
                <a href="<?php echo e($openSourceUrl); ?>"
                    class="hidden lg:flex items-center gap-2 px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-all hover:scale-105 active:scale-95 group"
                    title="View on GitHub">
                    <i class="bi bi-github text-xl group-hover:rotate-12 transition-transform"></i>
                    <span class="text-xs font-semibold">Open Source</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-[1440px] mx-auto px-6 py-8 md:py-10">
        <div class="mb-6">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                <a class="hover:text-primary transition-colors" href="<?php echo e($homePageUrl); ?>">Home</a>
                <span class="mx-2">/</span>
                <span>Color <?php echo e($hexWithHash); ?></span>
            </p>
        </div>

        <section class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="grid grid-cols-1 lg:grid-cols-[420px,1fr]">
                <div class="min-h-[280px] md:min-h-[360px] p-6 flex items-end" style="background-color: <?php echo e($hexWithHash); ?>;">
                    <div class="px-4 py-3 rounded-xl backdrop-blur-sm" style="background: rgba(15, 23, 42, 0.25); color: <?php echo e($contrast); ?>;">
                        <p class="text-sm uppercase tracking-widest opacity-85">Selected Color</p>
                        <p class="text-3xl font-bold"><?php echo e($hexWithHash); ?></p>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-3"><?php echo e($hexName); ?> Color Details</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-base md:text-lg mb-6">
                        Complete color profile for <?php echo e($hexWithHash); ?> with copy-ready values and related palettes.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">Hex</p>
                            <p class="font-mono font-bold text-lg mb-3"><?php echo e($hexWithHash); ?></p>
                            <button class="copy-btn px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors" data-copy="<?php echo e($hexWithHash); ?>">Copy Hex</button>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">RGB</p>
                            <p class="font-mono font-bold text-lg mb-3"><?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?></p>
                            <button class="copy-btn px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors" data-copy="rgb(<?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?>)">Copy RGB</button>
                        </div>

                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800/60">
                            <p class="text-xs uppercase tracking-widest text-slate-500 mb-1">HSL</p>
                            <p class="font-mono font-bold text-lg mb-3"><?php echo e((string) $hsl['h']); ?>, <?php echo e((string) $hsl['s']); ?>%, <?php echo e((string) $hsl['l']); ?>%</p>
                            <button class="copy-btn px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors" data-copy="hsl(<?php echo e((string) $hsl['h']); ?>, <?php echo e((string) $hsl['s']); ?>%, <?php echo e((string) $hsl['l']); ?>%)">Copy HSL</button>
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

        <section class="mt-8" aria-labelledby="related-palettes-title">
            <h2 id="related-palettes-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-5">Related Color Palettes</h2>

            <?php if ($relatedPalettes === []): ?>
                <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900">
                    No related palettes found in the current library.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($relatedPalettes as $palette): ?>
                        <article class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                            <div class="mb-3">
                                <h3 class="text-lg font-bold"><?php echo e((string) ($palette['name'] ?? 'Untitled Palette')); ?></h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Style: <?php echo e((string) ($palette['style'] ?? 'General')); ?></p>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <?php foreach (($palette['colors'] ?? []) as $c): ?>
                                    <?php $chipHex = normalizeHex((string) $c); ?>
                                    <?php if (!(bool) preg_match('/^[A-F0-9]{6}$/', $chipHex)) { continue; } ?>
                                    <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
                                        <a class="group block hover:opacity-95 transition-opacity" href="<?php echo e($colorRouteBase . strtolower($chipHex) . '/'); ?>" aria-label="View color #<?php echo e($chipHex); ?> details">
                                            <div class="h-24 md:h-28" style="background-color: #<?php echo e($chipHex); ?>;"></div>
                                        </a>
                                        <div class="px-2 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 flex items-center justify-between">
                                            <span>#<?php echo e($chipHex); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <div class="text-center pt-12 mt-12 border-t border-slate-200 dark:border-slate-800">
            <p class="text-slate-600 dark:text-slate-400 mb-4">
                Made with ❤ by <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer"
                    class="text-primary hover:underline font-semibold">TechKreative</a>
            </p>
            <div class="flex items-center justify-center gap-6">
                <a href="<?php echo e($homePageUrl); ?>" class="text-slate-500 hover:text-primary transition-colors" aria-label="Go to home">
                    <i class="bi bi-house-door text-xl"></i>
                </a>
                <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer"
                    class="text-slate-500 hover:text-primary transition-colors" aria-label="GitHub repository">
                    <i class="bi bi-github text-xl"></i>
                </a>
            </div>
        </div>
    </main>

    <script>
        const copyFeedback = document.getElementById('copyFeedback');
        document.querySelectorAll('.copy-btn').forEach((button) => {
            button.addEventListener('click', async () => {
                const text = button.getAttribute('data-copy') || '';
                try {
                    await navigator.clipboard.writeText(text);
                    if (copyFeedback) {
                        copyFeedback.classList.add('show');
                        copyFeedback.textContent = text + ' copied';
                        setTimeout(() => copyFeedback.classList.remove('show'), 1200);
                    }
                } catch (error) {
                    if (copyFeedback) {
                        copyFeedback.classList.add('show');
                        copyFeedback.textContent = 'Copy failed. Please copy manually.';
                        setTimeout(() => copyFeedback.classList.remove('show'), 1500);
                    }
                }
            });
        });
    </script>
</body>
</html>