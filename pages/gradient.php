<?php include '../components/config.php';
/** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <base href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/">
    <title>CSS Gradient | Color Magic</title>
    <meta name="description"
        content="Explore this beautiful CSS gradient with copy-ready CSS, hex colors, and related gradients. Perfect for web design projects." />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/logo.png" fetchpriority="high" />

    <!-- Resource Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />

    <!-- Preload Fonts & Non-Blocking CSS -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900&display=swap" /></noscript>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>
    <style>
        /* ── Critical Layout Stability (Zero CLS) ── */
        header {
            min-height: 64px;
        }
        .gradient-hero {
            transition: transform 0.4s ease;
        }

        /* Swatch hover interactions (same as Explore Palettes) */
        .swatch {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .swatch-hex {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .swatch:hover .swatch-hex {
            opacity: 1;
            display: block !important;
        }

        @keyframes swatchBtnIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        .swatch-icon-btn {
            opacity: 0;
            transform: translateY(-6px);
        }

        .swatch:hover .swatch-icon-btn { animation: swatchBtnIn 0.18s ease forwards; }
        .swatch:hover .swatch-btn-1    { animation-delay: 0s;    }
        .swatch:hover .swatch-btn-2    { animation-delay: 0.06s; }
        .swatch:hover .swatch-btn-3    { animation-delay: 0.12s; }

        .swatch-hex.copied-state {
            opacity: 1 !important;
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            font-weight: 700;
        }

        .gradient-hero:hover {
            transform: scale(1.02);
        }

        .copy-flash {
            animation: copyFlash 0.4s ease;
        }

        @keyframes copyFlash {
            0% {
                background-color: rgba(34, 197, 94, 0.2);
            }

            100% {
                background-color: transparent;
            }
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

    <main class="w-full max-w-7xl mx-auto pt-24 p-5 md:p-8 flex flex-col gap-8">
        <!-- Top Navigation: Breadcrumb -->
        <div>
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="<?= $base ?>/" class="hover:text-primary transition-colors">Home</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <a href="<?= $base ?>/gradients" class="hover:text-primary transition-colors">Gradients</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span id="gradientBreadcrumb" class="text-slate-900 dark:text-white font-medium">Gradient Details</span>
            </nav>
        </div>

        <!-- Skeleton Loader (visible until loaded) -->
        <div id="gradientSkeleton" class="flex flex-col gap-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800 animate-pulse">
                <div class="h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] bg-slate-200/80 dark:bg-slate-800 rounded-2xl"></div>
                <div class="flex flex-col gap-5 justify-between">
                    <div class="space-y-3">
                        <div class="h-9 w-3/4 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-5 w-1/2 bg-slate-200/80 dark:bg-slate-800 rounded-lg"></div>
                        <div class="h-4 w-full bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="h-11 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-11 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 border border-slate-200/60 dark:border-slate-800 animate-pulse">
                <div class="h-6 w-36 bg-slate-200/80 dark:bg-slate-800 rounded mb-4"></div>
                <div class="h-28 bg-slate-100 dark:bg-slate-800 rounded-xl"></div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 border border-slate-200/60 dark:border-slate-800 animate-pulse">
                <div class="h-6 w-40 bg-slate-200/80 dark:bg-slate-800 rounded mb-4"></div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="h-44 bg-slate-100 dark:bg-slate-800 rounded-2xl"></div>
                    <div class="h-44 bg-slate-100 dark:bg-slate-800 rounded-2xl"></div>
                    <div class="h-44 bg-slate-100 dark:bg-slate-800 rounded-2xl"></div>
                </div>
            </div>
        </div>

        <!-- Gradient detail (hidden until loaded) -->
        <section id="gradientDetail" class="hidden flex flex-col gap-8">

            <!-- Hero: Preview + Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
                <!-- Large gradient preview -->
                <div class="h-full">
                    <div id="heroGradientPreview"
                        class="gradient-hero h-72 md:h-80 lg:h-full min-h-[300px] md:min-h-[380px] rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30 relative flex items-end cursor-pointer group">
                        
                        <!-- Overlay Content (matches color page style) -->
                        <div id="heroOverlayText" class="relative z-10 select-none p-6 md:p-8 flex flex-col justify-end w-full pointer-events-none transition-colors duration-200" style="color: #ffffff; text-shadow: 0 2px 10px rgba(0,0,0,0.45);">
                            <p class="text-xs sm:text-sm font-bold uppercase tracking-[0.22em] opacity-85 mb-1.5">COLOR MAGIC</p>
                            <h2 id="heroGradientName" class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight mb-2"></h2>
                            <div id="heroGradientColors" class="flex flex-wrap items-center gap-2 text-xs sm:text-sm font-semibold tracking-tight opacity-95"></div>
                        </div>

                        <!-- Hover copy hint -->
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                            <span class="px-3 py-1.5 bg-black/40 backdrop-blur-md text-white rounded-lg text-xs font-medium flex items-center gap-1.5 shadow-sm">
                                <i class="bi bi-clipboard"></i> Click to copy CSS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info panel: Consistent Order -->
                <div class="flex flex-col gap-5 justify-between">
                    <!-- 1. Heading, 2. Subheading, 3. Label / Description text -->
                    <div>
                        <h1 id="gradientName" class="text-3xl md:text-4xl font-bold tracking-tight"></h1>
                        <h2 id="gradientSubheading" class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"></h2>
                        <p id="gradientDescription" class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            CSS gradient with copy-ready code, color stops breakdown, and related palettes.
                        </p>
                    </div>

                    <!-- 2. Details: Info chips -->
                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">Type</p>
                            <p id="gradientType" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">Colors</p>
                            <p id="gradientColorCount" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400" id="angleShapeLabel">Angle</p>
                            <p id="gradientAngleShape" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"></p>
                        </div>
                    </div>

                    <!-- 3. Color swatches breakdown (1-click copy) -->
                    <div>
                        <div id="gradientColorList" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
                        <div class="h-4 mt-1">
                            <p id="copyFeedback"
                                class="copy-feedback opacity-0 -translate-y-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 transition-all">
                                Copied to clipboard</p>
                        </div>
                    </div>

                    <!-- 4. Primary Action Buttons -->
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

            <!-- CSS Code Block -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">CSS Code</h2>
                <div class="relative">
                    <pre id="cssCodeBlock" class="bg-slate-50 dark:bg-slate-800 rounded-xl p-5 font-mono text-sm overflow-x-auto border border-slate-200 dark:border-slate-700 leading-relaxed"></pre>
                    <button id="copyCssBlockBtn"
                        class="absolute top-3 right-3 px-3 py-1.5 bg-white dark:bg-slate-700 hover:bg-primary hover:text-white text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all shadow-sm border border-slate-200 dark:border-slate-600">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
            </section>

            <!-- Color Information -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Color Information</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    RGB, HSL, and brightness values for each color in this gradient
                </p>
                <div id="colorInfoGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
            </section>

            <!-- Related Colors -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Colors</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Complementary and analogous colors derived from this gradient's palette
                </p>
                <div id="relatedColorsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
            </section>

            <!-- Related Gradients -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Gradients</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    More gradients in the same style or type
                </p>
                <div id="relatedGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
            </section>

            <!-- Related Palettes -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
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

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_COLOR_BASE    = '<?= $base ?>/color/';
        window.CM_PALETTE_BASE  = '<?= $base ?>/palette/';
        window.CM_GRADIENT_BASE = '<?= $base ?>/gradient/';
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=3.1" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=3.1" defer></script>
    <script src="<?= $base ?>/assets/js/image-export.js?v=3.1" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=3.1" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=3.1" defer></script>
    <script src="<?= $base ?>/assets/js/gradient-page.js?v=3.1" defer></script>
</body>

</html>