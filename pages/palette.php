<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <base href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>/">
    <title>Color Palette | Color Magic</title>
    <meta name="description"
        content="Explore this curated color palette with copy-ready HEX, RGB, and CSS values. Perfect for design projects." />
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
                <span id="paletteBreadcrumb" class="text-slate-900 dark:text-white font-medium">Palette Details</span>
            </nav>
        </div>

        <section id="paletteDetail" class="hidden flex flex-col gap-8">
            <div id="paletteTopSection"
                class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
                <div class="h-full">
                    <div id="heroPaletteStrips"
                        class="grid h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30">
                    </div>
                </div>

                <div class="flex flex-col gap-5">
                    <!-- 1. Heading, 2. Subheading, 3. Label / Description text -->
                    <div>
                        <h1 id="paletteName" class="text-3xl md:text-4xl font-bold tracking-tight"></h1>
                        <h2 id="paletteSubheading" class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"></h2>
                        <p id="paletteDescription" class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Complete color palette with copy-ready HEX, RGB, and CSS variable values.
                        </p>
                    </div>

                    <!-- 4. Action Buttons (2x2 on desktop, 1x4 on mobile) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button id="copyAllBtn"
                            class="w-full px-4 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                            <i class="bi bi-clipboard"></i>
                            <span>Copy All Colors</span>
                        </button>
                        <button id="copyCssVarsBtn"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="bi bi-code-slash"></i>
                            <span>Copy CSS Variables</span>
                        </button>
                        <button id="downloadPalettePngBtn"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="bi bi-download"></i>
                            <span>Download PNG</span>
                        </button>
                        <button id="favPaletteBtn"
                            class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i class="bi bi-heart"></i>
                            <span>Favorite</span>
                        </button>
                    </div>

                    <!-- 5. Details / Metric Chips -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">
                                Type
                            </p>
                            <p id="paletteType" class="font-bold mt-1"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">
                                Total Colors
                            </p>
                            <p id="paletteCount" class="font-bold mt-1"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">
                                Format
                            </p>
                            <p class="font-bold mt-1">HEX</p>
                        </div>
                    </div>

                    <!-- Color Swatches list with inline copy -->
                    <div id="detailColorList" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1"></div>
                </div>
            </div>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="contrastCardA" class="rounded-2xl p-6 min-h-[180px] flex flex-col justify-between"></div>
                <div id="contrastCardB" class="rounded-2xl p-6 min-h-[180px] flex flex-col justify-between"></div>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-3">Palette Story</h2>
                <p id="darkColorParagraph" class="text-base md:text-lg leading-relaxed font-medium"></p>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Color Information</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Detailed breakdown of each color's RGB, HSL, and brightness values
                </p>
                <div id="colorInfoGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Brightness Levels</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Visual representation of each color's lightness on a spectrum
                </p>
                <div id="brightnessChart" class="space-y-2"></div>
            </section>

            <!-- Export: HEX Array -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">HEX Array</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Standard hex color array for JavaScript and CSS</p>
                    </div>
                    <button id="copyHexArrayBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="hexArrayCode"></pre>
                </div>
            </section>

            <!-- Export: CSS Variables -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">CSS Custom Properties</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">CSS variables for use in stylesheets with :root declaration</p>
                    </div>
                    <button id="copyCssVarsExportBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="cssVarsCode"></pre>
                </div>
            </section>

            <!-- Export: JSON -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">JSON</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Structured JSON format for APIs, config files, and data exchange</p>
                    </div>
                    <button id="copyJsonBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="jsonCode"></pre>
                </div>
            </section>

            <!-- Export: SCSS Variables -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">SCSS Variables</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Sass/SCSS variable declarations for preprocessor workflows</p>
                    </div>
                    <button id="copyScssBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="scssCode"></pre>
                </div>
            </section>

            <!-- Export: Tailwind Config -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Tailwind CSS Config</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Extend your tailwind.config.js theme with custom palette colors</p>
                    </div>
                    <button id="copyTailwindBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="tailwindCode"></pre>
                </div>
            </section>

            <!-- Export: RGB Array -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">RGB Array</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">RGB functional notation for CSS and canvas rendering</p>
                    </div>
                    <button id="copyRgbArrayBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="rgbArrayCode"></pre>
                </div>
            </section>

            <!-- Export: HSL Array -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">HSL Array</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">HSL notation for dynamic color manipulation in CSS</p>
                    </div>
                    <button id="copyHslArrayBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="hslArrayCode"></pre>
                </div>
            </section>

            <!-- Export: Android XML -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Android XML</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Android resource file format for colors.xml</p>
                    </div>
                    <button id="copyAndroidBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="androidCode"></pre>
                </div>
            </section>

            <!-- Export: Swift UIColor -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Swift UIColor</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Swift extension for iOS/macOS UIColor declarations</p>
                    </div>
                    <button id="copySwiftBtn"
                        class="shrink-0 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64">
                    <pre id="swiftCode"></pre>
                </div>
            </section>

            <!-- Related Colors -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Colors</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Complementary, analogous, and triadic colors derived from this palette
                </p>
                <div id="relatedColorsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
            </section>

            <!-- Related Palettes -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Palettes</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Palettes with similar colors to this one
                </p>
                <div id="relatedPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8"></div>
            </section>

            <!-- Related Gradients -->
            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Related Gradients</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Gradients that use colors from this palette
                </p>
                <div id="relatedGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
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
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=2.1" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=2.1" defer></script>
    <script src="<?= $base ?>/assets/js/image-export.js?v=2.1" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=2.1" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=2.1" defer></script>
    <script src="<?= $base ?>/assets/js/palette-page.js?v=2.1" defer></script>
</body>

</html>