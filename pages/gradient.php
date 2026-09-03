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

        <!-- Gradient detail (hidden until loaded) -->
        <section id="gradientDetail" class="hidden flex flex-col gap-8">

            <!-- Hero: Preview + Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
                <!-- Large gradient preview -->
                <div class="h-full">
                    <div id="heroGradientPreview"
                        class="gradient-hero h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30 relative cursor-pointer group">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 backdrop-blur-[2px]">
                            <span class="px-4 py-2 bg-white/90 dark:bg-slate-900/90 rounded-xl text-sm font-semibold shadow-lg flex items-center gap-2">
                                <i class="bi bi-clipboard"></i> Click to copy CSS
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info panel: Consistent Order -->
                <div class="flex flex-col gap-5">
                    <!-- 1. Heading, 2. Subheading, 3. Label / Description text -->
                    <div>
                        <h1 id="gradientName" class="text-3xl md:text-4xl font-bold tracking-tight"></h1>
                        <h2 id="gradientSubheading" class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"></h2>
                        <p id="gradientDescription" class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            CSS gradient with copy-ready code, color stops breakdown, and related palettes.
                        </p>
                    </div>

                    <!-- 4. Action Buttons (2x2 on desktop, 1x4 on mobile) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button id="copyCssBtn"
                            class="w-full px-4 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                            <i class="bi bi-clipboard"></i>
                            <span>Copy CSS</span>
                        </button>
                        <button id="copyGradientColorsBtn"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="bi bi-palette"></i>
                            <span>Copy Colors</span>
                        </button>
                        <button id="downloadGradientPngBtn"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="bi bi-download"></i>
                            <span>Download PNG</span>
                        </button>
                        <button id="favGradientBtn"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="bi bi-heart"></i>
                            <span>Favorite</span>
                        </button>
                    </div>

                    <!-- 5. Details: Info chips -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">Type</p>
                            <p id="gradientType" class="font-bold mt-1"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">Colors</p>
                            <p id="gradientColorCount" class="font-bold mt-1"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400" id="angleShapeLabel">Angle</p>
                            <p id="gradientAngleShape" class="font-bold mt-1"></p>
                        </div>
                    </div>

                    <!-- Color swatches list -->
                    <div id="gradientColorList" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1"></div>
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
    <script src="<?= $base ?>/assets/js/utils.js?v=3.0" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=3.0" defer></script>
    <script src="<?= $base ?>/assets/js/image-export.js?v=3.0" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=3.0" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=3.0" defer></script>
    <script src="<?= $base ?>/assets/js/gradient-page.js?v=3.0" defer></script>
</body>

</html>