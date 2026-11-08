<?php
include '../components/config.php';
/** @var string $base */

// Fast server-side load of featured palettes and gradients for instant rendering & SEO
$palettesFile = __DIR__ . '/../data/palettes.json';
$allPalettes = file_exists($palettesFile) ? json_decode((string)file_get_contents($palettesFile), true) : [];
$featuredPalettes = is_array($allPalettes) ? array_slice($allPalettes, 0, 6) : [];

$gradientsFile = __DIR__ . '/../data/gradients.json';
$allGradients = file_exists($gradientsFile) ? json_decode((string)file_get_contents($gradientsFile), true) : [];
$featuredGradients = is_array($allGradients) ? array_slice($allGradients, 0, 6) : [];

function slugify_home(string $text): string {
    return trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($text)), '-');
}

function normalize_hex_home(string $hex): string {
    return strtoupper(ltrim($hex, '#'));
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta name="p:domain_verify" content="938654d30b38c1ce54405b051fff4560"/>
    <meta name="yandex-verification" content="797f9c11121b55d1" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Color Magic | Color Name Finder, Palette Generator & Design System</title>
    <meta name="description"
        content="Free color name finder & palette generator. Look up hex color names, generate harmonious color palettes with color theory, explore 800+ curated schemes and 300+ CSS gradients." />
    <meta name="keywords"
        content="color name finder, hex to color name, color palette generator, css gradients, color theory harmonies, hex color lookup, RGB converter, WCAG contrast checker" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/" />
    <meta property="og:title" content="Color Magic | Color Name Finder & Palette Generator" />
    <meta property="og:description"
        content="Free color name finder — convert any hex code to its color name, RGB, and HSL. Generate professional palettes, browse 800+ schemes and 300+ CSS gradients." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" content="Color Magic | Color Name Finder & Palette Generator" />
    <meta property="twitter:description"
        content="Free color name finder & palette generator with 800+ curated palettes, 300+ gradients, and WCAG contrast analysis." />
    <meta property="twitter:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/logo.png" fetchpriority="high" />

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
    <style>
        /* ── Critical Layout Stability (Zero CLS) ── */
        header {
            min-height: 64px;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -15px rgba(124, 58, 237, 0.12);
        }

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
            transition: transform 0.22s ease;
        }

        .sb-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #ffffff;
            box-shadow: 0 4px 12px -2px rgba(236, 72, 153, 0.2);
        }

        .sb-inactive {
            color: #475569;
        }

        .dark .sb-inactive {
            color: #cbd5e1;
        }

        .sb-inactive:hover {
            background-color: #f1f5f9;
        }

        .dark .sb-inactive:hover {
            background-color: #1e1b4b;
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

        /* Palette scrolling animations with strict layout containment */
        @keyframes scrollUp {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-50%);
            }
        }

        @keyframes scrollDown {
            0% {
                transform: translateY(-50%);
            }
            100% {
                transform: translateY(0);
            }
        }

        .animate-scroll-up {
            animation: scrollUp 25s linear infinite;
            will-change: transform;
        }

        .animate-scroll-down {
            animation: scrollDown 25s linear infinite;
            will-change: transform;
        }

        .animation-container {
            contain: strict;
            content-visibility: auto;
            contain-intrinsic-size: 350px;
            height: 350px;
            mask-image: linear-gradient(to bottom,
                    transparent,
                    white 20%,
                    white 80%,
                    transparent);
            -webkit-mask-image: linear-gradient(to bottom,
                    transparent,
                    white 20%,
                    white 80%,
                    transparent);
        }

        /* Toast notifications */
        #cmToast {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen relative overflow-x-hidden">
    <!-- Background Glow Effects -->
    <div class="glow-bg top-[-100px] left-[-100px]"></div>
    <div class="glow-bg bottom-[-100px] right-[-100px]"></div>

    <!-- Toast Notification for Copying -->
    <div id="cmToast" class="fixed bottom-6 right-6 z-[100] hidden items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-2xl pointer-events-none transform translate-y-3 opacity-0">
        <i class="bi bi-check2-circle text-emerald-400 dark:text-emerald-600 text-base"></i>
        <span id="cmToastMessage">Copied to clipboard!</span>
    </div>

    <!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
    <?php include '../components/navbar.php'; ?>

    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="<?= $base ?>/" class="flex items-center gap-2 text-primary">
                <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic Logo" width="32" height="32" class="h-8 w-8 object-contain" />
                <span class="text-xl font-bold tracking-tight">
                    <span class="text-slate-900 dark:text-white">Color</span>
                    <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
                </span>
            </a>
            <button id="closeMobileMenuBtn"
                class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                aria-label="Close menu">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        <!-- Mobile nav items -->
        <div class="space-y-2">
            <a href="<?= $base ?>/" class="sb-btn sb-active w-full"><span class="sb-icon"><i
                        class="bi bi-house-door"></i></span>
                <div>
                    <span class="block font-bold">Home</span><span class="text-xs opacity-75">Back to homepage</span>
                </div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div>
                    <span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">800+ curated schemes</span>
                </div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div>
                    <span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS gradients</span>
                </div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div>
                    <span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp; info</span>
                </div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div>
                    <span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Harmonious color schemes</span>
                </div>
            </a>
            <a href="<?= $base ?>/palette-from-image" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-image"></i></span>
                <div>
                    <span class="block font-bold">Palette from Image</span><span class="text-xs opacity-60">Extract colors from photo</span>
                </div>
            </a>
            <a href="<?= $base ?>/profile" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div>
                    <span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved collection</span>
                </div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div>
                    <span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on GitHub</span>
                </div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-10 px-6 md:px-12 lg:px-16 flex flex-col justify-center items-center">
        
        <!-- ── 1. HERO SECTION ────────────────────────────────────────────── -->
        <div class="grid grid-cols-1 py-12 lg:grid-cols-12 gap-12 items-center w-full mb-12">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.14]">
                    Discover <span class="bg-gradient-to-r from-secondary via-fuchsia-500 to-primary bg-clip-text text-transparent">Exact Color Names</span> &amp; Craft Harmonious Palettes
                </h1>

                <p class="text-base md:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-medium max-w-2xl mx-auto lg:mx-0">
                    Turn any hex code into its verified color name, inspect RGB &amp; HSL metrics, check WCAG contrast accessibility, and explore 800+ designer palettes and 300+ CSS gradients with downloadable high-res posters.
                </p>

                <!-- Interactive Quick Color Lookup Form in Hero -->
                <form id="heroColorForm" class="flex flex-col sm:flex-row items-center gap-3 w-full max-w-xl mx-auto lg:mx-0 p-2 bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-purple-500/5 border border-pink-100 dark:border-slate-800 transition-all focus-within:ring-2 focus-within:ring-primary/40">
                    <div class="flex items-center gap-3 px-3 py-1 flex-1 w-full">
                        <label for="heroColorPicker" class="relative cursor-pointer shrink-0" title="Click to choose a color">
                            <span id="heroColorPreview" class="block w-10 h-10 rounded-xl shadow-inner border border-slate-200 dark:border-slate-700 transition-colors" style="background-color: #00AEEF;"></span>
                            <input type="color" id="heroColorPicker" value="#00aeef" class="sr-only" aria-label="Choose a color" />
                        </label>
                        <div class="flex items-center flex-1">
                            <span class="text-slate-400 dark:text-slate-500 font-mono font-bold text-lg select-none">#</span>
                            <input type="text" id="heroColorInput" value="00AEEF" maxlength="6" placeholder="00AEEF"
                                   class="w-full bg-transparent border-none text-slate-900 dark:text-white font-mono font-bold text-lg uppercase tracking-wider focus:outline-none focus:ring-0 px-1" />
                        </div>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-secondary to-primary hover:opacity-95 text-white font-bold rounded-xl transition-all shadow-md shadow-primary/20 shrink-0 flex items-center justify-center gap-2">
                        <span>Inspect Color</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <!-- Quick Hue Recommendations -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 text-xs text-slate-500 dark:text-slate-400 pt-1">
                    <span class="font-semibold text-slate-400">Popular:</span>
                    <button type="button" onclick="setHeroColor('00AEEF')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-mono font-medium transition flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #00AEEF;"></span>#00AEEF
                    </button>
                    <button type="button" onclick="setHeroColor('FF5733')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-mono font-medium transition flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #FF5733;"></span>#FF5733
                    </button>
                    <button type="button" onclick="setHeroColor('2E7D32')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-mono font-medium transition flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #2E7D32;"></span>#2E7D32
                    </button>
                    <button type="button" onclick="setHeroColor('8B5CF6')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-mono font-medium transition flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #8B5CF6;"></span>#8B5CF6
                    </button>
                    <button type="button" onclick="setHeroColor('F59E0B')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 font-mono font-medium transition flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #F59E0B;"></span>#F59E0B
                    </button>
                </div>
            </div>

            <!-- Right Visual Scrolling Animation -->
            <div
                class="lg:col-span-5 hidden lg:flex items-center justify-center h-[380px] relative overflow-hidden animation-container w-full">
                <!-- Column 1 (Scrolls Up) -->
                <div class="w-1/2 px-2 flex flex-col gap-4 animate-scroll-up">
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #6366f1"></div>
                            <div class="flex-1" style="background-color: #8b5cf6"></div>
                            <div class="flex-1" style="background-color: #d946ef"></div>
                            <div class="flex-1" style="background-color: #ec4899"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Neon Sunset
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #fca5a5"></div>
                            <div class="flex-1" style="background-color: #fef08a"></div>
                            <div class="flex-1" style="background-color: #a7f3d0"></div>
                            <div class="flex-1" style="background-color: #93c5fd"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Pastel Dream
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #10b981"></div>
                            <div class="flex-1" style="background-color: #059669"></div>
                            <div class="flex-1" style="background-color: #047857"></div>
                            <div class="flex-1" style="background-color: #064e3b"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Forest Hills
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #f97316"></div>
                            <div class="flex-1" style="background-color: #ea580c"></div>
                            <div class="flex-1" style="background-color: #c2410c"></div>
                            <div class="flex-1" style="background-color: #7c2d12"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Autumn Fire
                        </div>
                    </div>
                    <!-- Duplicate for infinite scroll loop -->
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #6366f1"></div>
                            <div class="flex-1" style="background-color: #8b5cf6"></div>
                            <div class="flex-1" style="background-color: #d946ef"></div>
                            <div class="flex-1" style="background-color: #ec4899"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Neon Sunset
                        </div>
                    </div>
                </div>

                <!-- Column 2 (Scrolls Down) -->
                <div class="w-1/2 px-2 flex flex-col gap-4 animate-scroll-down">
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #1e293b"></div>
                            <div class="flex-1" style="background-color: #334155"></div>
                            <div class="flex-1" style="background-color: #475569"></div>
                            <div class="flex-1" style="background-color: #64748b"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Slate Monochrome
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #06b6d4"></div>
                            <div class="flex-1" style="background-color: #0891b2"></div>
                            <div class="flex-1" style="background-color: #0e7490"></div>
                            <div class="flex-1" style="background-color: #155e75"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Deep Ocean
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #f43f5e"></div>
                            <div class="flex-1" style="background-color: #e11d48"></div>
                            <div class="flex-1" style="background-color: #be123c"></div>
                            <div class="flex-1" style="background-color: #9f1239"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Rose Crimson
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #1e293b"></div>
                            <div class="flex-1" style="background-color: #334155"></div>
                            <div class="flex-1" style="background-color: #475569"></div>
                            <div class="flex-1" style="background-color: #64748b"></div>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Slate Monochrome
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── 1.5 DESIGN SUITE QUICK LAUNCHPAD ───────────────────────────── -->
        <section class="w-full mb-14 -mt-4" aria-label="Explore Color Magic Suite">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                
                <!-- Card 1: Explore Palettes -->
                <a href="<?= $base ?>/palettes" class="group p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-primary/40 dark:hover:border-primary/40 transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 dark:bg-primary/20 text-primary flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight group-hover:text-primary transition-colors">
                            Explore 800+ Palettes
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            Browse curated color harmonies across Pastel, Neon, Vintage, and Retro aesthetics with downloadable official posters.
                        </p>
                    </div>
                    <div class="pt-5 flex items-center gap-1.5 text-xs font-bold text-primary">
                        <span>Browse Library</span>
                        <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </div>
                </a>

                <!-- Card 2: Generate Palette -->
                <a href="<?= $base ?>/generate-palette" class="group p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-secondary/40 dark:hover:border-secondary/40 transition-all duration-300 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-secondary/10 dark:bg-secondary/20 text-secondary flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight group-hover:text-secondary transition-colors">
                            Generate Color Scheme
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            Instant color theory engine: Complementary, Analogous, and Triadic harmonies with 1-click lock and copy.
                        </p>
                    </div>
                    <div class="pt-5 flex items-center gap-1.5 text-xs font-bold text-secondary">
                        <span>Open Studio</span>
                        <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </div>
                </a>

                <!-- Card 3: CSS Gradients -->
                <a href="<?= $base ?>/gradients" class="group p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-amber-400/40 dark:hover:border-amber-400/40 transition-all duration-300 flex flex-col justify-between sm:col-span-2 lg:col-span-1">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            <i class="bi bi-rainbow"></i>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight group-hover:text-amber-500 transition-colors">
                            300+ CSS Gradients
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            High-contrast, production-ready linear and radial CSS gradients ready for instant copy and paste into your UI.
                        </p>
                    </div>
                    <div class="pt-5 flex items-center gap-1.5 text-xs font-bold text-amber-500">
                        <span>Explore Gradients</span>
                        <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </div>
                </a>

            </div>
        </section>

        <!-- ── 2. METRICS & TRUST STATS BAR ───────────────────────────────── -->
        <section class="w-full mb-16 py-6" aria-label="Platform Statistics">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <div class="p-5 md:p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 shadow-sm text-center">
                    <p class="text-3xl md:text-4xl font-black bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">800+</p>
                    <p class="text-xs md:text-sm font-semibold text-slate-600 dark:text-slate-400 mt-1">Curated Palettes</p>
                </div>
                <div class="p-5 md:p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 shadow-sm text-center">
                    <p class="text-3xl md:text-4xl font-black bg-gradient-to-r from-amber-500 to-rose-500 bg-clip-text text-transparent">300+</p>
                    <p class="text-xs md:text-sm font-semibold text-slate-600 dark:text-slate-400 mt-1">CSS Gradients</p>
                </div>
                <div class="p-5 md:p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 shadow-sm text-center">
                    <p class="text-3xl md:text-4xl font-black bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent">6,000+</p>
                    <p class="text-xs md:text-sm font-semibold text-slate-600 dark:text-slate-400 mt-1">Named Colors</p>
                </div>
                <div class="p-5 md:p-6 rounded-2xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-800 shadow-sm text-center">
                    <p class="text-3xl md:text-4xl font-black bg-gradient-to-r from-blue-500 to-indigo-500 bg-clip-text text-transparent">100%</p>
                    <p class="text-xs md:text-sm font-semibold text-slate-600 dark:text-slate-400 mt-1">Free &amp; Open Source</p>
                </div>
            </div>
        </section>

        <!-- ── 3. LIVE INTERACTIVE SPACEBAR GENERATOR SIMULATOR ─────────────── -->
        <section class="w-full mb-20 bg-gradient-to-br from-purple-50/50 via-white to-pink-50/50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800/80 rounded-3xl p-6 md:p-10 border border-purple-100 dark:border-slate-800 shadow-sm" aria-labelledby="live-generator-heading">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary text-xs font-bold mb-2">
                        <i class="bi bi-stars"></i>
                        <span>Instant Playground</span>
                    </div>
                    <h2 id="live-generator-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        Try the Palette Generator Live
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Click any color to copy its Hex code. Lock shades you love and press <kbd class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 font-mono text-xs font-bold">Spacebar</kbd> or click Shuffle.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="shuffleHomePaletteBtn" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-2">
                        <i class="bi bi-shuffle text-sm"></i>
                        <span>Shuffle (Spacebar)</span>
                    </button>
                    <a href="<?= $base ?>/generate-palette" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 transition">
                        Studio &rarr;
                    </a>
                </div>
            </div>

            <!-- 5-Color Interactive Swatch Columns -->
            <div id="homePaletteContainer" class="grid grid-cols-1 sm:grid-cols-5 gap-3 h-auto sm:h-64 rounded-2xl overflow-hidden p-2 bg-slate-100 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <!-- Injected via JavaScript -->
            </div>
        </section>

        <!-- ── 4. TRENDING COLOR PALETTES (LIVE SHOWCASE) ─────────────────── -->
        <section class="w-full mb-20" aria-labelledby="featured-palettes-heading">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold mb-2">
                        <i class="bi bi-palette-fill"></i>
                        <span>Curated Schemes</span>
                    </div>
                    <h2 id="featured-palettes-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        Trending Color Palettes
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Hand-picked harmonious combinations ready for branding, web design, and UI kits.
                    </p>
                </div>
                <a href="<?= $base ?>/palettes" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary/80 transition group shrink-0">
                    <span>Browse All 800+ Palettes</span>
                    <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($featuredPalettes as $palette): 
                    $pName = (string)($palette['name'] ?? 'Palette');
                    $pSlug = slugify_home($pName);
                    if (empty($pSlug)) $pSlug = (string)($palette['id'] ?? 'palette_1');
                    $pStyle = (string)($palette['style'] ?? 'Curated');
                    $pColors = is_array($palette['colors'] ?? null) ? $palette['colors'] : ['#1B5E20', '#2E7D32', '#66BB6A', '#C8E6C9'];
                ?>
                    <div class="card-hover bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Header: Name + Badge -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <h3 class="font-bold text-base text-slate-900 dark:text-white tracking-tight truncate">
                                    <a href="<?= $base ?>/palette/<?= urlencode($pSlug) ?>/" class="hover:text-primary transition-colors">
                                        <?= htmlspecialchars($pName, ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    <?= htmlspecialchars($pStyle, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>

                            <!-- Swatch Bar -->
                            <div class="flex h-20 rounded-xl overflow-hidden shadow-inner border border-slate-100 dark:border-slate-800 mb-4">
                                <?php foreach ($pColors as $colHex): 
                                    $cleanCol = normalize_hex_home($colHex);
                                ?>
                                    <button type="button"
                                            onclick="copyHexValue('#<?= $cleanCol ?>')"
                                            class="flex-1 h-full relative group transition-transform hover:scale-105 hover:z-10 focus:outline-none"
                                            style="background-color: #<?= $cleanCol ?>;"
                                            title="Click to copy #<?= $cleanCol ?>">
                                        <span class="absolute inset-x-0 bottom-1 text-[9px] font-mono font-bold opacity-0 group-hover:opacity-100 transition-opacity bg-black/60 text-white rounded px-1 mx-0.5 text-center">
                                            #<?= $cleanCol ?>
                                        </span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-slate-800/80">
                            <span class="font-mono text-slate-400 text-[11px]"><?= count($pColors) ?> Swatches</span>
                            <a href="<?= $base ?>/palette/<?= urlencode($pSlug) ?>/" class="font-bold text-primary hover:underline flex items-center gap-1">
                                <span>Inspect &amp; Poster</span>
                                <i class="bi bi-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ── 5. TRENDING CSS GRADIENTS (LIVE SHOWCASE) ──────────────────── -->
        <section class="w-full mb-20" aria-labelledby="featured-gradients-heading">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-500 text-xs font-bold mb-2">
                        <i class="bi bi-rainbow"></i>
                        <span>Modern Blends</span>
                    </div>
                    <h2 id="featured-gradients-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        Curated CSS Gradients
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        High-contrast, eye-catching color gradients with instant 1-click ready-to-paste CSS.
                    </p>
                </div>
                <a href="<?= $base ?>/gradients" class="inline-flex items-center gap-1.5 text-sm font-bold text-amber-500 hover:text-amber-600 transition group shrink-0">
                    <span>Explore All 300+ Gradients</span>
                    <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($featuredGradients as $gradient): 
                    $gId = (string)($gradient['id'] ?? 'gradient_1');
                    $gName = (string)($gradient['name'] ?? 'Gradient');
                    $gCss = (string)($gradient['css'] ?? 'linear-gradient(135deg, #FF512F, #DD2476)');
                    $gColors = is_array($gradient['colors'] ?? null) ? $gradient['colors'] : ['#FF512F', '#DD2476'];
                ?>
                    <div class="card-hover bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <!-- Visual Gradient Preview Box -->
                            <a href="<?= $base ?>/gradient/<?= urlencode($gId) ?>/" class="block h-28 rounded-xl overflow-hidden shadow-inner border border-slate-100 dark:border-slate-800 mb-4 transition-transform hover:scale-[1.02]" style="background: <?= htmlspecialchars($gCss, ENT_QUOTES, 'UTF-8') ?>;"></a>

                            <!-- Title & Info -->
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <h3 class="font-bold text-base text-slate-900 dark:text-white tracking-tight truncate">
                                    <a href="<?= $base ?>/gradient/<?= urlencode($gId) ?>/" class="hover:text-amber-500 transition-colors">
                                        <?= htmlspecialchars($gName, ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <div class="flex items-center gap-1">
                                    <?php foreach ($gColors as $colHex): ?>
                                        <span class="w-3.5 h-3.5 rounded-full border border-white dark:border-slate-800 shadow-sm" style="background-color: <?= htmlspecialchars($colHex, ENT_QUOTES, 'UTF-8') ?>;" title="<?= htmlspecialchars($colHex, ENT_QUOTES, 'UTF-8') ?>"></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button: Copy CSS -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2">
                            <button type="button"
                                    onclick="copyGradientCss('background: <?= addslashes($gCss) ?>;', this)"
                                    class="w-full py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 dark:hover:text-white font-semibold text-xs transition flex items-center justify-center gap-1.5">
                                <i class="bi bi-clipboard"></i>
                                <span>Copy CSS</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ── 6. COLOR THEORY & HARMONIES GUIDE ───────────────────────────── -->
        <section class="w-full mb-20 bg-slate-50 dark:bg-slate-900/60 rounded-3xl p-6 md:p-10 border border-slate-200 dark:border-slate-800" aria-labelledby="color-theory-heading">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-secondary/10 text-secondary text-xs font-bold mb-3">
                    <i class="bi bi-compass"></i>
                    <span>Design Principles</span>
                </div>
                <h2 id="color-theory-heading" class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                    Master Color Harmonies in Seconds
                </h2>
                <p class="text-sm md:text-base text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                    Color theory provides mathematical rules for selecting colors that visually resonate together. Explore the 6 essential harmony systems used in modern UI/UX and branding.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Complementary -->
                <a href="<?= $base ?>/complementary-colors" class="group p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-base text-slate-900 dark:text-white group-hover:text-primary transition-colors">Complementary</span>
                            <span class="text-xs font-mono font-bold text-secondary">180° Opposite</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                            Direct opposites on the 360° color wheel. Produces high visual tension, making call-to-actions, buttons, and badges stand out immediately.
                        </p>
                    </div>
                    <div>
                        <div class="flex h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 mb-3">
                            <div class="flex-1" style="background-color: #0077FE;" title="#0077FE"></div>
                            <div class="flex-1" style="background-color: #FE8800;" title="#FE8800"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-primary">
                            <span>Explore Guide &amp; Generator</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- 2. Analogous -->
                <a href="<?= $base ?>/analogous-colors" class="group p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-base text-slate-900 dark:text-white group-hover:text-primary transition-colors">Analogous</span>
                            <span class="text-xs font-mono font-bold text-secondary">30° Adjacent</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                            Colors sitting directly next to each other on the color wheel. Creates serene, comfortable, and nature-inspired unified compositions.
                        </p>
                    </div>
                    <div>
                        <div class="flex h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 mb-3">
                            <div class="flex-1" style="background-color: #10B981;" title="#10B981"></div>
                            <div class="flex-1" style="background-color: #059669;" title="#059669"></div>
                            <div class="flex-1" style="background-color: #34D399;" title="#34D399"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-primary">
                            <span>Explore Guide &amp; Generator</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- 3. Triadic -->
                <a href="<?= $base ?>/triadic-colors" class="group p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-base text-slate-900 dark:text-white group-hover:text-primary transition-colors">Triadic</span>
                            <span class="text-xs font-mono font-bold text-secondary">120° Triangle</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                            Three equidistant points around the wheel. Vibrant and energetic while preserving harmonious balance between background and accents.
                        </p>
                    </div>
                    <div>
                        <div class="flex h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 mb-3">
                            <div class="flex-1" style="background-color: #EF4444;" title="#EF4444"></div>
                            <div class="flex-1" style="background-color: #F59E0B;" title="#F59E0B"></div>
                            <div class="flex-1" style="background-color: #3B82F6;" title="#3B82F6"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-primary">
                            <span>Explore Guide &amp; Generator</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- 4. Monochromatic -->
                <a href="<?= $base ?>/monochromatic-colors" class="group p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-base text-slate-900 dark:text-white group-hover:text-primary transition-colors">Monochromatic</span>
                            <span class="text-xs font-mono font-bold text-secondary">Single Hue</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                            Variations in saturation and lightness derived from a single hue. Guaranteed visual cohesion, ideal for sleek SaaS products and clean portfolios.
                        </p>
                    </div>
                    <div>
                        <div class="flex h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 mb-3">
                            <div class="flex-1" style="background-color: #1E1B4B;" title="#1E1B4B"></div>
                            <div class="flex-1" style="background-color: #4338CA;" title="#4338CA"></div>
                            <div class="flex-1" style="background-color: #818CF8;" title="#818CF8"></div>
                            <div class="flex-1" style="background-color: #C7D2FE;" title="#C7D2FE"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-primary">
                            <span>Explore Guide &amp; Generator</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- 5. Split-Complementary -->
                <a href="<?= $base ?>/split-complementary-colors" class="group p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-base text-slate-900 dark:text-white group-hover:text-primary transition-colors">Split-Complementary</span>
                            <span class="text-xs font-mono font-bold text-secondary">150° + 210°</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                            A base color plus the two colors flanking its direct complement. Offers vibrant contrast with less tension than a strict complementary pairing.
                        </p>
                    </div>
                    <div>
                        <div class="flex h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 mb-3">
                            <div class="flex-1" style="background-color: #8B5CF6;" title="#8B5CF6"></div>
                            <div class="flex-1" style="background-color: #F59E0B;" title="#F59E0B"></div>
                            <div class="flex-1" style="background-color: #10B981;" title="#10B981"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-primary">
                            <span>Explore Guide &amp; Generator</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- 6. Tetradic (Rectangle) -->
                <a href="<?= $base ?>/tetradic-colors" class="group p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-base text-slate-900 dark:text-white group-hover:text-primary transition-colors">Tetradic (Four Hues)</span>
                            <span class="text-xs font-mono font-bold text-secondary">Double Pair</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                            Two complementary pairs arranged in a rectangle. Rich and colorful, allowing designers to establish distinct accents across multiple app modules.
                        </p>
                    </div>
                    <div>
                        <div class="flex h-10 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 mb-3">
                            <div class="flex-1" style="background-color: #EC4899;" title="#EC4899"></div>
                            <div class="flex-1" style="background-color: #F59E0B;" title="#F59E0B"></div>
                            <div class="flex-1" style="background-color: #06B6D4;" title="#06B6D4"></div>
                            <div class="flex-1" style="background-color: #84CC16;" title="#84CC16"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-primary">
                            <span>Explore Guide &amp; Generator</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- ── 7. WCAG ACCESSIBILITY & CONTRAST PRIMER ─────────────────────── -->
        <section class="w-full mb-20 bg-gradient-to-br from-indigo-50/50 via-white to-purple-50/50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-10 border border-indigo-100 dark:border-slate-800 shadow-sm" aria-labelledby="wcag-primer-heading">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-xs font-bold">
                        <i class="bi bi-universal-access"></i>
                        <span>WCAG 2.1 Guidelines</span>
                    </div>
                    <h2 id="wcag-primer-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        Build Accessible Interfaces with High Contrast
                    </h2>
                    <p class="text-sm md:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                        Accessibility is critical for inclusive design. Every color page on Color Magic calculates real-time contrast ratios against white and black backgrounds according to World Wide Web Consortium (W3C) standards.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold text-lg">4.5:1</span>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">AA Normal Text</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Regular copy &lt; 18pt</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold text-lg">3.0:1</span>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">AA Large Text</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Headings &ge; 18pt</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold text-lg">7.0:1</span>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">AAA Enhanced</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">High-legibility rating</p>
                        </div>
                    </div>
                </div>

                <!-- Visual Contrast Box Demonstration -->
                <div class="lg:col-span-5 flex flex-col gap-3">
                    <div class="p-5 rounded-2xl bg-white text-slate-900 border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="font-bold text-sm">Dark Text on White</p>
                            <p class="text-xs text-slate-500">Ratio: 16.2:1</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                            ✓ AAA Pass
                        </span>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-900 text-white border border-slate-800 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="font-bold text-sm">Light Text on Dark</p>
                            <p class="text-xs text-slate-400">Ratio: 18.5:1</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                            ✓ AAA Pass
                        </span>
                    </div>

                    <div class="p-5 rounded-2xl bg-pink-100 text-pink-400 border border-pink-200 shadow-sm flex items-center justify-between opacity-80">
                        <div>
                            <p class="font-bold text-sm">Low Contrast (Avoid)</p>
                            <p class="text-xs text-pink-500">Ratio: 2.1:1</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">
                            ✗ Fails AA
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── 8. BROWSE COLORS BY HUE FAMILIES ───────────────────────────── -->
        <section class="w-full mb-20" aria-labelledby="color-families-heading">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold mb-2">
                    <i class="bi bi-grid-fill"></i>
                    <span>Quick Discovery</span>
                </div>
                <h2 id="color-families-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Browse Popular Colors by Hue
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Explore curated named colors across core tonal spectra.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Blues -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-blue-600 dark:text-blue-400">Blues</span>
                    <a href="<?= $base ?>/color/0077be/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-blue-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#0077be;"></span>Ocean Blue</a>
                    <a href="<?= $base ?>/color/126180/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-blue-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#126180;"></span>Blue Sapphire</a>
                    <a href="<?= $base ?>/color/00aeef/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-blue-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#00aeef;"></span>Cyan Blue</a>
                    <a href="<?= $base ?>/color/191970/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-blue-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#191970;"></span>Midnight Blue</a>
                </div>

                <!-- Greens -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Greens</span>
                    <a href="<?= $base ?>/color/228b22/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#228b22;"></span>Forest Green</a>
                    <a href="<?= $base ?>/color/50c878/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#50c878;"></span>Emerald</a>
                    <a href="<?= $base ?>/color/3eb489/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#3eb489;"></span>Mint Green</a>
                    <a href="<?= $base ?>/color/2e8b57/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#2e8b57;"></span>Sea Green</a>
                </div>

                <!-- Purples -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-purple-600 dark:text-purple-400">Purples</span>
                    <a href="<?= $base ?>/color/7851a9/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-purple-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#7851a9;"></span>Royal Purple</a>
                    <a href="<?= $base ?>/color/e6e6fa/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-purple-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#e6e6fa;"></span>Lavender</a>
                    <a href="<?= $base ?>/color/4b0082/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-purple-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#4b0082;"></span>Indigo</a>
                    <a href="<?= $base ?>/color/8e4585/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-purple-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#8e4585;"></span>Plum</a>
                </div>

                <!-- Reds & Pinks -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-rose-600 dark:text-rose-400">Reds &amp; Pinks</span>
                    <a href="<?= $base ?>/color/dc143c/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-rose-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#dc143c;"></span>Crimson</a>
                    <a href="<?= $base ?>/color/ff7f50/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-rose-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ff7f50;"></span>Coral</a>
                    <a href="<?= $base ?>/color/ff007f/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-rose-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ff007f;"></span>Rose Pink</a>
                    <a href="<?= $base ?>/color/e0115f/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-rose-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#e0115f;"></span>Ruby Red</a>
                </div>

                <!-- Yellows & Oranges -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-amber-600 dark:text-amber-400">Warm Hues</span>
                    <a href="<?= $base ?>/color/ffbf00/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-amber-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ffbf00;"></span>Amber Gold</a>
                    <a href="<?= $base ?>/color/f28500/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-amber-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#f28500;"></span>Tangerine</a>
                    <a href="<?= $base ?>/color/ffd700/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-amber-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ffd700;"></span>Gold</a>
                    <a href="<?= $base ?>/color/ffe5b4/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-amber-600 transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ffe5b4;"></span>Peach</a>
                </div>

                <!-- Darks & Neutrals -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">Darks</span>
                    <a href="<?= $base ?>/color/0b0b0b/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#0b0b0b;"></span>Obsidian</a>
                    <a href="<?= $base ?>/color/36454f/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#36454f;"></span>Charcoal</a>
                    <a href="<?= $base ?>/color/708090/" class="flex items-center gap-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#708090;"></span>Slate Grey</a>
                    <a href="<?= $base ?>/dark-color-finder" class="font-bold text-xs text-primary hover:underline mt-1">View All Darks &rarr;</a>
                </div>
            </div>
        </section>

        <!-- ── 9. COMPLETE TOOLS SUITE (REDESIGNED 9-CARD GRID) ────────────── -->
        <section class="w-full mb-20" aria-labelledby="all-tools-heading">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold mb-2">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    <span>Everything You Need</span>
                </div>
                <h2 id="all-tools-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Explore Color Magic Tools
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Free, client-side, privacy-first color instruments for creators, developers, and UI designers.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 md:gap-8 w-full">
                <!-- Card 1: Explore Palettes -->
                <a href="<?= $base ?>/palettes"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-2xl mb-6">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-primary transition-colors">
                            Explore Palettes
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Discover over 800+ curated color palettes. Filter by style (Eco, Pastel, Neon, Vintage), save your favorites, and copy hex codes instantly.
                        </p>
                    </div>
                    <div class="flex gap-1.5 h-6 rounded-lg overflow-hidden w-full border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-violet-600"></div>
                        <div class="flex-1 bg-fuchsia-500"></div>
                        <div class="flex-1 bg-pink-400"></div>
                        <div class="flex-1 bg-amber-300"></div>
                        <div class="flex-1 bg-emerald-400"></div>
                    </div>
                </a>

                <!-- Card 2: Generate Palette -->
                <a href="<?= $base ?>/generate-palette"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary text-2xl mb-6">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-secondary transition-colors">
                            Generate Palette
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Build color harmonies using color theory. Lock favorite swatches, fine-tune hues, and export print-ready palette cards and CSS.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 justify-center py-1">
                        <div class="w-4 h-4 rounded-full bg-violet-500 ring-2 ring-violet-200 dark:ring-violet-900"></div>
                        <div class="w-3 h-0.5 bg-slate-300 dark:bg-slate-700"></div>
                        <div class="w-4 h-4 rounded-full bg-fuchsia-500 ring-2 ring-fuchsia-200 dark:ring-fuchsia-900"></div>
                        <div class="w-3 h-0.5 bg-slate-300 dark:bg-slate-700"></div>
                        <div class="w-4 h-4 rounded-full bg-pink-500 ring-2 ring-pink-200 dark:ring-pink-900"></div>
                    </div>
                </a>

                <!-- Card 3: Find Color -->
                <a href="<?= $base ?>/find-color"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 text-2xl mb-6">
                            <i class="bi bi-eyedropper"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-emerald-500 transition-colors">
                            Hex Color Finder
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Convert any hex code into its verified color name, RGB, HSL, and CMYK. Inspect WCAG contrast accessibility and download swatch cards.
                        </p>
                    </div>
                    <div class="font-mono text-xs text-slate-400 dark:text-slate-600 flex justify-between items-center bg-slate-50 dark:bg-slate-800/40 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-800/80">
                        <span>#00AEEF</span>
                        <i class="bi bi-arrow-right"></i>
                        <span>RGB(0, 174, 239)</span>
                    </div>
                </a>

                <!-- Card 4: CSS Gradients -->
                <a href="<?= $base ?>/gradients"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 text-2xl mb-6">
                            <i class="bi bi-rainbow"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-amber-500 transition-colors">
                            CSS Gradients
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Browse 300+ hand-crafted linear and radial CSS gradients. Preview live on canvas and copy production-ready CSS with a single click.
                        </p>
                    </div>
                    <div class="flex gap-1.5 h-6 rounded-lg overflow-hidden w-full border border-slate-100 dark:border-slate-800">
                        <div class="flex-1" style="background: linear-gradient(135deg, #7F00FF, #E100FF)"></div>
                        <div class="flex-1" style="background: linear-gradient(135deg, #FF512F, #DD2476)"></div>
                        <div class="flex-1" style="background: radial-gradient(circle, #11998E, #38EF7D)"></div>
                    </div>
                </a>

                <!-- Card 5: Palette from Image -->
                <a href="<?= $base ?>/palette-from-image"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-fuchsia-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-fuchsia-500/10 rounded-2xl flex items-center justify-center text-fuchsia-500 text-2xl mb-6">
                            <i class="bi bi-image"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-fuchsia-500 transition-colors">
                            Palette from Image
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Upload any photograph to extract its dominant color harmony. 100% private in-browser canvas processing — your photos never touch a server.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1"><i class="bi bi-upload text-fuchsia-400"></i> Client-Side Extraction</span>
                    </div>
                </a>

                <!-- Card 6: Hex to RGB Converter -->
                <a href="<?= $base ?>/hex-to-rgb"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-cyan-500/10 rounded-2xl flex items-center justify-center text-cyan-500 text-2xl mb-6">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-cyan-500 transition-colors">
                            Hex to RGB &amp; HSL
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Bidirectional real-time conversion between Hexadecimal, RGB, and HSL. Includes conversion formulas and precision CSS values.
                        </p>
                    </div>
                    <div class="font-mono text-xs text-slate-400 dark:text-slate-600 flex justify-between items-center bg-slate-50 dark:bg-slate-800/40 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-800/80">
                        <span>#3B82F6</span>
                        <i class="bi bi-arrow-left-right"></i>
                        <span>59, 130, 246</span>
                    </div>
                </a>

                <!-- Card 7: Dark Color Finder -->
                <a href="<?= $base ?>/dark-color-finder"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 text-2xl mb-6">
                            <i class="bi bi-moon-stars-fill"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-indigo-500 transition-colors">
                            Dark Color Finder
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Discover names and hex codes for deep midnight tones, dark slate, charcoal, and obsidian shades tailored for dark-mode UI themes.
                        </p>
                    </div>
                    <div class="flex gap-1.5 h-6 rounded-lg overflow-hidden w-full border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-slate-900"></div>
                        <div class="flex-1 bg-indigo-950"></div>
                        <div class="flex-1 bg-violet-950"></div>
                        <div class="flex-1 bg-purple-950"></div>
                        <div class="flex-1 bg-rose-950"></div>
                    </div>
                </a>

                <!-- Card 8: Brand Colors -->
                <a href="<?= $base ?>/brand-color-lookup"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 text-2xl mb-6">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors">
                            Brand Color Lookup
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            Inspect official hex codes, design guidelines, and color palettes from iconic technology companies, startups, and global brands.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1"><i class="bi bi-patch-check-fill text-blue-500"></i> Verified Brand Hex Codes</span>
                    </div>
                </a>

                <!-- Card 9: Favorites Collection -->
                <a href="<?= $base ?>/profile"
                    class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 text-2xl mb-6">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-rose-500 transition-colors">
                            Personal Collection
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                            All your liked colors, saved palettes, and starred CSS gradients in one organized workspace. Export anytime in JSON or CSS format.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1"><i class="bi bi-heart-fill text-rose-500"></i> Saved to Local Storage</span>
                    </div>
                </a>
            </div>
        </section>

        <!-- ── 10. COMPREHENSIVE FREQUENTLY ASKED QUESTIONS (FAQ) ─────────── -->
        <section class="w-full mb-20" aria-labelledby="faq-heading">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary text-xs font-bold mb-2">
                    <i class="bi bi-question-circle"></i>
                    <span>Got Questions?</span>
                </div>
                <h2 id="faq-heading" class="text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Frequently Asked Questions
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Everything you need to know about color formats, conversion, and usage.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>What is a hex color code?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        A hexadecimal color code is a 6-character code (using numbers 0–9 and letters A–F) preceded by a hash symbol (#). It represents exact Red, Green, and Blue light values. For example, <code>#00AEEF</code> defines red=0, green=174, blue=239.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>How do I find a color's name from its hex code?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Enter any hex code in our <a href="<?= $base ?>/find-color" class="text-primary font-semibold hover:underline">Color Finder tool</a> or type it in the hero search box above. Our database maps across 6,000+ official Pantone, standard, and design color names.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>What is the difference between RGB, HSL, and CMYK?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        <strong>RGB</strong> is an additive light model for digital screens. <strong>HSL</strong> specifies Hue (0–360°), Saturation %, and Lightness %, making color harmony adjustments intuitive. <strong>CMYK</strong> is a subtractive four-ink model used in physical printing.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>How does the Palette Generator build harmonies?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Our generator uses color theory algorithms (Complementary, Analogous, Triadic, Split-Complementary) combined with luminance balancing to ensure every generated scheme looks professionally curated.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>Can I extract palettes from images without uploading?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Yes! With <a href="<?= $base ?>/palette-from-image" class="text-primary font-semibold hover:underline">Palette from Image</a>, all color extraction occurs directly inside your browser via the HTML5 Canvas API. Your pictures are never uploaded or stored on any server.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>Can I download high-resolution posters of swatches?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Yes. Every palette, gradient, and color page includes a downloadable high-resolution Pantone-style swatch poster graphic (JPEG/PNG) ready for mood boards, client presentations, or Pinterest sharing.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>What is WCAG contrast compliance?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        The Web Content Accessibility Guidelines (WCAG) require minimum luminance contrast between foreground text and backgrounds (4.5:1 for AA normal text, 7:1 for AAA) to ensure legibility for users with vision impairments.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="font-bold text-base mb-2 text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-patch-question text-primary"></i>
                        <span>Is Color Magic free for commercial projects?</span>
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Yes, Color Magic is completely free and open-source. All palettes, gradients, and hex conversions are free to use in personal, client, or commercial websites, mobile apps, and graphic designs without attribution required.
                    </p>
                </div>
            </div>
        </section>

        <!-- ── 11. HIGH-CONVERTING BOTTOM CTA ──────────────────────────────── -->
        <section class="w-full mb-16 bg-gradient-to-r from-secondary via-purple-600 to-primary rounded-3xl p-8 md:p-12 text-center text-white shadow-xl shadow-primary/20 relative overflow-hidden" aria-label="Get Started">
            <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-tight">
                    Start Designing Your Perfect Color Scheme Today
                </h2>
                <p class="text-sm md:text-base text-white/90 leading-relaxed font-medium">
                    Explore 800+ curated palettes, generate unique schemes with color theory, or find the exact name for any hex code. Free forever.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <a href="<?= $base ?>/generate-palette" class="px-7 py-3.5 bg-white hover:bg-slate-100 text-slate-900 font-bold rounded-xl shadow-lg transition">
                        Launch Palette Studio
                    </a>
                    <a href="<?= $base ?>/palettes" class="px-7 py-3.5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold rounded-xl border border-white/30 transition">
                        Browse Palettes (800+)
                    </a>
                </div>
            </div>
        </section>

        <!-- FAQ Schema (JSON-LD) -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "What is a hex color code?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "A hexadecimal color code is a 6-character code (using numbers 0–9 and letters A–F) preceded by a hash symbol (#). It represents exact Red, Green, and Blue light values."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How do I find a color's name from its hex code?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Enter any hex code in our Color Finder tool or hero search box. Our database maps across 6,000+ official Pantone, standard, and design color names."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What is the difference between RGB, HSL, and CMYK?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "RGB is an additive light model for digital screens. HSL specifies Hue, Saturation, and Lightness. CMYK is a subtractive four-ink model used in physical printing."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does the Palette Generator build harmonies?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Our generator uses color theory algorithms (Complementary, Analogous, Triadic, Split-Complementary) combined with luminance balancing to produce harmonious palettes."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I extract palettes from images without uploading?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes! All color extraction occurs directly inside your browser via the HTML5 Canvas API. Your pictures are never uploaded to any server."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I download high-resolution posters of swatches?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Every palette, gradient, and color page includes a downloadable high-resolution Pantone-style swatch poster graphic (JPEG/PNG) ready for presentations."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What is WCAG contrast compliance?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "WCAG guidelines require minimum luminance contrast between text and backgrounds (4.5:1 for AA normal text, 7:1 for AAA) to ensure legibility."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is Color Magic free for commercial projects?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, Color Magic is completely free and open-source. All palettes and conversions can be used in commercial projects without attribution."
                    }
                }
            ]
        }
        </script>

    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "home";

        // ── 1. Toast Notification Helper ──────────────────────────────────
        function showToast(msg) {
            var toast = document.getElementById('cmToast');
            var msgEl = document.getElementById('cmToastMessage');
            if (!toast || !msgEl) return;
            msgEl.textContent = msg;
            toast.classList.remove('hidden', 'opacity-0', 'translate-y-3');
            toast.classList.add('flex', 'opacity-100', 'translate-y-0');
            setTimeout(function() {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'translate-y-3');
                setTimeout(function() { toast.classList.add('hidden'); }, 250);
            }, 1800);
        }

        function copyHexValue(hex) {
            navigator.clipboard.writeText(hex).then(function() {
                showToast("Copied " + hex + " to clipboard!");
            }).catch(function() {
                showToast("Copied " + hex);
            });
        }

        function copyGradientCss(css, btn) {
            navigator.clipboard.writeText(css).then(function() {
                if (btn) {
                    var oldHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check2 text-emerald-500 text-base"></i><span>Copied!</span>';
                    setTimeout(function() { btn.innerHTML = oldHtml; }, 1200);
                }
                showToast("CSS copied to clipboard!");
            });
        }

        // ── 2. Hero Quick Color Search ─────────────────────────────────────
        var heroColorInput = document.getElementById('heroColorInput');
        var heroColorPicker = document.getElementById('heroColorPicker');
        var heroColorPreview = document.getElementById('heroColorPreview');
        var heroColorForm = document.getElementById('heroColorForm');

        function setHeroColor(hex) {
            var clean = hex.replace('#', '').toUpperCase();
            if (heroColorInput) heroColorInput.value = clean;
            if (heroColorPicker) heroColorPicker.value = '#' + clean;
            if (heroColorPreview) heroColorPreview.style.backgroundColor = '#' + clean;
        }

        if (heroColorInput && heroColorPicker && heroColorPreview) {
            heroColorInput.addEventListener('input', function() {
                var val = this.value.replace(/[^0-9a-fA-F]/g, '').slice(0, 6);
                this.value = val.toUpperCase();
                if (val.length === 3 || val.length === 6) {
                    var full = val.length === 3 ? (val[0]+val[0]+val[1]+val[1]+val[2]+val[2]) : val;
                    heroColorPreview.style.backgroundColor = '#' + full;
                    heroColorPicker.value = '#' + full;
                }
            });

            heroColorPicker.addEventListener('input', function() {
                var hex = this.value.replace('#', '').toUpperCase();
                heroColorInput.value = hex;
                heroColorPreview.style.backgroundColor = '#' + hex;
            });

            if (heroColorForm) {
                heroColorForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var val = (heroColorInput.value || '').trim().replace(/[^0-9a-fA-F]/g, '');
                    if (!val) val = '00AEEF';
                    if (val.length === 3) val = val[0]+val[0]+val[1]+val[1]+val[2]+val[2];
                    window.location.href = '<?= $base ?>/color/' + val.toLowerCase() + '/';
                });
            }
        }

        // ── 3. Live Interactive Spacebar Palette Generator ─────────────────
        (function() {
            var swatches = [
                { hex: '#6366F1', locked: false },
                { hex: '#8B5CF6', locked: false },
                { hex: '#D946EF', locked: false },
                { hex: '#EC4899', locked: false },
                { hex: '#F43F5E', locked: false }
            ];

            var container = document.getElementById('homePaletteContainer');
            var shuffleBtn = document.getElementById('shuffleHomePaletteBtn');

            function hslToHex(h, s, l) {
                s /= 100; l /= 100;
                var c = (1 - Math.abs(2 * l - 1)) * s;
                var x = c * (1 - Math.abs((h / 60) % 2 - 1));
                var m = l - c / 2;
                var r = 0, g = 0, b = 0;
                if (h < 60) { r = c; g = x; b = 0; }
                else if (h < 120) { r = x; g = c; b = 0; }
                else if (h < 180) { r = 0; g = c; b = x; }
                else if (h < 240) { r = 0; g = x; b = c; }
                else if (h < 300) { r = x; g = 0; b = c; }
                else { r = c; g = 0; b = x; }
                var toHex = function(val) {
                    var n = Math.round((val + m) * 255).toString(16);
                    return n.length === 1 ? '0' + n : n;
                };
                return ('#' + toHex(r) + toHex(g) + toHex(b)).toUpperCase();
            }

            function isLight(hex) {
                var c = hex.replace('#', '');
                var r = parseInt(c.substring(0, 2), 16);
                var g = parseInt(c.substring(2, 4), 16);
                var b = parseInt(c.substring(4, 6), 16);
                return (r * 299 + g * 587 + b * 114) / 1000 > 150;
            }

            function generateHarmonies() {
                var baseHue = Math.floor(Math.random() * 360);
                var sat = Math.floor(65 + Math.random() * 25);
                var hues = [
                    baseHue,
                    (baseHue + 30) % 360,
                    (baseHue + 60) % 360,
                    (baseHue + 180) % 360,
                    (baseHue + 210) % 360
                ];

                swatches.forEach(function(s, idx) {
                    if (!s.locked) {
                        var lit = (idx === 0) ? 35 : ((idx === 4) ? 82 : (45 + Math.floor(Math.random() * 20)));
                        s.hex = hslToHex(hues[idx], sat, lit);
                    }
                });
                renderSwatches();
            }

            function renderSwatches() {
                if (!container) return;
                container.innerHTML = '';
                swatches.forEach(function(s, idx) {
                    var light = isLight(s.hex);
                    var textClass = light ? 'text-slate-900' : 'text-white';
                    var bgBtnClass = light ? 'bg-black/10 hover:bg-black/20' : 'bg-white/20 hover:bg-white/30';

                    var col = document.createElement('div');
                    col.className = 'relative flex-1 min-h-[90px] sm:min-h-full rounded-xl flex flex-col justify-between p-3.5 transition-all select-none group';
                    col.style.backgroundColor = s.hex;

                    col.innerHTML = [
                        '<div class="flex items-center justify-between w-full ' + textClass + '">',
                        '  <button type="button" class="lock-btn p-1.5 rounded-lg ' + bgBtnClass + ' transition" title="' + (s.locked ? 'Unlock' : 'Lock') + '">',
                        '    <i class="bi ' + (s.locked ? 'bi-lock-fill' : 'bi-unlock') + ' text-xs"></i>',
                        '  </button>',
                        '  <a href="<?= $base ?>/color/' + s.hex.replace('#','').toLowerCase() + '/" class="p-1.5 rounded-lg ' + bgBtnClass + ' opacity-0 group-hover:opacity-100 transition" title="Inspect color details">',
                        '    <i class="bi bi-box-arrow-up-right text-xs"></i>',
                        '  </a>',
                        '</div>',
                        '<div class="flex items-center justify-between w-full ' + textClass + '">',
                        '  <button type="button" class="copy-btn font-mono font-bold text-sm tracking-wider hover:scale-105 transition" title="Click to copy">',
                        '    ' + s.hex,
                        '  </button>',
                        '  <i class="bi bi-copy opacity-0 group-hover:opacity-80 transition text-xs"></i>',
                        '</div>'
                    ].join('');

                    col.querySelector('.lock-btn').addEventListener('click', function(e) {
                        e.stopPropagation();
                        s.locked = !s.locked;
                        renderSwatches();
                    });

                    col.querySelector('.copy-btn').addEventListener('click', function(e) {
                        e.stopPropagation();
                        copyHexValue(s.hex);
                    });

                    col.addEventListener('click', function() {
                        copyHexValue(s.hex);
                    });

                    container.appendChild(col);
                });
            }

            if (shuffleBtn) {
                shuffleBtn.addEventListener('click', generateHarmonies);
            }

            // Spacebar trigger (when not in an input or textarea)
            document.addEventListener('keydown', function(e) {
                if (e.code === 'Space' || e.key === ' ') {
                    var tag = (document.activeElement && document.activeElement.tagName) ? document.activeElement.tagName.toLowerCase() : '';
                    if (tag !== 'input' && tag !== 'textarea') {
                        e.preventDefault();
                        generateHarmonies();
                    }
                }
            });

            renderSwatches();
        })();

        // Mobile menu toggle
        (function () {
            var openBtn = document.getElementById('mobileMenuBtn');
            var closeBtn = document.getElementById('closeMobileMenuBtn');
            var overlay = document.getElementById('mobileMenuOverlay');
            if (openBtn && closeBtn && overlay) {
                openBtn.addEventListener('click', function () { overlay.style.display = 'flex'; });
                closeBtn.addEventListener('click', function () { overlay.style.display = 'none'; });
            }
        })();
    </script>
</body>

</html>
