<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Color Magic | Explore Color Palettes</title>
    <meta name="description"
        content="Explore thousands of professional color palettes with Color Magic — the open-source color tool for designers." />
    <meta name="keywords" content="color palette explorer, color schemes, designer tools, TechKreative" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/" />
    <meta property="og:title" content="Color Magic | Explore Color Palettes" />
    <meta property="og:description"
        content="The ultimate tool for finding and organizing professional color palettes." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/" />
    <link rel="manifest" href="manifest.json" />
    <link rel="icon" type="image/png" href="assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script id="tailwind-config" src="assets/js/tailwind-config.js"></script>
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

        /* Inactive */
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

        /* Active */
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

        /* Dark mode overrides */
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

        /* Swatch / palette card */
        .swatch {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .swatch-hex {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .swatch:hover .swatch-hex {
            opacity: 1;
        }

        .swatch-hex.copied-state {
            opacity: 1 !important;
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            font-weight: 700;
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
    <!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
    <?php include 'components/navbar.php'; ?>


    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="/" class="flex items-center gap-2 text-primary">
                <img src="assets/images/logo.png" alt="Color Magic Logo" class="h-8 w-8 object-contain" />
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
            <a href="/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-house-door"></i></span>
                <div>
                    <span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="palettes.php" class="sb-btn sb-active w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div>
                    <span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-75">Browse
                        collections</span>
                </div>
            </a>
            <a href="find-color.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div>
                    <span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span>
                </div>
            </a>
            <a href="generate-palette.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div>
                    <span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span>
                </div>
            </a>
            <a href="palettes.php?filter=favorites" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div>
                    <span class="block font-bold">Saved Palettes</span><span class="text-xs opacity-60">Your
                        favourites</span>
                </div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="open-source.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div>
                    <span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span>
                </div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-28 pb-8 px-5 md:px-8 flex flex-col gap-8">
        <!-- Page hero -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">
                    Explore Color Palettes
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-base max-w-xl">
                    Browse thousands of professional color schemes — search by hex,
                    theme, or style.
                </p>
            </div>
        </div>

        <!-- Palette explorer card -->
        <div
            class="bg-white dark:bg-slate-900 p-4 md:p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <!-- Search -->
            <div class="relative group w-full mb-5">
                <i
                    class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-primary transition-colors"></i>
                <input id="searchInput"
                    class="w-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-2xl py-3 pl-11 pr-5 text-sm focus:border-primary focus:outline-none transition-all shadow-sm placeholder:text-slate-400"
                    placeholder="Search hex, theme or color name…" type="text" aria-label="Search palettes" />
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2 mb-7" role="group" aria-label="Filter by theme">
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-primary text-white text-xs font-bold shadow-lg shadow-primary/20 border border-transparent"
                    data-theme="all">
                    All Styles
                </button>
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors border border-slate-200 dark:border-slate-700"
                    data-theme="favorites">
                    <i class="bi bi-heart-fill"></i> Favorites
                </button>
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors border border-slate-200 dark:border-slate-700"
                    data-theme="pastel">
                    <i class="bi bi-flower1"></i> Pastel
                </button>
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors border border-slate-200 dark:border-slate-700"
                    data-theme="vintage">
                    <i class="bi bi-clock-history"></i> Vintage
                </button>
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors border border-slate-200 dark:border-slate-700"
                    data-theme="neon">
                    <i class="bi bi-lightning-fill"></i> Neon
                </button>
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors border border-slate-200 dark:border-slate-700"
                    data-theme="minimalist">
                    <i class="bi bi-check-circle"></i> Minimalist
                </button>
                <button
                    class="theme-filter flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors border border-slate-200 dark:border-slate-700"
                    data-theme="earthy">
                    <i class="bi bi-tree"></i> Earthy
                </button>
            </div>

            <!-- Grid -->
            <div id="paletteGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>

            <!-- Pagination -->
            <div class="flex flex-col items-center gap-3 pt-6 mb-12" id="palettePagination">
                <p id="paletteCountStatus" class="text-sm text-slate-500 dark:text-slate-400"></p>
                <button id="loadMoreBtn" type="button"
                    class="hidden px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20">
                    Load More Palettes
                </button>
            </div>

            <!-- Footer -->
    </main>
    
    <?php include 'components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "explore";
    </script>
    <script src="assets/js/utils.js" defer></script>
    <script src="assets/js/services/favorites.js" defer></script>
    <script src="assets/js/components/palette-card.js" defer></script>
    <script src="assets/js/explore-palettes.js?v=2.0" defer></script>
</body>

</html>