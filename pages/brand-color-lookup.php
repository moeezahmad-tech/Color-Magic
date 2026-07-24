<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Brand Color Lookup — Find Hex Codes for Popular Brand Colors | Color Magic</title>
    <meta name="description"
        content="Find exact hex codes and RGB values for popular brand colors. Google, Apple, Spotify, Netflix, Meta, and 25+ more brand color palettes." />
    <meta name="keywords"
        content="brand colors, brand color codes, company hex codes, brand palette, Google color, Apple color, Spotify color, brand identity colors" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/brand-color-lookup" />
    <meta property="og:title" content="Brand Color Lookup — Find Hex Codes for Popular Brand Colors | Color Magic" />
    <meta property="og:description"
        content="Find exact hex codes and RGB values for popular brand colors. Google, Apple, Spotify, Netflix, and 25+ more." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/brand-color-lookup" />
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
        .sb-btn .sb-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
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
                <span class="text-xl font-bold tracking-tight"><span class="text-slate-900 dark:text-white">Color</span><span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span></span>
            </a>
            <button id="closeMobileMenuBtn" class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" aria-label="Close menu">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        <div class="space-y-2">
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-house-door"></i></span><div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span></div></a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-palette"></i></span><div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse collections</span></div></a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-rainbow"></i></span><div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS gradients</span></div></a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-eyedropper"></i></span><div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp; info</span></div></a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-stars"></i></span><div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color schemes</span></div></a>
            <a href="<?= $base ?>/favorites" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-heart-fill"></i></span><div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors, palettes &amp; gradients</span></div></a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i class="bi bi-github"></i></span><div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on GitHub</span></div></a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-6 md:px-8 flex flex-col gap-6">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-pink-50 to-amber-500/10 dark:from-emerald-500/20 dark:via-slate-900 dark:to-amber-500/20 p-7 md:p-10 border border-emerald-100 dark:border-slate-800">
            <div class="absolute w-56 h-56 bg-emerald-500/15 rounded-full blur-[70px] top-[-30px] right-[-30px] pointer-events-none"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-emerald-600 dark:text-emerald-400 mb-3 border border-emerald-200/50 dark:border-emerald-700/50">
                    <i class="bi bi-bookmark-star"></i> Brand Color Lookup
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Popular Brand Color Lookup</h1>
                <p class="text-slate-600 dark:text-slate-300 text-base max-w-lg">
                    Find the exact hex codes, RGB values, and color palettes used by the world's most recognizable brands. Click any color to explore its full details.
                </p>
            </div>
        </div>

        <!-- Brand Colors Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php
            $brands = [
                ['name' => 'Apple', 'colors' => [['hex' => '000000', 'label' => 'Black'], ['hex' => 'F5F5F7', 'label' => 'Page Background']]],
                ['name' => 'Google', 'colors' => [['hex' => '4285F4', 'label' => 'Blue'], ['hex' => 'EA4335', 'label' => 'Red'], ['hex' => 'FBBC05', 'label' => 'Yellow'], ['hex' => '34A853', 'label' => 'Green']]],
                ['name' => 'Meta (Facebook)', 'colors' => [['hex' => '0668E1', 'label' => 'Meta Blue'], ['hex' => '0081FB', 'label' => 'Light Blue']]],
                ['name' => 'Microsoft', 'colors' => [['hex' => 'F25022', 'label' => 'Red'], ['hex' => '7FBA00', 'label' => 'Green'], ['hex' => '00A4EF', 'label' => 'Blue'], ['hex' => 'FFB900', 'label' => 'Yellow']]],
                ['name' => 'Netflix', 'colors' => [['hex' => 'E50914', 'label' => 'Netflix Red'], ['hex' => '221F1F', 'label' => 'Black']]],
                ['name' => 'Spotify', 'colors' => [['hex' => '1DB954', 'label' => 'Spotify Green'], ['hex' => '191414', 'label' => 'Black']]],
                ['name' => 'Twitter / X', 'colors' => [['hex' => '000000', 'label' => 'X Black'], ['hex' => '1DA1F2', 'label' => 'Twitter Blue']]],
                ['name' => 'Amazon', 'colors' => [['hex' => 'FF9900', 'label' => 'Amazon Orange'], ['hex' => '232F3E', 'label' => 'Dark Blue'], ['hex' => '146EB4', 'label' => 'Light Blue']]],
                ['name' => 'Tesla', 'colors' => [['hex' => 'CC0000', 'label' => 'Tesla Red'], ['hex' => '171A20', 'label' => 'Dark Gray']]],
                ['name' => 'Airbnb', 'colors' => [['hex' => 'FF5A5F', 'label' => 'Rausch'], ['hex' => '00A699', 'label' => 'Babu']]],
                ['name' => 'Uber', 'colors' => [['hex' => '000000', 'label' => 'Black'], ['hex' => '276EF1', 'label' => 'Blue']]],
                ['name' => 'Slack', 'colors' => [['hex' => '4A154B', 'label' => 'Aubergine'], ['hex' => '36C5F0', 'label' => 'Blue'], ['hex' => '2EB67D', 'label' => 'Green'], ['hex' => 'ECB22E', 'label' => 'Yellow']]],
                ['name' => 'Adobe', 'colors' => [['hex' => 'FF0000', 'label' => 'Adobe Red']]],
                ['name' => 'Salesforce', 'colors' => [['hex' => '0176D3', 'label' => 'Cloud Blue'], ['hex' => '1B96FF', 'label' => 'Light Blue']]],
                ['name' => 'Stripe', 'colors' => [['hex' => '635BFF', 'label' => 'Stripe Purple'], ['hex' => '0A2540', 'label' => 'Dark']]],
                ['name' => 'Shopify', 'colors' => [['hex' => '7AB55C', 'label' => 'Green'], ['hex' => '004C3F', 'label' => 'Dark Green']]],
                ['name' => 'Figma', 'colors' => [['hex' => 'F24E1E', 'label' => 'Red'], ['hex' => 'A259FF', 'label' => 'Purple'], ['hex' => '1ABCFE', 'label' => 'Blue'], ['hex' => '0ACF83', 'label' => 'Green']]],
                ['name' => 'Notion', 'colors' => [['hex' => '000000', 'label' => 'Black'], ['hex' => 'FFFFFF', 'label' => 'White']]],
                ['name' => 'LinkedIn', 'colors' => [['hex' => '0A66C2', 'label' => 'LinkedIn Blue']]],
                ['name' => 'Instagram', 'colors' => [['hex' => 'E4405F', 'label' => 'Pink'], ['hex' => 'FCAF45', 'label' => 'Orange'], ['hex' => '833AB4', 'label' => 'Purple']]],
                ['name' => 'YouTube', 'colors' => [['hex' => 'FF0000', 'label' => 'YouTube Red'], ['hex' => '282828', 'label' => 'Dark Gray']]],
                ['name' => 'TikTok', 'colors' => [['hex' => '010101', 'label' => 'Black'], ['hex' => '69C9D0', 'label' => 'Cyan'], ['hex' => 'EE1D52', 'label' => 'Red']]],
                ['name' => 'WhatsApp', 'colors' => [['hex' => '25D366', 'label' => 'WhatsApp Green'], ['hex' => '128C7E', 'label' => 'Dark Green']]],
                ['name' => 'Discord', 'colors' => [['hex' => '5865F2', 'label' => 'Blurple'], ['hex' => '2C2F33', 'label' => 'Dark Gray']]],
                ['name' => 'GitHub', 'colors' => [['hex' => '181717', 'label' => 'GitHub Black'], ['hex' => '238636', 'label' => 'Green'], ['hex' => 'F78166', 'label' => 'Orange']]],
                ['name' => 'Vercel', 'colors' => [['hex' => '000000', 'label' => 'Black'], ['hex' => 'FFFFFF', 'label' => 'White']]],
                ['name' => 'PayPal', 'colors' => [['hex' => '003087', 'label' => 'Dark Blue'], ['hex' => '009CDE', 'label' => 'Light Blue']]],
                ['name' => 'IBM', 'colors' => [['hex' => '052FAD', 'label' => 'IBM Blue']]],
            ];
            foreach ($brands as $brand):
            ?>
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 hover:shadow-lg transition-shadow">
                <h3 class="font-bold text-base mb-3 text-slate-800 dark:text-white"><?= $brand['name'] ?></h3>
                <div class="space-y-2">
                    <?php foreach ($brand['colors'] as $color):
                        $hexUpper = strtoupper($color['hex']);
                        $colorSlug = strtolower($color['hex']);
                    ?>
                    <a href="<?= $base ?>/color/<?= $colorSlug ?>/" class="flex items-center gap-3 group">
                        <span class="w-10 h-10 rounded-lg shadow-sm shrink-0 border border-slate-100 dark:border-slate-700" style="background:#<?= $hexUpper ?>"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= $color['label'] ?></p>
                            <p class="font-mono font-bold text-sm">#<?= $hexUpper ?></p>
                        </div>
                        <i class="bi bi-arrow-up-right text-slate-300 dark:text-slate-600 group-hover:text-primary transition-colors"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- FAQ Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Frequently Asked Questions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What colors do popular brands use?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Most tech brands use blue for trust (Google #4285F4, Meta #0668E1, LinkedIn #0A66C2). Red is popular for energy and excitement (Netflix #E50914, YouTube #FF0000, Adobe #FF0000). Green represents growth (Spotify #1DB954, Shopify #7AB55C). Black conveys premium quality (Apple #000000, Uber #000000).</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">Where to find brand color codes?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">This page provides a curated collection of brand color hex codes. You can also find official brand guidelines on each company's press/brand page. Click any color above to see its full RGB, HSL, and palette details on its dedicated color page.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What is Google's brand color?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Google uses four brand colors: Blue (#4285F4), Red (#EA4335), Yellow (#FBBC05), and Green (#34A853). These are used across their logo, products, and marketing materials. The blue (#4285F4) is the most recognizable and is used for the Google "G" logomark.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How to use brand colors in my design?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Brand colors can inspire your own palette. Use them as a starting point — pick a primary color similar to the brand you admire, then use our <a href="<?= $base ?>/generate-palette" class="text-primary hover:underline">Palette Generator</a> to create harmonious schemes. Be careful not to copy trademarked colors directly for commercial use.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">Are brand colors trademarked?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Yes, brand colors can be trademarked in certain contexts. While you can use hex codes for personal projects and inspiration, using a competitor's exact brand colors in commercial work may cause legal issues. Always consult brand guidelines and legal counsel for commercial applications.</p>
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
                <a href="<?= $base ?>/palettes" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-palette text-primary text-lg"></i>
                    <span class="text-sm font-semibold">Explore Palettes</span>
                </a>
                <a href="<?= $base ?>/hex-to-color-name" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-tag text-secondary text-lg"></i>
                    <span class="text-sm font-semibold">Hex to Color Name</span>
                </a>
                <a href="<?= $base ?>/what-color-is" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-question-circle text-amber-500 text-lg"></i>
                    <span class="text-sm font-semibold">What Color Is This?</span>
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
                    "name": "What colors do popular brands use?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Most tech brands use blue for trust (Google #4285F4, Meta #0668E1). Red is popular for energy (Netflix #E50914, YouTube #FF0000). Green represents growth (Spotify #1DB954). Black conveys premium quality (Apple #000000)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Where to find brand color codes?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "This page provides a curated collection of brand color hex codes. You can also find official brand guidelines on each company's press or brand page."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What is Google's brand color?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Google uses four brand colors: Blue (#4285F4), Red (#EA4335), Yellow (#FBBC05), and Green (#34A853)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How to use brand colors in my design?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Brand colors can inspire your own palette. Pick a primary color similar to the brand you admire, then use a palette generator to create harmonious schemes."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Are brand colors trademarked?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, brand colors can be trademarked in certain contexts. You can use hex codes for personal projects and inspiration, but using exact brand colors commercially may cause legal issues."
                    }
                }
            ]
        }
        </script>
    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "brand-color-lookup";
    </script>
</body>

</html>
