<?php 
header('X-Robots-Tag: noindex, nofollow', true);
header('Cache-Control: private, no-cache, no-store, must-revalidate');
require_once __DIR__ . '/../../components/config.php'; 
/** @var string $base */ 
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Saved Collections &amp; Favorites | Color Magic</title>
    <meta name="description"
        content="Access and manage all your saved color palettes, eye-catching CSS gradients, and favorite hex colors in one unified design workspace." />
    <meta name="keywords" content="saved palettes, favorite colors, saved gradients, color magic collections, css gradient favorites" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="noindex, nofollow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/favorites" />
    <meta property="og:title" content="Color Magic | Saved Collections" />
    <meta property="og:description" content="All your saved palettes, gradients, and colors in one place." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
        <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="apple-touch-icon" href="<?= $base ?>/assets/images/logo.png" />

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
    <style>
        /* ── Top Menu Bar Tabs ── */
        .fav-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 700;
            color: #64748b;
            background: transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 1px solid transparent;
            user-select: none;
            white-space: nowrap;
        }

        .fav-tab:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .dark .fav-tab {
            color: #94a3b8;
        }

        .dark .fav-tab:hover {
            color: #ffffff;
            background-color: #1e293b;
        }

        .fav-tab.active {
            color: #ffffff;
            background-color: #0f172a;
            box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.25);
        }

        .dark .fav-tab.active {
            color: #0f172a;
            background-color: #ffffff;
            box-shadow: 0 4px 12px -2px rgba(255, 255, 255, 0.25);
        }

        .fav-tab .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            padding: 0 0.375rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 800;
            background-color: rgba(100, 116, 139, 0.15);
            color: inherit;
        }

        .fav-tab.active .tab-count {
            background-color: rgba(255, 255, 255, 0.2);
            color: inherit;
        }

        .dark .fav-tab.active .tab-count {
            background-color: rgba(15, 23, 42, 0.15);
            color: inherit;
        }

        /* ── Swatch Hover Interactions (for palette cards) ── */
        .swatch {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .swatch-hex {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .swatch:hover .swatch-hex {
            opacity: 1;
            display: block !important;
        }

        .color-fav-card {
            transition: all 0.2s ease;
        }

        .color-fav-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen flex flex-col">
    <?php include '../../components/navbar.php'; ?>

    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 flex-1">
        
        <!-- ── HEADER TITLE & STATS ──────────────────────────────────────────────── -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 pb-6 border-b border-slate-200/80 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-500 via-pink-500 to-amber-400 flex items-center justify-center text-white shadow-md shadow-rose-500/20">
                        <i class="bi bi-heart-fill text-lg"></i>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                        Saved Collections
                    </h1>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                    Your personalized design vault. All your liked color palettes, CSS gradients, and custom hex codes saved in one place.
                </p>
            </div>

            <!-- Total Saved Counter Pill -->
            <div class="flex items-center gap-2 self-start md:self-end">
                <span id="favTotalCount" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs shadow-sm border border-slate-200/60 dark:border-slate-700">
                    <i class="bi bi-bookmark-check text-primary"></i>
                    <span>0 items saved</span>
                </span>
            </div>
        </div>

        <!-- ── TOP MENU BAR (HORIZONTAL TABS & QUICK EXPLORE) ────────────────────── -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <!-- Tabs Menu -->
            <nav id="favTabs" class="flex items-center gap-2 p-1.5 rounded-full bg-slate-100/90 dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-sm" aria-label="Collections Filter">
                <button type="button" class="fav-tab active" data-tab="palettes">
                    <i class="bi bi-palette text-sm"></i>
                    <span>Palettes</span>
                    <span id="favPalettesCount" class="tab-count">0</span>
                </button>
                <button type="button" class="fav-tab" data-tab="gradients">
                    <i class="bi bi-rainbow text-sm"></i>
                    <span>Gradients</span>
                    <span id="favGradientsCount" class="tab-count">0</span>
                </button>
                <button type="button" class="fav-tab" data-tab="colors">
                    <i class="bi bi-eyedropper text-sm"></i>
                    <span>Colors</span>
                    <span id="favColorsCount" class="tab-count">0</span>
                </button>
            </nav>

            <!-- Quick Action Links -->
            <div class="flex items-center gap-2">
                <a href="<?= $base ?>/palettes" class="px-4 py-2 rounded-full text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="bi bi-grid-fill text-xs"></i>
                    <span>Browse All Palettes</span>
                </a>
                <a href="<?= $base ?>/gradients" class="px-4 py-2 rounded-full text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-amber-500 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="bi bi-sliders text-xs"></i>
                    <span>Explore Gradients</span>
                </a>
            </div>
        </div>

        <!-- ── PANELS CONTAINER ──────────────────────────────────────────────────── -->
        <div id="favPanelsContainer" class="w-full">
            
            <!-- 1. Palettes Panel (Default Active) -->
            <section id="favPanelPalettes" class="w-full">
                <div id="favPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            </section>

            <!-- 2. Gradients Panel -->
            <section id="favPanelGradients" class="w-full hidden">
                <div id="favGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            </section>

            <!-- 3. Colors Panel -->
            <section id="favPanelColors" class="w-full hidden">
                <div id="favColorsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
            </section>

        </div>

    </main>

    <?php include '../../components/footer.php'; ?>

    <!-- Config & Dependencies -->
    <script>
        window.CM_ACTIVE_PAGE   = "favorites";
        window.CM_COLOR_BASE    = "<?= $base ?>/color/";
        window.CM_PALETTE_BASE  = "<?= $base ?>/palette/";
        window.CM_GRADIENT_BASE = "<?= $base ?>/gradient/";
        window.CM_BASE          = "<?= $base ?>";
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/favorites-page.js?v=<?= time() ?>" defer></script>
</body>

</html>

