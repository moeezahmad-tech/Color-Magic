<?php
require_once __DIR__ . '/../../components/config.php';
/** @var string $base */

$canonicalUrl = 'https://colormagic.techkreative.com/triadic-colors';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Triadic Colors: Guide, Formula &amp; Palette Generator | Color Magic</title>
    <meta name="description"
        content="Master triadic color harmony (120° equilateral triangle). Discover how three balanced, equidistant hues create vibrant, dynamic, and high-energy designs with our interactive tool." />
    <meta name="keywords"
        content="triadic colors, triadic color palette, 120 degree color harmony, equilateral color wheel, vibrant color scheme, color theory, UI design" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Triadic Colors: The Complete Color Theory Guide &amp; Generator" />
    <meta property="og:description"
        content="Learn how 120° equidistant color harmonies produce vibrant, dynamic, and balanced digital palettes." />
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
          "headline": "Triadic Colors: Guide, Formula & UI Design Rules",
          "description": "Learn the theory, formula, and design best practices for triadic color schemes in digital interfaces and brand identity.",
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
              "name": "What are triadic colors?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Triadic colors are three colors evenly spaced around the 360-degree color wheel, separated by exactly 120 degrees. They form an equilateral triangle on the color wheel, offering high vibrance while retaining optical balance."
              }
            },
            {
              "@type": "Question",
              "name": "How do you calculate a triadic color scheme?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Convert your primary color to HSL (Hue, Saturation, Lightness). Add 120 degrees to find the second hue, and add 240 degrees (or subtract 120) to find the third hue: H1 = Base, H2 = (Base + 120) % 360, H3 = (Base + 240) % 360."
              }
            },
            {
              "@type": "Question",
              "name": "How do you prevent a triadic palette from looking overwhelming?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Follow the 60-30-10 or 70-20-10 distribution rule. Let one color dominate the visual hierarchy (e.g. 70%), use the second to highlight secondary elements (20%), and reserve the third for high-priority calls to action or micro-interactions (10%)."
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-bold mb-3">
                <i class="bi bi-triangle"></i>
                <span>120° Triangle Harmony</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Triadic Colors
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-3xl leading-relaxed">
                Three equidistant hues spaced 120° apart on the color wheel. Delivers lively contrast and dynamic energy while preserving optical stability for modern digital interfaces.
            </p>
        </div>

        <!-- Interactive Triadic Calculator Tool -->
        <section class="bg-white dark:bg-card-dark rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800/80 mb-14" aria-labelledby="calc-heading">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 id="calc-heading" class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-palette2 text-primary"></i>
                        <span>Live Triadic Formula Calculator</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Select any base color to automatically calculate its two 120° equidistant companion hues: <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 120°) % 360</code> &amp; <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 240°) % 360</code>.
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="baseHexPicker" class="relative flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-primary transition-colors">
                        <input type="color" id="baseHexPicker" value="#2563EB" class="w-7 h-7 rounded-lg border-0 cursor-pointer bg-transparent" />
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pick Hue</span>
                    </label>
                    <div class="flex items-center bg-slate-50 dark:bg-slate-800 rounded-xl px-3 py-2 border border-slate-200 dark:border-slate-700 flex-1 sm:flex-initial">
                        <span class="text-slate-400 text-xs font-mono mr-1">#</span>
                        <input type="text" id="baseHexInput" value="2563EB" maxlength="6" class="bg-transparent border-0 p-0 text-xs font-mono font-bold uppercase text-slate-900 dark:text-white w-20 focus:ring-0 focus:outline-none" />
                    </div>
                </div>
            </div>

            <!-- Visual Preview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Base Color -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="baseCard" class="h-32 transition-colors duration-300 relative flex items-end p-3" style="background-color: #2563EB;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Base Hue (H)
                        </span>
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block uppercase">Primary Anchor</span>
                            <span id="baseHexVal" class="text-base font-bold font-mono text-slate-900 dark:text-white">#2563EB</span>
                        </div>
                        <button onclick="copyHex('baseHexVal')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Triad 2 (H + 120°) -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="triad1Card" class="h-32 transition-colors duration-300 relative flex items-end p-3" style="background-color: #EB2563;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Triad 2 (H + 120°)
                        </span>
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block uppercase">Supporting Harmony</span>
                            <span id="triad1HexVal" class="text-base font-bold font-mono text-slate-900 dark:text-white">#EB2563</span>
                        </div>
                        <button onclick="copyHex('triad1HexVal')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Triad 3 (H + 240°) -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="triad2Card" class="h-32 transition-colors duration-300 relative flex items-end p-3" style="background-color: #63EB25;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Triad 3 (H + 240°)
                        </span>
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block uppercase">High-Impact Accent</span>
                            <span id="triad2HexVal" class="text-base font-bold font-mono text-slate-900 dark:text-white">#63EB25</span>
                        </div>
                        <button onclick="copyHex('triad2HexVal')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 dark:text-slate-400">
                    <i class="bi bi-info-circle mr-1"></i>
                    Tip: Keep one hue dominant and mute the saturation of one secondary tone for balanced UI.
                </span>
                <a id="openInStudioBtn" href="<?= $base ?>/generate-palette?colors=2563EB,EB2563,63EB25" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold transition-colors shadow-sm shadow-primary/20">
                    <span>Export to Palette Studio</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- Color Theory Deep-Dive -->
        <article class="prose prose-slate dark:prose-invert max-w-none mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="bi bi-book text-primary"></i>
                <span>The Geometry &amp; Psychology of Triadic Harmony</span>
            </h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-base">
                Triadic color palettes are constructed by selecting three hues positioned at precisely 120-degree intervals across the 360-degree color circle. 
                Because the three points form an equilateral triangle spanning both warm and cool spectrum zones, a triadic palette inherently captures chromatic balance.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 not-prose my-8">
                <div class="p-6 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg mb-3">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Vibrant Visual Balance</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Unlike complementary pairs which can clash violently when used in equal measure, triadic schemes provide natural relief because each color counterweights the other two. They feel spirited, youthful, and energetic.
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg mb-3">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">The 70-20-10 UI Hierarchy Rule</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        To master triadic design in apps and websites, never allocate colors equally. Dedicate 70% of visual weight to one dominant anchor (e.g. Navy), 20% to the second hue (cards, navigation), and 10% to the third as an accent CTA button.
                    </p>
                </div>
            </div>
        </article>

        <!-- 4 Curated Real-World Triadic Palettes -->
        <section class="mb-16" aria-labelledby="palettes-heading">
            <h2 id="palettes-heading" class="text-2xl font-black text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <i class="bi bi-collection text-primary"></i>
                <span>4 Curated Triadic Palettes for Digital Products</span>
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">
                Hand-picked triadic schemes tailored for software, mobile dashboards, creative brands, and e-commerce.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Palette 1: Comic & Gaming Pop -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#2563EB] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#2563EB')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#2563EB</span>
                        </div>
                        <div class="flex-1 bg-[#DC2626] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#DC2626')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#DC2626</span>
                        </div>
                        <div class="flex-1 bg-[#EAB308] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#EAB308')">
                            <span class="text-[10px] font-mono text-slate-900 opacity-0 group-hover:opacity-100 transition-opacity bg-white/70 px-1 rounded">#EAB308</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Primary Heroic Trio</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Classic Blue, Crimson Red &amp; Golden Sun</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=2563EB,DC2626,EAB308" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 2: Modern SaaS Dashboard -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#6366F1] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#6366F1')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#6366F1</span>
                        </div>
                        <div class="flex-1 bg-[#10B981] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#10B981')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#10B981</span>
                        </div>
                        <div class="flex-1 bg-[#F59E0B] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#F59E0B')">
                            <span class="text-[10px] font-mono text-slate-900 opacity-0 group-hover:opacity-100 transition-opacity bg-white/70 px-1 rounded">#F59E0B</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Fintech Analytics</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Indigo Core, Emerald Profit &amp; Amber Warning</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=6366F1,10B981,F59E0B" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 3: Cyberpunk Neon -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#06B6D4] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#06B6D4')">
                            <span class="text-[10px] font-mono text-slate-900 opacity-0 group-hover:opacity-100 transition-opacity bg-white/70 px-1 rounded">#06B6D4</span>
                        </div>
                        <div class="flex-1 bg-[#D946EF] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#D946EF')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#D946EF</span>
                        </div>
                        <div class="flex-1 bg-[#FACC15] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#FACC15')">
                            <span class="text-[10px] font-mono text-slate-900 opacity-0 group-hover:opacity-100 transition-opacity bg-white/70 px-1 rounded">#FACC15</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Cyber Synthwave</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Cyan Glow, Magenta Accent &amp; Yellow Highlight</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=06B6D4,D946EF,FACC15" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 4: Organic Botanical Trio -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#059669] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#059669')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#059669</span>
                        </div>
                        <div class="flex-1 bg-[#EA580C] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#EA580C')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#EA580C</span>
                        </div>
                        <div class="flex-1 bg-[#7C3AED] relative group flex items-end justify-center pb-2 cursor-pointer" onclick="navigator.clipboard.writeText('#7C3AED')">
                            <span class="text-[10px] font-mono text-white opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 px-1 rounded">#7C3AED</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Botanical Heritage</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Forest Sage, Burnt Terracotta &amp; Royal Violet</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=059669,EA580C,7C3AED" class="text-xs font-bold text-primary hover:underline">
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
                        <span>How does a triadic scheme differ from split-complementary?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        In a triadic scheme, all three colors are separated by equal 120° distances on the wheel. In a split-complementary scheme, the two supporting colors sit adjacent to the true complement (150° and 210° from the base), creating a narrower, less aggressive contrast angle.
                    </p>
                </details>

                <details class="group bg-white dark:bg-card-dark rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-5 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 dark:text-white text-sm sm:text-base">
                        <span>Can I use triadic colors for accessible text and buttons?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Yes, provided you test WCAG contrast ratios. Placing triadic colors directly on top of one another (e.g. yellow text on a blue card) often causes optical vibration unless lightness values differ by at least 4.5:1. Always pair hues with neutral dark or light backgrounds.
                    </p>
                </details>

                <details class="group bg-white dark:bg-card-dark rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-5 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 dark:text-white text-sm sm:text-base">
                        <span>What brands famously use triadic palettes?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Iconic brands including Burger King, Superman, eBay, and Google prominently feature primary or secondary triadic relationships to communicate friendliness, approachability, and high brand recall.
                    </p>
                </details>
            </div>
        </section>

        <!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
        <?php
        $currentHarmony = 'triadic';
        include __DIR__ . '/../../components/explore-harmonies.php';
        ?>

    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Interactive Triadic Calculator Script -->
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

        function updateTriadic(hex) {
            hex = hex.replace('#', '');
            if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

            var rgb = hexToRgb(hex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            // 120° equidistant points
            var triad1H = (hsl.h + 120) % 360;
            var triad2H = (hsl.h + 240) % 360;

            var triad1Rgb = hslToRgb(triad1H, hsl.s, hsl.l);
            var triad1Hex = rgbToHex(triad1Rgb.r, triad1Rgb.g, triad1Rgb.b);

            var triad2Rgb = hslToRgb(triad2H, hsl.s, hsl.l);
            var triad2Hex = rgbToHex(triad2Rgb.r, triad2Rgb.g, triad2Rgb.b);

            var fullBaseHex = '#' + hex.toUpperCase();
            
            // Update Base Card
            document.getElementById('baseCard').style.backgroundColor = fullBaseHex;
            document.getElementById('baseHexVal').textContent = fullBaseHex;
            document.getElementById('baseHexPicker').value = fullBaseHex.toLowerCase();
            document.getElementById('baseHexInput').value = hex.toUpperCase();

            // Update Triad 1 Card
            document.getElementById('triad1Card').style.backgroundColor = triad1Hex;
            document.getElementById('triad1HexVal').textContent = triad1Hex;

            // Update Triad 2 Card
            document.getElementById('triad2Card').style.backgroundColor = triad2Hex;
            document.getElementById('triad2HexVal').textContent = triad2Hex;

            // Update studio link
            var studioBtn = document.getElementById('openInStudioBtn');
            if (studioBtn) {
                studioBtn.href = '<?= $base ?>/generate-palette?colors=' + hex.toUpperCase() + ',' + triad1Hex.replace('#', '') + ',' + triad2Hex.replace('#', '');
            }
        }

        document.getElementById('baseHexPicker').addEventListener('input', function(e) {
            updateTriadic(e.target.value);
        });

        document.getElementById('baseHexInput').addEventListener('input', function(e) {
            var val = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            if (val.length === 6) {
                updateTriadic(val);
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

