<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Hex to Color Name Converter — Find What Color is Your Hex Code | Color Magic</title>
    <meta name="description"
        content="Free hex to color name converter. Enter any hex code to instantly find its human-readable color name. Browse 1000+ named colors with hex, RGB, and HSL values." />
    <meta name="keywords"
        content="hex to color name, color name converter, hex color lookup, what color is this hex code, named colors, color identifier" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/hex-to-color-name" />
    <meta property="og:title" content="Hex to Color Name Converter — Find What Color is Your Hex Code | Color Magic" />
    <meta property="og:description"
        content="Free hex to color name converter. Enter any hex code to instantly find its human-readable color name from 1000+ named colors." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/hex-to-color-name" />
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
        .sb-btn { display: flex; align-items: center; gap: 12px; width: 100%; padding: 11px 14px; border-radius: 14px; font-size: 0.875rem; font-weight: 600; transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; cursor: pointer; border: none; background: none; }
        .sb-btn .sb-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; transition: transform 0.22s ease; }
        .sb-btn.sb-inactive { color: #475569; background: transparent; }
        .sb-btn.sb-inactive:hover { background: #fdf2f8; color: #ec4899; }
        .sb-btn.sb-inactive .sb-icon { background: #fdf2f8; color: #ec4899; }
        .sb-btn.sb-active { background: linear-gradient(135deg, #7c3aed, #ec4899); color: #fff; box-shadow: 0 8px 24px -6px rgba(236, 72, 153, 0.45); }
        .sb-btn.sb-active .sb-icon { background: rgba(255, 255, 255, 0.18); color: #fff; }
        .dark .sb-btn.sb-inactive { color: #94a3b8; }
        .dark .sb-btn.sb-inactive:hover { background: #1e293b; color: #ec4899; }
        .dark .sb-btn.sb-inactive .sb-icon { background: #1e293b; color: #ec4899; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .animate-fadeIn { animation: fadeIn 0.2s ease both; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        .result-card { animation: fadeInUp 0.35s ease both; }
        #colorCodeInput:focus { box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.12); }
        #livePreview { transition: background-color 0.3s ease; }
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
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-house-door"></i></span>
                <div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span></div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-palette"></i></span>
                <div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse collections</span></div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-rainbow"></i></span>
                <div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS gradients</span></div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-eyedropper"></i></span>
                <div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp; info</span></div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-stars"></i></span>
                <div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color schemes</span></div>
            </a>
            <a href="<?= $base ?>/favorites" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-heart-fill"></i></span>
                <div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors, palettes &amp; gradients</span></div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-github"></i></span>
                <div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on GitHub</span></div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-6 md:px-8 flex flex-col gap-6">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500/10 via-pink-50 to-fuchsia-500/10 dark:from-violet-500/20 dark:via-slate-900 dark:to-fuchsia-500/20 p-7 md:p-10 border border-violet-100 dark:border-slate-800">
            <div class="absolute w-56 h-56 bg-violet-500/15 rounded-full blur-[70px] top-[-30px] right-[-30px] pointer-events-none"></div>
            <div class="absolute w-44 h-44 bg-fuchsia-500/15 rounded-full blur-[55px] bottom-[-25px] left-[-25px] pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-7">
                <div class="flex-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-violet-600 dark:text-violet-400 mb-3 border border-violet-200/50 dark:border-violet-700/50">
                        <i class="bi bi-tag"></i> Hex to Color Name Converter
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">
                        Hex to Color Name Converter
                    </h1>
                    <p class="text-slate-600 dark:text-slate-300 text-base max-w-lg">
                        Enter any hex code and instantly discover its human-readable color name. Our database includes over 1,000 named colors — from Alice Blue to Zinc, and everything in between.
                    </p>
                </div>
                <div class="shrink-0 flex flex-col items-center gap-2">
                    <div id="livePreview" class="w-28 h-28 md:w-36 md:h-36 rounded-2xl shadow-2xl border-4 border-white dark:border-slate-700" style="background-color: #ec4899"></div>
                    <span id="liveHexLabel" class="text-sm font-mono font-bold text-slate-500 dark:text-slate-400">#EC4899</span>
                </div>
            </div>
        </div>

        <!-- Search card -->
        <div class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="flex gap-3 flex-wrap items-start">
                <div class="flex-1 relative min-w-[220px]">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-lg select-none">#</span>
                    <input id="colorCodeInput" type="text" placeholder="FF5733 — enter any hex code" maxlength="7"
                        class="w-full pl-10 pr-4 py-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary focus:outline-none transition-all text-sm font-mono uppercase placeholder:normal-case placeholder:font-sans placeholder:text-slate-400"
                        aria-label="Hex color code input" />
                </div>
                <button id="findColorBtn"
                    class="px-7 py-3.5 bg-gradient-to-r from-secondary to-primary hover:from-secondary/90 hover:to-primary/90 text-white font-bold rounded-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 shadow-lg shadow-primary/25 shrink-0">
                    <i class="bi bi-search text-base"></i> Find Name
                </button>
            </div>
            <p id="errorMessage" class="text-red-500 text-sm mt-2 hidden" role="alert"></p>
        </div>

        <!-- Results -->
        <div id="colorResultsGrid" class="flex flex-col gap-6"></div>

        <!-- Popular Hex Color Examples -->
        <div class="my-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Popular Hex Color Examples</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#FF5733"></span>
                    <div><p class="font-bold text-sm">#FF5733</p><p class="text-xs text-slate-500 dark:text-slate-400">Vermilion / Red-orange</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#EC4899"></span>
                    <div><p class="font-bold text-sm">#EC4899</p><p class="text-xs text-slate-500 dark:text-slate-400">Pink</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#3B82F6"></span>
                    <div><p class="font-bold text-sm">#3B82F6</p><p class="text-xs text-slate-500 dark:text-slate-400">Blue (Tailwind 500)</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#10B981"></span>
                    <div><p class="font-bold text-sm">#10B981</p><p class="text-xs text-slate-500 dark:text-slate-400">Emerald</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#8B5CF6"></span>
                    <div><p class="font-bold text-sm">#8B5CF6</p><p class="text-xs text-slate-500 dark:text-slate-400">Violet</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#F59E0B"></span>
                    <div><p class="font-bold text-sm">#F59E0B</p><p class="text-xs text-slate-500 dark:text-slate-400">Amber</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#EF4444"></span>
                    <div><p class="font-bold text-sm">#EF4444</p><p class="text-xs text-slate-500 dark:text-slate-400">Red</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#06B6D4"></span>
                    <div><p class="font-bold text-sm">#06B6D4</p><p class="text-xs text-slate-500 dark:text-slate-400">Cyan</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#84CC16"></span>
                    <div><p class="font-bold text-sm">#84CC16</p><p class="text-xs text-slate-500 dark:text-slate-400">Lime</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#191970"></span>
                    <div><p class="font-bold text-sm">#191970</p><p class="text-xs text-slate-500 dark:text-slate-400">Midnight Blue</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#FF6347"></span>
                    <div><p class="font-bold text-sm">#FF6347</p><p class="text-xs text-slate-500 dark:text-slate-400">Tomato</p></div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                    <span class="w-10 h-10 rounded-lg shadow-sm shrink-0" style="background:#000000"></span>
                    <div><p class="font-bold text-sm">#000000</p><p class="text-xs text-slate-500 dark:text-slate-400">Black</p></div>
                </div>
            </div>
        </div>

        <!-- How Hex Codes Map to Color Names -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-800 mb-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">How Hex Codes Map to Color Names</h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                A hex color code is a 6-digit hexadecimal number representing Red, Green, and Blue channels (e.g., <code class="font-mono text-primary">#RRGGBB</code>). Each pair of digits ranges from 00 to FF (0–255 in decimal), giving over 16.7 million possible color combinations. Color names are human-readable labels assigned to specific hex values or the closest match in a named color database. When you enter a hex code, our converter finds the nearest named color by calculating the Euclidean distance in RGB space.
            </p>
            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                Named colors come from standardized lists like CSS/X11 colors, as well as curated design palettes. While not every hex code has an exact name, our tool identifies the closest match from over 1,000 named entries.
            </p>
        </div>

        <!-- FAQ Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Frequently Asked Questions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What color is hex #FF5733?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">#FF5733 is a vibrant red-orange color, commonly called Vermilion. It has RGB values of (255, 87, 51) and is a popular choice for call-to-action buttons due to its energetic appearance.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How to convert hex code to color name?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Enter the hex code (with or without #) into the converter above. It looks up the closest named color from a database of 1,000+ entries by measuring the distance between colors in RGB space. The result includes the name, hex, RGB, and HSL values.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What are named colors?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Named colors are specific hex color values that have been given human-readable names. Examples include "Coral" (#FF7F50), "Dodger Blue" (#1E90FF), and "Midnight Blue" (#191970). CSS supports 148 standard named colors, but extended databases include thousands more.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">Where do color names come from?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Color names originate from several sources: the CSS/X11 color standard (148 names), the Resene colour dictionary, the XKCD color survey of 220,000 participants, and various design industry standards. Our database combines these sources for comprehensive coverage.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How many hex colors have names?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Our database contains over 1,000 named colors. While there are 16,777,216 possible hex combinations, only a fraction have widely recognized names. For unnamed colors, our tool finds the closest named match so you always get a useful result.</p>
                </div>
            </div>
        </div>

        <!-- Related Tools -->
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900/50 rounded-2xl p-8 mb-12">
            <h2 class="font-bold text-lg mb-4 text-slate-800 dark:text-white">Related Color Tools</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="<?= $base ?>/find-color" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-eyedropper text-emerald-500 text-lg"></i>
                    <span class="text-sm font-semibold">Find Color</span>
                </a>
                <a href="<?= $base ?>/hex-to-rgb" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-sliders text-secondary text-lg"></i>
                    <span class="text-sm font-semibold">Hex to RGB Converter</span>
                </a>
                <a href="<?= $base ?>/what-color-is" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-question-circle text-amber-500 text-lg"></i>
                    <span class="text-sm font-semibold">What Color Is This?</span>
                </a>
                <a href="<?= $base ?>/palettes" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-palette text-primary text-lg"></i>
                    <span class="text-sm font-semibold">Explore Palettes</span>
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
                    "name": "What color is hex #FF5733?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "#FF5733 is a vibrant red-orange color, commonly called Vermilion. It has RGB values of (255, 87, 51) and is a popular choice for call-to-action buttons."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How to convert hex code to color name?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Enter the hex code into the converter. It looks up the closest named color from a database of 1,000+ entries by measuring the distance between colors in RGB space."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What are named colors?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Named colors are specific hex color values given human-readable names. CSS supports 148 standard named colors, but extended databases include thousands more."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Where do color names come from?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Color names come from the CSS/X11 color standard, the Resene colour dictionary, the XKCD color survey, and various design industry standards."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How many hex colors have names?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Our database contains over 1,000 named colors. While there are 16,777,216 possible hex combinations, only a fraction have widely recognized names."
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
        window.CM_ACTIVE_PAGE = "hex-to-color-name";
    </script>
    <script src="<?= $base ?>/assets/js/find-color-page.js?v=1.2" defer></script>
</body>

</html>
