<?php /** @var string $base */ ?>
<header
        class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-8">
            <a href="<?= $base ?>/" class="flex items-center gap-2 text-primary hover:opacity-80 transition-opacity">
                <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic by TechKreative Logo"
                    class="h-8 w-8 object-contain" />
                <h2 class="text-xl font-bold tracking-tight">
                    <span class="text-slate-900 dark:text-white">Color</span>
                    <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
                </h2>
            </a>

            <div class="flex items-center gap-3">
                <a href="<?= $base ?>/"
                    class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    <!-- <i class="bi bi-house-door" aria-label="Home icon"></i> -->
                    <span>Home</span>
                </a>
                <div class="relative group">
                    <button
                        class="flex items-center gap-1 px-3 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-primary transition-colors focus:outline-none">
                        <!-- <i class="bi bi-tools" aria-label="Tools icon"></i> -->
                        <span>Tools</span>
                        <i class="bi bi-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
                    </button>
                    <!-- Dropdown Menu sliding down -->
                    <div
                        class="absolute top-full right-0 mt-0 w-64 rounded-xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-xl opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50 p-2 text-left">
                        <!-- Section: Explore -->
                        <p class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Explore</p>
                        <a href="<?= $base ?>/palettes"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-primary transition-colors">
                            <i class="bi bi-palette text-base text-primary"></i>
                            <span>Explore Palettes</span>
                        </a>
                        <a href="<?= $base ?>/gradients"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-amber-500 transition-colors">
                            <i class="bi bi-rainbow text-base text-amber-500"></i>
                            <span>Gradients</span>
                        </a>
                        <!-- Section: Create -->
                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                        <p class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Create</p>
                        <a href="<?= $base ?>/generate-palette"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-secondary transition-colors">
                            <i class="bi bi-stars text-base text-secondary"></i>
                            <span>Generate Palette</span>
                        </a>
                        <a href="<?= $base ?>/find-color"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-emerald-500 transition-colors">
                            <i class="bi bi-eyedropper text-base text-emerald-500"></i>
                            <span>Find Color</span>
                        </a>
                        <!-- Section: Converters -->
                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                        <p class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Converters</p>
                        <a href="<?= $base ?>/hex-to-color-name"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-violet-500 transition-colors">
                            <i class="bi bi-tag text-base text-violet-500"></i>
                            <span>Hex to Color Name</span>
                        </a>
                        <a href="<?= $base ?>/hex-to-rgb"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-secondary transition-colors">
                            <i class="bi bi-sliders text-base text-secondary"></i>
                            <span>Hex to RGB</span>
                        </a>
                        <a href="<?= $base ?>/what-color-is"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-amber-500 transition-colors">
                            <i class="bi bi-question-circle text-base text-amber-500"></i>
                            <span>What Color Is This?</span>
                        </a>
                        <a href="<?= $base ?>/dark-color-finder"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-indigo-500 transition-colors">
                            <i class="bi bi-moon-stars text-base text-indigo-500"></i>
                            <span>Dark Color Finder</span>
                        </a>
                        <a href="<?= $base ?>/brand-color-lookup"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-emerald-500 transition-colors">
                            <i class="bi bi-bookmark-star text-base text-emerald-500"></i>
                            <span>Brand Colors</span>
                        </a>
                        <!-- Section: Other -->
                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                        <a href="<?= $base ?>/favorites"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-red-500 transition-colors">
                            <i class="bi bi-heart-fill text-base text-red-500"></i>
                            <span>Favorites</span>
                        </a>
                        <a href="<?= $base ?>/open-source"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-slate-600 transition-colors">
                            <i class="bi bi-github text-base text-slate-500"></i>
                            <span>Open Source</span>
                        </a>
                    </div>
                </div>
                <a href="<?= $base ?>/open-source"
                    class="hidden lg:flex items-center gap-2 px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-all hover:scale-105 active:scale-95 group"
                    title="View on GitHub">
                    <i class="bi bi-github text-xl group-hover:rotate-12 transition-transform"
                        aria-label="GitHub icon"></i>
                    <span class="text-xs font-semibold">Open Source</span>
                </a>
            </div>
        </div>
    </header>