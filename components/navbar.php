<header
        class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-8">
            <a href="/" class="flex items-center gap-2 text-primary hover:opacity-80 transition-opacity">
                <img src="assets/images/logo.png" alt="Color Magic by TechKreative Logo"
                    class="h-8 w-8 object-contain" />
                <h2 class="text-xl font-bold tracking-tight">
                    <span class="text-slate-900 dark:text-white">Color</span>
                    <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
                </h2>
            </a>

            <div class="flex items-center gap-3">
                <a href="/"
                    class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <i class="bi bi-house-door" aria-label="Home icon"></i>
                    <span>Home</span>
                </a>
                <div class="relative group">
                    <button
                        class="flex items-center gap-1 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-primary transition-colors focus:outline-none">
                        <span>Tools</span>
                        <i class="bi bi-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
                    </button>
                    <!-- Dropdown Menu sliding down -->
                    <div
                        class="absolute top-full right-0 mt-1 w-56 rounded-xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-xl opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50 p-2 text-left">
                        <a href="palettes.php"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-primary transition-colors">
                            <i class="bi bi-palette text-base text-primary"></i>
                            <span>Explore Palettes</span>
                        </a>
                        <a href="generate-palette.php"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-secondary transition-colors">
                            <i class="bi bi-stars text-base text-secondary"></i>
                            <span>Generate Palette</span>
                        </a>
                        <a href="find-color.php"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-emerald-500 transition-colors">
                            <i class="bi bi-eyedropper text-base text-emerald-500"></i>
                            <span>Find Color</span>
                        </a>
                        <a href="open-source.php"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-slate-600 transition-colors">
                            <i class="bi bi-github text-base text-slate-500"></i>
                            <span>Open Source</span>
                        </a>
                    </div>
                </div>
                <a href="open-source.php"
                    class="hidden lg:flex items-center gap-2 px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-all hover:scale-105 active:scale-95 group"
                    title="View on GitHub">
                    <i class="bi bi-github text-xl group-hover:rotate-12 transition-transform"
                        aria-label="GitHub icon"></i>
                    <span class="text-xs font-semibold">Open Source</span>
                </a>
            </div>
        </div>
    </header>