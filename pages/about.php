<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>About Color Magic — Modern Color Discovery &amp; Palettes | Color Magic</title>
    <meta name="description"
        content="Learn about Color Magic by TechKreative. Our mission is to make color theory, 30,000+ color names, and aesthetic palette generation free and accessible for designers and developers." />
    <meta name="keywords"
        content="about color magic, color theory, color tool, TechKreative, palette generator, color name finder, hex codes" />
    <meta name="author" content="TechKreative" />
    <meta name="robots" content="index, follow" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag("js", new Date());
        gtag("config", "G-537L4MR968");
    </script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/about" />
    <meta property="og:title" content="About Color Magic — The Open-Source Color Platform" />
    <meta property="og:description"
        content="Explore the mission, design principles, and technology behind Color Magic by TechKreative." />
    <meta property="og:image" content="<?= $base ?>/assets/images/logo.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/about" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen selection:bg-primary/20 selection:text-primary">
    <!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
    <?php include '../components/navbar.php'; ?>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-primary text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="bi bi-magic"></i>
                <span>About Color Magic</span>
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6">
                Where Science Meets <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-secondary via-pink-500 to-primary bg-clip-text text-transparent">Digital Color Beauty</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-lg sm:text-xl max-w-2xl mx-auto leading-relaxed">
                Color Magic is a modern, open-source color discovery suite engineered by <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer" class="text-primary font-semibold hover:underline">TechKreative</a> to turn hex codes into vivid human stories.
            </p>
        </div>

        <!-- Metrics / Highlights Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-16">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 text-center shadow-sm">
                <div class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-pink-500 to-purple-600 bg-clip-text text-transparent mb-1">30,000+</div>
                <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Named Colors</div>
            </div>
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 text-center shadow-sm">
                <div class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-purple-600 to-indigo-500 bg-clip-text text-transparent mb-1">150+</div>
                <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Curated Palettes</div>
            </div>
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 text-center shadow-sm">
                <div class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent mb-1">100%</div>
                <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Free &amp; Open Source</div>
            </div>
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 text-center shadow-sm">
                <div class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-amber-500 to-rose-500 bg-clip-text text-transparent mb-1">&lt; 50ms</div>
                <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Search Speeds</div>
            </div>
        </div>

        <!-- The Story & Mission -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-secondary mb-2">
                    <i class="bi bi-lightbulb-fill"></i>
                    <span>The Mission</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-4">
                    Why We Created Color Magic
                </h2>
                <div class="space-y-4 text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    <p>
                        Have you ever spotted the perfect shade of moody cyan or velvet crimson and wondered what it was called, how to reproduce it, or which harmonious colors pair with it?
                    </p>
                    <p>
                        Designers, developers, and digital illustrators often struggle with clunky color tools bogged down by paywalls, ads, or slow interfaces. We built Color Magic to solve this: an ultra-fast, visually stunning, and privacy-respecting color workspace.
                    </p>
                    <p>
                        With exact mathematical Delta-E Euclidean space matching and an expansive dataset of over 30,000 authentic color names, Color Magic gives every hex code an identity and a voice.
                    </p>
                </div>
            </div>

            <div class="rounded-3xl p-8 bg-gradient-to-br from-purple-500/10 via-pink-500/10 to-amber-500/10 border border-slate-200 dark:border-slate-800 space-y-4 shadow-sm">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-palette2 text-primary"></i>
                    Core Design Pillars
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-pink-100 dark:bg-pink-900/50 text-primary flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-check text-base"></i>
                        </div>
                        <div>
                            <strong class="text-slate-900 dark:text-white">Precision &amp; Accuracy:</strong>
                            <p class="text-slate-600 dark:text-slate-400 text-xs">RGB, HSL, HSV, CMYK conversions calculated with strict precision.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/50 text-secondary flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-check text-base"></i>
                        </div>
                        <div>
                            <strong class="text-slate-900 dark:text-white">Design Accessibility (WCAG):</strong>
                            <p class="text-slate-600 dark:text-slate-400 text-xs">Instant contrast checking against pure white and black text for web accessibility.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="bi bi-check text-base"></i>
                        </div>
                        <div>
                            <strong class="text-slate-900 dark:text-white">Open Source Philosophy:</strong>
                            <p class="text-slate-600 dark:text-slate-400 text-xs">Built transparently on GitHub for community contributions and learning.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- What You Can Do with Color Magic -->
        <div class="mb-16">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-3">
                    Explore the Ecosystem
                </h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-xl mx-auto text-sm sm:text-base">
                    From color name lookups to complete aesthetic schemes, Color Magic is your all-in-one studio.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="<?= $base ?>/find-color" class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all hover:shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-eyedropper"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 group-hover:text-primary transition-colors">Color Name Finder</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Match any 6-digit hex code with its closest human name from our 30,000+ directory.</p>
                </a>

                <a href="<?= $base ?>/generate-palette" class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-secondary/50 transition-all hover:shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 group-hover:text-secondary transition-colors">Palette Generator</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Create complementary, analogous, triadic, and monochromatic schemes in one click.</p>
                </a>

                <a href="<?= $base ?>/palette-from-image" class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-fuchsia-500/50 transition-all hover:shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-600 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-image"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 group-hover:text-fuchsia-500 transition-colors">Palette from Image</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Upload photos, illustrations, or posters to extract their dominant color harmonies.</p>
                </a>

                <a href="<?= $base ?>/palettes" class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-pink-500/50 transition-all hover:shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 group-hover:text-pink-500 transition-colors">150+ Curated Palettes</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Handcrafted color schemes designed for modern UI/UX, branding, and web products.</p>
                </a>

                <a href="<?= $base ?>/gradients" class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-amber-500/50 transition-all hover:shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-rainbow"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 group-hover:text-amber-500 transition-colors">CSS Gradients</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Lush multi-stop CSS gradient combinations ready for quick copy-paste into stylesheets.</p>
                </a>

                <a href="<?= $base ?>/brand-color-lookup" class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-blue-500/50 transition-all hover:shadow-lg group">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-award"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 group-hover:text-blue-500 transition-colors">Brand Color Lookup</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Official brand color codes for Google, Apple, Spotify, Netflix, Meta, and 25+ more.</p>
                </a>
            </div>
        </div>

        <!-- Creator & Community Callout -->
        <div class="rounded-3xl p-8 sm:p-10 bg-slate-900 text-white relative overflow-hidden shadow-xl border border-slate-800">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-pink-400 mb-2">Developed By TechKreative</div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">Join Our Creative Journey</h2>
                    <p class="text-slate-400 text-sm sm:text-base max-w-xl">
                        Follow Color Magic on Instagram and Pinterest for daily palette inspiration, or contribute directly to our open-source codebase on GitHub.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="https://www.pinterest.com/colormagic_techkreative/" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
                        <i class="bi bi-pinterest"></i>
                        <span>Pinterest</span>
                    </a>
                    <a href="https://www.instagram.com/colormagic.techkreative/" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90 text-white text-sm font-semibold transition">
                        <i class="bi bi-instagram"></i>
                        <span>Instagram</span>
                    </a>
                    <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition border border-slate-700">
                        <i class="bi bi-github"></i>
                        <span>GitHub</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
