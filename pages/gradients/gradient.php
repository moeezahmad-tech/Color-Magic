<?php
if (!headers_sent()) {
    header('Cache-Control: public, max-age=3600, stale-while-revalidate=86400');
    header('Cloudflare-CDN-Cache-Control: public, max-age=86400, stale-while-revalidate=604800');
}
include '../../components/config.php';
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

function slugify(string $text): string {
    return trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($text)), '-');
}

$gradientId = $_GET['id'] ?? '';
$gradientsFile = __DIR__ . '/../../data/gradients.json';
$allGradients = file_exists($gradientsFile) ? json_decode(file_get_contents($gradientsFile), true) : [];

$currentGradient = null;
if (!empty($gradientId) && is_array($allGradients)) {
    foreach ($allGradients as $g) {
        if (($g['id'] ?? '') === $gradientId || slugify($g['name'] ?? '') === slugify($gradientId)) {
            $currentGradient = $g;
            break;
        }
    }
}

$gradientName = $currentGradient['name'] ?? 'Curated Gradient';
$gradientColors = $currentGradient['colors'] ?? ['#FF512F', '#DD2476'];
$gradientStyle = $currentGradient['style'] ?? 'Modern';
$gradientType = $currentGradient['type'] ?? 'linear';
$gradientAngle = $currentGradient['angle'] ?? 135;
$gradientCss = $currentGradient['css'] ?? "linear-gradient(135deg, " . implode(', ', $gradientColors) . ")";
$colorListString = implode(' → ', $gradientColors);

$metaTitle = $currentGradient 
    ? e($gradientName) . ' CSS Gradient (' . e($colorListString) . ') | Color Magic'
    : 'Curated CSS Gradient | Color Magic';
$metaDescription = 'Explore ' . e($gradientName) . ' CSS gradient (' . e($colorListString) . '). Copy CSS, Tailwind syntax, SVG gradients, and preview UI components in real time.';
$canonicalUrl = 'https://colormagic.techkreative.com/gradient/' . ($gradientId !== '' ? e($gradientId) . '/' : '');
$posterPath = '/posters/gradients/' . ($gradientId !== '' ? $gradientId : 'gradient_1') . '.jpg';
$posterUrl = $base . $posterPath;
$absolutePosterUrl = 'https://colormagic.techkreative.com' . $posterPath;

$faqItems = [
    [
        'q' => "What colors create the $gradientName gradient?",
        'a' => "The $gradientName gradient blends " . count($gradientColors) . " colors: " . implode(', ', $gradientColors) . ". It is configured with an angle of {$gradientAngle}°."
    ],
    [
        'q' => "How do I use $gradientName in CSS?",
        'a' => "You can apply this gradient with the standard CSS property: background: $gradientCss; For cross-browser support, set a solid background fallback using the first color (" . ($gradientColors[0] ?? '#FF512F') . ")."
    ],
    [
        'q' => "How do I make gradient text with $gradientName?",
        'a' => "To render gradient text in CSS, use: background: $gradientCss; -webkit-background-clip: text; -webkit-text-fill-color: transparent; In Tailwind CSS, use: bg-gradient-to-r from-[" . ($gradientColors[0] ?? '') . "] to-[" . ($gradientColors[count($gradientColors)-1] ?? '') . "] bg-clip-text text-transparent."
    ],
    [
        'q' => "What UI elements are best suited for $gradientName?",
        'a' => "This $gradientStyle gradient is ideal for high-impact hero section banners, modern Call-to-Action (CTA) buttons, decorative avatar rings, and card border highlights."
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
                'name' => $gradientName,
                'description' => $metaDescription,
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => $absolutePosterUrl,
                    'width' => 1648,
                    'height' => 2048,
                    'caption' => $gradientName . ' CSS Gradient (' . $colorListString . ')'
                ],
                'keywords' => 'css gradient, gradient generator, ' . $gradientStyle . ', tailwind gradient, web design'
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
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="apple-touch-icon" href="<?= $base ?>/assets/images/logo.png" />

    <!-- Open Graph / Facebook / Pinterest -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="<?= $metaTitle ?>" />
    <meta property="og:description" content="<?= $metaDescription ?>" />
    <meta property="og:image" content="<?= $absolutePosterUrl ?>" />
    <meta property="og:image:width" content="1648" />
    <meta property="og:image:height" content="2048" />
    <meta property="og:image:alt" content="<?= e($gradientName) ?> CSS Gradient Poster (<?= e($colorListString) ?>)" />
    <meta property="og:site_name" content="Color Magic" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="<?= $canonicalUrl ?>" />
    <meta name="twitter:title" content="<?= $metaTitle ?>" />
    <meta name="twitter:description" content="<?= $metaDescription ?>" />
    <meta name="twitter:image" content="<?= $absolutePosterUrl ?>" />
    <meta name="twitter:image:alt" content="<?= e($gradientName) ?> CSS Gradient Poster" />

    <!-- Resource Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />

    <!-- Preload Fonts & Non-Blocking CSS -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" /></noscript>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" /></noscript>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script>
        (function(){
            var w = console.warn;
            console.warn = function() {
                if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].indexOf('cdn.tailwindcss.com') !== -1) return;
                return w.apply(console, arguments);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>

    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

    <style>
        header { min-height: 64px; }
        .gradient-hero { transition: transform 0.4s ease; }
        .gradient-hero:hover { transform: scale(1.015); }
        .swatch { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .swatch-hex { opacity: 0; transition: opacity 0.2s ease; }
        .swatch:hover .swatch-hex { opacity: 1; display: block !important; }
        @keyframes swatchBtnIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .swatch-icon-btn { opacity: 0; transform: translateY(-6px); }
        .swatch:hover .swatch-icon-btn { animation: swatchBtnIn 0.18s ease forwards; }
        .swatch-hex.copied-state {
            opacity: 1 !important;
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            font-weight: 700;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include '../../components/navbar.php'; ?>

    <main class="w-full max-w-7xl mx-auto pt-24 p-5 md:p-8 flex flex-col gap-8">
        <!-- Top Navigation: Breadcrumb -->
        <div>
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="<?= $base ?>/" class="hover:text-primary transition-colors">Home</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <a href="<?= $base ?>/gradients" class="hover:text-primary transition-colors">Gradients</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span id="gradientBreadcrumb" class="text-slate-900 dark:text-white font-medium"><?= e($gradientName) ?></span>
            </nav>
        </div>

        <!-- Skeleton Loader (hidden if loaded server-side) -->
        <div id="gradientSkeleton" class="<?= $currentGradient ? 'hidden' : 'flex' ?> flex-col gap-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800 animate-pulse">
                <div class="h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] bg-slate-200/80 dark:bg-slate-800 rounded-2xl"></div>
                <div class="flex flex-col gap-5 justify-between">
                    <div class="space-y-3">
                        <div class="h-9 w-3/4 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-5 w-1/2 bg-slate-200/80 dark:bg-slate-800 rounded-lg"></div>
                        <div class="h-4 w-full bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gradient detail -->
        <section id="gradientDetail" class="<?= $currentGradient ? 'flex' : 'hidden' ?> flex-col gap-8">
            <!-- Hero: Preview + Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
                <!-- Large gradient preview -->
                <div class="h-full">
                    <div id="heroGradientPreview"
                        class="gradient-hero h-72 md:h-80 lg:h-full min-h-[300px] md:min-h-[380px] rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30 relative flex items-end cursor-pointer group"
                        style="background: <?= e($gradientCss) ?>;">
                        
                        <div id="heroOverlayText" class="relative z-10 select-none p-6 md:p-8 flex flex-col justify-end w-full pointer-events-none" style="color: #ffffff; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                            <p class="text-xs sm:text-sm font-bold uppercase tracking-[0.22em] opacity-85 mb-1.5">COLOR MAGIC</p>
                            <h2 id="heroGradientName" class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight mb-2"><?= e($gradientName) ?></h2>
                            <div id="heroGradientColors" class="flex flex-wrap items-center gap-2 text-xs sm:text-sm font-semibold tracking-tight opacity-95">
                                <?php foreach ($gradientColors as $c): ?>
                                    <span class="px-2.5 py-1 rounded-lg bg-black/30 backdrop-blur-sm border border-white/20 font-mono"><?= e($c) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                            <span class="px-3 py-1.5 bg-black/50 backdrop-blur-md text-white rounded-lg text-xs font-medium flex items-center gap-1.5 shadow-sm">
                                <i class="bi bi-clipboard"></i> Click to copy CSS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info panel -->
                <div class="flex flex-col gap-5 justify-between">
                    <div>
                        <h1 id="gradientName" class="text-3xl md:text-4xl font-bold tracking-tight"><?= e($gradientName) ?></h1>
                        <h2 id="gradientSubheading" class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"><?= e($colorListString) ?></h2>
                        <p id="gradientDescription" class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            High-fidelity <?= e($gradientStyle) ?> CSS gradient with copy-ready code, angle variations, and UI component integration.
                        </p>
                    </div>

                    <!-- Details: Info chips -->
                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Type</p>
                            <p id="gradientType" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight capitalize"><?= e($gradientType) ?></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Colors</p>
                            <p id="gradientColorCount" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"><?= count($gradientColors) ?> Stops</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold" id="angleShapeLabel">Angle</p>
                            <p id="gradientAngleShape" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"><?= e((string)$gradientAngle) ?>°</p>
                        </div>
                    </div>

                    <!-- Color swatches breakdown (1-click copy) -->
                    <div>
                        <div id="gradientColorList" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php foreach ($gradientColors as $cHex): ?>
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

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button id="downloadGradientPngBtn"
                            class="w-full px-5 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/25 hover:shadow-primary/40 active:scale-[0.99]"
                            title="Download gradient image">
                            <i class="bi bi-download text-base"></i>
                            <span>Download Image</span>
                        </button>
                        <button id="favGradientBtn"
                            class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 border border-slate-200/80 dark:border-slate-700/80 active:scale-[0.99]">
                            <i class="bi bi-heart text-sm"></i>
                            <span>Favorite</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: Direction & Angle Variations -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800" aria-labelledby="gradient-angles-title">
                <h2 id="gradient-angles-title" class="text-xl md:text-2xl font-bold mb-2">Direction & Angle Variations</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Explore how <?= e($gradientName) ?> renders under different geometric rotations and radial shapes.</p>

                <?php 
                $cList = implode(', ', $gradientColors);
                $variations = [
                    ['label' => '45° Diagonal', 'css' => "linear-gradient(45deg, $cList)"],
                    ['label' => '90° Vertical (Top to Bottom)', 'css' => "linear-gradient(180deg, $cList)"],
                    ['label' => '135° Standard Angle', 'css' => "linear-gradient(135deg, $cList)"],
                    ['label' => 'Radial / Centered Circle', 'css' => "radial-gradient(circle at center, $cList)"],
                ];
                ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php foreach ($variations as $var): ?>
                        <div class="group bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-3 flex flex-col justify-between">
                            <div class="h-28 rounded-xl shadow-md mb-3 transition-transform group-hover:scale-[1.02]" style="background: <?= e($var['css']) ?>;"></div>
                            <div>
                                <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block mb-2"><?= e($var['label']) ?></span>
                                <button type="button" class="copy-btn w-full py-1.5 px-2.5 rounded-lg text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-primary hover:text-white transition font-medium text-center" data-copy="background: <?= e($var['css']) ?>;">
                                    Copy CSS
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- SECTION: Official Gradient Poster Showcase (Google Images & Pinterest Indexing) -->
            <section class="bg-gradient-to-br from-pink-50/60 via-white to-purple-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-pink-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8" aria-labelledby="gradient-poster-title">
                <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                    <a href="<?= $posterUrl ?>" target="_blank" rel="noopener noreferrer" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                        <img src="<?= $posterUrl ?>" 
                             alt="<?= e($gradientName) ?> CSS Gradient (<?= e($colorListString) ?>) Poster Graphic" 
                             width="1648" height="2048" 
                             loading="lazy" decoding="async" fetchpriority="low"
                             class="w-full h-auto object-cover" />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                <i class="bi bi-arrows-fullscreen"></i> View High-Res
                            </span>
                        </div>
                    </a>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pink-100 dark:bg-pink-950/70 text-pink-700 dark:text-pink-300 text-xs font-bold border border-pink-200 dark:border-pink-800">
                        <i class="bi bi-image"></i> Official Gradient Poster Graphic
                    </div>
                    <h2 id="gradient-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <?= e($gradientName) ?> Gradient Card
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Download or share the high-resolution <strong><?= e($gradientName) ?></strong> gradient poster featuring full-bleed canvas rendering, exact color stops, architectural dividers, and verified CSS specifications.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="<?= $posterUrl ?>" download="<?= e($gradientId) ?>-gradient-poster.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                            <i class="bi bi-download"></i> Download Poster (JPEG)
                        </a>
                        <button onclick="window.ColorMagic && window.ColorMagic.exportGradientImage ? window.ColorMagic.exportGradientImage(<?= json_encode($currentGradient) ?>) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                            <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                        </button>
                        <?php if (!empty($currentGradient['pinterest_pin_id'])): ?>
                            <a href="https://www.pinterest.com/pin/<?= e($currentGradient['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                                <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: UI Application Showcase Mockup -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800" aria-labelledby="gradient-ui-title">
                <h2 id="gradient-ui-title" class="text-xl md:text-2xl font-bold mb-2">Live UI Component Mockups</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Real-world design examples implementing <?= e($gradientName) ?> in modern interfaces.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Component 1: Gradient Button -->
                    <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 text-center flex flex-col items-center justify-center">
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-4">CTA Button</span>
                        <button type="button" class="px-6 py-3 rounded-xl font-bold text-sm text-white shadow-lg transition-transform hover:scale-105 active:scale-95" style="background: <?= e($gradientCss) ?>; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25);">
                            Explore Now <i class="bi bi-arrow-right ml-1"></i>
                        </button>
                    </div>

                    <!-- Component 2: Gradient Text Clip -->
                    <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 text-center flex flex-col items-center justify-center">
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-3">Gradient Text</span>
                        <h3 class="text-2xl lg:text-3xl font-black tracking-tight" style="background: <?= e($gradientCss) ?>; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Next Gen UI
                        </h3>
                        <p class="text-xs text-slate-500 mt-2">Background clipped typography</p>
                    </div>

                    <!-- Component 3: Gradient Badge & Card Header -->
                    <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 flex flex-col justify-between">
                        <div class="h-10 rounded-xl mb-3" style="background: <?= e($gradientCss) ?>;"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Card Header Banner</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Use as hero strip or avatar ring</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Export Formats: Modern Tabbed Interface -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Code & Export</h2>
                        <p id="gradientExportDescription" class="text-sm text-slate-600 dark:text-slate-400 mt-1">Structured JSON format for APIs, AI prompts, and data exchange</p>
                    </div>
                    <button id="copyGradientActiveExportBtn" type="button"
                        class="self-start sm:self-auto shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center gap-1.5 active:scale-[0.98]"
                        title="Copy code to clipboard">
                        <i class="bi bi-clipboard"></i>
                        <span>Copy Code</span>
                    </button>
                </div>

                <!-- Tabs Navigation -->
                <div id="gradientExportTabs" class="flex items-center gap-1.5 overflow-x-auto pb-2 mb-4 border-b border-slate-200/80 dark:border-slate-800 scrollbar-none">
                    <button type="button" data-tab="json" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap bg-primary text-white shadow-sm flex items-center gap-1.5">
                        <i class="bi bi-stars"></i>
                        <span>JSON (AI Friendly)</span>
                    </button>
                    <button type="button" data-tab="css" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        CSS Code
                    </button>
                    <button type="button" data-tab="cssVars" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        CSS Variables
                    </button>
                    <button type="button" data-tab="tailwind" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        Tailwind CSS
                    </button>
                    <button type="button" data-tab="scss" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        SCSS
                    </button>
                    <button type="button" data-tab="hex" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        HEX Array
                    </button>
                    <button type="button" data-tab="svg" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        SVG Gradient
                    </button>
                    <button type="button" data-tab="android" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        Android XML
                    </button>
                    <button type="button" data-tab="swift" class="export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80">
                        Swift (SwiftUI)
                    </button>
                </div>

                <!-- Active Code Block -->
                <div class="relative bg-slate-50 dark:bg-slate-800/80 rounded-xl p-4 md:p-5 font-mono text-xs sm:text-sm overflow-auto max-h-80 border border-slate-200/80 dark:border-slate-700/80">
                    <pre id="gradientExportActiveCode" class="leading-relaxed"></pre>
                </div>
            </section>

            <!-- SECTION 3: Frequently Asked Questions (FAQ) -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800" aria-labelledby="gradient-faq-title">
                <h2 id="gradient-faq-title" class="text-xl md:text-2xl font-bold mb-2">Frequently Asked Questions</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Technical answers and CSS implementation guidelines for <?= e($gradientName) ?>.</p>

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

            <!-- Related Colors -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Colors</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Colors and swatches that form this gradient
                </p>
                <div id="relatedColorsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
            </section>

            <!-- Related Gradients -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Gradients</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    More gradients in the same style or type
                </p>
                <div id="relatedGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
            </section>

            <!-- Related Palettes -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm border border-slate-200/60 dark:border-slate-800">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Palettes</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Color palettes that share colors with this gradient
                </p>
                <div id="relatedPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8"></div>
            </section>
        </section>

        <!-- Error state -->
        <section id="gradientError"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center hidden">
            <i class="bi bi-exclamation-circle text-4xl text-red-500"></i>
            <h2 class="text-xl font-bold mt-3">Gradient not found</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                The requested gradient does not exist.
            </p>
            <a href="<?= $base ?>/gradients" class="inline-block mt-4 px-5 py-2 bg-primary text-white rounded-xl text-sm font-semibold">
                Browse all gradients
            </a>
        </section>
    </main>

    <?php include '../../components/footer.php'; ?>

    <script>
        window.CM_COLOR_BASE    = '<?= $base ?>/color/';
        window.CM_PALETTE_BASE  = '<?= $base ?>/palette/';
        window.CM_GRADIENT_BASE = '<?= $base ?>/gradient/';
        window.CM_CACHE_KEY     = '<?= cm_asset_v("assets/js/gradient-page.js") ?>';
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=<?= cm_asset_v('assets/js/utils.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=<?= cm_asset_v('assets/js/services/favorites.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/image-export.js?v=<?= cm_asset_v('assets/js/image-export.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=<?= cm_asset_v('assets/js/components/palette-card.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=<?= cm_asset_v('assets/js/components/gradient-card.js') ?>" defer></script>
    <script src="<?= $base ?>/assets/js/gradient-page.js?v=<?= cm_asset_v('assets/js/gradient-page.js') ?>" defer></script>
</body>

</html>
