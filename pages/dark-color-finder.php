<?php
include '../components/config.php';
/** @var string $base */

// Load color names and filter for dark colors (lightness < 40%)
$colorNames = [];
$colorNamesPath = 'https://api.colormagic.techkreative.com/color-names.json';
$jsonContent = @file_get_contents($colorNamesPath);
if ($jsonContent !== false) {
    $decoded = json_decode((string) $jsonContent, true);
    if (is_array($decoded)) {
        $colorNames = $decoded;
    }
}

function hexToHslForFilter(string $hex): array
{
    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;
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
    return ['h' => (int) round($h * 360), 's' => (int) round($s * 100), 'l' => (int) round($l * 100)];
}

function getHueFamily(int $h, int $s, int $l): string
{
    if ($s < 10)
        return 'Neutrals';
    if ($h < 15 || $h >= 345)
        return 'Dark Reds';
    if ($h < 45)
        return 'Dark Oranges';
    if ($h < 70)
        return 'Dark Yellows';
    if ($h < 160)
        return 'Dark Greens';
    if ($h < 200)
        return 'Dark Cyans';
    if ($h < 260)
        return 'Dark Blues';
    if ($h < 290)
        return 'Dark Purples';
    return 'Dark Pinks';
}

$darkColors = [];
foreach ($colorNames as $entry) {
    if (!is_array($entry) || !isset($entry['hex']))
        continue;
    $hex = $entry['hex'];
    $hsl = hexToHslForFilter($hex);
    if ($hsl['l'] < 40) {
        $entry['_hsl'] = $hsl;
        $entry['_family'] = getHueFamily($hsl['h'], $hsl['s'], $hsl['l']);
        $darkColors[] = $entry;
    }
}

// Group by hue family
$grouped = [];
foreach ($darkColors as $c) {
    $grouped[$c['_family']][] = $c;
}
ksort($grouped);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dark Color Name Finder — Discover Deep & Dark Shade Names | Color Magic</title>
    <meta name="description"
        content="Find names for dark and deep color shades. Browse 50+ dark colors organized by hue family — dark reds, blues, greens, purples, and more." />
    <meta name="keywords"
        content="dark color names, dark shade finder, deep color names, dark hex colors, midnight blue, dark color palette" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/dark-color-finder" />
    <meta property="og:title" content="Dark Color Name Finder — Discover Deep & Dark Shade Names | Color Magic" />
    <meta property="og:description"
        content="Find names for dark and deep color shades. Browse 50+ dark colors organized by hue family." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/dark-color-finder" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>
    <style>
        .sb-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: none;
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
        }

        .sb-btn.sb-inactive {
            color: #475569;
            background: transparent;
        }

        .sb-btn.sb-inactive:hover {
            background: #fdf2f8;
            color: #ec4899;
        }

        .sb-btn.sb-inactive .sb-icon {
            background: #fdf2f8;
            color: #ec4899;
        }

        .sb-btn.sb-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            box-shadow: 0 8px 24px -6px rgba(236, 72, 153, 0.45);
        }

        .sb-btn.sb-active .sb-icon {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .dark .sb-btn.sb-inactive {
            color: #94a3b8;
        }

        .dark .sb-btn.sb-inactive:hover {
            background: #1e293b;
            color: #ec4899;
        }

        .dark .sb-btn.sb-inactive .sb-icon {
            background: #1e293b;
            color: #ec4899;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.2s ease both;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include '../components/navbar.php'; ?>
    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="<?= $base ?>/" class="flex items-center gap-2 text-primary">
                <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic Logo" class="h-8 w-8 object-contain" />
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
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-house-door"></i></span>
                <div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span></div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS
                        gradients</span></div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span></div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span></div>
            </a>
            <a href="<?= $base ?>/profile" class=" sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors,
                        palettes &amp; gradients</span></div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span></div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-6 md:px-8 flex flex-col gap-6">
        <!-- Hero -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500/10 via-slate-100 to-slate-200 dark:from-indigo-500/20 dark:via-slate-900 dark:to-slate-800 p-7 md:p-10 border border-indigo-100 dark:border-slate-800">
            <div
                class="absolute w-56 h-56 bg-indigo-500/15 rounded-full blur-[70px] top-[-30px] right-[-30px] pointer-events-none">
            </div>
            <div class="relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-3 border border-indigo-200/50 dark:border-indigo-700/50">
                    <i class="bi bi-moon-stars"></i> Dark Color Name Finder
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Find Dark Color Names</h1>
                <p class="text-slate-600 dark:text-slate-300 text-base max-w-lg">
                    Discover the names of deep, dark color shades. Browse <?= count($darkColors) ?>+ dark colors
                    organized by hue family — from dark reds and blues to deep purples and midnight tones.
                </p>
            </div>
        </div>

        <!-- Dark Color Gallery by Hue Family -->
        <?php foreach ($grouped as $family => $colors): ?>
            <div class="mb-6">
                <h2 class="text-lg font-bold mb-3 text-slate-800 dark:text-white flex items-center gap-2">
                    <?php
                    $familyIcons = [
                        'Dark Blues' => 'bi-droplet-fill text-blue-500',
                        'Dark Reds' => 'bi-droplet-fill text-red-500',
                        'Dark Greens' => 'bi-tree text-emerald-500',
                        'Dark Purples' => 'bi-gem text-purple-500',
                        'Dark Pinks' => 'bi-heart-fill text-pink-500',
                        'Dark Cyans' => 'bi-droplet-fill text-cyan-500',
                        'Dark Oranges' => 'bi-sun text-orange-500',
                        'Dark Yellows' => 'bi-brightness-high text-yellow-500',
                        'Neutrals' => 'bi-circle-half text-slate-500',
                    ];
                    $iconClass = $familyIcons[$family] ?? 'bi-palette text-slate-500';
                    ?>
                    <i class="bi <?= $iconClass ?>"></i> <?= $family ?>
                    <span class="text-xs font-normal text-slate-400">(<?= count($colors) ?>)</span>
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    <?php foreach ($colors as $color): ?>
                        <a href="<?= $base ?>/color/<?= htmlspecialchars($color['slug'] ?? strtolower($color['hex'])) ?>/"
                            class="group flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            <span class="w-full h-16 rounded-lg shadow-sm"
                                style="background:#<?= htmlspecialchars($color['hex']) ?>"></span>
                            <p class="font-bold text-xs text-center leading-tight"><?= htmlspecialchars($color['name']) ?></p>
                            <p class="text-[10px] font-mono text-slate-400">#<?= htmlspecialchars(strtoupper($color['hex'])) ?>
                            </p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- FAQ Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Frequently Asked Questions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What makes a color dark?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">A color is considered dark when its lightness
                        value in the HSL color model is below 40%. This means it reflects less light and appears deeper
                        or closer to black. Dark colors can still be vibrant — they just have low lightness regardless
                        of their saturation or hue.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What are popular dark color names?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Some of the most popular dark colors include
                        Midnight Blue (#191970), Navy (#000080), Dark Slate Gray (#2F4F4F), Burgundy (#800020), Dark
                        Green (#006400), and Indigo (#4B0082). These are widely used in web design, branding, and
                        interior design.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">Best dark colors for web design?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Dark colors work excellently as backgrounds,
                        text colors, and accent shades. Popular choices include dark navy (#1E293B) for backgrounds,
                        charcoal (#334155) for text, and deep jewel tones like Dark Emerald (#006400) or Burgundy
                        (#800020) for accents and CTAs.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What is the darkest color besides black?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">The darkest named colors besides pure black
                        (#000000) include Rich Black (#000814), Dark Navy (#000C24), and Vampire Black (#080808). In
                        practice, Vantablack — a synthetic material — absorbs 99.965% of visible light, making it the
                        darkest substance known.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How to find dark shade hex codes?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Browse the gallery above — every color shown
                        has a lightness value below 40%, making them all genuinely dark. Click any swatch to see its
                        full details, or use our <a href="<?= $base ?>/find-color"
                            class="text-primary hover:underline">Find Color tool</a> to check if any hex code qualifies
                        as dark.</p>
                </div>
            </div>
        </div>

        <!-- Related Tools -->
        <div
            class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900/50 rounded-2xl p-8 mb-12">
            <h2 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Related Color Tools</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="<?= $base ?>/find-color"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-eyedropper text-emerald-500 text-lg"></i>
                    <span class="text-sm font-semibold">Find Color</span>
                </a>
                <a href="<?= $base ?>/hex-to-color-name"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-tag text-primary text-lg"></i>
                    <span class="text-sm font-semibold">Hex to Color Name</span>
                </a>
                <a href="<?= $base ?>/what-color-is"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-question-circle text-amber-500 text-lg"></i>
                    <span class="text-sm font-semibold">What Color Is This?</span>
                </a>
                <a href="<?= $base ?>/generate-palette"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-stars text-secondary text-lg"></i>
                    <span class="text-sm font-semibold">Generate Palette</span>
                </a>
            </div>
        </div>

        <!-- FAQ Schema -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "What makes a color dark?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "A color is considered dark when its lightness value in the HSL color model is below 40%. This means it reflects less light and appears deeper or closer to black."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What are popular dark color names?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Popular dark colors include Midnight Blue (#191970), Navy (#000080), Dark Slate Gray (#2F4F4F), Burgundy (#800020), and Dark Green (#006400)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Best dark colors for web design?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Dark navy (#1E293B) for backgrounds, charcoal (#334155) for text, and deep jewel tones like Dark Emerald or Burgundy for accents and CTAs."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What is the darkest color besides black?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "The darkest named colors besides black include Rich Black (#000814), Dark Navy (#000C24), and Vampire Black (#080808)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How to find dark shade hex codes?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Browse the gallery above where every color has a lightness value below 40%. You can also use our Find Color tool to check if any hex code qualifies as dark."
                    }
                }
            ]
        }
        </script>
    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "dark-color-finder";
    </script>
</body>

</html>