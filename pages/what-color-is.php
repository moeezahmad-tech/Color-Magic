<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>What Color Is This Code? — Online Hex Color Identifier | Color Magic</title>
    <meta name="description"
        content="Paste any hex code and we'll tell you exactly what color it is. Free online hex color identifier with 20 common examples organized by category." />
    <meta name="keywords"
        content="what color is this, hex color identifier, identify color from code, color lookup tool, hex code color finder" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/what-color-is" />
    <meta property="og:title" content="What Color Is This Code? — Online Hex Color Identifier | Color Magic" />
    <meta property="og:description"
        content="Paste any hex code and we'll tell you exactly what color it is. Free online hex color identifier with visual examples." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/what-color-is" />
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

        .result-card {
            animation: fadeInUp 0.35s ease both;
        }

        #colorCodeInput:focus {
            box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.12);
        }

        #livePreview {
            transition: background-color 0.3s ease;
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
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-house-door"></i></span>
                <div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span></div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS
                        gradients</span></div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span></div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span></div>
            </a>
            <a href="<?= $base ?>/profile" class=" sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors,
                        palettes &amp; gradients</span></div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span></div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-6 md:px-8 flex flex-col gap-6">
        <!-- Hero -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/10 via-pink-50 to-orange-500/10 dark:from-amber-500/20 dark:via-slate-900 dark:to-orange-500/20 p-7 md:p-10 border border-amber-100 dark:border-slate-800">
            <div
                class="absolute w-56 h-56 bg-amber-500/15 rounded-full blur-[70px] top-[-30px] right-[-30px] pointer-events-none">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-7">
                <div class="flex-1">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-amber-600 dark:text-amber-400 mb-3 border border-amber-200/50 dark:border-amber-700/50">
                        <i class="bi bi-question-circle"></i> Hex Color Identifier
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">What Color Is This Code?</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-base max-w-lg">
                        Paste any hex code and we'll tell you exactly what color it is. Just type or paste the code
                        below — no design software needed.
                    </p>
                </div>
                <div class="shrink-0 flex flex-col items-center gap-2">
                    <div id="livePreview"
                        class="w-28 h-28 md:w-36 md:h-36 rounded-2xl shadow-2xl border-4 border-white dark:border-slate-700"
                        style="background-color: #f59e0b"></div>
                    <span id="liveHexLabel"
                        class="text-sm font-mono font-bold text-slate-500 dark:text-slate-400">#F59E0B</span>
                </div>
            </div>
        </div>

        <!-- Search card -->
        <div
            class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="flex gap-3 flex-wrap items-start">
                <div class="flex-1 relative min-w-[220px]">
                    <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-lg select-none">#</span>
                    <input id="colorCodeInput" type="text" placeholder="Paste any hex code here…" maxlength="7"
                        class="w-full pl-10 pr-4 py-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary focus:outline-none transition-all text-sm font-mono uppercase placeholder:normal-case placeholder:font-sans placeholder:text-slate-400"
                        aria-label="Hex color code input" />
                </div>
                <button id="findColorBtn"
                    class="px-7 py-3.5 bg-gradient-to-r from-secondary to-primary hover:from-secondary/90 hover:to-primary/90 text-white font-bold rounded-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 shadow-lg shadow-primary/25 shrink-0">
                    <i class="bi bi-search text-base"></i> Identify Color
                </button>
            </div>
            <p id="errorMessage" class="text-red-500 text-sm mt-2 hidden" role="alert"></p>
        </div>

        <!-- Results -->
        <div id="colorResultsGrid" class="flex flex-col gap-6"></div>

        <!-- 20 Common Color Examples by Category -->
        <div class="my-6">
            <h2 class="text-xl font-bold mb-6 text-slate-800 dark:text-white">Common Color Examples</h2>

            <!-- Warm Colors -->
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3"><i
                    class="bi bi-sun text-orange-500"></i> Warm Colors</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#FF0000"></span>
                    <p class="font-bold text-xs">#FF0000</p>
                    <p class="text-[10px] text-slate-500">Red</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#FF5733"></span>
                    <p class="font-bold text-xs">#FF5733</p>
                    <p class="text-[10px] text-slate-500">Vermilion</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#FF6347"></span>
                    <p class="font-bold text-xs">#FF6347</p>
                    <p class="text-[10px] text-slate-500">Tomato</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#FFA500"></span>
                    <p class="font-bold text-xs">#FFA500</p>
                    <p class="text-[10px] text-slate-500">Orange</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#FFD700"></span>
                    <p class="font-bold text-xs">#FFD700</p>
                    <p class="text-[10px] text-slate-500">Gold</p>
                </div>
            </div>

            <!-- Cool Colors -->
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3"><i
                    class="bi bi-snow2 text-blue-500"></i> Cool Colors</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#0000FF"></span>
                    <p class="font-bold text-xs">#0000FF</p>
                    <p class="text-[10px] text-slate-500">Blue</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#3B82F6"></span>
                    <p class="font-bold text-xs">#3B82F6</p>
                    <p class="text-[10px] text-slate-500">Blue (Tailwind)</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#06B6D4"></span>
                    <p class="font-bold text-xs">#06B6D4</p>
                    <p class="text-[10px] text-slate-500">Cyan</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#10B981"></span>
                    <p class="font-bold text-xs">#10B981</p>
                    <p class="text-[10px] text-slate-500">Emerald</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#8B5CF6"></span>
                    <p class="font-bold text-xs">#8B5CF6</p>
                    <p class="text-[10px] text-slate-500">Violet</p>
                </div>
            </div>

            <!-- Neutral Colors -->
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3"><i
                    class="bi bi-circle-half text-slate-500"></i> Neutral Colors</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm border border-slate-300 dark:border-slate-600"
                        style="background:#FFFFFF"></span>
                    <p class="font-bold text-xs">#FFFFFF</p>
                    <p class="text-[10px] text-slate-500">White</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#C0C0C0"></span>
                    <p class="font-bold text-xs">#C0C0C0</p>
                    <p class="text-[10px] text-slate-500">Silver</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#808080"></span>
                    <p class="font-bold text-xs">#808080</p>
                    <p class="text-[10px] text-slate-500">Gray</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#8B4513"></span>
                    <p class="font-bold text-xs">#8B4513</p>
                    <p class="text-[10px] text-slate-500">Saddle Brown</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#F5F5DC"></span>
                    <p class="font-bold text-xs">#F5F5DC</p>
                    <p class="text-[10px] text-slate-500">Beige</p>
                </div>
            </div>

            <!-- Dark Colors -->
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3"><i
                    class="bi bi-moon-stars text-indigo-500"></i> Dark Colors</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#000000"></span>
                    <p class="font-bold text-xs">#000000</p>
                    <p class="text-[10px] text-slate-500">Black</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#191970"></span>
                    <p class="font-bold text-xs">#191970</p>
                    <p class="text-[10px] text-slate-500">Midnight Blue</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#800020"></span>
                    <p class="font-bold text-xs">#800020</p>
                    <p class="text-[10px] text-slate-500">Burgundy</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#2F4F4F"></span>
                    <p class="font-bold text-xs">#2F4F4F</p>
                    <p class="text-[10px] text-slate-500">Dark Slate Gray</p>
                </div>
                <div
                    class="flex flex-col items-center gap-2 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-12 h-12 rounded-lg shadow-sm" style="background:#006400"></span>
                    <p class="font-bold text-xs">#006400</p>
                    <p class="text-[10px] text-slate-500">Dark Green</p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Frequently Asked Questions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How do I identify a color from its code?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Enter the hex code into the search box above.
                        Our tool matches it against a database of 1,000+ named colors and shows you the color name,
                        RGB/HSL values, and a live preview. You can also see the color rendered visually before knowing
                        its name.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What color is #000000?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">#000000 is pure Black — the absence of all
                        color light. In RGB, it's rgb(0, 0, 0), meaning zero intensity on all three channels. It's the
                        darkest possible color and provides maximum contrast against white text.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What color is #FFFFFF?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">#FFFFFF is pure White — full intensity on all
                        RGB channels: rgb(255, 255, 255). It's the lightest possible color and serves as the default
                        background for most web pages and documents.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How to read a hex color code?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">A hex code has the format #RRGGBB where RR is
                        Red, GG is Green, and BB is Blue. Each pair uses base-16 (0-9, A-F). Higher values = more of
                        that color. For example, #FF0000 is maximum red (pure red), while #0000FF is maximum blue.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">Where can I find hex codes for my colors?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Use our <a href="<?= $base ?>/find-color"
                            class="text-primary hover:underline">Find Color tool</a> to look up any hex code's details,
                        or browse our <a href="<?= $base ?>/palettes" class="text-primary hover:underline">Palette
                            Explorer</a> for curated color schemes with ready-to-use hex codes. You can also pick colors
                        from images using browser developer tools.</p>
                </div>
            </div>
        </div>

        <!-- Related Tools -->
        <div
            class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900/50 rounded-2xl p-8 mb-12">
            <h2 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Related Color Tools</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="<?= $base ?>/find-color"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-eyedropper text-emerald-500 text-lg"></i>
                    <span class="text-sm font-semibold">Find Color</span>
                </a>
                <a href="<?= $base ?>/hex-to-color-name"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-tag text-primary text-lg"></i>
                    <span class="text-sm font-semibold">Hex to Color Name</span>
                </a>
                <a href="<?= $base ?>/dark-color-finder"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-moon-stars text-indigo-500 text-lg"></i>
                    <span class="text-sm font-semibold">Dark Color Finder</span>
                </a>
                <a href="<?= $base ?>/brand-color-lookup"
                    class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-bookmark-star text-amber-500 text-lg"></i>
                    <span class="text-sm font-semibold">Brand Color Lookup</span>
                </a>
            </div>
        </div>

        <!-- FAQ Schema -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "How do I identify a color from its code?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Enter the hex code into the search box. Our tool matches it against a database of 1,000+ named colors and shows you the color name, RGB/HSL values, and a live preview."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What color is #000000?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "#000000 is pure Black — the absence of all color light. In RGB, it's rgb(0, 0, 0)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What color is #FFFFFF?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "#FFFFFF is pure White — full intensity on all RGB channels: rgb(255, 255, 255)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How to read a hex color code?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "A hex code has the format #RRGGBB where RR is Red, GG is Green, and BB is Blue. Each pair uses base-16 (0-9, A-F). Higher values mean more of that color channel."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Where can I find hex codes for my colors?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Use our Find Color tool to look up any hex code's details, or browse our Palette Explorer for curated color schemes with ready-to-use hex codes."
                    }
                }
            ]
        }
        </script>
    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        const colorInput = document.getElementById("colorCodeInput");
        const livePreview = document.getElementById("livePreview");
        const liveHexLabel = document.getElementById("liveHexLabel");
        colorInput?.addEventListener("input", function () {
            let val = this.value.trim().replace("#", "");
            if (val.length === 3) val = val.split("").map((c) => c + c).join("");
            if (/^[0-9A-Fa-f]{6}$/.test(val)) {
                livePreview.style.backgroundColor = "#" + val;
                liveHexLabel.textContent = "#" + val.toUpperCase();
            }
        });
        colorInput?.addEventListener("keypress", (e) => {
            if (e.key === "Enter") document.getElementById("findColorBtn")?.click();
        });
        window.CM_ACTIVE_PAGE = "what-color-is";
    </script>
    <?php renderInlineData(['color-names']); ?>
    <script src="<?= $base ?>/assets/js/utils.js" defer></script>
    <script src="<?= $base ?>/assets/js/find-color-page.js?v=1.2" defer></script>
</body>

</html>