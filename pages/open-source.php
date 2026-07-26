<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Open Source | Color Magic - Professional Color Palette Generator
    </title>
    <meta name="description"
        content="Color Magic is an open-source color palette generator and explorer. Browse the code, contribute, and help make color selection easier for designers worldwide." />
    <meta name="keywords"
        content="open source, color palette, GitHub, color tool, design tool, contribute, color magic" />
    <meta name="author" content="TechKreative" />
    <meta name="robots" content="index, follow" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag("js", new Date());
        gtag("config", "G-537L4MR968");
    </script>

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/open-source" />
    <meta property="og:title" content="Open Source | Color Magic" />
    <meta property="og:description"
        content="Color Magic is an open-source project. Explore the code and contribute to make color selection easier for designers." />
    <meta property="og:image" content="<?= $base ?>/assets/images/logo.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/open-source" />
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

        .sb-btn:hover .sb-icon {
            transform: scale(1.12);
        }

        .sb-btn.sb-inactive {
            color: #475569;
            background: transparent;
        }

        .sb-btn.sb-inactive:hover {
            background: #fdf2f8;
            color: #ec4899;
        }

        .sb-btn.sb-inactive .sb-icon {
            background: #fdf2f8;
            color: #ec4899;
        }

        .sb-btn.sb-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            box-shadow: 0 8px 24px -6px rgba(236, 72, 153, 0.45);
        }

        .sb-btn.sb-active .sb-icon {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .dark .sb-btn.sb-inactive {
            color: #94a3b8;
        }

        .dark .sb-btn.sb-inactive:hover {
            background: #1e293b;
            color: #ec4899;
        }

        .dark .sb-btn.sb-inactive .sb-icon {
            background: #1e293b;
            color: #ec4899;
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
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
    <?php include '../components/navbar.php'; ?>


    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="<?= $base ?>/" class="flex items-center gap-2 text-primary">
                <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic Logo" class="h-8 w-8 object-contain" />
                <span class="text-xl font-bold tracking-tight"><span
                        class="text-slate-900 dark:text-white">Color</span><span
                        class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span></span>
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
                <div>
                    <span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="<?= $base ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div>
                    <span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span>
                </div>
            </a>
            <a href="<?= $base ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div>
                    <span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS
                        gradients</span>
                </div>
            </a>
            <a href="<?= $base ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div>
                    <span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span>
                </div>
            </a>
            <a href="<?= $base ?>/generate-palette" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-stars"></i></span>
                <div>
                    <span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span>
                </div>
            </a>
            <a href="<?= $base ?>/profile" class=" sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div>
                    <span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors,
                        palettes &amp; gradients</span>
                </div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?= $base ?>/open-source" class="sb-btn sb-active w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div>
                    <span class="block font-bold">Open Source</span><span class="text-xs opacity-75">View on
                        GitHub</span>
                </div>
            </a>
        </div>
    </div>

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ -->
    <main class="w-full max-w-7xl mx-auto pt-24 p-6 md:p-8 flex flex-col gap-6">
        <!-- Hero Section -->
        <div class="text-center mb-16 md:mb-24">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-bold mb-6">
                <i class="bi bi-code-slash"></i>
                <span>Open Source Project</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6">
                <span class="text-slate-900">Color</span>
                <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-400 mb-4">
                Professional Color Palette Generator & Explorer
            </p>
            <p class="text-lg text-slate-500 dark:text-slate-500">
                Founded by
                <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer"
                    class="text-primary hover:underline font-semibold">TechKreative</a>
            </p>
        </div>

        <!-- Project Overview -->
        <div class="mb-16 md:mb-20">
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-8 md:p-12 shadow-sm border border-slate-200 dark:border-slate-800">
                <h2 class="text-3xl md:text-4xl font-bold mb-6 flex items-center gap-3">
                    <i class="bi bi-palette text-primary"></i>
                    About the Project
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                    Color Magic is a beautiful, fully-featured web application designed
                    for designers and developers to explore color palettes, find color
                    names from hex codes, and generate professional color schemes using
                    color theory principles.
                </p>
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                    Built with vanilla JavaScript and modern web technologies, this
                    project demonstrates how powerful web applications can be created
                    without heavy frameworks, while maintaining excellent performance
                    and user experience.
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mb-16 md:mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">
                ✨ Features
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <!-- Find Color -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                        <i class="bi bi-eyedropper text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">🔍 Find Color</h3>
                    <ul class="space-y-2 text-slate-600 dark:text-slate-400">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Enter any hex code to discover color information</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Get color names from 300+ named colors</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>View RGB, HSL values, luminance, and contrast</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Copy hex and RGB values with one click</span>
                        </li>
                    </ul>
                </div>

                <!-- Explore Palettes -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                        <i class="bi bi-palette text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">🎨 Explore Palettes</h3>
                    <ul class="space-y-2 text-slate-600 dark:text-slate-400">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Browse 150+ professionally curated palettes</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Filter by style: Pastel, Vintage, Neon, Minimalist</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Search palettes by name, theme, or hex code</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Copy entire palettes to clipboard</span>
                        </li>
                    </ul>
                </div>

                <!-- Generate Palette -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                        <i class="bi bi-stars text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">⚡ Generate Palette</h3>
                    <ul class="space-y-2 text-slate-600 dark:text-slate-400">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Create 5-color palettes from any base color</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Choose from color theory schemes</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>Apply variations: Classic, Soft & Muted, Deep & Bold</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-primary mt-1"></i>
                            <span>All calculations done locally - no API required</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tech Stack -->
        <div class="mb-16 md:mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">
                🛠️ Tech Stack
            </h2>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-8 md:p-12 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-500/10 rounded-lg flex items-center justify-center">
                            <i class="bi bi-filetype-html text-xl text-orange-500"></i>
                        </div>
                        <div>
                            <p class="font-bold">HTML5</p>
                            <p class="text-sm text-slate-500">Semantic markup</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                            <i class="bi bi-filetype-css text-xl text-blue-500"></i>
                        </div>
                        <div>
                            <p class="font-bold">CSS3</p>
                            <p class="text-sm text-slate-500">Modern styling</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                            <i class="bi bi-filetype-js text-xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="font-bold">JavaScript</p>
                            <p class="text-sm text-slate-500">JS</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-cyan-500/10 rounded-lg flex items-center justify-center">
                            <i class="bi bi-wind text-xl text-cyan-500"></i>
                        </div>
                        <div>
                            <p class="font-bold">Tailwind CSS</p>
                            <p class="text-sm text-slate-500">Utility-first CSS</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center">
                            <i class="bi bi-bootstrap text-xl text-purple-500"></i>
                        </div>
                        <div>
                            <p class="font-bold">Bootstrap Icons</p>
                            <p class="text-sm text-slate-500">Icon library</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                            <i class="bi bi-google text-xl text-green-500"></i>
                        </div>
                        <div>
                            <p class="font-bold">Google Fonts</p>
                            <p class="text-sm text-slate-500">Inter</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GitHub Repository -->
        <div class="mb-16 md:mb-20">
            <div class="bg-gradient-to-r from-secondary to-primary rounded-2xl p-8 md:p-12 text-white text-center">
                <i class="bi bi-github text-6xl mb-6 inline-block"></i>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">View on GitHub</h2>
                <p class="text-lg mb-8 opacity-90">
                    Explore the source code, report issues, or contribute to the project
                </p>
                <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-white text-primary font-bold rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg">
                    <i class="bi bi-github text-2xl"></i>
                    <span>moeezahmad-tech/Color-Magic</span>
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </div>

        <!-- Getting Started -->
        <div class="mb-16 md:mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">
                🏃 Getting Started
            </h2>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-8 md:p-12 shadow-sm border border-slate-200 dark:border-slate-800">
                <p class="text-lg text-slate-600 dark:text-slate-400 mb-6">
                    Clone the repository and start exploring the code:
                </p>
                <div class="bg-slate-100 dark:bg-slate-800 rounded-xl p-6 font-mono text-sm mb-6 overflow-x-auto">
                    <code class="text-primary">git clone
              https://github.com/moeezahmad-tech/Color-Magic.git</code>
                </div>
                <p class="text-slate-600 dark:text-slate-400 mb-4">
                    Then simply open
                    <code class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-primary">index.php</code>
                    in your browser to run the application locally.
                </p>
                <p class="text-slate-600 dark:text-slate-400">
                    No build process or dependencies required - it's pure vanilla
                    JavaScript!
                </p>
            </div>
        </div>

        <!-- Top Contributors -->
        <div class="mb-16 md:mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">
                👥 Top Contributors
            </h2>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-8 md:p-12 shadow-sm border border-slate-200 dark:border-slate-800">
                <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 text-center">
                    Meet the amazing people who have contributed to Color Magic
                </p>
                <div class="flex flex-wrap justify-center gap-8">
                    <!-- Contributor: Moeez Ahmad -->
                    <a href="https://github.com/moeezahmad-tech" target="_blank" rel="noopener noreferrer"
                        class="group flex flex-col items-center gap-4 p-6 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all hover:scale-105">
                        <div class="relative">
                            <img src="<?= $base ?>/assets/images/TopContributers/MoeezAhmad.webp" alt="Moeez Ahmad"
                                class="w-24 h-24 rounded-full object-cover border-4 border-primary shadow-lg group-hover:shadow-xl transition-shadow" />
                            <div
                                class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary rounded-full flex items-center justify-center border-4 border-white dark:border-slate-900">
                                <i class="bi bi-github text-white text-sm"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="font-bold text-lg group-hover:text-primary transition-colors">
                                Moeez Ahmad
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                @moeezahmad-tech
                            </p>
                            <div class="mt-2 px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold">
                                Founder & Lead Developer
                            </div>
                        </div>
                    </a>

                    <!-- Placeholder for future contributors -->
                    <div
                        class="flex flex-col items-center justify-center gap-4 p-6 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 min-w-[200px]">
                        <div
                            class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <i class="bi bi-person-plus text-4xl text-slate-400"></i>
                        </div>
                        <div class="text-center">
                            <h3 class="font-bold text-slate-600 dark:text-slate-400">
                                Your Name Here
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">
                                Be a contributor!
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <a href="https://github.com/moeezahmad-tech/Color-Magic/graphs/contributors" target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 text-primary hover:underline font-semibold">
                        <i class="bi bi-people"></i>
                        <span>View all contributors on GitHub</span>
                        <i class="bi bi-arrow-up-right text-sm"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Contributing -->
        <div class="mb-16 md:mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">
                🤝 Contributing
            </h2>
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl p-8 md:p-12 shadow-sm border border-slate-200 dark:border-slate-800">
                <p class="text-lg text-slate-600 dark:text-slate-400 mb-6">
                    Contributions are welcome! Here's how you can help make Color Magic
                    even better:
                </p>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center shrink-0 mt-1">
                            <span class="text-primary font-bold">1</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Fork the Repository</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Create your own fork of the project
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center shrink-0 mt-1">
                            <span class="text-primary font-bold">2</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Create a Feature Branch</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Work on your changes in a dedicated branch
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center shrink-0 mt-1">
                            <span class="text-primary font-bold">3</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Make Your Changes</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Add features, fix bugs, or improve documentation
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center shrink-0 mt-1">
                            <span class="text-primary font-bold">4</span>
                        </div>
                        <div>
                            <h3 class="font-bold mb-1">Submit a Pull Request</h3>
                            <p class="text-slate-600 dark:text-slate-400">
                                Open a PR with a clear description of your changes
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 p-6 bg-primary/5 border border-primary/20 rounded-xl">
                    <p class="text-slate-600 dark:text-slate-400">
                        <i class="bi bi-info-circle text-primary mr-2"></i>
                        Feel free to open issues for bug reports, feature requests, or
                        questions about the project.
                    </p>
                </div>
            </div>
        </div>

        <!-- Try Our Tools -->
        <div class="mb-16 md:mb-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center">
                🧰 Try Our Tools
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="<?= $base ?>/find-color"
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 hover:shadow-lg transition group">
                    <i class="bi bi-eyedropper text-2xl text-emerald-500 mb-3 block"></i>
                    <h3 class="font-bold mb-1 group-hover:text-primary transition">Find Color</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Enter any hex code and get its name, RGB, HSL
                        and more</p>
                </a>
                <a href="<?= $base ?>/hex-to-color-name"
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 hover:shadow-lg transition group">
                    <i class="bi bi-tag text-2xl text-violet-500 mb-3 block"></i>
                    <h3 class="font-bold mb-1 group-hover:text-primary transition">Hex to Color Name</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Convert any hex code to a human-readable color
                        name</p>
                </a>
                <a href="<?= $base ?>/palettes"
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 hover:shadow-lg transition group">
                    <i class="bi bi-palette text-2xl text-primary mb-3 block"></i>
                    <h3 class="font-bold mb-1 group-hover:text-primary transition">Explore Palettes</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Browse 150+ curated color palettes</p>
                </a>
                <a href="<?= $base ?>/generate-palette"
                    class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 hover:shadow-lg transition group">
                    <i class="bi bi-stars text-2xl text-secondary mb-3 block"></i>
                    <h3 class="font-bold mb-1 group-hover:text-primary transition">Generate Palette</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Create color schemes using color theory</p>
                </a>
            </div>
        </div>

    </main>

    <?php include '../components/footer.php'; ?>

</body>

</html>