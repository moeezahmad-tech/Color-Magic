<footer
    class="border-t border-slate-200 dark:border-slate-800 bg-background-light dark:bg-background-dark py-10 px-6 mt-12">
    <div class="max-w-7xl mx-auto">
        <!-- Sitemap Links -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 mb-8">
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">Tools</h3>
                <ul class="space-y-2">
                    <li><a href="<?= $base ?>/find-color" class="text-sm text-slate-500 hover:text-primary transition-colors">Find Color</a></li>
                    <li><a href="<?= $base ?>/generate-palette" class="text-sm text-slate-500 hover:text-primary transition-colors">Generate Palette</a></li>
                    <li><a href="<?= $base ?>/palettes" class="text-sm text-slate-500 hover:text-primary transition-colors">Explore Palettes</a></li>
                    <li><a href="<?= $base ?>/gradients" class="text-sm text-slate-500 hover:text-primary transition-colors">Gradients</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">Converters</h3>
                <ul class="space-y-2">
                    <li><a href="<?= $base ?>/hex-to-color-name" class="text-sm text-slate-500 hover:text-primary transition-colors">Hex to Color Name</a></li>
                    <li><a href="<?= $base ?>/hex-to-rgb" class="text-sm text-slate-500 hover:text-primary transition-colors">Hex to RGB</a></li>
                    <li><a href="<?= $base ?>/what-color-is" class="text-sm text-slate-500 hover:text-primary transition-colors">What Color Is This?</a></li>
                    <li><a href="<?= $base ?>/dark-color-finder" class="text-sm text-slate-500 hover:text-primary transition-colors">Dark Color Finder</a></li>
                    <li><a href="<?= $base ?>/brand-color-lookup" class="text-sm text-slate-500 hover:text-primary transition-colors">Brand Colors</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">Resources</h3>
                <ul class="space-y-2">
                    <li><a href="<?= $base ?>/open-source" class="text-sm text-slate-500 hover:text-primary transition-colors">Open Source</a></li>
                    <li><a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer" class="text-sm text-slate-500 hover:text-primary transition-colors">GitHub</a></li>
                    <li><a href="<?= $base ?>/favorites" class="text-sm text-slate-500 hover:text-primary transition-colors">Favorites</a></li>
                </ul>
            </div>
        </div>
        <!-- Bottom Bar -->
        <div class="border-t border-slate-200 dark:border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-slate-600 dark:text-slate-400 text-sm">
                 Powered by
                <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer"
                    class="text-primary hover:underline font-semibold">TechKreative</a>
            </p>
            <div class="flex items-center gap-6">
                <a href="https://colormagic.techkreative.com/"
                    class="text-slate-500 hover:text-primary transition-colors">
                    <i class="bi bi-house-door text-xl"></i>
                </a>
                <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer"
                    class="text-slate-500 hover:text-primary transition-colors">
                    <i class="bi bi-github text-xl"></i>
                </a>
            </div>
        </div>
    </div>
</footer>