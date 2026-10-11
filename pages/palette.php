<?php
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
include '../components/config.php'; /** @var string $base */ ?>
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

    <script>
        // Force clear any stale service workers or browser cache storage
        try {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function (r) {
                    r.forEach(function (w) { w.unregister(); });
                });
            }
            if ('caches' in window) {
                caches.keys().then(function (k) {
                    k.forEach(function (n) { caches.delete(n); });
                });
            }
        } catch (_) {}
    </script>

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

        <!-- Skeleton Loader (visible until API loads) -->
        <div id="paletteSkeleton" class="flex flex-col gap-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800 animate-pulse">
                <div class="h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] bg-slate-200/80 dark:bg-slate-800 rounded-2xl"></div>
                <div class="flex flex-col gap-5 justify-between">
                    <div class="space-y-3">
                        <div class="h-9 w-3/4 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-5 w-1/2 bg-slate-200/80 dark:bg-slate-800 rounded-lg"></div>
                        <div class="h-4 w-full bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                    </div>
                    <!-- 2. Details: Metric Chips -->
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-16 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                    <!-- 3. Swatches list -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="h-12 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-12 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-12 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-12 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                    <!-- 4. Action buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="h-12 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                        <div class="h-12 bg-slate-200/80 dark:bg-slate-800 rounded-xl"></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-pulse">
                <div class="h-44 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 p-6"></div>
                <div class="h-44 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/60 dark:border-slate-800 p-6"></div>
            </div>
        </div>

        <section id="paletteDetail" class="hidden flex flex-col gap-8">
            <div id="paletteTopSection"
                class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
                <div class="h-full">
                    <div id="heroPaletteStrips"
                        class="grid h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30">
                    </div>
                </div>

                <!-- Info panel: Consistent Hierarchy -->
                <div class="flex flex-col gap-5 justify-between">
                    <!-- 1. Heading, 2. Subheading, 3. Label / Description text -->
                    <div>
                        <h1 id="paletteName" class="text-3xl md:text-4xl font-bold tracking-tight"></h1>
                        <h2 id="paletteSubheading" class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"></h2>
                        <p id="paletteDescription" class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Complete color palette with copy-ready HEX, RGB, and CSS variable values.
                        </p>
                    </div>

                    <!-- 2. Details: Metric Chips -->
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">Style</p>
                            <p id="paletteType" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0">
                            <p class="text-[11px] uppercase tracking-wider text-slate-400">Colors</p>
                            <p id="paletteCount" class="font-bold text-xs sm:text-sm mt-1 whitespace-nowrap tracking-tight"></p>
                        </div>
                    </div>

                    <!-- 3. Color Swatches list (1-click interactive copy chips) -->
                    <div>
                        <div id="detailColorList" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
                        <div class="h-4 mt-1">
                            <p id="copyFeedback"
                                class="copy-feedback opacity-0 -translate-y-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 transition-all">
                                Copied to clipboard</p>
                        </div>
                    </div>

                    <!-- 4. Primary Action Buttons -->
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

            <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="contrastCardA" class="rounded-2xl p-6 min-h-[180px] flex flex-col justify-between"></div>
                <div id="contrastCardB" class="rounded-2xl p-6 min-h-[180px] flex flex-col justify-between"></div>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-3">Palette Story</h2>
                <p id="darkColorParagraph" class="text-base md:text-lg leading-relaxed font-medium"></p>
            </section>


            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Brightness Levels</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Visual representation of each color's lightness on a spectrum
                </p>
                <div id="brightnessChart" class="space-y-2"></div>
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