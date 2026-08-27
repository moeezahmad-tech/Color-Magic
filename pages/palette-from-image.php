<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Color Magic | Extract Palette from Image</title>
    <meta name="description"
        content="Upload any image and instantly extract a beautiful color palette. Get hex, RGB, and HSL values for every dominant color — all processed in your browser." />
    <meta name="keywords"
        content="palette from image, extract colors from photo, image color picker, dominant colors, designer tools, Color Magic" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/palette-from-image" />
    <meta property="og:title" content="Color Magic | Extract Palette from Image" />
    <meta property="og:description"
        content="Upload an image and extract dominant colors instantly — hex, RGB, HSL values ready to copy." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/palette-from-image" />
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
        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        /* Drop zone */
        #dropZone {
            transition: border-color 0.25s, background 0.25s, transform 0.25s;
        }

        #dropZone.drag-over {
            border-color: #ec4899;
            background: rgba(236, 72, 153, 0.06);
            transform: scale(1.01);
        }

        .dark #dropZone.drag-over {
            background: rgba(236, 72, 153, 0.12);
        }

        /* Preview thumbnail */
        #imagePreviewContainer {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        #imagePreviewContainer img {
            display: block;
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            background: repeating-conic-gradient(#e2e8f0 0% 25%, transparent 0% 50%) 50% / 20px 20px;
        }

        .dark #imagePreviewContainer img {
            background: repeating-conic-gradient(#1e293b 0% 25%, transparent 0% 50%) 50% / 20px 20px;
        }

        /* Slider track styling */
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(90deg, #ec4899, #7c3aed);
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: white;
            border: 3px solid #ec4899;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
            transition: transform 0.15s;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }

        input[type="range"]::-moz-range-thumb {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: white;
            border: 3px solid #ec4899;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        /* Spinner */
        @keyframes spin-slow {
            to {
                transform: rotate(360deg);
            }
        }

        .spin-slow {
            animation: spin-slow 1.1s linear infinite;
        }

        /* Fade-in for results */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-fade-in-up {
            animation: fadeInUp 0.45s ease both;
        }

        /* Swatch styles — matching explore palettes page */
        .swatch {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .swatch-hex {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .swatch:hover .swatch-hex {
            opacity: 1;
        }

        @keyframes swatchBtnIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .swatch-icon-btn {
            opacity: 0;
            transform: translateY(-6px);
        }

        .swatch:hover .swatch-icon-btn {
            animation: swatchBtnIn 0.18s ease forwards;
        }

        .swatch:hover .swatch-btn-1 {
            animation-delay: 0s;
        }

        .swatch:hover .swatch-btn-2 {
            animation-delay: 0.06s;
        }

        .swatch-hex.copied-state {
            opacity: 1 !important;
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            font-weight: 700;
        }

        /* Color detail rows */
        .color-row {
            transition: border-color 0.2s, background 0.2s;
        }

        .color-row:hover {
            border-color: #f9a8d4;
            background: #fdf2f8;
        }

        .dark .color-row:hover {
            border-color: #475569;
            background: #1e293b;
        }

        /* Toast */
        #copyToast {
            transition: opacity 0.3s, transform 0.3s;
        }
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
                <span class="text-xl font-bold tracking-tight">
                    <span class="text-slate-900 dark:text-white">Color</span><span
                        class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
                </span>
            </a>
            <button id="closeMobileMenuBtn"
                class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                aria-label="Close menu">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        <div class="space-y-2">
            <a href="<?= $base ?>/" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-house-door"></i></span>
                <div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span></div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS
                        gradients</span></div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span></div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span></div>
            </a>
            <a href="<?= $base ?>/palette-from-image" class="sb-btn sb-active w-full"><span class="sb-icon"><i
                        class="bi bi-image"></i></span>
                <div><span class="block font-bold">Palette from Image</span><span class="text-xs opacity-75">Extract
                        colors from photos</span></div>
            </a>
            <a href="<?= $base ?>/profile" class=" sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors,
                        palettes &amp; gradients</span></div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span></div>
            </a>
        </div>
    </div>

    <!-- ══ COPY TOAST ═══════════════════════════════════════════════════════ -->
    <div id="copyToast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[90] px-5 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl shadow-2xl text-sm font-semibold flex items-center gap-2 opacity-0 pointer-events-none translate-y-2">
        <i class="bi bi-check-circle-fill text-green-400 dark:text-green-600 text-lg"></i>
        <span id="copyToastText">Copied to clipboard!</span>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-16 pb-8 px-6 md:px-8 flex flex-col gap-6">

        <!-- Hero -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-100 via-pink-50 to-fuchsia-100 dark:from-violet-900/40 dark:via-slate-900 dark:to-fuchsia-900/40 p-7 md:p-10 border border-pink-100 dark:border-slate-800">
            <div class="hero-blob w-64 h-64 bg-fuchsia-400/25 top-[-50px] left-[-50px]"></div>
            <div class="hero-blob w-56 h-56 bg-violet-400/25 bottom-[-35px] right-[-35px]"></div>
            <div class="relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm rounded-full text-xs font-bold text-fuchsia-600 dark:text-fuchsia-400 mb-3 border border-fuchsia-200 dark:border-fuchsia-800">
                    <i class="bi bi-image"></i> Image Powered
                </div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">
                    Extract Palette from Image
                </h1>
                <p class="text-slate-600 dark:text-slate-300 text-base max-w-xl">
                    Upload any photo and instantly discover its dominant colors — hex, RGB &amp; HSL values ready to
                    copy. All processing happens in your browser, your images never leave your device.
                </p>
            </div>
        </div>

        <!-- Upload area (full width) -->
        <div id="dropZone"
            class="relative border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl bg-white dark:bg-slate-900 p-10 flex flex-col items-center justify-center text-center cursor-pointer min-h-[220px] transition-all">

            <input id="fileInput" type="file" accept="image/*"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />

            <div id="dropZoneContent" class="flex flex-col items-center gap-4 pointer-events-none">
                <div
                    class="w-20 h-20 rounded-2xl bg-gradient-to-br from-fuchsia-100 to-violet-100 dark:from-fuchsia-900/50 dark:to-violet-900/50 flex items-center justify-center">
                    <i class="bi bi-cloud-arrow-up text-4xl text-fuchsia-500"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Drop your image here or <span
                            class="text-fuchsia-500 underline underline-offset-2">browse</span>
                    </p>
                    <p class="text-xs text-slate-400">PNG · JPG · WEBP · GIF · BMP · SVG — any size</p>
                </div>
            </div>

            <!-- Loading overlay (hidden by default) -->
            <div id="extractingOverlay"
                class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-2xl hidden flex-col items-center justify-center gap-4 z-10">
                <div class="w-16 h-16 rounded-full border-4 border-fuchsia-200 border-t-fuchsia-500 spin-slow">
                </div>
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Extracting colors…</p>
            </div>
        </div>

        <!-- Image preview + slider (full width, hidden until image loaded) -->
        <div id="imagePreviewWrapper" class="hidden flex flex-col gap-5">
            <div class="flex flex-col md:flex-row gap-5">
                <div id="imagePreviewContainer"
                    class="relative border border-slate-200 dark:border-slate-700 shadow-sm rounded-2xl overflow-hidden flex-1">
                    <img id="imagePreview" alt="Uploaded image preview"
                        class="max-h-[360px] w-full object-contain bg-slate-50 dark:bg-slate-800" />
                    <button id="removeImageBtn"
                        class="absolute top-3 right-3 w-9 h-9 bg-black/50 hover:bg-black/70 text-white rounded-xl flex items-center justify-center transition-all text-lg"
                        title="Remove image" aria-label="Remove image">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Color count slider -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex flex-col gap-4 md:w-80 shrink-0">
                    <div class="flex items-center justify-between">
                        <label for="colorCountSlider" class="text-sm font-bold">Number of Colors</label>
                        <span id="colorCountBadge"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-fuchsia-500 to-violet-500 text-white font-bold text-sm shadow">
                            6
                        </span>
                    </div>
                    <input id="colorCountSlider" type="range" min="3" max="12" value="6" step="1"
                        class="w-full cursor-pointer" />
                    <div class="flex justify-between text-xs text-slate-400 font-medium">
                        <span>3</span><span>6</span><span>9</span><span>12</span>
                    </div>
                    <button id="extractBtn"
                        class="w-full px-6 py-4 bg-gradient-to-r from-fuchsia-500 to-violet-500 hover:from-fuchsia-600 hover:to-violet-600 text-white font-bold rounded-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-lg shadow-fuchsia-500/25 text-sm">
                        <i class="bi bi-eyedropper text-lg"></i>
                        <span>Extract Colors</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Placeholder (shown before extraction) -->
        <div id="resultsPlaceholder"
            class="flex flex-col items-center justify-center min-h-[200px] text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-900/30 p-8 text-center">
            <div
                class="w-20 h-20 rounded-2xl bg-gradient-to-br from-fuchsia-100 to-violet-100 dark:from-fuchsia-900/40 dark:to-violet-900/40 flex items-center justify-center mb-5">
                <i class="bi bi-palette2 text-4xl text-fuchsia-400/60"></i>
            </div>
            <p class="text-lg font-semibold text-slate-500 dark:text-slate-400 mb-1">
                Your extracted palette will appear here
            </p>
            <p class="text-sm text-slate-400 dark:text-slate-500 max-w-xs">
                Upload an image and click <strong>Extract Colors</strong> to discover its dominant palette.
            </p>
        </div>

        <!-- Swatch results (full width, hidden until extracted) -->
        <div id="paletteSwatchResults" class="hidden"></div>

        <!-- Color details (full width, hidden until extracted) -->
        <div id="paletteDetails" class="hidden"></div>

        <!-- How it works -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-12 mt-2">
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 flex gap-4 items-start">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-fuchsia-500/10 shrink-0">
                    <i class="bi bi-cloud-arrow-up text-xl text-fuchsia-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm mb-1">1. Upload Image</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Drag &amp; drop or browse — any common image format works. Images never leave your browser.
                    </p>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 flex gap-4 items-start">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-pink-500/10 shrink-0">
                    <i class="bi bi-clipboard-check text-xl text-pink-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm mb-1">2. Copy &amp; Use</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Click any swatch or value to copy hex, RGB, or HSL — ready for your design workflow.
                    </p>
                </div>
            </div>
        </div>

    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "palette-from-image";
        window.CM_BASE = "<?= $base ?>";
    </script>
    <script src="<?= $base ?>/assets/js/palette-from-image.js?v=1.0" defer></script>
</body>

</html>