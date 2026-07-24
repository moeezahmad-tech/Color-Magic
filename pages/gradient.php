<?php include '../components/config.php';
/** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script>
        (function() {
            var path = window.location.pathname;
            var idx = path.indexOf("/gradient/");
            var base =
                idx !== -1 ?
                path.substring(0, idx + 1) :
                path.substring(0, path.lastIndexOf("/") + 1);
            document.write('<base href="' + base + '">');
        })();
    </script>
    <title>CSS Gradient | Color Magic</title>
    <meta name="description"
        content="Explore this beautiful CSS gradient with copy-ready CSS, hex colors, and related gradients. Perfect for web design projects." />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>
    <style>
        .gradient-hero {
            transition: transform 0.4s ease;
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
        <!-- Back link -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="<?= $base ?>/gradients"
                class="inline-flex items-center gap-2 text-primary font-semibold hover:opacity-90 transition-opacity">
                <i class="bi bi-arrow-left"></i>
                Back to Gradients
            </a>
            <p id="gradientIdText" class="text-xs text-slate-500 dark:text-slate-400 font-mono"></p>
        </div>

        <!-- Gradient detail (hidden until loaded) -->
        <section id="gradientDetail" class="hidden flex flex-col gap-8">

            <!-- Hero: Preview + Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 bg-white dark:bg-slate-900 rounded-3xl p-5 md:p-7 shadow-sm">
                <!-- Large gradient preview -->
                <div id="heroGradientPreview"
                    class="gradient-hero h-64 md:h-80 rounded-2xl overflow-hidden shadow-xl shadow-black/10 dark:shadow-black/30 relative cursor-pointer group">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 backdrop-blur-[2px]">
                        <span class="px-4 py-2 bg-white/90 dark:bg-slate-900/90 rounded-xl text-sm font-semibold shadow-lg flex items-center gap-2">
                            <i class="bi bi-clipboard"></i> Click to copy CSS
                        </span>
                    </div>
                </div>

                <!-- Info panel -->
                <div class="flex flex-col gap-4">
                    <div>
                        <h1 id="gradientName" class="text-3xl md:text-4xl font-bold tracking-tight"></h1>
                        <p id="gradientMeta" class="text-sm text-slate-500 dark:text-slate-400 mt-2"></p>
                    </div>

                    <!-- Info chips -->
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

                    <!-- Copy CSS button -->
                    <div class="flex items-center gap-3">
                        <button id="copyCssBtn"
                            class="px-4 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                            <i class="bi bi-clipboard"></i>
                            Copy CSS
                        </button>
                        <button id="favGradientBtn"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                            <i class="bi bi-heart"></i>
                            <span>Favorite</span>
                        </button>
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

    <script src="<?= $base ?>/assets/js/services/favorites.js" defer></script>
    <script src="<?= $base ?>/assets/js/gradient-page.js" defer></script>
</body>

</html>