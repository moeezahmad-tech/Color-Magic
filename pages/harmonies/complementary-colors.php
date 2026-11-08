<?php
require_once __DIR__ . '/../../components/config.php';
/** @var string $base */

$canonicalUrl = 'https://colormagic.techkreative.com/complementary-colors';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Complementary Colors: Guide, Formula &amp; Palette Generator | Color Magic</title>
    <meta name="description"
        content="Master complementary color harmony (180° opposite). Learn how to create high-contrast color palettes for websites, apps, and branding with interactive tools and WCAG rules." />
    <meta name="keywords"
        content="complementary colors, complementary color palette, opposite colors, color wheel 180 degrees, color harmony, UI contrast, color theory" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?= $canonicalUrl ?>" />
    <meta property="og:title" content="Complementary Colors: The Complete Color Theory Guide &amp; Generator" />
    <meta property="og:description"
        content="Understand how 180° opposite colors create maximum visual tension and high-impact UI call-to-actions." />
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
          "headline": "Complementary Colors: Guide, Formula & UI Design Rules",
          "description": "Learn the theory, formula, and best practices for complementary color harmonies in digital design and web development.",
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
              "name": "What are complementary colors?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Complementary colors are pairs of colors situated directly opposite each other on the 360-degree color wheel, such as blue and orange or red and green. When placed side by side, they produce the highest possible visual contrast."
              }
            },
            {
              "@type": "Question",
              "name": "How do you calculate a complementary color?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "In HSL (Hue, Saturation, Lightness), a complementary color is found by adding or subtracting 180 degrees from the base hue: Complementary Hue = (Base Hue + 180) % 360, while keeping saturation and lightness balanced."
              }
            },
            {
              "@type": "Question",
              "name": "What is the 60-30-10 rule for complementary colors?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "The 60-30-10 rule recommends using 60% dominant neutral or background tone, 30% primary hue, and only 10% complementary accent (such as buttons or badges) to avoid visual fatigue and chromatic clash."
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary text-xs font-bold mb-3">
                <i class="bi bi-circle-half"></i>
                <span>180° Opposite Harmony</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Complementary Colors
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 mt-3 leading-relaxed max-w-3xl">
                Two hues placed directly opposite each other on the 360° color wheel. Produces the maximum possible visual contrast, making call-to-actions, buttons, and accents stand out immediately.
            </p>
        </div>

        <!-- Interactive Live Complementary Generator -->
        <section class="mb-16 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm" aria-labelledby="generator-heading">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 id="generator-heading" class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                        Interactive Complementary Generator
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pick or enter any base hex code to compute its exact 180° opposite complement.</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <label for="baseHexPicker" class="relative cursor-pointer shrink-0">
                        <span id="baseSwatchPreview" class="block w-9 h-9 rounded-xl shadow-inner border border-slate-200 dark:border-slate-700" style="background-color: #0077FE;"></span>
                        <input type="color" id="baseHexPicker" value="#0077fe" class="sr-only" />
                    </label>
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl px-2.5 py-1.5 border border-slate-200 dark:border-slate-700">
                        <span class="text-slate-400 font-mono text-sm">#</span>
                        <input type="text" id="baseHexInput" value="0077FE" maxlength="6" class="w-20 bg-transparent border-none text-sm font-bold font-mono uppercase focus:ring-0 p-0 text-slate-900 dark:text-white" />
                    </div>
                </div>
            </div>

            <!-- Visual Comparison Swatches -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <!-- Base Hue Card -->
                <div class="rounded-2xl p-6 flex flex-col justify-between h-48 transition-all" id="baseCard" style="background-color: #0077FE;">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full bg-black/30 backdrop-blur-md text-white text-[11px] font-bold">Base Hue (0°)</span>
                        <button type="button" onclick="copyHex('baseHexVal')" class="text-white/80 hover:text-white text-xs font-bold transition flex items-center gap-1 bg-black/20 px-2.5 py-1 rounded-lg">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <div>
                        <span id="baseName" class="text-white text-lg font-bold block drop-shadow-sm">Electric Blue</span>
                        <span id="baseHexVal" class="text-white/90 font-mono text-sm font-bold drop-shadow-sm">#0077FE</span>
                    </div>
                </div>

                <!-- Complementary Card -->
                <div class="rounded-2xl p-6 flex flex-col justify-between h-48 transition-all" id="compCard" style="background-color: #FE8800;">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full bg-black/30 backdrop-blur-md text-white text-[11px] font-bold">Complement (180°)</span>
                        <button type="button" onclick="copyHex('compHexVal')" class="text-white/80 hover:text-white text-xs font-bold transition flex items-center gap-1 bg-black/20 px-2.5 py-1 rounded-lg">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <div>
                        <span id="compName" class="text-white text-lg font-bold block drop-shadow-sm">Amber Sunset</span>
                        <span id="compHexVal" class="text-white/90 font-mono text-sm font-bold drop-shadow-sm">#FE8800</span>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-500 dark:text-slate-400">Formula: <code class="font-mono font-bold text-slate-800 dark:text-slate-200">Hue_Complement = (Hue_Base + 180) mod 360</code></span>
                <a id="openInStudioBtn" href="<?= $base ?>/generate-palette" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold transition flex items-center gap-1.5">
                    <span>Open in Palette Studio</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- Curated Complementary Palettes Showcase -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-6">
                Iconic Complementary Pairings in Modern Design
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <!-- Pair 1 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Ultramarine &amp; Warm Tangerine</span>
                        <span class="text-[11px] text-slate-400 font-mono">SaaS / Fintech</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#1E40AF] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#1E40AF</span></div>
                        <div class="flex-1 bg-[#F97316] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#F97316</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        The gold standard in digital product design. Deep ultramarine anchors dashboards, while tangerine commands immediate action on primary CTAs.
                    </p>
                </div>

                <!-- Pair 2 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Deep Forest &amp; Rose Crimson</span>
                        <span class="text-[11px] text-slate-400 font-mono">E-Commerce / Luxury</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#064E3B] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#064E3B</span></div>
                        <div class="flex-1 bg-[#E11D48] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#E11D48</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Refined red and green pairing with adjusted luminance. Avoids festive cliché by deepening the emerald base and utilizing soft rose accents.
                    </p>
                </div>

                <!-- Pair 3 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Cyberpunk Violet &amp; Neon Lime</span>
                        <span class="text-[11px] text-slate-400 font-mono">Web3 / Gaming</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#6D28D9] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#6D28D9</span></div>
                        <div class="flex-1 bg-[#84CC16] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#84CC16</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        High-voltage visual contrast designed specifically for dark themes. Gives user interfaces an electric, forward-looking aesthetic.
                    </p>
                </div>

                <!-- Pair 4 -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-sm text-slate-900 dark:text-white">Dark Slate &amp; Electric Amber</span>
                        <span class="text-[11px] text-slate-400 font-mono">Developer Tools</span>
                    </div>
                    <div class="flex h-14 rounded-xl overflow-hidden mb-3 border border-slate-100 dark:border-slate-800">
                        <div class="flex-1 bg-[#0F172A] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#0F172A</span></div>
                        <div class="flex-1 bg-[#F59E0B] flex items-end p-2"><span class="text-[10px] text-white font-mono font-bold">#F59E0B</span></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Ultra-high contrast pairing engineered for developer consoles, code editors, and high-readability notifications.
                    </p>
                </div>

            </div>
        </section>

        <!-- In-Depth Color Theory & UI Best Practices -->
        <article class="prose dark:prose-invert max-w-none mb-16 space-y-6 text-slate-700 dark:text-slate-300">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                How Complementary Colors Work in Human Perception
            </h2>
            <p>
                When the human eye gazes at a color for several seconds, the photoreceptor cone cells sensitive to that wavelength gradually desensitize. If you shift your gaze to a neutral surface, your brain generates an "afterimage" in the exact <strong>complementary hue</strong>. This biological phenomenon, known as <em>simultaneous contrast</em>, explains why complementary pairs appear to vibrate with intensity when positioned side by side.
            </p>

            <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                The 60-30-10 Rule for Complementary Web UI
            </h3>
            <p>
                A common mistake in digital interface design is using complementary colors in equal 50/50 proportions. This causes extreme ocular fatigue and visual competition. Instead, follow the time-tested <strong>60-30-10 distribution</strong>:
            </p>
            <ul class="list-disc pl-5 space-y-2 text-sm">
                <li><strong>60% Dominant Base:</strong> Typically neutral backgrounds (whites, slates, or dark charcoal).</li>
                <li><strong>30% Secondary Hue:</strong> Your primary brand color across cards, headers, and navigation bars.</li>
                <li><strong>10% Complementary Accent:</strong> The 180° opposite hue, reserved strictly for primary conversion buttons, notification dots, and key stats.</li>
            </ul>

            <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                WCAG Contrast &amp; Accessibility Tips
            </h3>
            <p>
                While complementary colors offer strong chromatic contrast, they do not always satisfy <strong>WCAG 2.1 luminance contrast</strong> for text readability. For example, placing saturated red text on saturated green background fails accessibility standards despite being complementary. Always adjust the lightness/value of one hue to achieve a minimum contrast ratio of <strong>4.5:1</strong> for normal text and <strong>3:1</strong> for large UI components.
            </p>
        </article>

        <!-- FAQs Section -->
        <section class="mb-16 bg-slate-50 dark:bg-slate-900/60 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-6">
                Frequently Asked Questions About Complementary Colors
            </h2>
            <div class="space-y-4 text-sm">
                <details class="group bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <summary class="font-bold text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                        <span>Why are complementary colors opposite on the color wheel?</span>
                        <i class="bi bi-chevron-down text-xs group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
                        In the optical color wheel (RGB/additive and CMYK/subtractive), complementary colors represent the exact opposing wavelengths of light. When optical complements are combined as light, they yield white; when mixed as pigment, they cancel out into neutral dark gray.
                    </p>
                </details>

                <details class="group bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <summary class="font-bold text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                        <span>Can I use complementary colors in dark mode designs?</span>
                        <i class="bi bi-chevron-down text-xs group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
                        Yes! Complementary schemes look stunning in dark mode when you desaturate both hues slightly (between 60%–75% saturation). Full saturation on dark backgrounds creates a harsh glow (chromatic aberration) that strains the user's vision.
                    </p>
                </details>

                <details class="group bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <summary class="font-bold text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                        <span>What is the difference between complementary and split-complementary?</span>
                        <i class="bi bi-chevron-down text-xs group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
                        Complementary uses exactly 2 colors situated 180° apart. Split-complementary uses 3 colors: a base hue plus the two colors flanking its opposite (150° and 210°), resulting in softer visual tension and greater palette versatility.
                    </p>
                </details>
            </div>
        </section>

        <!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
        <?php
        $currentHarmony = 'complementary';
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
            return { h: h * 360, s: s, l: l };
        }

        function hslToRgb(h, s, l) {
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

        function updateComplementary(hex) {
            hex = hex.replace('#', '');
            if (!/^[0-9A-Fa-f]{6}$/.test(hex)) return;

            var rgb = hexToRgb(hex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            
            // 180° opposite
            var compH = (hsl.h + 180) % 360;
            var compRgb = hslToRgb(compH, hsl.s, hsl.l);
            var compHex = rgbToHex(compRgb.r, compRgb.g, compRgb.b);

            var fullBaseHex = '#' + hex.toUpperCase();
            
            // Update Base Card
            document.getElementById('baseCard').style.backgroundColor = fullBaseHex;
            document.getElementById('baseHexVal').textContent = fullBaseHex;
            document.getElementById('baseSwatchPreview').style.backgroundColor = fullBaseHex;
            document.getElementById('baseHexPicker').value = fullBaseHex.toLowerCase();
            document.getElementById('baseHexInput').value = hex.toUpperCase();

            // Update Comp Card
            document.getElementById('compCard').style.backgroundColor = compHex;
            document.getElementById('compHexVal').textContent = compHex;

            // Update studio link
            var studioBtn = document.getElementById('openInStudioBtn');
            if (studioBtn) {
                studioBtn.href = '<?= $base ?>/generate-palette?colors=' + hex.toUpperCase() + ',' + compHex.replace('#', '');
            }
        }

        document.getElementById('baseHexPicker').addEventListener('input', function(e) {
            updateComplementary(e.target.value);
        });

        document.getElementById('baseHexInput').addEventListener('input', function(e) {
            var val = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
            if (val.length === 6) {
                updateComplementary(val);
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

