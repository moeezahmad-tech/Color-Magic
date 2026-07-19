<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Color Magic | The Open-Source Color Palette & Design Tool</title>
    <meta name="description"
        content="Color Magic is a premium, open-source color tool. Explore palettes, generate harmonic schemes, find names of color codes, and export formats." />
    <meta name="keywords"
        content="color palette explorer, color schemes, designer tools, TechKreative, color generator" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="index, follow" />

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="assets/js/app.js" defer></script>

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/" />
    <meta property="og:title" content="Color Magic | The Open-Source Color Palette & Design Tool" />
    <meta property="og:description" content="Discover, generate, and explore professional color schemes." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/" />
    <link rel="manifest" href="manifest.json" />
    <link rel="icon" type="image/png" href="assets/images/logo.png" />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script id="tailwind-config" src="assets/js/tailwind-config.js"></script>
    <style>
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -15px rgba(124, 58, 237, 0.12);
        }

        .sb-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: none;
        }

        .sb-btn .sb-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            transition: transform 0.22s ease;
        }

        .sb-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #ffffff;
            box-shadow: 0 4px 12px -2px rgba(236, 72, 153, 0.2);
        }

        .sb-inactive {
            color: #475569;
        }

        .dark .sb-inactive {
            color: #cbd5e1;
        }

        .sb-inactive:hover {
            background-color: #f1f5f9;
        }

        .dark .sb-inactive:hover {
            background-color: #1e1b4b;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.2s ease both;
        }

        /* Palette scrolling animations */
        @keyframes scrollUp {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-50%);
            }
        }

        @keyframes scrollDown {
            0% {
                transform: translateY(-50%);
            }

            100% {
                transform: translateY(0);
            }
        }

        .animate-scroll-up {
            animation: scrollUp 25s linear infinite;
        }

        .animate-scroll-down {
            animation: scrollDown 25s linear infinite;
        }

        .animation-container {
            mask-image: linear-gradient(to bottom,
                    transparent,
                    white 20%,
                    white 80%,
                    transparent);
            -webkit-mask-image: linear-gradient(to bottom,
                    transparent,
                    white 20%,
                    white 80%,
                    transparent);
        }

    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen relative overflow-x-hidden">
    <!-- Background Glow Effects -->
    <div class="glow-bg top-[-100px] left-[-100px]"></div>
    <div class="glow-bg bottom-[-100px] right-[-100px]"></div>

    <!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
       <?php include 'components/navbar.php'; ?>

    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="/" class="flex items-center gap-2 text-primary">
                <img src="assets/images/logo.png" alt="Color Magic Logo" class="h-8 w-8 object-contain" />
                <span class="text-xl font-bold tracking-tight">
                    <span class="text-slate-900 dark:text-white">Color</span>
                    <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
                </span>
            </a>
            <button id="closeMobileMenuBtn"
                class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                aria-label="Close menu">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        <!-- Mobile nav items -->
        <div class="space-y-2">
            <a href="/" class="sb-btn sb-active w-full"><span class="sb-icon"><i class="bi bi-house-door"></i></span>
                <div>
                    <span class="block font-bold">Home</span><span class="text-xs opacity-75">Back to homepage</span>
                </div>
            </a>
            <a href="palettes.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div>
                    <span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span>
                </div>
            </a>
            <a href="find-color.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div>
                    <span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span>
                </div>
            </a>
            <a href="generate-palette.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div>
                    <span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span>
                </div>
            </a>
            <a href="palettes.php?filter=favorites" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div>
                    <span class="block font-bold">Saved Palettes</span><span class="text-xs opacity-60">Your
                        favourites</span>
                </div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="open-source.php" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div>
                    <span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span>
                </div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-28 px-6 md:px-12 lg:px-16 flex flex-col justify-center items-center">
        <!-- Hero Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full mb-16 mt-6">
            <!-- Left Text Content -->
            <div class="lg:col-span-7 text-center lg:text-left">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight mb-6 leading-tight">
                    Create & Explore <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Professional
                        Colors</span>
                </h1>
                <p
                    class="text-base md:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-medium mb-8 max-w-2xl mx-auto lg:mx-0">
                    Color Magic is the ultimate open-source suite for designers,
                    developers, and color enthusiasts. Generate harmonic palettes,
                    search colors by hex codes, and copy curated palettes instantly.
                </p>
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                    <a href="palettes.php"
                        class="px-6 py-3 bg-gradient-to-r from-secondary to-primary hover:opacity-95 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20">
                        Explore Palettes
                    </a>
                    <a href="generate-palette.php"
                        class="px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition-all">
                        Generate Now
                    </a>
                </div>
            </div>

            <!-- Right Animation Section -->
            <div
                class="lg:col-span-5 hidden lg:flex items-center justify-center h-[350px] relative overflow-hidden animation-container w-full">
                <!-- Column 1 (Scrolls Up) -->
                <div class="w-1/2 px-2 flex flex-col gap-4 animate-scroll-up">
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #6366f1"></div>
                            <div class="flex-1" style="background-color: #8b5cf6"></div>
                            <div class="flex-1" style="background-color: #d946ef"></div>
                            <div class="flex-1" style="background-color: #ec4899"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Neon Sunset
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #fca5a5"></div>
                            <div class="flex-1" style="background-color: #fef08a"></div>
                            <div class="flex-1" style="background-color: #a7f3d0"></div>
                            <div class="flex-1" style="background-color: #93c5fd"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Pastel Dream
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #10b981"></div>
                            <div class="flex-1" style="background-color: #059669"></div>
                            <div class="flex-1" style="background-color: #047857"></div>
                            <div class="flex-1" style="background-color: #064e3b"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Forest Hills
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #f97316"></div>
                            <div class="flex-1" style="background-color: #ea580c"></div>
                            <div class="flex-1" style="background-color: #c2410c"></div>
                            <div class="flex-1" style="background-color: #7c2d12"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Autumn Fire
                        </div>
                    </div>
                    <!-- Duplicate for infinite scroll -->
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #6366f1"></div>
                            <div class="flex-1" style="background-color: #8b5cf6"></div>
                            <div class="flex-1" style="background-color: #d946ef"></div>
                            <div class="flex-1" style="background-color: #ec4899"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Neon Sunset
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #fca5a5"></div>
                            <div class="flex-1" style="background-color: #fef08a"></div>
                            <div class="flex-1" style="background-color: #a7f3d0"></div>
                            <div class="flex-1" style="background-color: #93c5fd"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Pastel Dream
                        </div>
                    </div>
                </div>

                <!-- Column 2 (Scrolls Down) -->
                <div class="w-1/2 px-2 flex flex-col gap-4 animate-scroll-down">
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #1e293b"></div>
                            <div class="flex-1" style="background-color: #334155"></div>
                            <div class="flex-1" style="background-color: #475569"></div>
                            <div class="flex-1" style="background-color: #64748b"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Slate Monochrome
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #06b6d4"></div>
                            <div class="flex-1" style="background-color: #0891b2"></div>
                            <div class="flex-1" style="background-color: #0e7490"></div>
                            <div class="flex-1" style="background-color: #155e75"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Deep Ocean
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #e2e8f0"></div>
                            <div class="flex-1" style="background-color: #cbd5e1"></div>
                            <div class="flex-1" style="background-color: #94a3b8"></div>
                            <div class="flex-1" style="background-color: #64748b"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Cool Grey
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #f43f5e"></div>
                            <div class="flex-1" style="background-color: #e11d48"></div>
                            <div class="flex-1" style="background-color: #be123c"></div>
                            <div class="flex-1" style="background-color: #9f1239"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Rose Crimson
                        </div>
                    </div>
                    <!-- Duplicate for infinite scroll -->
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #1e293b"></div>
                            <div class="flex-1" style="background-color: #334155"></div>
                            <div class="flex-1" style="background-color: #475569"></div>
                            <div class="flex-1" style="background-color: #64748b"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Slate Monochrome
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-3 rounded-2xl border border-pink-100/50 dark:border-slate-800/80 shadow-sm flex flex-col gap-1.5 shrink-0">
                        <div class="flex h-12 rounded-lg overflow-hidden">
                            <div class="flex-1" style="background-color: #06b6d4"></div>
                            <div class="flex-1" style="background-color: #0891b2"></div>
                            <div class="flex-1" style="background-color: #0e7490"></div>
                            <div class="flex-1" style="background-color: #155e75"></div>
                        </div>
                        <div
                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 text-center uppercase tracking-wider">
                            Deep Ocean
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4-Card Grid Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 w-full mb-16">
            <!-- Card 1: Explore Palettes -->
            <a href="palettes.php"
                class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110">
                </div>
                <div>
                    <div
                        class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-2xl mb-6">
                        <i class="bi bi-palette-fill"></i>
                    </div>
                    <h3
                        class="text-xl md:text-4xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-primary transition-colors">
                        Explore Palettes
                    </h3>
                    <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-6">
                        Discover thousands of curated, trending color palettes. Filter by
                        style, save your favorites, and copy hex codes with a single
                        click.
                    </p>
                </div>
                <!-- Mini Visual Preview -->
                <div
                    class="flex gap-1.5 h-6 rounded-lg overflow-hidden w-full border border-slate-100 dark:border-slate-800">
                    <div class="flex-1 bg-violet-600"></div>
                    <div class="flex-1 bg-fuchsia-500"></div>
                    <div class="flex-1 bg-pink-400"></div>
                    <div class="flex-1 bg-amber-300"></div>
                    <div class="flex-1 bg-emerald-400"></div>
                </div>
            </a>

            <!-- Card 2: Generate Palette -->
            <a href="generate-palette.php"
                class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110">
                </div>
                <div>
                    <div
                        class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary text-2xl mb-6">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h3
                        class="text-xl md:text-4xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-secondary transition-colors">
                        Generate Palette
                    </h3>
                    <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-6">
                        Design unique color schemes based on color theory. Generate
                        monochromatic, analogous, complementary, and triadic harmonies
                        instantly.
                    </p>
                </div>
                <!-- Mini Visual Preview -->
                <div class="flex items-center gap-2 justify-center py-1">
                    <div class="w-4 h-4 rounded-full bg-violet-500 ring-2 ring-violet-200 dark:ring-violet-900"></div>
                    <div class="w-3 h-0.5 bg-slate-300 dark:bg-slate-700"></div>
                    <div class="w-4 h-4 rounded-full bg-fuchsia-500 ring-2 ring-fuchsia-200 dark:ring-fuchsia-900">
                    </div>
                    <div class="w-3 h-0.5 bg-slate-300 dark:bg-slate-700"></div>
                    <div class="w-4 h-4 rounded-full bg-pink-500 ring-2 ring-pink-200 dark:ring-pink-900"></div>
                </div>
            </a>

            <!-- Card 3: Find Color -->
            <a href="find-color.php"
                class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110">
                </div>
                <div>
                    <div
                        class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 text-2xl mb-6">
                        <i class="bi bi-eyedropper"></i>
                    </div>
                    <h3
                        class="text-xl md:text-4xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-emerald-500 transition-colors">
                        Find Color
                    </h3>
                    <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-6">
                        Convert Hex codes to human-readable names. Access exact values for
                        RGB and HSL formats, review contrast levels, and download shade
                        profiles.
                    </p>
                </div>
                <!-- Mini Visual Preview -->
                <div
                    class="font-mono text-xs text-slate-400 dark:text-slate-600 flex justify-between items-center bg-slate-50 dark:bg-slate-800/40 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-800/80">
                    <span>#EC4899</span>
                    <i class="bi bi-arrow-right"></i>
                    <span>RGB(236, 72, 153)</span>
                </div>
            </a>

            <!-- Card 4: Open Source -->
            <a href="open-source.php"
                class="card-hover bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 flex flex-col justify-between transition-all duration-300 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-slate-500/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110">
                </div>
                <div>
                    <div
                        class="w-12 h-12 bg-slate-500/10 rounded-2xl flex items-center justify-center text-slate-600 dark:text-slate-400 text-2xl mb-6">
                        <i class="bi bi-github"></i>
                    </div>
                    <h3
                        class="text-xl md:text-4xl font-bold mb-3 text-slate-800 dark:text-white group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors">
                        Open Source
                    </h3>
                    <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-6">
                        Explore the code behind Color Magic. Built entirely with vanilla
                        HTML, CSS, and JS. Fork, modify, and contribute back to the
                        project on GitHub.
                    </p>
                </div>
                <!-- Mini Visual Preview -->
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-1"><i class="bi bi-star-fill text-amber-400"></i> Star on
                        GitHub</span>
                </div>
            </a>
        </div>

    </main>

    <?php include 'components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE = "home";
    </script>
    <script src="assets/js/sidebar.js" defer></script>
</body>

</html>