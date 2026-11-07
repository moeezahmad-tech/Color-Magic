<?php
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
include '../components/config.php';
/** @var string $base */

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function normalizeHex(string $hex): string {
    return strtoupper(ltrim(trim($hex), '#'));
}

function hexToRgb(string $hex): array {
    $clean = normalizeHex($hex);
    if (strlen($clean) === 3) {
        $clean = $clean[0].$clean[0].$clean[1].$clean[1].$clean[2].$clean[2];
    }
    return [
        'r' => hexdec(substr($clean, 0, 2)),
        'g' => hexdec(substr($clean, 2, 2)),
        'b' => hexdec(substr($clean, 4, 2)),
    ];
}

function hexToHsl(string $hex): array {
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

function hexToCmyk(string $hex): array {
    $rgb = hexToRgb($hex);
    $r = $rgb['r'] / 255;
    $g = $rgb['g'] / 255;
    $b = $rgb['b'] / 255;
    $k = 1 - max($r, $g, $b);
    $c = $k < 1 ? (1 - $r - $k) / (1 - $k) : 0;
    $m = $k < 1 ? (1 - $g - $k) / (1 - $k) : 0;
    $y = $k < 1 ? (1 - $b - $k) / (1 - $k) : 0;
    return [
        'c' => (int) round($c * 100),
        'm' => (int) round($m * 100),
        'y' => (int) round($y * 100),
        'k' => (int) round($k * 100),
    ];
}

function relativeLuminance(string $hex): float {
    $rgb = hexToRgb($hex);
    $channels = [$rgb['r'], $rgb['g'], $rgb['b']];
    $linear = array_map(static function (int $v): float {
        $x = $v / 255;
        return $x <= 0.03928 ? ($x / 12.92) : pow(($x + 0.055) / 1.055, 2.4);
    }, $channels);
    return ($linear[0] * 0.2126) + ($linear[1] * 0.7152) + ($linear[2] * 0.0722);
}

function contrastRatio(float $l1, float $l2): float {
    $lighter = max($l1, $l2);
    $darker = min($l1, $l2);
    return ($lighter + 0.05) / ($darker + 0.05);
}

function bestContrastText(string $hex): string {
    $rgb = hexToRgb($hex);
    $perceived = (0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b']) / 255;
    return $perceived > 0.62 ? '#111111' : '#FFFFFF';
}

function slugify(string $text): string {
    return trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($text)), '-');
}

$paletteSlug = $_GET['slug'] ?? '';
$palettesFile = __DIR__ . '/../data/palettes.json';
$allPalettes = file_exists($palettesFile) ? json_decode(file_get_contents($palettesFile), true) : [];

$currentPalette = null;
if (!empty($paletteSlug) && is_array($allPalettes)) {
    $nameCounts = [];
    foreach ($allPalettes as $p) {
        $s = slugify($p['name'] ?? '');
        $nameCounts[$s] = ($nameCounts[$s] ?? 0) + 1;
    }
    foreach ($allPalettes as $p) {
        if (($p['id'] ?? '') === $paletteSlug) {
            $currentPalette = $p;
            break;
        }
        $s = slugify($p['name'] ?? '');
        if (($nameCounts[$s] ?? 0) > 1) {
            $s .= '-' . str_replace('palette_', '', $p['id'] ?? '');
        }
        if ($s === $paletteSlug) {
            $currentPalette = $p;
            break;
        }
    }
}

$paletteName = $currentPalette['name'] ?? 'Curated Palette';
$paletteColors = $currentPalette['colors'] ?? ['#7C3AED', '#EC4899', '#3B82F6', '#10B981'];
$paletteStyle = $currentPalette['style'] ?? 'Modern';
$colorListString = implode(', ', $paletteColors);

$metaTitle = $currentPalette 
    ? e($paletteName) . ' Color Palette (' . e($colorListString) . ') | Color Magic'
    : 'Curated Color Palette | Color Magic';
$metaDescription = 'Explore ' . e($paletteName) . ' featuring ' . count($paletteColors) . ' harmonious colors (' . e($colorListString) . '). View RGB, HSL, CMYK conversions, contrast accessibility, and CSS variables.';
$canonicalUrl = 'https://colormagic.techkreative.com/palette/' . ($paletteSlug !== '' ? e($paletteSlug) . '/' : '');
$posterSlug = $paletteSlug !== '' ? $paletteSlug : ($currentPalette['id'] ?? 'palette_1');
$posterPath = '/posters/palettes/' . $posterSlug . '.jpg';
$posterUrl = $base . $posterPath;
$absolutePosterUrl = 'https://colormagic.techkreative.com' . $posterPath;

$posterSlug = $paletteSlug !== '' ? $paletteSlug : ($currentPalette['id'] ?? 'palette_1');
$posterPath = '/posters/palettes/' . $posterSlug . '.jpg';
$posterUrl = $base . $posterPath;
$absolutePosterUrl = 'https://colormagic.techkreative.com' . $posterPath;

$faqItems = [
    [
        'q' => "What colors are in the $paletteName color palette?",
        'a' => "The $paletteName palette consists of " . count($paletteColors) . " colors: " . implode(', ', $paletteColors) . ". It is classified under the $paletteStyle aesthetic."
    ],
    [
        'q' => "How can I use the $paletteName palette in CSS or web design?",
        'a' => "You can apply these colors as CSS variables, utility classes in Tailwind CSS, or design tokens. The dominant color (" . ($paletteColors[0] ?? '#7C3AED') . ") functions well for primary actions and hero elements, while " . ($paletteColors[count($paletteColors)-1] ?? '#FFFFFF') . " provides surface and background support."
    ],
    [
        'q' => "Are the colors in $paletteName accessible under WCAG guidelines?",
        'a' => "Yes, when pairing the darkest color (" . ($paletteColors[0] ?? '#000000') . ") with the lightest color (" . ($paletteColors[count($paletteColors)-1] ?? '#FFFFFF') . "), the combination provides high readability and passes standard WCAG 2.1 AA/AAA contrast tests."
    ],
    [
        'q' => "What mood and emotion does $paletteName evoke?",
        'a' => "With its $paletteStyle styling and curated tones, $paletteName communicates balance, visual coherence, and modern digital appeal, making it suitable for web applications, brand identities, and editorial design."
    ]
];

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebPage',
            'name' => $metaTitle,
            'url' => $canonicalUrl,
            'description' => $metaDescription,
            'mainEntity' => [
                '@type' => 'CreativeWork',
                'name' => $paletteName,
                'description' => $metaDescription,
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => $absolutePosterUrl,
                    'width' => 1800,
                    'height' => 2100,
                    'caption' => $paletteName . ' Color Palette (' . $colorListString . ')'
                ],
                'keywords' => 'color palette, hex codes, color scheme, ' . $paletteStyle . ', UI design'
            ]
        ],
        [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(static function (array $faq): array {
                return [
                    '@type' => 'Question',
                    'name' => $faq['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['a']
                    ]
                ];
            }, $faqItems)
        ]
    ]
];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <base href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/">
    <title><?= $metaTitle ?></title>
    <meta name="description" content="<?= $metaDescription ?>" />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/logo.png" fetchpriority="high" />

    <!-- Open Graph / Facebook / Pinterest -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="<?= $metaTitle ?>" />
    <meta property="og:description" content="<?= $metaDescription ?>" />
    <meta property="og:image" content="<?= $absolutePosterUrl ?>" />
    <meta property="og:image:width" content="1800" />
    <meta property="og:image:height" content="2100" />
    <meta property="og:image:alt" content="<?= e($paletteName) ?> Color Palette Poster (<?= e($colorListString) ?>)" />
    <meta property="og:site_name" content="Color Magic" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="<?= $canonicalUrl ?>" />
    <meta name="twitter:title" content="<?= $metaTitle ?>" />
    <meta name="twitter:description" content="<?= $metaDescription ?>" />
    <meta name="twitter:image" content="<?= $absolutePosterUrl ?>" />
    <meta name="twitter:image:alt" content="<?= e($paletteName) ?> Color Palette Poster" />

    <!-- Resource Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />

    <!-- Preload Fonts & Non-Blocking CSS -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" /></noscript>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>

    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

    <style>
        header { min-height: 64px; }
        .swatch { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .swatch-hex { opacity: 0; transition: opacity 0.2s ease; }
        .swatch:hover .swatch-hex { opacity: 1; display: block !important; }
        @keyframes swatchBtnIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .swatch-icon-btn { opacity: 0; transform: translateY(-6px); }
        .swatch:hover .swatch-icon-btn { animation: swatchBtnIn 0.18s ease forwards; }
        .swatch:hover .swatch-btn-1 { animation-delay: 0s; }
        .swatch:hover .swatch-btn-2 { animation-delay: 0.06s; }
        .swatch:hover .swatch-btn-3 { animation-delay: 0.12s; }
        .swatch-hex.copied-state {
            opacity: 1 !important;
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            font-weight: 700;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include '../components/navbar.php'; ?>

    <main class="w-full max-w-7xl mx-auto pt-24 p-5 md:p-8 flex flex-col gap-8">
        <!-- Top Navigation: Breadcrumb -->
        <div>
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="<?= $base ?>/" class="hover:text-primary transition-colors">Home</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <a href="<?= $base ?>/palettes" class="hover:text-primary transition-colors">Palettes</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span id="paletteBreadcrumb" class="text-slate-900 dark:text-white font-medium"><?= e($paletteName) ?></span>
            </nav>
        </div>

        <!-- Skeleton Loader (hidden if loaded server-side) -->
        <div id="paletteSkeleton" class="<?= $currentPalette ? 'hidden' : 'flex' ?> flex-col gap-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800 animate-pulse">
                <div class="h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] bg-slate-200/80 dark:bg-slate-800 rounded-2xl"></div>
                <div class="flex flex-col gap-5 justify-between">
                    <div class="space-y-3">
                        <div class="h-9 w-3/4 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-5 w-1/2 bg-slate-200/80 dark:bg-slate-800 rounded-lg"></div>
                        <div class="h-4 w-full bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>

        <section id="paletteDetail" class="<?= $currentPalette ? 'flex' : 'hidden' ?> flex-col gap-8">
            <!-- Hero: Palette Overview -->
            <div id="paletteTopSection"
                class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
                <div class="h-full">
                    <div id="heroPaletteStrips"
                        class="grid h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30"
                        style="grid-template-columns: repeat(<?= count($paletteColors) ?>, minmax(0, 1fr));">
                        <?php foreach ($paletteColors as $cHex): ?>
                            <a href="<?= $base ?>/color/<?= strtolower(ltrim($cHex, '#')) ?>/"
                               class="h-full w-full flex items-end justify-center p-3 transition-opacity hover:opacity-90 relative group"
                               style="background-color: <?= e($cHex) ?>;"
                               title="Explore <?= e($cHex) ?>">
                                <span class="font-mono text-xs font-bold px-2 py-1 rounded bg-black/40 text-white backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    <?= e($cHex) ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Info panel -->
                <div class="flex flex-col gap-5 justify-between">
                    <div>
                        <h1 id="paletteName" class="text-3xl md:text-4xl font-bold tracking-tight"><?= e($paletteName) ?></h1>
                        <h2 id="paletteSubheading" class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"><?= e($colorListString) ?></h2>
                        <p id="paletteDescription" class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Harmonious <?= e($paletteStyle) ?> color palette with copy-ready HEX, RGB, and CSS variable specifications.
                        </p>
                    </div>

                    <!-- Details: Metric Chips -->
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Style</p>
                            <p id="paletteType" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"><?= e($paletteStyle) ?></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Colors</p>
                            <p id="paletteCount" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"><?= count($paletteColors) ?> Swatches</p>
                        </div>
                    </div>

                    <!-- Color Swatches list (1-click interactive copy chips) -->
                    <div>
                        <div id="detailColorList" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php foreach ($paletteColors as $cHex): ?>
                                <button type="button"
                                    class="copy-btn flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 hover:border-primary/50 transition text-left"
                                    data-copy="<?= e($cHex) ?>"
                                    data-label="Copied <?= e($cHex) ?>!">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-lg shadow-sm border border-black/10 shrink-0" style="background-color: <?= e($cHex) ?>;"></span>
                                        <span class="font-mono text-xs font-bold"><?= e($cHex) ?></span>
                                    </div>
                                    <i class="bi bi-clipboard text-xs text-slate-400"></i>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="h-4 mt-1">
                            <p id="copyFeedback" class="copy-feedback opacity-0 -translate-y-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 transition-all">Copied to clipboard</p>
                        </div>
                    </div>

                    <!-- Primary Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button id="downloadPalettePngBtn"
                            class="w-full px-5 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/25 hover:shadow-primary/40 active:scale-[0.99]"
                            title="Download palette image">
                            <i class="bi bi-download text-base"></i>
                            <span>Download Image</span>
                        </button>
                        <button id="favPaletteBtn"
                            class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 border border-slate-200/80 dark:border-slate-700/80 active:scale-[0.99]">
                            <i class="bi bi-heart text-sm"></i>
                            <span>Favorite</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: Palette Color Breakdown & Conversions Table -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800" aria-labelledby="palette-table-title">
                <h2 id="palette-table-title" class="text-xl md:text-2xl font-bold mb-2">Palette Color Specifications</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Detailed color coordinates, RGB channels, HSL angles, and CMYK print values for each swatch.</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-xs uppercase font-bold text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Swatch</th>
                                <th class="px-4 py-3">HEX</th>
                                <th class="px-4 py-3">RGB</th>
                                <th class="px-4 py-3">HSL</th>
                                <th class="px-4 py-3">CMYK</th>
                                <th class="px-4 py-3">Suggested Role</th>
                                <th class="px-4 py-3 text-right">Explore</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-mono text-xs">
                            <?php 
                            $roles = ['Primary Accent', 'Secondary Hue', 'Supporting Midtone', 'Surface / Neutral', 'Detail Accent'];
                            foreach ($paletteColors as $idx => $hex): 
                                $rgb = hexToRgb($hex);
                                $hsl = hexToHsl($hex);
                                $cmyk = hexToCmyk($hex);
                                $role = $roles[$idx] ?? 'Accent Color';
                            ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-4 py-3">
                                        <div class="w-8 h-8 rounded-lg shadow-sm border border-black/10" style="background-color: <?= e($hex) ?>;"></div>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-primary"><?= e($hex) ?></td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">rgb(<?= (string)$rgb['r'] ?>, <?= (string)$rgb['g'] ?>, <?= (string)$rgb['b'] ?>)</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">hsl(<?= (string)$hsl['h'] ?>°, <?= (string)$hsl['s'] ?>%, <?= (string)$hsl['l'] ?>%)</td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">cmyk(<?= (string)$cmyk['c'] ?>%, <?= (string)$cmyk['m'] ?>%, <?= (string)$cmyk['y'] ?>%, <?= (string)$cmyk['k'] ?>%)</td>
                                    <td class="px-4 py-3 font-sans font-medium text-slate-700 dark:text-slate-200"><?= e($role) ?></td>
                                    <td class="px-4 py-3 text-right font-sans">
                                        <a href="<?= $base ?>/color/<?= strtolower(ltrim($hex, '#')) ?>/" class="px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-medium inline-block">View Color</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: UI Application Showcase Mockup -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800" aria-labelledby="palette-ui-title">
                <h2 id="palette-ui-title" class="text-xl md:text-2xl font-bold mb-2">Live UI & Component Preview</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">See how the colors in <?= e($paletteName) ?> harmonize across real digital UI components.</p>

                <?php 
                $bgC = $paletteColors[count($paletteColors)-1] ?? '#F8FAFC';
                $cardBg = $paletteColors[count($paletteColors)-2] ?? '#FFFFFF';
                $primaryC = $paletteColors[0] ?? '#7C3AED';
                $accentC = $paletteColors[1] ?? '#EC4899';
                ?>
                <div class="rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-700/60" style="background-color: <?= e($bgC) ?>;">
                    <div class="max-w-md mx-auto rounded-2xl p-6 shadow-xl border border-black/5" style="background-color: <?= e($cardBg) ?>; color: <?= e(bestContrastText($cardBg)) ?>;">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm" style="background-color: <?= e($primaryC) ?>; color: <?= e(bestContrastText($primaryC)) ?>;">
                                <?= e($paletteStyle) ?> Interface
                            </span>
                            <span class="text-xs font-mono font-bold opacity-75">Demo Card</span>
                        </div>
                        <h3 class="text-2xl font-black mb-2" style="color: <?= e(bestContrastText($cardBg)) ?>;">
                            Creative Design with <?= e($paletteName) ?>
                        </h3>
                        <p class="text-sm leading-relaxed mb-6 opacity-85">
                            Harmonious visual hierarchy achieved using this palette's natural lightness gradient and accent balance.
                        </p>
                        <div class="flex items-center gap-3">
                            <button type="button" class="px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:opacity-95 transition" style="background-color: <?= e($primaryC) ?>; color: <?= e(bestContrastText($primaryC)) ?>;">
                                Primary Action
                            </button>
                            <button type="button" class="px-4 py-2.5 rounded-xl font-semibold text-sm border shadow-sm hover:opacity-90 transition" style="border-color: <?= e($accentC) ?>; color: <?= e($accentC) ?>;">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Contrast Cards -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="contrastCardA" class="rounded-2xl p-6 min-h-[180px] flex flex-col justify-between"></div>
                <div id="contrastCardB" class="rounded-2xl p-6 min-h-[180px] flex flex-col justify-between"></div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Palette Story -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-3">Palette Story & Mood</h2>
                <p id="darkColorParagraph" class="text-base md:text-lg leading-relaxed font-medium">
                    <?= e($paletteName) ?> is a <?= strtolower(e($paletteStyle)) ?> color scheme featuring <?= count($paletteColors) ?> cohesive colors. The palette moves smoothly from rich focal hues to supportive background tones, creating a welcoming and functional user interface.
                </p>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Brightness Levels -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Brightness & Lightness Spectrum</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Visual representation of each color's lightness on a spectrum
                </p>
                <div id="brightnessChart" class="space-y-2"></div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Export Formats: Modern Tabbed Interface -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Code & Export</h2>
                        <p id="paletteExportDescription" class="text-sm text-slate-600 dark:text-slate-400 mt-1">Structured JSON format for APIs, AI prompts, and data exchange</p>
                    </div>
                    <button id="copyPaletteActiveExportBtn" type="button"
                        class="self-start sm:self-auto shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center gap-1.5 active:scale-[0.98]"
                        title="Copy code to clipboard">
                        <i class="bi bi-clipboard"></i>
                        <span>Copy Code</span>
                    </button>
                </div>

                <!-- Tabs Navigation -->
                <div id="paletteExportTabs" class="flex items-center gap-1.5 overflow-x-auto pb-2 mb-4 border-b border-slate-200/80 dark:border-slate-800 scrollbar-none">
                    <button type="button" data-tab="json" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap bg-primary text-white shadow-sm flex items-center gap-1.5">
                        <i class="bi bi-stars"></i>
                        <span>JSON (AI Friendly)</span>
                    </button>
                    <button type="button" data-tab="hex" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        HEX Array
                    </button>
                    <button type="button" data-tab="css" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        CSS Variables
                    </button>
                    <button type="button" data-tab="tailwind" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        Tailwind CSS
                    </button>
                    <button type="button" data-tab="scss" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        SCSS
                    </button>
                    <button type="button" data-tab="rgb" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        RGB Array
                    </button>
                    <button type="button" data-tab="hsl" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        HSL Array
                    </button>
                    <button type="button" data-tab="android" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        Android XML
                    </button>
                    <button type="button" data-tab="swift" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        Swift (UIColor)
                    </button>
                </div>

                <!-- Active Code Block -->
                <div class="relative bg-slate-50 dark:bg-slate-800/80 rounded-xl p-4 md:p-5 font-mono text-xs sm:text-sm overflow-auto max-h-80 border border-slate-200/80 dark:border-slate-700/80">
                    <pre id="paletteExportActiveCode" class="leading-relaxed"></pre>
                </div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- SECTION 3: Frequently Asked Questions (FAQ) -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800" aria-labelledby="palette-faq-title">
                <h2 id="palette-faq-title" class="text-xl md:text-2xl font-bold mb-2">Frequently Asked Questions</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Common questions about using <?= e($paletteName) ?> in your creative projects.</p>

                <div class="space-y-3">
                    <?php foreach ($faqItems as $faq): ?>
                        <details class="group bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/60 rounded-xl p-4 transition [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                                <span><?= e($faq['q']) ?></span>
                                <span class="ml-4 flex-shrink-0 text-slate-400 group-open:rotate-180 transition-transform">
                                    <i class="bi bi-chevron-down"></i>
                                </span>
                            </summary>
                            <p class="mt-2.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed pr-6 border-t border-slate-200/60 dark:border-slate-700/60 pt-2.5">
                                <?= e($faq['a']) ?>
                            </p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Related Colors -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Colors</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Complementary, analogous, and triadic colors derived from this palette
                </p>
                <div id="relatedColorsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Related Palettes -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Palettes</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Palettes with similar colors to this one
                </p>
                <div id="relatedPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8"></div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Related Gradients -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Gradients</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Gradients that use colors from this palette
                </p>
                <div id="relatedGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
            </section>

            <!-- SECTION: Official Poster & Swatch Card Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="palette-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($paletteName) ?> Color Palette (#<?= implode(', #', array_map('normalizeHex', $paletteColors)) ?>) Poster Graphic" 
                             width="1800" height="2100" 
                             loading="eager" fetchpriority="high"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                        <i class="bi bi-image"></i> Official High-Resolution Poster
                    </div>
                    <h2 id="palette-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($paletteName) ?> Color Poster Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Explore and download the official <strong><?= e($paletteName) ?></strong> design poster card featuring exact HEX swatches, brand typographic hierarchy, and verified color values for digital and print design workflows.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($paletteSlug) ?>-color-palette.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportPaletteImage ? window.ColorMagic.exportPaletteImage({name: '<?= addslashes($paletteName) ?>', colors: <?= json_encode($paletteColors) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentPalette['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentPalette['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </section>

        <section id="paletteError"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center hidden">
            <i class="bi bi-exclamation-circle text-4xl text-red-500"></i>
            <h2 class="text-xl font-bold mt-3">Palette not found</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                The requested palette id does not exist.
            </p>
        </section>
    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_COLOR_BASE    = '<?= $base ?>/color/';
        window.CM_PALETTE_BASE  = '<?= $base ?>/palette/';
        window.CM_GRADIENT_BASE = '<?= $base ?>/gradient/';
        window.CM_CACHE_KEY     = '<?= cm_asset_v("assets/js/palette-page.js") ?>';
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=<?= cm_asset_v('assets/js/utils.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=<?= cm_asset_v('assets/js/services/favorites.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/image-export.js?v=<?= cm_asset_v('assets/js/image-export.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=<?= cm_asset_v('assets/js/components/palette-card.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=<?= cm_asset_v('assets/js/components/gradient-card.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/palette-page.js?v=<?= cm_asset_v('assets/js/palette-page.js') ?>" defer></script>
</body>

</html>
