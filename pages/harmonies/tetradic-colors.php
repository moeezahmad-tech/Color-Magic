<?php
require_once __DIR__ . '/../../components/config.php';
/** @var string $base */

$canonicalUrl = 'https://colormagic.techkreative.com/tetradic-colors';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Tetradic Colors: Guide, Formula &amp; Palette Generator | Color Magic</title>
    <meta name="description"
        content="Master tetradic (double-complementary rectangle) color harmony. Learn how to balance four vibrant hues across two complementary pairs with our interactive tool." />
    <meta name="keywords"
        content="tetradic colors, tetradic color palette, double complementary colors, rectangle color harmony, four color scheme, UI color balance, color theory" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Tetradic Colors: The Complete Color Theory Guide &amp; Generator" />
    <meta property="og:description"
        content="Learn how double-complementary 4-color harmonies bring rich, multifaceted balance to complex products." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>

    <!-- Schema.org Article & FAQPage JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "TechArticle",
          "headline": "Tetradic Colors: Guide, Formula & UI Design Rules",
          "description": "Learn the theory, formula, and orchestration rules for tetradic (double-complementary) color schemes in complex interfaces and digital applications.",
          "author": {
            "@type": "Organization",
            "name": "Color Magic"
          },
          "publisher": {
            "@type": "Organization",
            "name": "Color Magic",
            "logo": {
              "@type": "ImageObject",
              "url": "https://colormagic.techkreative.com/assets/images/logo.png"
            }
          },
          "mainEntityOfPage": "<?= $canonicalUrl ?>"
        },
        {
          "@type": "FAQPage",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "What is a tetradic color scheme?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "A tetradic (or double-complementary) scheme uses four colors arranged into two complementary pairs on the color wheel, creating a rectangular or square geometric relationship."
              }
            },
            {
              "@type": "Question",
              "name": "Why is tetradic harmony challenging to use in UI design?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Because four vivid colors compete for human visual attention. If used in equal amounts, the layout will look chaotic or aggressive. One color must always be chosen as dominant, while the other three serve strictly functional accent roles."
              }
            },
            {
              "@type": "Question",
              "name": "What is the formula for calculating a rectangular tetradic palette?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Given a base hue H: Color 1 = H, Color 2 = (H + 60) % 360, Color 3 = (H + 180) % 360, and Color 4 = (H + 240) % 360."
              }
            }
          ]
        }
      ]
    }
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen flex flex-col">
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <main class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 flex-1">
        
        <!-- Hero Header -->
        <div class="mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 text-xs font-bold mb-3">
                <i class="bi bi-bounding-box-circles"></i>
                <span>Double-Complementary Harmony</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Tetradic Colors
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-3xl leading-relaxed">
                Four colors arranged into two complementary pairs across the color wheel. Delivers rich multifaceted variety, dynamic depth, and clear categorical distinction for complex interfaces.
            </p>
        </div>

        <!-- Interactive Tetradic Calculator Tool -->
        <section class="bg-white dark:bg-card-dark rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800/80 mb-14" aria-labelledby="calc-heading">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 id="calc-heading" class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-palette2 text-primary"></i>
                        <span>Live Tetradic Rectangle Calculator</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Select a base hue to calculate its 4-color rectangular harmony: <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">H</code>, <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 60°)</code>, <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 180°)</code> &amp; <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 240°)</code>.
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="baseHexPicker" class="relative flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-primary transition-colors">
                        <input type="color" id="baseHexPicker" value="#EC4899" class="w-7 h-7 rounded-lg border-0 cursor-pointer bg-transparent" />
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pick Hue</span>
                    </label>
                    <div class="flex items-center bg-slate-50 dark:bg-slate-800 rounded-xl px-3 py-2 border border-slate-200 dark:border-slate-700 flex-1 sm:flex-initial">
                        <span class="text-slate-400 text-xs font-mono mr-1">#</span>
                        <input type="text" id="baseHexInput" value="EC4899" maxlength="6" class="bg-transparent border-0 p-0 text-xs font-mono font-bold uppercase text-slate-900 dark:text-white w-20 focus:ring-0 focus:outline-none" />
                    </div>
                </div>
            </div>

            <!-- Visual 4-Color Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Color 1: Base -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="tetra1Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #EC4899;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">
                            Pair A1 (H)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Dominant</span>
                            <span id="tetra1HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#EC4899</span>
                        </div>
                        <button onclick="copyHex('tetra1HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Color 2: Offset -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="tetra2Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #8B5CF6;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">
                            Pair B1 (+60°)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Secondary</span>
                            <span id="tetra2HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#8B5CF6</span>
                        </div>
                        <button onclick="copyHex('tetra2HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Color 3: Complement 1 -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="tetra3Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #48EC9B;">
                        <span class="bg-black/40 backdrop-blur-md text-slate-900 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase bg-white/70">
                            Pair A2 (+180°)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Accent A</span>
                            <span id="tetra3HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#48EC9B</span>
                        </div>
                        <button onclick="copyHex('tetra3HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Color 4: Complement 2 -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="tetra4Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #C7EC48;">
                        <span class="bg-black/40 backdrop-blur-md text-slate-900 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase bg-white/70">
                            Pair B2 (+240°)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Accent B</span>
                            <span id="tetra4HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#C7EC48</span>
                        </div>
                        <button onclick="copyHex('tetra4HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 dark:text-slate-400">
                    <i class="bi bi-info-circle mr-1"></i>
                    Balance two warm colors and two cool colors to prevent visual competition.
                </span>
                <a id="openInStudioBtn" href="<?= $base ?>/generate-palette?colors=EC4899,8B5CF6,48EC9B,C7EC48" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold transition-colors shadow-sm shadow-primary/20">
                    <span>Export to Palette Studio</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- Color Theory Deep-Dive -->
        <article class="prose prose-slate dark:prose-invert max-w-none mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="bi bi-book text-primary"></i>
                <span>Taming the Tetrad: Rules for Multi-Hue Harmony</span>
            </h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-base">
                Tetradic color harmonies connect four hues situated at the vertices of a rectangle or square across the color wheel. 
                Because it encompasses two distinct pairs of complementary colors, the system contains both natural tension and balanced opposition.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 not-prose my-8">
                <div class="p-6 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg mb-3">
                        <i class="bi bi-crown"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Crown One Dominant King</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        If all four colors are given equal visual prominence (25% each), the user's gaze won't know where to land. Establish one hue for primary brand identity, using the other three as discrete contextual accents.
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold text-lg mb-3">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Ideal for Data Visualization</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Tetradic harmonies excel in complex dashboards and multi-series charts. Because each of the 4 hues occupies distinct spectral territory, users instantly distinguish between multiple metrics without visual ambiguity.
                    </p>
                </div>
            </div>
        </article>

        <!-- 4 Curated Tetradic Palettes -->
        <section class="mb-16" aria-labelledby="palettes-heading">
            <h2 id="palettes-heading" class="text-2xl font-black text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <i class="bi bi-collection text-primary"></i>
                <span>4 Curated Tetradic Palettes</span>
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">
                Professional four-color configurations engineered for data visualization, festive events, and multi-category retail.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Palette 1: Google Spectrum -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#4285F4]" title="#4285F4"></div>
                        <div class="flex-1 bg-[#EA4335]" title="#EA4335"></div>
                        <div class="flex-1 bg-[#FBBC05]" title="#FBBC05"></div>
                        <div class="flex-1 bg-[#34A853]" title="#34A853"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Tech Ecosystem Quad</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Blue, Red, Yellow &amp; Green Data Series</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=4285F4,EA4335,FBBC05,34A853" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 2: Tropical Sunset Reef -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#0284C7]" title="#0284C7"></div>
                        <div class="flex-1 bg-[#F97316]" title="#F97316"></div>
                        <div class="flex-1 bg-[#10B981]" title="#10B981"></div>
                        <div class="flex-1 bg-[#E11D48]" title="#E11D48"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Tropical Reef</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Sky, Tangerine, Mint &amp; Crimson</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=0284C7,F97316,10B981,E11D48" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 3: Neon Cyberpunk Arcade -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#06B6D4]" title="#06B6D4"></div>
                        <div class="flex-1 bg-[#A855F7]" title="#A855F7"></div>
                        <div class="flex-1 bg-[#FACC15]" title="#FACC15"></div>
                        <div class="flex-1 bg-[#F43F5E]" title="#F43F5E"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Neon Arcade</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Cyan, Purple, Electric Yellow &amp; Rose</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=06B6D4,A855F7,FACC15,F43F5E" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 4: Autumn Harvest -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#1E3A8A]" title="#1E3A8A"></div>
                        <div class="flex-1 bg-[#B45309]" title="#B45309"></div>
                        <div class="flex-1 bg-[#047857]" title="#047857"></div>
                        <div class="flex-1 bg-[#991B1B]" title="#991B1B"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Autumn Estate</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Oxford Navy, Amber, British Green &amp; Burgundy</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=1E3A8A,B45309,047857,991B1B" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Frequently Asked Questions Accordion -->
        <section class="mb-16" aria-labelledby="faq-heading">
            <h2 id="faq-heading" class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="bi bi-question-circle text-primary"></i>
                <span>Frequently Asked Questions</span>
            </h2>

            <div class="space-y-4">
                <details class="group bg-white dark:bg-card-dark rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-5 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 dark:text-white text-sm sm:text-base">
                        <span>What is the difference between rectangular and square tetradic?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        In a square tetradic scheme, all four colors are separated by equal 90-degree angles around the wheel. In a rectangular tetradic scheme, two colors are paired closely (e.g. 60 degrees apart) and matched across the wheel by their direct complements, giving you closer control over warm and cool pairings.
                    </p>
                </details>

                <details class="group bg-white dark:bg-card-dark rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-5 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 dark:text-white text-sm sm:text-base">
                        <span>How can I keep tetradic palettes accessible according to WCAG?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Never place one of the tetradic colors directly as text on another tetradic background. Always use neutral backgrounds (such as slate dark #0f172a or light gray #f8fafc) and use the four hues for outlines, badges, icons, and chart lines.
                    </p>
                </details>
            </div>
        </section>

        <!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
        <?php
        $currentHarmony = 'tetradic';
        include __DIR__ . '/../../components/explore-harmonies.php';
        ?>

    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Interactive Tetradic Calculator Script -->
    <script>
        function hexToRgb(hex) {
            var bigint = parseInt(hex, 16);
            return {
                r: (bigint >> 16) & 255,
                g: (bigint >> 8) & 255,
                b: bigint & 255
            };
        }

        function rgbToHsl(r, g, b) {
            r /= 255; g /= 255; b /= 255;
            var max = Math.max(r, g, b), min = Math.min(r, g, b);
            var h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                var d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return { h: Math.round(h * 360), s: s, l: l };
        }

        function hslToRgb(h, s, l) {
            h = (h % 360 + 360) % 360;
            h /= 360;
            var r, g, b;

            if (s === 0) {
                r = g = b = l;
            } else {
                function hue2rgb(p, q, t) {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1/6) return p + (q - p) * 6 * t;
                    if (t < 1/2) return q;
                    if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                    return p;
                }
                var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                var p = 2 * l - q;
                r = hue2rgb(p, q, h + 1/3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1/3);
            }
            return {
                r: Math.round(r * 255),
                g: Math.round(g * 255),
                b: Math.round(b * 255)
            };
        }

        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(x => {
                var hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('').toUpperCase();
        }

        function updateTetradic(hex) {
            hex = hex.replace('#', '');
            if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

            var rgb = hexToRgb(hex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            // H, H+60, H+180, H+240
            var t1H = hsl.h;
            var t2H = (hsl.h + 60) % 360;
            var t3H = (hsl.h + 180) % 360;
            var t4H = (hsl.h + 240) % 360;

            var c1 = '#' + hex.toUpperCase();
            var c2 = rgbToHex(...Object.values(hslToRgb(t2H, hsl.s, hsl.l)));
            var c3 = rgbToHex(...Object.values(hslToRgb(t3H, hsl.s, hsl.l)));
            var c4 = rgbToHex(...Object.values(hslToRgb(t4H, hsl.s, hsl.l)));

            // Update inputs
            document.getElementById('baseHexPicker').value = c1.toLowerCase();
            document.getElementById('baseHexInput').value = hex.toUpperCase();

            // Update Cards
            document.getElementById('tetra1Card').style.backgroundColor = c1;
            document.getElementById('tetra1HexVal').textContent = c1;

            document.getElementById('tetra2Card').style.backgroundColor = c2;
            document.getElementById('tetra2HexVal').textContent = c2;

            document.getElementById('tetra3Card').style.backgroundColor = c3;
            document.getElementById('tetra3HexVal').textContent = c3;

            document.getElementById('tetra4Card').style.backgroundColor = c4;
            document.getElementById('tetra4HexVal').textContent = c4;

            // Update studio link
            var studioBtn = document.getElementById('openInStudioBtn');
            if (studioBtn) {
                studioBtn.href = '<?= $base ?>/generate-palette?colors=' + [c1, c2, c3, c4].map(c => c.replace('#', '')).join(',');
            }
        }

        document.getElementById('baseHexPicker').addEventListener('input', function(e) {
            updateTetradic(e.target.value);
        });

        document.getElementById('baseHexInput').addEventListener('input', function(e) {
            var val = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            if (val.length === 6) {
                updateTetradic(val);
            }
        });

        function copyHex(elementId) {
            var text = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied ' + text + ' to clipboard!');
            });
        }
    </script>
</body>

</html>

