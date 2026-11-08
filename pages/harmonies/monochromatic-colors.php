<?php
require_once __DIR__ . '/../../components/config.php';
/** @var string $base */

$canonicalUrl = 'https://colormagic.techkreative.com/monochromatic-colors';
?>
<!doctype html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Monochromatic Colors: Guide, Formula &amp; Palette Generator | Color Magic</title>
    <meta name="description"
        content="Master monochromatic color harmony. Discover how tints, shades, and tones of a single hue build elegant, cohesive, and accessible SaaS design systems." />
    <meta name="keywords"
        content="monochromatic colors, monochromatic color palette, tints and shades, single hue color scheme, UI design system, color theory, minimalist palette" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Monochromatic Colors: The Complete Color Theory Guide &amp; Generator" />
    <meta property="og:description"
        content="Learn how single-hue tints and shades create clean, sophisticated, and accessible digital products." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="<?= $canonicalUrl ?>" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
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
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" /></noscript>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>

    <!-- Schema.org Article & FAQPage JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "TechArticle",
          "headline": "Monochromatic Colors: Guide, Formula & Design System Hierarchy",
          "description": "Learn the theory, mathematics, and best practices for monochromatic color schemes in modern web interfaces and design systems.",
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
              "name": "What is a monochromatic color scheme?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "A monochromatic color scheme uses a single base hue extended across various lightness and saturation values—producing tints (adding white), shades (adding black), and tones (adding gray) without changing the underlying color angle."
              }
            },
            {
              "@type": "Question",
              "name": "Why do SaaS and tech apps prefer monochromatic palettes?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Monochromatic systems eliminate chromatic distraction. They allow users to focus on content, data, and workflows while establishing clear hierarchical contrast (such as 900 for headings, 500 for buttons, and 50 for surfaces)."
              }
            },
            {
              "@type": "Question",
              "name": "How do you ensure contrast accessibility in monochromatic schemes?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "By maintaining at least a 40% lightness delta between text and its background. For WCAG AA compliance (4.5:1 ratio), pair ultra-light tints (e.g. 90-95% lightness) with deep dark shades (10-25% lightness)."
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-bold mb-3">
                <i class="bi bi-layers"></i>
                <span>Single Hue Spectrum</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Monochromatic Colors
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-3xl leading-relaxed">
                Variations in lightness and saturation derived from a single hue. Guarantees cohesive design systems, elegant minimalism, and accessible contrast hierarchies.
            </p>
        </div>

        <!-- Interactive Monochromatic Calculator Tool -->
        <section class="bg-white dark:bg-card-dark rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800/80 mb-14" aria-labelledby="calc-heading">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 id="calc-heading" class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-palette2 text-primary"></i>
                        <span>Live Monochromatic Scale Generator</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Select any base hue to generate a 5-step UI scale from deep shade to ultra-light tint while keeping the base angle constant.
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="baseHexPicker" class="relative flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-primary transition-colors">
                        <input type="color" id="baseHexPicker" value="#4F46E5" class="w-7 h-7 rounded-lg border-0 cursor-pointer bg-transparent" />
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pick Hue</span>
                    </label>
                    <div class="flex items-center bg-slate-50 dark:bg-slate-800 rounded-xl px-3 py-2 border border-slate-200 dark:border-slate-700 flex-1 sm:flex-initial">
                        <span class="text-slate-400 text-xs font-mono mr-1">#</span>
                        <input type="text" id="baseHexInput" value="4F46E5" maxlength="6" class="bg-transparent border-0 p-0 text-xs font-mono font-bold uppercase text-slate-900 dark:text-white w-20 focus:ring-0 focus:outline-none" />
                    </div>
                </div>
            </div>

            <!-- Visual 5-Step Scale -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                <!-- Step 1: Deep Shade -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="mono1Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #1E1B4B;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">
                            Shade (900)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Dark Value</span>
                            <span id="mono1HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#1E1B4B</span>
                        </div>
                        <button onclick="copyHex('mono1HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Medium Shade -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="mono2Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #3730A3;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">
                            Deep (700)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Mid-Dark</span>
                            <span id="mono2HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#3730A3</span>
                        </div>
                        <button onclick="copyHex('mono2HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Base Anchor -->
                <div class="rounded-xl overflow-hidden border-2 border-primary bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="monoBaseCard" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #4F46E5;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">
                            Base (500)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-primary font-bold block uppercase">Core Brand</span>
                            <span id="monoBaseHexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#4F46E5</span>
                        </div>
                        <button onclick="copyHex('monoBaseHexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Light Tint -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="mono3Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #A5B4FC;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">
                            Tint (300)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Soft Tint</span>
                            <span id="mono3HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#A5B4FC</span>
                        </div>
                        <button onclick="copyHex('mono3HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 5: Surface Tint -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col col-span-2 sm:col-span-1">
                    <div id="mono4Card" class="h-28 transition-colors duration-300 relative flex items-end p-2.5" style="background-color: #EEF2FF;">
                        <span class="bg-black/40 backdrop-blur-md text-slate-900 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase bg-white/70">
                            Surface (50)
                        </span>
                    </div>
                    <div class="p-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block uppercase">Background</span>
                            <span id="mono4HexVal" class="text-xs font-bold font-mono text-slate-900 dark:text-white">#EEF2FF</span>
                        </div>
                        <button onclick="copyHex('mono4HexVal')" class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 dark:text-slate-400">
                    <i class="bi bi-check-circle-fill text-emerald-500 mr-1"></i>
                    Pair the 900 shade with the 50 surface tint to guarantee WCAG AAA 7:1 contrast.
                </span>
                <a id="openInStudioBtn" href="<?= $base ?>/generate-palette?colors=1E1B4B,3730A3,4F46E5,A5B4FC,EEF2FF" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold transition-colors shadow-sm shadow-primary/20">
                    <span>Export to Palette Studio</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- Color Theory Deep-Dive -->
        <article class="prose prose-slate dark:prose-invert max-w-none mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="bi bi-book text-primary"></i>
                <span>Tints, Shades, and Tones: The Anatomy of a Single Hue</span>
            </h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-base">
                A monochromatic scheme is not just "one color"—it is the entire tonal universe of that color. 
                By manipulating brightness and purity, you provide visual variety without introducing conflicting color temperatures.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 not-prose my-8">
                <div class="p-5 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base mb-2">
                        <i class="bi bi-sun"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Tints (Hue + White)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Created by increasing lightness. Tints serve as soft card surfaces, table row alternating states, badge backgrounds, and subtle hover borders.
                    </p>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-base mb-2">
                        <i class="bi bi-circle-half"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Tones (Hue + Gray)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Formed by reducing saturation. Tones dial back vibrancy to create calming, neutral dividers, disabled buttons, and secondary captions.
                    </p>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-base mb-2">
                        <i class="bi bi-moon-stars"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Shades (Hue + Black)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Generated by lowering lightness. Shades provide authoritative typography, high-contrast headings, and foundational dark-mode containers.
                    </p>
                </div>
            </div>
        </article>

        <!-- 4 Curated Monochromatic Palettes -->
        <section class="mb-16" aria-labelledby="palettes-heading">
            <h2 id="palettes-heading" class="text-2xl font-black text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <i class="bi bi-collection text-primary"></i>
                <span>4 Curated Monochromatic Systems for Web &amp; Mobile</span>
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">
                Ready-to-use tonal progressions designed specifically for clean typography, user interfaces, and luxury branding.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Palette 1: Slate Executive -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#0F172A]" title="#0F172A"></div>
                        <div class="flex-1 bg-[#334155]" title="#334155"></div>
                        <div class="flex-1 bg-[#64748B]" title="#64748B"></div>
                        <div class="flex-1 bg-[#CBD5E1]" title="#CBD5E1"></div>
                        <div class="flex-1 bg-[#F8FAFC]" title="#F8FAFC"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Slate Architectural</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Neutral Blue-Gray for Developer Tools</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=0F172A,334155,64748B,CBD5E1,F8FAFC" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 2: Emerald Health -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#064E3B]" title="#064E3B"></div>
                        <div class="flex-1 bg-[#047857]" title="#047857"></div>
                        <div class="flex-1 bg-[#10B981]" title="#10B981"></div>
                        <div class="flex-1 bg-[#6EE7B7]" title="#6EE7B7"></div>
                        <div class="flex-1 bg-[#ECFDF5]" title="#ECFDF5"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Emerald Vitality</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Health, Wellness &amp; Sustainability Tech</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=064E3B,047857,10B981,6EE7B7,ECFDF5" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 3: Obsidian Noir -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#18181B]" title="#18181B"></div>
                        <div class="flex-1 bg-[#27272A]" title="#27272A"></div>
                        <div class="flex-1 bg-[#52525B]" title="#52525B"></div>
                        <div class="flex-1 bg-[#A1A1AA]" title="#A1A1AA"></div>
                        <div class="flex-1 bg-[#FAFAFA]" title="#FAFAFA"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Obsidian Minimal</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Monochrome Luxury &amp; Portfolio Typography</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=18181B,27272A,52525B,A1A1AA,FAFAFA" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 4: Rose Gold Velvet -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#881337]" title="#881337"></div>
                        <div class="flex-1 bg-[#BE123C]" title="#BE123C"></div>
                        <div class="flex-1 bg-[#F43F5E]" title="#F43F5E"></div>
                        <div class="flex-1 bg-[#FDA4AF]" title="#FDA4AF"></div>
                        <div class="flex-1 bg-[#FFF1F2]" title="#FFF1F2"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Crimson Romance</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Cosmetics, Lifestyle &amp; Premium Commerce</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=881337,BE123C,F43F5E,FDA4AF,FFF1F2" class="text-xs font-bold text-primary hover:underline">
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
                        <span>Is a monochromatic website boring?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Not when paired with high-contrast typography, rich textures, and bold white space. Apple, Stripe, and Linear heavily utilize monochromatic hierarchies to deliver high-end, clean aesthetics that emphasize clarity and photography over noisy background elements.
                    </p>
                </details>

                <details class="group bg-white dark:bg-card-dark rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-5 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 dark:text-white text-sm sm:text-base">
                        <span>How many shades should a monochromatic design system have?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Most modern UI frameworks (like Tailwind CSS) standardize on a 10-step scale (50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950). This provides dedicated slots for background surfaces, subtle borders, inactive icons, primary buttons, and deep readable text.
                    </p>
                </details>
            </div>
        </section>

        <!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
        <?php
        $currentHarmony = 'monochromatic';
        include __DIR__ . '/../../components/explore-harmonies.php';
        ?>

    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Interactive Monochromatic Calculator Script -->
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

        function updateMonochromatic(hex) {
            hex = hex.replace('#', '');
            if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

            var rgb = hexToRgb(hex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            // 5 lightness steps: 18% (900), 32% (700), Base hsl.l, 78% (300), 95% (50)
            var c1 = rgbToHex(...Object.values(hslToRgb(hsl.h, Math.min(1, hsl.s * 1.1), 0.18)));
            var c2 = rgbToHex(...Object.values(hslToRgb(hsl.h, hsl.s, 0.32)));
            var cBase = '#' + hex.toUpperCase();
            var c3 = rgbToHex(...Object.values(hslToRgb(hsl.h, hsl.s * 0.85, 0.78)));
            var c4 = rgbToHex(...Object.values(hslToRgb(hsl.h, hsl.s * 0.45, 0.95)));

            // Update inputs
            document.getElementById('baseHexPicker').value = cBase.toLowerCase();
            document.getElementById('baseHexInput').value = hex.toUpperCase();

            // Update Cards
            document.getElementById('mono1Card').style.backgroundColor = c1;
            document.getElementById('mono1HexVal').textContent = c1;

            document.getElementById('mono2Card').style.backgroundColor = c2;
            document.getElementById('mono2HexVal').textContent = c2;

            document.getElementById('monoBaseCard').style.backgroundColor = cBase;
            document.getElementById('monoBaseHexVal').textContent = cBase;

            document.getElementById('mono3Card').style.backgroundColor = c3;
            document.getElementById('mono3HexVal').textContent = c3;

            document.getElementById('mono4Card').style.backgroundColor = c4;
            document.getElementById('mono4HexVal').textContent = c4;

            // Update studio link
            var studioBtn = document.getElementById('openInStudioBtn');
            if (studioBtn) {
                studioBtn.href = '<?= $base ?>/generate-palette?colors=' + [c1, c2, cBase, c3, c4].map(c => c.replace('#', '')).join(',');
            }
        }

        document.getElementById('baseHexPicker').addEventListener('input', function(e) {
            updateMonochromatic(e.target.value);
        });

        document.getElementById('baseHexInput').addEventListener('input', function(e) {
            var val = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            if (val.length === 6) {
                updateMonochromatic(val);
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

