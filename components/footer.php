<footer
    class="border-t border-slate-200 dark:border-slate-800 bg-background-light dark:bg-background-dark py-12 px-6 mt-16">
    <div class="max-w-7xl mx-auto">
        <!-- Sitemap & Navigation Links -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 mb-10">
            <!-- 1. Tools -->
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">
                    Tools
                </h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $base ?>/find-color"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Find Color</a></li>
                    <li><a href="<?= $base ?>/generate-palette"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Generate Palette</a>
                    </li>
                    <li><a href="<?= $base ?>/palette-from-image"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Palette from Image</a>
                    </li>
                    <li><a href="<?= $base ?>/palettes"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Explore Palettes</a>
                    </li>
                    <li><a href="<?= $base ?>/gradients"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">CSS Gradients</a></li>
                </ul>
            </div>

            <!-- 2. Converters -->
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">
                    Converters
                </h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $base ?>/hex-to-color-name"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Hex to Color Name</a>
                    </li>
                    <li><a href="<?= $base ?>/hex-to-rgb"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Hex to RGB</a></li>
                    <li><a href="<?= $base ?>/what-color-is"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">What Color Is This?</a>
                    </li>
                    <li><a href="<?= $base ?>/dark-color-finder"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Dark Color Finder</a>
                    </li>
                    <li><a href="<?= $base ?>/brand-color-lookup"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Brand Colors</a></li>
                </ul>
            </div>

            <!-- 3. Company & Legal -->
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">
                    Company &amp; Legal
                </h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $base ?>/about"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">About Us</a></li>
                    <li><a href="<?= $base ?>/contact"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Contact Us</a></li>
                    <li><a href="<?= $base ?>/privacy-policy"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Privacy Policy</a></li>
                    <li><a href="<?= $base ?>/terms-and-conditions"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Terms of Service</a></li>
                    <li><a href="<?= $base ?>/cookie-policy"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Cookie Policy</a></li>
                </ul>
            </div>

            <!-- 4. Resources -->
            <div>
                <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">
                    Resources
                </h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $base ?>/open-source"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Open Source</a></li>
                    <li><a href="<?= $base ?>/profile"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">Favorites &amp; Profile</a></li>
                    <li><a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">GitHub Repository</a></li>
                    <li><a href="<?= $base ?>/sitemap.xml" target="_blank"
                            class="text-sm text-slate-500 hover:text-primary transition-colors">XML Sitemap</a></li>
                </ul>
            </div>

            <!-- 5. Follow Us -->
            <div class="col-span-2 sm:col-span-1">
                <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-white mb-3 uppercase tracking-wider">
                    Follow Us
                </h3>
                <ul class="space-y-2.5">
                    <li>
                        <a href="https://www.instagram.com/colormagic.techkreative/" target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-pink-600 transition-colors group">
                            <i class="bi bi-instagram text-base text-pink-500 group-hover:scale-110 transition-transform"></i>
                            <span>Instagram</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.pinterest.com/colormagic_techkreative/" target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-red-600 transition-colors group">
                            <i class="bi bi-pinterest text-base text-red-500 group-hover:scale-110 transition-transform"></i>
                            <span>Pinterest</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors group">
                            <i class="bi bi-github text-base text-slate-600 dark:text-slate-400 group-hover:scale-110 transition-transform"></i>
                            <span>GitHub</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div
            class="border-t border-slate-200 dark:border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm space-y-1">
                <p>
                    &copy; <?= date('Y') ?> <strong class="text-slate-900 dark:text-white">Color Magic</strong>. All rights reserved.
                </p>
                <p class="text-xs text-slate-500">
                    Engineered &amp; Powered by
                    <a href="https://techkreative.com" target="_blank" rel="noopener noreferrer"
                        class="text-primary hover:underline font-semibold">TechKreative</a>
                </p>
            </div>

            <!-- Quick Legal Nav Links -->
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-xs text-slate-500">
                <a href="<?= $base ?>/privacy-policy" class="hover:text-primary transition-colors">Privacy</a>
                <span>&bull;</span>
                <a href="<?= $base ?>/terms-and-conditions" class="hover:text-primary transition-colors">Terms</a>
                <span>&bull;</span>
                <a href="<?= $base ?>/cookie-policy" class="hover:text-primary transition-colors">Cookies</a>
                <span>&bull;</span>
                <a href="<?= $base ?>/contact" class="hover:text-primary transition-colors">Contact</a>
            </div>

            <!-- Social / Quick Icons -->
            <div class="flex items-center gap-4">
                <a href="https://colormagic.techkreative.com/"
                    class="text-slate-500 hover:text-primary transition-colors"
                    aria-label="Color Magic Home" title="Home">
                    <i class="bi bi-house-door text-lg"></i>
                </a>
                <a href="https://www.instagram.com/colormagic.techkreative/" target="_blank" rel="noopener noreferrer"
                    class="text-slate-500 hover:text-pink-600 transition-colors"
                    aria-label="Follow Color Magic on Instagram" title="Instagram">
                    <i class="bi bi-instagram text-lg"></i>
                </a>
                <a href="https://www.pinterest.com/colormagic_techkreative/" target="_blank" rel="noopener noreferrer"
                    class="text-slate-500 hover:text-red-600 transition-colors"
                    aria-label="Follow Color Magic on Pinterest" title="Pinterest">
                    <i class="bi bi-pinterest text-lg"></i>
                </a>
                <a href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" rel="noopener noreferrer"
                    class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
                    aria-label="Color Magic on GitHub" title="GitHub">
                    <i class="bi bi-github text-lg"></i>
                </a>
            </div>
        </div>
    </div>
</footer>