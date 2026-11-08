<?php
require_once __DIR__ . '/../../components/config.php';
/** @var string $base */

$canonicalUrl = 'https://colormagic.techkreative.com/analogous-colors';
?>
<!doctype html>
<html lang="en">

<head>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Analogous Colors: Guide, Formula &amp; Palette Generator | Color Magic</title>
    <meta name="description"
        content="Learn analogous color harmony (30° adjacent). Discover how neighboring color wheel hues build serene, organic, and cohesive user interfaces with interactive tools." />
    <meta name="keywords"
        content="analogous colors, analogous color palette, adjacent colors, 30 degrees color wheel, cohesive color scheme, color harmony, UI design, color theory" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Analogous Colors: The Complete Color Theory Guide &amp; Generator" />
    <meta property="og:description"
        content="Discover how adjacent colors build calm, natural, and visually unified digital designs." />
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
          "headline": "Analogous Colors: Guide, Formula & UI Design Rules",
          "description": "Learn the theory, formula, and best practices for analogous color schemes in digital design and web branding.",
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
              "name": "What are analogous colors?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Analogous colors are groups of three to four colors that sit immediately adjacent to one another on the 360-degree color wheel, separated by approximately 30 degrees (for example, green, teal, and blue)."
              }
            },
            {
              "@type": "Question",
              "name": "Why are analogous color palettes popular in branding?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Because they share similar wavelengths, analogous colors mimic nature (like sunsets or foliage). They evoke serenity, visual harmony, and cohesion without jarring color clashes."
              }
            },
            {
              "@type": "Question",
              "name": "What is the primary challenge when using analogous colors in UI?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "The main risk is low contrast. Because adjacent colors lack complementary tension, designers must introduce significant value (lightness) differences or use neutral background tones to maintain accessible readability."
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold mb-3">
                <i class="bi bi-water"></i>
                <span>30° Adjacent Harmony</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Analogous Colors
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 mt-3 leading-relaxed max-w-3xl">
                Adjacent colors sitting side-by-side on the 360° color wheel. Emulates natural harmonies found in sunsets and oceans, creating peaceful, unified, and cohesive digital experiences.
            </p>
        </div>

        <!-- Interactive Live Analogous Generator -->
        <section class="mb-16 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm" aria-labelledby="generator-heading">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 id="generator-heading" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                        Interactive Analogous Generator
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pick any key hue to compute its adjacent ±30° and ±60° harmony companions.</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <label for="baseHexPicker" class="relative cursor-pointer shrink-0">
                        <span id="baseSwatchPreview" class="block w-9 h-9 rounded-xl shadow-inner border border-slate-200 dark:border-slate-700" style="background-color: #10B981;"></span>
                        <input type="color" id="baseHexPicker" value="#10b981" class="sr-only" />
                    </label>
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl px-2.5 py-1.5 border border-slate-200 dark:border-slate-700">
                        <span class="text-slate-400 font-mono text-sm">#</span>
                        <input type="text" id="baseHexInput" value="10B981" maxlength="6" class="w-20 bg-transparent border-none text-sm font-bold font-mono uppercase focus:ring-0 p-0 text-slate-900 dark:text-white" />
                    </div>
                </div>
            </div>

            <!-- 3-Color Analogous Swatch Ribbon -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <!-- Left Flank (-30°) -->
                <div class="rounded-2xl p-5 flex flex-col justify-between h-44 transition-all" id="leftCard" style="background-color: #059669;">
                    <span class="px-2.5 py-1 rounded-full bg-black/30 backdrop-blur-md text-white text-[10px] font-bold self-start">-30° Adjacent</span>
                    <div>
                        <span class="text-white text-base font-bold block drop-shadow-sm">Deep Emerald</span>
                        <span id="leftHexVal" class="text-white/90 font-mono text-xs font-bold drop-shadow-sm">#059669</span>
                    </div>
                </div>

                <!-- Base Hue (0°) -->
                <div class="rounded-2xl p-5 flex flex-col justify-between h-44 transition-all ring-2 ring-primary ring-offset-2 dark:ring-offset-slate-900" id="baseCard" style="background-color: #10B981;">
                    <span class="px-2.5 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[10px] font-bold self-start">Base Hue (0°)</span>
                    <div>
                        <span class="text-white text-base font-bold block drop-shadow-sm">Mint Green</span>
                        <span id="baseHexVal" class="text-white/90 font-mono text-xs font-bold drop-shadow-sm">#10B981</span>
                    </div>
                </div>

                <!-- Right Flank (+30°) -->
                <div class="rounded-2xl p-5 flex flex-col justify-between h-44 transition-all" id="rightCard" style="background-color: #34D399;">
                    <span class="px-2.5 py-1 rounded-full bg-black/30 backdrop-blur-md text-white text-[10px] font-bold self-start">+30° Adjacent</span>
                    <div>
                        <span class="text-white text-base font-bold block drop-shadow-sm">Spring Teal</span>
                        <span id="rightHexVal" class="text-white/90 font-mono text-xs font-bold drop-shadow-sm">#34D399</span>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 dark:text-slate-400">Formula: <code class="font-mono font-bold text-slate-800 dark:text-slate-200">Hue_1 = H - 30°, Hue_2 = H, Hue_3 = H + 30°</code></span>
                <a id="openInStudioBtn" href="<?= $base ?>/generate-palette?colors=059669,10B981,34D399" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold transition flex items-center gap-1.5">
                    <span>Open in Palette Studio</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- Curated Analogous Palettes Showcase -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-6">
                Curated Analogous Palettes for Digital Brands
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <!-- Pair 1 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Nordic Forest Ecosystem</span>
                        <span class="text-[11px] text-slate-400 font-mono">Eco / Sustainability</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#064E3B] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#064E3B</span></div>
                        <div class="flex-1 bg-[#059669] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#059669</span></div>
                        <div class="flex-1 bg-[#10B981] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#10B981</span></div>
                        <div class="flex-1 bg-[#34D399] flex items-end p-2"><span class="text-[10px] text-slate-900 font-mono font-bold">#34D399</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Evokes organic calm and natural growth. Perfect for environmental organizations, clean tech startups, and wellness brands.
                    </p>
                </div>

                <!-- Pair 2 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Pacific Wave &amp; Cyan</span>
                        <span class="text-[11px] text-slate-400 font-mono">Cloud / Analytics</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#1E3A8A] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#1E3A8A</span></div>
                        <div class="flex-1 bg-[#2563EB] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#2563EB</span></div>
                        <div class="flex-1 bg-[#0284C7] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#0284C7</span></div>
                        <div class="flex-1 bg-[#06B6D4] flex items-end p-2"><span class="text-[10px] text-slate-900 font-mono font-bold">#06B6D4</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        A cool spectrum ranging from indigo through azure to cyan. Delivers dependability and seamless progression in data visualization.
                    </p>
                </div>

                <!-- Pair 3 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Autumn Ember Glow</span>
                        <span class="text-[11px] text-slate-400 font-mono">Food / Lifestyle</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#B91C1C] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#B91C1C</span></div>
                        <div class="flex-1 bg-[#EA580C] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#EA580C</span></div>
                        <div class="flex-1 bg-[#F97316] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#F97316</span></div>
                        <div class="flex-1 bg-[#FBBF24] flex items-end p-2"><span class="text-[10px] text-slate-900 font-mono font-bold">#FBBF24</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Warm amber, tangerine, and scarlet tones that trigger appetite and evoke welcoming, tactile comfort.
                    </p>
                </div>

                <!-- Pair 4 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Neo Tokyo Cyber Violet</span>
                        <span class="text-[11px] text-slate-400 font-mono">Creative Portfolio</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#4C1D95] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#4C1D95</span></div>
                        <div class="flex-1 bg-[#7C3AED] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#7C3AED</span></div>
                        <div class="flex-1 bg-[#C026D3] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#C026D3</span></div>
                        <div class="flex-1 bg-[#DB2777] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#DB2777</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Deep indigo flowing into fuchsia and pink. Highly coveted in creative studio portfolios, NFT marketplaces, and modern web apps.
                    </p>
                </div>

            </div>
        </section>

        <!-- Guide & UI Strategy -->
        <article class="prose dark:prose-invert max-w-none mb-16 space-y-6 text-slate-700 dark:text-slate-300">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Designing with Analogous Color Systems
            </h2>
            <p>
                Because analogous colors share neighboring wavelengths, the human eye processes them effortlessly without cognitive friction. Nature is almost entirely analogous: autumn trees present transitions from deep red to golden yellow, while ocean horizons blend deep sapphire into cyan and seafoam green.
            </p>

            <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                The Core Rule: Pick One Master Hue
            </h3>
            <p>
                Never allow all three analogous colors to fight for equal dominance. Choose <strong>one color as the anchor</strong> (representing 70% of the chromatic surface area), a <strong>second color to support</strong> (20%), and the <strong>third color to accent</strong> (10%). This creates natural hierarchy and prevents the interface from feeling muddy.
            </p>
        </article>

        <!-- FAQs -->
        <section class="mb-16 bg-slate-50 dark:bg-slate-900/60 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-6">
                Frequently Asked Questions About Analogous Colors
            </h2>
            <div class="space-y-4 text-sm">
                <details class="group bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <summary class="font-bold text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                        <span>How many colors should an analogous palette contain?</span>
                        <i class="bi bi-chevron-down text-xs group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
                        Most professional design systems use 3 to 4 adjacent hues. Using fewer than 3 can feel monochromatic, while using more than 4 begins to cross into half the color wheel, diluting the harmonic unity.
                    </p>
                </details>

                <details class="group bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <summary class="font-bold text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                        <span>How do I make my call-to-action button stand out in an analogous UI?</span>
                        <i class="bi bi-chevron-down text-xs group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
                        You have two powerful options: either boost the luminance and saturation of the warmest analogous accent (e.g. vibrant gold against dark forest green), or introduce a single complementary accent purely reserved for critical buttons.
                    </p>
                </details>
            </div>
        </section>

        <!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
        <?php
        $currentHarmony = 'analogous';
        include __DIR__ . '/../../components/explore-harmonies.php';
        ?>

    </main>

    <?php include __DIR__ . '/../../components/footer.php'; ?>

    <script>
        function hexToRgb(hex) {
            hex = hex.replace('#', '');
            if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
            var num = parseInt(hex, 16);
            return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
        }

        function rgbToHsl(r, g, b) {
            r /= 255; g /= 255; b /= 255;
            var max = Math.max(r, g, b), min = Math.min(r, g, b);
            var h, s, l = (max + min) / 2;
            if (max === min) { h = s = 0; }
            else {
                var d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return { h: h * 360, s: s, l: l };
        }

        function hslToRgb(h, s, l) {
            h /= 360;
            var r, g, b;
            if (s === 0) { r = g = b = l; }
            else {
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
            return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
        }

        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(x => {
                var hex = x.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('').toUpperCase();
        }

        function updateAnalogous(hex) {
            hex = hex.replace('#', '');
            if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

            var rgb = hexToRgb(hex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

            // -30° left, +30° right
            var leftH = (hsl.h - 30 + 360) % 360;
            var rightH = (hsl.h + 30) % 360;

            var leftHex = rgbToHex(...Object.values(hslToRgb(leftH, hsl.s, hsl.l)));
            var rightHex = rgbToHex(...Object.values(hslToRgb(rightH, hsl.s, hsl.l)));
            var fullBaseHex = '#' + hex.toUpperCase();

            document.getElementById('baseCard').style.backgroundColor = fullBaseHex;
            document.getElementById('baseHexVal').textContent = fullBaseHex;
            document.getElementById('baseSwatchPreview').style.backgroundColor = fullBaseHex;
            document.getElementById('baseHexPicker').value = fullBaseHex.toLowerCase();
            document.getElementById('baseHexInput').value = hex.toUpperCase();

            document.getElementById('leftCard').style.backgroundColor = leftHex;
            document.getElementById('leftHexVal').textContent = leftHex;

            document.getElementById('rightCard').style.backgroundColor = rightHex;
            document.getElementById('rightHexVal').textContent = rightHex;

            var studioBtn = document.getElementById('openInStudioBtn');
            if (studioBtn) {
                studioBtn.href = '<?= $base ?>/generate-palette?colors=' + leftHex.replace('#', '') + ',' + hex.toUpperCase() + ',' + rightHex.replace('#', '');
            }
        }

        document.getElementById('baseHexPicker').addEventListener('input', function(e) {
            updateAnalogous(e.target.value);
        });

        document.getElementById('baseHexInput').addEventListener('input', function(e) {
            var val = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            if (val.length === 6) updateAnalogous(val);
        });
    </script>
</body>

</html>

