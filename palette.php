<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script>
        // Set base href so relative assets resolve correctly under /palette/slug/ URLs
        (function () {
            var path = window.location.pathname;
            var idx = path.indexOf("/palette/");
            var base =
                idx !== -1
                    ? path.substring(0, idx + 1)
                    : path.substring(0, path.lastIndexOf("/") + 1);
            document.write('<base href="' + base + '">');
        })();
    </script>
    <title>Color Palette | Color Magic</title>
    <meta name="description"
        content="Explore this curated color palette with copy-ready HEX, RGB, and CSS values. Perfect for design projects." />
    <link rel="icon" type="image/png" href="assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script id="tailwind-config" src="assets/js/tailwind-config.js"></script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include 'components/navbar.php'; ?>


    <main class="w-full max-w-7xl mx-auto pt-24 p-5 md:p-8 flex flex-col gap-8">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="palettes.php"
                class="inline-flex items-center gap-2 text-primary font-semibold hover:opacity-90 transition-opacity">
                <i class="bi bi-arrow-left"></i>
                Back to Explore
            </a>
            <p id="paletteIdText" class="text-xs text-slate-500 dark:text-slate-400 font-mono"></p>
        </div>

        <section id="paletteDetail" class="hidden flex flex-col gap-8">
            <div id="paletteTopSection"
                class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-white dark:bg-slate-900 rounded-3xl p-5 md:p-7 shadow-sm">
                <div>
                    <div id="heroPaletteStrips"
                        class="grid h-full rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30">
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <div>
                        <h1 id="paletteName" class="text-3xl md:text-4xl font-bold tracking-tight"></h1>
                        <p id="paletteMeta" class="text-sm text-slate-500 dark:text-slate-400 mt-2"></p>
                    </div>

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

                    <div class="flex items-center gap-3">
                        <button id="copyAllBtn"
                            class="px-4 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-colors">
                            <i class="bi bi-clipboard me-1"></i>
                            Copy All Colors
                        </button>
                        <button id="copyCssVarsBtn"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors">
                            <i class="bi bi-code-slash me-1"></i>
                            Copy CSS Variables
                        </button>
                    </div>

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

            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl md:text-2xl font-bold">All Colors</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Click to open color details in new tab
                    </p>
                </div>
                <div id="paletteSwatches" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Color Information</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Detailed breakdown of each color's RGB, HSL, and brightness values
                </p>
                <div id="colorInfoGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Brightness Levels</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Visual representation of each color's lightness on a spectrum
                </p>
                <div id="brightnessChart" class="space-y-2"></div>
            </section>

            <section class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-7 shadow-sm">
                <h2 class="text-xl md:text-2xl font-bold mb-4">Export Formats</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    Copy palette in different formats for your project
                </p>
                <div class="flex flex-wrap gap-2 mb-4">
                    <button id="exportHexBtn"
                        class="export-btn px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold transition-colors"
                        data-format="hex">
                        <i class="bi bi-file-earmark-code me-1"></i>HEX Array
                    </button>
                    <button id="exportJsonBtn"
                        class="export-btn px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-lg text-sm font-semibold transition-colors"
                        data-format="json">
                        <i class="bi bi-file-earmark-json me-1"></i>JSON
                    </button>
                    <button id="exportScssBtn"
                        class="export-btn px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-lg text-sm font-semibold transition-colors"
                        data-format="scss">
                        <i class="bi bi-file-earmark-code me-1"></i>SCSS
                    </button>
                    <button id="exportTailwindBtn"
                        class="export-btn px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-lg text-sm font-semibold transition-colors"
                        data-format="tailwind">
                        <i class="bi bi-wind me-1"></i>Tailwind
                    </button>
                </div>
                <div id="exportPreview"
                    class="bg-slate-50 h-100 dark:bg-slate-800 rounded-xl p-4 font-mono text-xs overflow-auto max-h-64 hidden">
                    <pre id="exportCode"></pre>
                </div>
                <div class="flex gap-2 mt-4">
                    <button id="copyExportBtn"
                        class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition-colors hidden">
                        <i class="bi bi-clipboard me-1"></i>Copy Code
                    </button>
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

    <footer
        class="border-t border-slate-200 dark:border-slate-800 bg-background-light dark:bg-background-dark py-8 px-6 mt-12">
        <div class="max-w-7xl mx-auto flex flex-col items-center gap-4">
            <p class="text-slate-600 dark:text-slate-400 mb-2">
                <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer"
                    class="text-primary hover:underline font-semibold">A product of TechKreative</a>
            </p>
            <div class="flex items-center justify-center gap-6">
                <a href="https://colormagic.techkreative.com/"
                    class="text-slate-500 hover:text-primary transition-colors">
                    <i class="bi bi-house-door text-xl"></i>
                </a>
                <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer"
                    class="text-slate-500 hover:text-primary transition-colors">
                    <i class="bi bi-github text-xl"></i>
                </a>
            </div>
        </div>
    </footer>

    <script src="assets/js/palette-page.js" defer></script>
</body>

</html>