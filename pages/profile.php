<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Color Magic | Your Favorites — Saved Colors, Palettes &amp; Gradients</title>
    <meta name="description"
        content="View all your saved colors, palettes and gradients in one place. Your personal collection on Color Magic." />
    <meta name="keywords" content="saved colors, favorite palettes, saved gradients, design tool, TechKreative" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="noindex, nofollow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/profile" />
    <meta property="og:title" content="Color Magic | Your Favorites" />
    <meta property="og:description" content="All your saved colors, palettes and gradients in one place." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/profile" />
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
        /* ── Sidebar nav button styles ── */
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

        /* ── Tab button styles ── */
        .fav-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.22s ease;
            cursor: pointer;
            border: none;
            background: none;
            color: #64748b;
        }

        .fav-tab:hover {
            background: #f8fafc;
            color: #ec4899;
        }

        .dark .fav-tab:hover {
            background: #1e293b;
        }

        .fav-tab.active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            box-shadow: 0 6px 18px -4px rgba(236, 72, 153, 0.4);
            border-color: transparent;
        }

        .fav-tab .fav-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.08);
        }

        .fav-tab.active .fav-count {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        /* ── Gradient card styles (reused from gradients page) ── */
        .gradient-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gradient-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(124, 58, 237, 0.15);
        }

        .gradient-preview {
            transition: transform 0.4s ease;
        }

        .gradient-card:hover .gradient-preview {
            transform: scale(1.04);
        }

        .copy-css-btn.copied-state {
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
        }

        /* ── Color card styles ── */
        .color-fav-card {
            transition: all 0.25s ease;
        }

        .color-fav-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.15);
        }

        /* Mobile overlay */
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
        <div class="space-y-2">
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-house-door"></i></span>
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
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div>
                    <span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span>
                </div>
            </a>
            <a href="<?= $base ?>/profile" class=" sb-btn sb-active w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div>
                    <span class="block font-bold">Favorites</span><span class="text-xs opacity-75">Your saved
                        items</span>
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
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-5 md:px-8 flex flex-col gap-8">
        <!-- Page hero -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2 flex items-center gap-3">
                    <?php if (isset($_SESSION['user']['picture'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user']['picture']) ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-sm">
                    <?php else: ?>
                        <i class="bi bi-person-circle text-indigo-500"></i>
                    <?php endif; ?>
                    Your Profile
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-base max-w-xl">
                    View your saved colors, palettes, and gradients. Manage your account settings below.
                </p>
            </div>
            <div id="favTotalCount" class="text-sm text-slate-500 dark:text-slate-400 font-mono bg-white dark:bg-slate-800 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm"></div>
        </div>

        <!-- Tabs -->
        <div
            class="bg-white dark:bg-slate-900 p-4 md:p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <div id="favTabs" class="flex flex-wrap gap-2 mb-6">
                <button class="fav-tab active" data-tab="colors">
                    <i class="bi bi-eyedropper"></i>
                    <span>Colors</span>
                    <span class="fav-count" id="favColorsCount">0</span>
                </button>
                <button class="fav-tab" data-tab="palettes">
                    <i class="bi bi-palette"></i>
                    <span>Palettes</span>
                    <span class="fav-count" id="favPalettesCount">0</span>
                </button>
                <button class="fav-tab" data-tab="gradients">
                    <i class="bi bi-rainbow"></i>
                    <span>Gradients</span>
                    <span class="fav-count" id="favGradientsCount">0</span>
                </button>
            </div>

            <!-- Tab panels -->
            <div id="favPanelColors" class="fav-panel">
                <div id="favColorsGrid"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"></div>
            </div>

            <div id="favPanelPalettes" class="fav-panel hidden">
                <div id="favPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
            </div>

            <div id="favPanelGradients" class="fav-panel hidden">
                <div id="favGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="mt-8 bg-white dark:bg-slate-900 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="bi bi-gear-fill text-slate-400"></i>
                Account Settings
            </h2>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold mb-1">SIGNED IN AS</p>
                    <p class="text-lg font-medium text-slate-900 dark:text-white">
                        <?= htmlspecialchars($_SESSION['user']['email'] ?? 'Not logged in') ?>
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <a href="<?= $base ?>/auth/logout" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg text-sm font-semibold transition-all shadow-sm">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                    <button onclick="if(confirm('Are you sure you want to delete your account? This action cannot be undone.')) window.location.href='<?= $base ?>/auth/delete-account'" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg text-sm font-semibold transition-all shadow-sm">
                        <i class="bi bi-trash3"></i>
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>

    <?php renderInlineData(['color-names', 'palettes', 'gradients']); ?>
    <script>
        window.CM_ACTIVE_PAGE = "favorites";
        window.CM_COLOR_BASE = "color/";
        window.CM_PALETTE_BASE = "palette/";
    </script>
    <script src="<?= $base ?>/assets/js/utils.js" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js" defer></script>
    <script src="<?= $base ?>/assets/js/favorites-page.js" defer></script>
</body>

</html>