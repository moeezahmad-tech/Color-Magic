<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Hex to RGB Converter — Free Online Color Code Conversion | Color Magic</title>
    <meta name="description"
        content="Free hex to RGB converter. Instantly convert hex color codes to RGB values and vice versa. Live color preview, conversion formula, and example conversions table." />
    <meta name="keywords"
        content="hex to RGB, RGB converter, hex to RGB converter, color code conversion, RGB to hex, online color converter" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/hex-to-rgb" />
    <meta property="og:title" content="Hex to RGB Converter — Free Online Color Code Conversion | Color Magic" />
    <meta property="og:description"
        content="Free hex to RGB converter. Instantly convert hex color codes to RGB values and vice versa with live color preview." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/hex-to-rgb" />
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
        #hexInput:focus, #rInput:focus, #gInput:focus, #bInput:focus { box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.12); }
        .converter-preview { transition: background-color 0.3s ease; }
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
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-secondary/10 via-pink-50 to-primary/10 dark:from-secondary/20 dark:via-slate-900 dark:to-primary/20 p-7 md:p-10 border border-pink-100 dark:border-slate-800">
            <div class="absolute w-56 h-56 bg-secondary/15 rounded-full blur-[70px] top-[-30px] right-[-30px] pointer-events-none"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-secondary mb-3 border border-secondary/20">
                    <i class="bi bi-sliders"></i> Hex to RGB Converter
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Convert Hex to RGB Instantly</h1>
                <p class="text-slate-600 dark:text-slate-300 text-base max-w-lg">
                    Convert hex color codes to RGB values in real-time. Use the hex-to-RGB converter above or the RGB-to-hex converter below. See live color previews as you type.
                </p>
            </div>
        </div>

        <!-- Hex → RGB Converter -->
        <div class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <h2 class="font-bold text-lg mb-4 flex items-center gap-2"><i class="bi bi-arrow-right-circle text-primary"></i> Hex to RGB</h2>
            <div class="flex gap-4 flex-wrap items-start">
                <div class="flex-1 relative min-w-[220px]">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-lg select-none">#</span>
                    <input id="hexInput" type="text" placeholder="3B82F6" maxlength="6"
                        class="w-full pl-10 pr-4 py-3.5 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary focus:outline-none transition-all text-sm font-mono uppercase"
                        aria-label="Hex color code input" />
                </div>
                <div id="hexToRgbResult" class="flex-1 min-w-[220px] py-3.5 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 font-mono text-sm text-slate-600 dark:text-slate-300 min-h-[52px] flex items-center">
                    Enter a hex code above
                </div>
                <div id="hexPreview" class="converter-preview w-14 h-14 rounded-xl border-2 border-slate-200 dark:border-slate-700 shrink-0" style="background-color: transparent"></div>
            </div>
        </div>

        <!-- RGB → Hex Converter -->
        <div class="bg-white dark:bg-slate-900 p-5 md:p-7 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
            <h2 class="font-bold text-lg mb-4 flex items-center gap-2"><i class="bi bi-arrow-left-circle text-secondary"></i> RGB to Hex</h2>
            <div class="flex gap-3 flex-wrap items-end">
                <div class="flex gap-2 flex-1 min-w-[220px]">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-400 uppercase mb-1 block">R</label>
                        <input id="rInput" type="number" min="0" max="255" placeholder="59" class="w-full px-3 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary focus:outline-none transition-all text-sm font-mono text-center" />
                    </div>
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-400 uppercase mb-1 block">G</label>
                        <input id="gInput" type="number" min="0" max="255" placeholder="130" class="w-full px-3 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary focus:outline-none transition-all text-sm font-mono text-center" />
                    </div>
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-400 uppercase mb-1 block">B</label>
                        <input id="bInput" type="number" min="0" max="255" placeholder="246" class="w-full px-3 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:border-primary focus:outline-none transition-all text-sm font-mono text-center" />
                    </div>
                </div>
                <div id="rgbToHexResult" class="flex-1 min-w-[180px] py-3.5 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 font-mono text-sm text-slate-600 dark:text-slate-300 min-h-[52px] flex items-center">
                    Enter RGB values
                </div>
                <div id="rgbPreview" class="converter-preview w-14 h-14 rounded-xl border-2 border-slate-200 dark:border-slate-700 shrink-0" style="background-color: transparent"></div>
            </div>
        </div>

        <!-- Conversion Formula -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-800">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">How to Convert Hex to RGB</h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                A hex color code consists of three pairs of hexadecimal digits: <code class="font-mono text-primary">#RRGGBB</code>. Each pair represents one color channel (Red, Green, Blue) and converts to a decimal value between 0 and 255.
            </p>
            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 font-mono text-sm mb-4 overflow-x-auto">
                <p class="text-slate-600 dark:text-slate-300"><strong>Example:</strong> #3B82F6</p>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Red: 3B₁₆ = 3×16 + 11 = <strong class="text-primary">59</strong></p>
                <p class="text-slate-500 dark:text-slate-400">Green: 82₁₆ = 8×16 + 2 = <strong class="text-primary">130</strong></p>
                <p class="text-slate-500 dark:text-slate-400">Blue: F6₁₆ = 15×16 + 6 = <strong class="text-primary">246</strong></p>
                <p class="text-slate-600 dark:text-slate-300 mt-2">Result: <strong>RGB(59, 130, 246)</strong></p>
            </div>
        </div>

        <!-- Example Conversions Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-800">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Example Conversions</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="text-left py-3 px-2 font-bold text-slate-500 uppercase text-xs">Hex</th>
                            <th class="text-left py-3 px-2 font-bold text-slate-500 uppercase text-xs">RGB</th>
                            <th class="text-left py-3 px-2 font-bold text-slate-500 uppercase text-xs">Color</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#FF0000</td><td class="py-2.5 px-2 font-mono">rgb(255, 0, 0)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#FF0000"></span> Red</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#00FF00</td><td class="py-2.5 px-2 font-mono">rgb(0, 255, 0)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#00FF00"></span> Lime</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#0000FF</td><td class="py-2.5 px-2 font-mono">rgb(0, 0, 255)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#0000FF"></span> Blue</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#FF5733</td><td class="py-2.5 px-2 font-mono">rgb(255, 87, 51)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#FF5733"></span> Vermilion</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#EC4899</td><td class="py-2.5 px-2 font-mono">rgb(236, 72, 153)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#EC4899"></span> Pink</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#3B82F6</td><td class="py-2.5 px-2 font-mono">rgb(59, 130, 246)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#3B82F6"></span> Blue</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#10B981</td><td class="py-2.5 px-2 font-mono">rgb(16, 185, 129)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block" style="background:#10B981"></span> Emerald</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#000000</td><td class="py-2.5 px-2 font-mono">rgb(0, 0, 0)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block border border-slate-300 dark:border-slate-600" style="background:#000000"></span> Black</td></tr>
                        <tr><td class="py-2.5 px-2 font-mono font-bold">#FFFFFF</td><td class="py-2.5 px-2 font-mono">rgb(255, 255, 255)</td><td class="py-2.5 px-2"><span class="w-6 h-6 rounded inline-block border border-slate-300 dark:border-slate-600" style="background:#FFFFFF"></span> White</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">Frequently Asked Questions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What does RGB stand for?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">RGB stands for Red, Green, Blue — the three additive primary colors of light. Each channel has a value from 0 (none) to 255 (full intensity). Combining all three channels produces the full spectrum of screen-displayable colors.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">How do I convert hex to RGB manually?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Split the hex code into three pairs (RR, GG, BB), then convert each pair from base-16 to decimal. For example, #3B82F6 → 3B=59, 82=130, F6=246 → RGB(59, 130, 246). Use our converter above for instant results.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What's the difference between hex and RGB?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">They represent the same color data in different formats. Hex uses base-16 notation (#RRGGBB), while RGB uses decimal values (0–255) per channel. Hex is more compact; RGB is more intuitive for adjusting individual channels.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">Why use RGB instead of hex?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">RGB is preferred in CSS when you need to manipulate individual color channels (e.g., rgba() for transparency), or when working with design tools that use decimal values. Hex is more compact for simple color specification.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-base mb-2">What is RGB(255, 0, 0) in hex?</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">RGB(255, 0, 0) is pure red, which is <strong>#FF0000</strong> in hex. 255 in decimal = FF in hexadecimal, and 0 = 00. Use our RGB-to-hex converter above for any conversion.</p>
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
                <a href="<?= $base ?>/hex-to-color-name" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-tag text-primary text-lg"></i>
                    <span class="text-sm font-semibold">Hex to Color Name</span>
                </a>
                <a href="<?= $base ?>/generate-palette" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-stars text-secondary text-lg"></i>
                    <span class="text-sm font-semibold">Generate Palette</span>
                </a>
                <a href="<?= $base ?>/palettes" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition">
                    <i class="bi bi-palette text-emerald-500 text-lg"></i>
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
                    "name": "What does RGB stand for?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "RGB stands for Red, Green, Blue — the three additive primary colors of light. Each channel has a value from 0 to 255."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How do I convert hex to RGB manually?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Split the hex code into three pairs (RR, GG, BB), then convert each pair from base-16 to decimal. For example, #3B82F6 → 3B=59, 82=130, F6=246 → RGB(59, 130, 246)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What's the difference between hex and RGB?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "They represent the same color data in different formats. Hex uses base-16 notation (#RRGGBB), while RGB uses decimal values (0–255) per channel."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Why use RGB instead of hex?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "RGB is preferred when you need to manipulate individual color channels, such as using rgba() for transparency in CSS."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What is RGB(255, 0, 0) in hex?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "RGB(255, 0, 0) is pure red, which is #FF0000 in hex."
                    }
                }
            ]
        }
        </script>
    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "hex-to-rgb";

        // Hex → RGB converter
        const hexInput = document.getElementById('hexInput');
        const hexToRgbResult = document.getElementById('hexToRgbResult');
        const hexPreview = document.getElementById('hexPreview');

        hexInput?.addEventListener('input', function () {
            let val = this.value.trim().replace('#', '');
            if (/^[0-9A-Fa-f]{6}$/.test(val)) {
                const r = parseInt(val.substring(0, 2), 16);
                const g = parseInt(val.substring(2, 4), 16);
                const b = parseInt(val.substring(4, 6), 16);
                hexToRgbResult.innerHTML = '<span class="font-bold">rgb(' + r + ', ' + g + ', ' + b + ')</span>';
                hexPreview.style.backgroundColor = '#' + val;
            } else {
                hexToRgbResult.textContent = 'Enter a valid 6-digit hex code';
                hexPreview.style.backgroundColor = 'transparent';
            }
        });

        // RGB → Hex converter
        const rInput = document.getElementById('rInput');
        const gInput = document.getElementById('gInput');
        const bInput = document.getElementById('bInput');
        const rgbToHexResult = document.getElementById('rgbToHexResult');
        const rgbPreview = document.getElementById('rgbPreview');

        function convertRgbToHex() {
            const r = parseInt(rInput?.value) || 0;
            const g = parseInt(gInput?.value) || 0;
            const b = parseInt(bInput?.value) || 0;
            if (rInput?.value || gInput?.value || bInput?.value) {
                const hex = '#' + [r, g, b].map(v => Math.max(0, Math.min(255, v)).toString(16).padStart(2, '0')).join('').toUpperCase();
                rgbToHexResult.innerHTML = '<span class="font-bold">' + hex + '</span>';
                rgbPreview.style.backgroundColor = hex;
            } else {
                rgbToHexResult.textContent = 'Enter RGB values';
                rgbPreview.style.backgroundColor = 'transparent';
            }
        }
        [rInput, gInput, bInput].forEach(el => el?.addEventListener('input', convertRgbToHex));
    </script>
</body>

</html>
