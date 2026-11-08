<?php
require_once __DIR__ . '/../../components/config.php';
/** @var string $base */

$canonicalUrl = 'https://colormagic.techkreative.com/split-complementary-colors';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Split-Complementary Colors: Guide, Formula &amp; Palette Generator | Color Magic</title>
    <meta name="description"
        content="Master split-complementary color harmony (150° and 210° flanking hues). Enjoy the high contrast of complementary pairs with lower visual friction and high versatility." />
    <meta name="keywords"
        content="split-complementary colors, split complementary palette, 150 210 degree color wheel, nuanced contrast, color harmony, UI design, color theory" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Split-Complementary Colors: The Complete Color Theory Guide &amp; Generator" />
    <meta property="og:description"
        content="Discover how flanking complementary colors provide dynamic contrast without optical vibration." />
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
          "headline": "Split-Complementary Colors: Guide, Formula & UI Design Rules",
          "description": "Learn the theory, formula, and best practices for split-complementary color schemes in modern web interfaces and digital marketing.",
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
              "name": "What are split-complementary colors?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "A split-complementary scheme consists of three colors: a base hue and the two hues directly adjacent to its opposite (complementary) point on the color wheel, specifically at 150 degrees and 210 degrees."
              }
            },
            {
              "@type": "Question",
              "name": "Why is split-complementary considered the most forgiving color scheme?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Direct complementary pairs (180°) can cause optical vibration or retinal fatigue when placed next to each other. By splitting the complement by 30° on either side, you preserve sharp visual distinction while smoothing harsh chromatic friction."
              }
            },
            {
              "@type": "Question",
              "name": "How do you calculate split-complementary colors?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Using HSL hue values: Base = H, Split 1 = (H + 150) % 360, Split 2 = (H + 210) % 360."
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/10 text-violet-600 dark:text-violet-400 text-xs font-bold mb-3">
                <i class="bi bi-diagram-3"></i>
                <span>150° + 210° Flanking Harmony</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Split-Complementary Colors
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-3xl leading-relaxed">
                A base hue paired with the two colors immediately flanking its direct complement (150° and 210°). Offers vibrant visual contrast with significantly softer tension than direct opposites.
            </p>
        </div>

        <!-- Interactive Calculator Tool -->
        <section class="bg-white dark:bg-card-dark rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800/80 mb-14" aria-labelledby="calc-heading">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 id="calc-heading" class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-palette2 text-primary"></i>
                        <span>Live Split-Complementary Calculator</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Select a base color to calculate its flanking split-complementary companions: <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 150°) % 360</code> &amp; <code class="bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs text-primary font-mono">(H + 210°) % 360</code>.
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label for="baseHexPicker" class="relative flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-primary transition-colors">
                        <input type="color" id="baseHexPicker" value="#8B5CF6" class="w-7 h-7 rounded-lg border-0 cursor-pointer bg-transparent" />
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pick Hue</span>
                    </label>
                    <div class="flex items-center bg-slate-50 dark:bg-slate-800 rounded-xl px-3 py-2 border border-slate-200 dark:border-slate-700 flex-1 sm:flex-initial">
                        <span class="text-slate-400 text-xs font-mono mr-1">#</span>
                        <input type="text" id="baseHexInput" value="8B5CF6" maxlength="6" class="bg-transparent border-0 p-0 text-xs font-mono font-bold uppercase text-slate-900 dark:text-white w-20 focus:ring-0 focus:outline-none" />
                    </div>
                </div>
            </div>

            <!-- Visual Preview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Base Color -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="baseCard" class="h-32 transition-colors duration-300 relative flex items-end p-3" style="background-color: #8B5CF6;">
                        <span class="bg-black/40 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                            Base Hue (H)
                        </span>
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block uppercase">Dominant Anchor</span>
                            <span id="baseHexVal" class="text-base font-bold font-mono text-slate-900 dark:text-white">#8B5CF6</span>
                        </div>
                        <button onclick="copyHex('baseHexVal')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Split 1 (H + 150°) -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="split1Card" class="h-32 transition-colors duration-300 relative flex items-end p-3" style="background-color: #F6D65C;">
                        <span class="bg-black/40 backdrop-blur-md text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider bg-white/70">
                            Split Flank 1 (H + 150°)
                        </span>
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block uppercase">Warm Contrast</span>
                            <span id="split1HexVal" class="text-base font-bold font-mono text-slate-900 dark:text-white">#F6D65C</span>
                        </div>
                        <button onclick="copyHex('split1HexVal')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Split 2 (H + 210°) -->
                <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 flex flex-col">
                    <div id="split2Card" class="h-32 transition-colors duration-300 relative flex items-end p-3" style="background-color: #5CF67C;">
                        <span class="bg-black/40 backdrop-blur-md text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider bg-white/70">
                            Split Flank 2 (H + 210°)
                        </span>
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block uppercase">Cool Accent</span>
                            <span id="split2HexVal" class="text-base font-bold font-mono text-slate-900 dark:text-white">#5CF67C</span>
                        </div>
                        <button onclick="copyHex('split2HexVal')" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-primary transition-colors" title="Copy HEX">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 dark:text-slate-400">
                    <i class="bi bi-info-circle mr-1"></i>
                    Split-complementary schemes provide high nuance and contrast with practically zero danger of discord.
                </span>
                <a id="openInStudioBtn" href="<?= $base ?>/generate-palette?colors=8B5CF6,F6D65C,5CF67C" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold transition-colors shadow-sm shadow-primary/20">
                    <span>Export to Palette Studio</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- Color Theory Deep-Dive -->
        <article class="prose prose-slate dark:prose-invert max-w-none mb-16">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <i class="bi bi-book text-primary"></i>
                <span>Why Designers Call Split-Complementary the "Safe Bet"</span>
            </h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-base">
                In color theory, the biggest difficulty with direct complementary schemes is their intensity. 
                Pure complementary pairs stimulate photoreceptor cones in opposite ways, causing rapid eye fatigue if saturated colors touch directly.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 not-prose my-8">
                <div class="p-6 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center font-bold text-lg mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">No Visual Jitter (Vibration)</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        By offsetting the opposing hue by 30 degrees to each side, the brain perceives contrast without the chromatic aberration or vibrating edge effect typical of direct red-on-green or blue-on-orange borders.
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-card-dark border border-slate-200/80 dark:border-slate-800/80">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg mb-3">
                        <i class="bi bi-palette"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Warm / Cool Temperature Dualism</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Because the two split colors sit adjacent to each other on the wheel, they share a temperature family (both warm or both cool), while contrasting together against your single dominant anchor.
                    </p>
                </div>
            </div>
        </article>

        <!-- 4 Curated Split-Complementary Palettes -->
        <section class="mb-16" aria-labelledby="palettes-heading">
            <h2 id="palettes-heading" class="text-2xl font-black text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                <i class="bi bi-collection text-primary"></i>
                <span>4 Curated Split-Complementary Palettes</span>
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">
                Tested color palettes balanced for mobile interfaces, creative web agencies, and packaging design.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Palette 1: Royal Amethyst -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#6D28D9]" title="#6D28D9"></div>
                        <div class="flex-1 bg-[#FBBF24]" title="#FBBF24"></div>
                        <div class="flex-1 bg-[#34D399]" title="#34D399"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Amethyst &amp; Citrine</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Deep Purple, Warm Ochre &amp; Mint Frost</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=6D28D9,FBBF24,34D399" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 2: Oceanic Coral -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#0284C7]" title="#0284C7"></div>
                        <div class="flex-1 bg-[#FB923C]" title="#FB923C"></div>
                        <div class="flex-1 bg-[#F43F5E]" title="#F43F5E"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Ocean Reef Sunset</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Cerulean Blue, Peach Orange &amp; Coral Rose</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=0284C7,FB923C,F43F5E" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 3: Forest Terracotta -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#15803D]" title="#15803D"></div>
                        <div class="flex-1 bg-[#E11D48]" title="#E11D48"></div>
                        <div class="flex-1 bg-[#9333EA]" title="#9333EA"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Forest Twilight</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pine Green, Berry Ruby &amp; Royal Violet</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=15803D,E11D48,9333EA" class="text-xs font-bold text-primary hover:underline">
                            Open &rarr;
                        </a>
                    </div>
                </div>

                <!-- Palette 4: Cyber Mustard -->
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex h-20 rounded-xl overflow-hidden mb-4 border border-slate-200/50 dark:border-slate-700/50">
                        <div class="flex-1 bg-[#CA8A04]" title="#CA8A04"></div>
                        <div class="flex-1 bg-[#3B82F6]" title="#3B82F6"></div>
                        <div class="flex-1 bg-[#8B5CF6]" title="#8B5CF6"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Industrial Mustard</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dijon Gold, Electric Blue &amp; Iris Violet</p>
                        </div>
                        <a href="<?= $base ?>/generate-palette?colors=CA8A04,3B82F6,8B5CF6" class="text-xs font-bold text-primary hover:underline">
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
                        <span>When should I choose split-complementary over direct complementary?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Choose split-complementary whenever you want the energetic pop of contrast without high visual risk. It gives you an extra hue to designate for secondary UI roles (such as separating secondary actions from notification badges).
                    </p>
                </details>

                <details class="group bg-white dark:bg-card-dark rounded-xl border border-slate-200/80 dark:border-slate-800/80 p-5 [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-900 dark:text-white text-sm sm:text-base">
                        <span>How do I assign roles to the 3 colors in a web layout?</span>
                        <i class="bi bi-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Designate your Base hue (H) as the brand identity (headers, active tabs, primary navigation). Use Flank 1 as your primary call-to-action button, and Flank 2 for informative accents, tags, badges, and chart metrics.
                    </p>
                </details>
            </div>
        </section>

        <!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
        <?php
        $currentHarmony = 'split-complementary';
        include __DIR__ . '/../../components/explore-harmonies.php';
        ?>

    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <!-- Interactive Split-Complementary Calculator Script -->
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

        function updateSplit(hex) {
            hex = hex.replace('#', '');
            if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

            var rgb = hexToRgb(hex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            // 150° and 210°
            var s1H = (hsl.h + 150) % 360;
            var s2H = (hsl.h + 210) % 360;

            var s1Rgb = hslToRgb(s1H, hsl.s, hsl.l);
            var s1Hex = rgbToHex(s1Rgb.r, s1Rgb.g, s1Rgb.b);

            var s2Rgb = hslToRgb(s2H, hsl.s, hsl.l);
            var s2Hex = rgbToHex(s2Rgb.r, s2Rgb.g, s2Rgb.b);

            var fullBaseHex = '#' + hex.toUpperCase();
            
            // Update Base Card
            document.getElementById('baseCard').style.backgroundColor = fullBaseHex;
            document.getElementById('baseHexVal').textContent = fullBaseHex;
            document.getElementById('baseHexPicker').value = fullBaseHex.toLowerCase();
            document.getElementById('baseHexInput').value = hex.toUpperCase();

            // Update Split Cards
            document.getElementById('split1Card').style.backgroundColor = s1Hex;
            document.getElementById('split1HexVal').textContent = s1Hex;

            document.getElementById('split2Card').style.backgroundColor = s2Hex;
            document.getElementById('split2HexVal').textContent = s2Hex;

            // Update studio link
            var studioBtn = document.getElementById('openInStudioBtn');
            if (studioBtn) {
                studioBtn.href = '<?= $base ?>/generate-palette?colors=' + hex.toUpperCase() + ',' + s1Hex.replace('#', '') + ',' + s2Hex.replace('#', '');
            }
        }

        document.getElementById('baseHexPicker').addEventListener('input', function(e) {
            updateSplit(e.target.value);
        });

        document.getElementById('baseHexInput').addEventListener('input', function(e) {
            var val = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            if (val.length === 6) {
                updateSplit(val);
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

