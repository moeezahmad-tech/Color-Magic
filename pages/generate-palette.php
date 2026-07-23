<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Color Magic | Generate Color Palette</title>
    <meta name="description"
        content="Generate professional 5-color palettes from any base color using color theory harmony rules — Monochromatic, Complementary, Triadic, Tetradic and Analogous." />
    <meta name="keywords"
        content="color palette generator, color theory, hex code generator, designer tools, TechKreative" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/generate-palette" />
    <meta property="og:title" content="Color Magic | Generate Color Palette" />
    <meta property="og:description" content="Create professional 5-color palettes using color theory harmony rules." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/generate-palette" />
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
            transition: transform 0.22s ease;
        }

        .sb-btn:hover .sb-icon {
            transform: scale(1.12);
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

        .sb-btn.sb-active:hover {
            box-shadow: 0 12px 28px -6px rgba(124, 58, 237, 0.45);
            transform: translateY(-1px);
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scheme-btn.scheme-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            box-shadow: 0 4px 14px -4px rgba(236, 72, 153, 0.5);
        }

        input[type="color"] {
            -webkit-appearance: none;
            appearance: none;
            border: none;
            cursor: pointer;
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 8px;
        }

        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
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
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-house-door"></i></span>
                <div>
                    <span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div>
                    <span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span>
                </div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div>
                    <span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS
                        gradients</span>
                </div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div>
                    <span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span>
                </div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-active w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div>
                    <span class="block font-bold">Generate Palette</span><span class="text-xs opacity-75">Create color
                        schemes</span>
                </div>
            </a>
            <a href="<?= $base ?>/favorites" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div>
                    <span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors,
                        palettes &amp; gradients</span>
                </div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div>
                    <span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span>
                </div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-6 md:px-8 flex flex-col gap-6">
        <!-- Hero -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-secondary/10 via-pink-50 to-primary/10 dark:from-secondary/20 dark:via-slate-900 dark:to-primary/20 p-7 md:p-10 border border-pink-100 dark:border-slate-800">
            <div class="hero-blob w-64 h-64 bg-secondary/20 top-[-50px] left-[-50px]"></div>
            <div class="hero-blob w-56 h-56 bg-primary/20 bottom-[-35px] right-[-35px]"></div>
            <div class="relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-secondary mb-3 border border-secondary/20">
                    <i class="bi bi-stars"></i> Color Theory Powered
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">
                    Generate Color Palette
                </h1>
                <p class="text-slate-600 dark:text-slate-300 text-base max-w-xl">
                    Create stunning 5-color palettes from any base color using proven
                    color theory harmony rules — instantly.
                </p>
            </div>
        </div>

        <!-- Generator layout -->
        <div class="flex flex-col gap-8">
            <!-- Controls panel -->
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 flex flex-col gap-6">
                <div class="flex flex-wrap items-center gap-y-2 gap-x-4">
                    <label for="paletteColorInput" class="text-sm font-bold shrink-0 min-w-[75px]">Base Color</label>
                    <div class="flex gap-2.5 items-center flex-1 w-full sm:w-auto min-w-[200px]">
                        <input id="paletteColorPicker" type="color" value="#ec4899"
                            class="w-10 h-10 rounded-xl cursor-pointer border-2 border-slate-200 dark:border-slate-700 overflow-hidden shrink-0"
                            aria-label="Pick a color" />
                        <div class="flex-1 relative min-w-0">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">#</span>
                            <input id="paletteColorInput" type="text" placeholder="EC4899" maxlength="7"
                                class="w-full pl-7 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-primary rounded-xl text-sm outline-none transition-all placeholder:text-slate-400 font-mono uppercase"
                                aria-label="Enter hex color code" />
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-3">Color Harmony</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            class="scheme-btn scheme-active px-3 py-2.5 rounded-xl font-semibold text-xs transition-all hover:scale-105 active:scale-95"
                            data-scheme="mono">
                            <i class="bi bi-circle-half block text-lg mb-1"></i>Mono
                        </button>
                        <button
                            class="scheme-btn px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all hover:scale-105 active:scale-95"
                            data-scheme="contrast">
                            <i class="bi bi-arrow-left-right block text-lg mb-1"></i>Contrast
                        </button>
                        <button
                            class="scheme-btn px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all hover:scale-105 active:scale-95"
                            data-scheme="triade">
                            <i class="bi bi-triangle block text-lg mb-1"></i>Triadic
                        </button>
                        <button
                            class="scheme-btn px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all hover:scale-105 active:scale-95"
                            data-scheme="tetrade">
                            <i class="bi bi-square block text-lg mb-1"></i>Tetradic
                        </button>
                        <button
                            class="scheme-btn px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-all hover:scale-105 active:scale-95"
                            data-scheme="analogic">
                            <i class="bi bi-layers block text-lg mb-1"></i>Analogous
                        </button>
                    </div>
                </div>

                <button id="generatePaletteBtn"
                    class="w-full px-6 py-4 bg-gradient-to-r from-secondary to-primary hover:from-secondary/90 hover:to-primary/90 text-white font-bold rounded-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/25">
                    <i class="bi bi-stars text-xl"></i><span>Generate Palette</span>
                </button>
                <p id="paletteErrorMessage" class="text-red-500 text-sm -mt-4 hidden" role="alert"></p>

                <div id="schemeInfoCard"
                    class="bg-gradient-to-br from-slate-50 to-pink-50/30 dark:from-slate-800 dark:to-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <p id="schemeInfoText" class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        <strong class="text-slate-800 dark:text-slate-200">Monochromatic:</strong>
                        Variations of the same hue with different lightness and saturation
                        for a cohesive look.
                    </p>
                </div>
            </div>

            <!-- Results panel -->
            <div class="flex flex-col gap-6">
                <div id="palettePlaceholder"
                    class="flex flex-col items-center justify-center min-h-[400px] text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-900/30 p-8 text-center">
                    <div
                        class="w-20 h-20 rounded-2xl bg-gradient-to-br from-secondary/20 to-primary/20 flex items-center justify-center mb-5">
                        <i class="bi bi-palette text-4xl text-primary/50"></i>
                    </div>
                    <p class="text-lg font-semibold text-slate-500 dark:text-slate-400 mb-1">
                        Your palette will appear here
                    </p>
                    <p class="text-sm text-slate-400 dark:text-slate-500">
                        Pick a base color and harmony rule, then hit
                        <strong>Generate Palette</strong>
                    </p>
                </div>
                <div id="paletteResults" class="flex flex-col gap-8 hidden"></div>
            </div>
        </div>

        <!-- Tips -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 flex gap-4 items-start">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-secondary/10 shrink-0">
                    <i class="bi bi-circle-half text-xl text-secondary"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm mb-1">Monochromatic</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        One hue, multiple tones. Safe, elegant, and always cohesive.
                    </p>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 flex gap-4 items-start">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary/10 shrink-0">
                    <i class="bi bi-arrow-left-right text-xl text-primary"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm mb-1">Complementary</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Opposite hues create high-contrast, vibrant combinations.
                    </p>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 flex gap-4 items-start">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-emerald-500/10 shrink-0">
                    <i class="bi bi-triangle text-xl text-emerald-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm mb-1">Triadic / Analogous</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Evenly spaced or neighbouring hues for dynamic, balanced palettes.
                    </p>
                </div>
            </div>
        </div>

    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "generate";
    </script>
    <script src="<?= $base ?>/assets/js/generate-palette-page.js?v=1.0" defer></script>
</body>

</html>