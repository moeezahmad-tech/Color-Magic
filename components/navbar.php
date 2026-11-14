<?php 
/** @var string $base */ 
/** @var string $apiBaseUrl */ 
require_once __DIR__ . '/../auth/AdminAuth.php';

$navUser = $_SESSION['user'] ?? null;
$navEmail = $navUser['email'] ?? null;
$navIsAdmin = AdminAuth::isAdminEmail($navEmail);
$navAvatar = $navUser['avatar'] ?? '1.webp';
$navAvatarUrl = $base . '/assets/avatars/' . $navAvatar;
$navName = $navUser['name'] ?? 'User';
?>
<script>
    window.COLORMAGIC_API_BASE = "<?= htmlspecialchars($apiBaseUrl ?? ($base . '/api'), ENT_QUOTES, 'UTF-8') ?>";
</script>
<header class="sticky top-0 z-50 w-full h-16 border-b border-slate-200/80 dark:border-slate-800 bg-white/85 dark:bg-background-dark/85 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-full flex items-center justify-between gap-4">
        
        <!-- ── LEFT: Brand Logo + Primary Nav ────────────────────────── -->
        <div class="flex items-center gap-8">
            <a href="<?= $base ?>/" class="flex items-center gap-2.5 text-primary hover:opacity-90 transition-opacity">
                <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic Logo"
                    width="32" height="32" class="h-8 w-8 object-contain" loading="eager" fetchpriority="high" />
                <h2 class="text-xl font-bold tracking-tight">
                    <span class="text-slate-900 dark:text-white">Color</span>
                    <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span>
                </h2>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-1" aria-label="Main Navigation">
                <a href="<?= $base ?>/palettes"
                    class="px-3 py-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Palettes
                </a>
                <a href="<?= $base ?>/gradients"
                    class="px-3 py-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                    Gradients
                </a>
                
                <!-- Tools Dropdown -->
                <div class="relative group">
                    <button type="button"
                        class="flex items-center gap-1 px-3 py-1.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none">
                        <span>Tools</span>
                        <i class="bi bi-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
                    </button>
                    <!-- Dropdown Content (with invisible hover bridge) -->
                    <div class="absolute top-full left-0 mt-1 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl opacity-0 translate-y-1 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-200 z-50 p-2 text-left before:content-[''] before:block before:absolute before:-top-3 before:left-0 before:right-0 before:h-3">
                        <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Explore</p>
                        <a href="<?= $base ?>/find-color" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-primary transition-colors">
                            <i class="bi bi-eyedropper text-primary"></i>
                            <span>Find Color Details</span>
                        </a>
                        <a href="<?= $base ?>/generate-palette" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-secondary transition-colors">
                            <i class="bi bi-stars text-secondary"></i>
                            <span>Generate Palette</span>
                        </a>
                        <a href="<?= $base ?>/palette-from-image" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-fuchsia-500 transition-colors">
                            <i class="bi bi-image text-fuchsia-500"></i>
                            <span>Palette from Image</span>
                        </a>
                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                        <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Converters &amp; Utilities</p>
                        <a href="<?= $base ?>/hex-to-color-name" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-violet-500 transition-colors">
                            <i class="bi bi-tag text-violet-500"></i>
                            <span>Hex to Color Name</span>
                        </a>
                        <a href="<?= $base ?>/hex-to-rgb" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-secondary transition-colors">
                            <i class="bi bi-sliders text-secondary"></i>
                            <span>Hex to RGB</span>
                        </a>
                        <a href="<?= $base ?>/what-color-is" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-amber-500 transition-colors">
                            <i class="bi bi-question-circle text-amber-500"></i>
                            <span>What Color Is This?</span>
                        </a>
                        <a href="<?= $base ?>/dark-color-finder" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-indigo-500 transition-colors">
                            <i class="bi bi-moon-stars text-indigo-500"></i>
                            <span>Dark Color Finder</span>
                        </a>
                        <a href="<?= $base ?>/brand-color-lookup" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 hover:text-emerald-500 transition-colors">
                            <i class="bi bi-bookmark-star text-emerald-500"></i>
                            <span>Brand Colors</span>
                        </a>
                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                        <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Community</p>
                        <div class="grid grid-cols-2 gap-1 px-1">
                            <a href="https://www.instagram.com/colormagic.techkreative/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-pink-600 transition-colors">
                                <i class="bi bi-instagram text-pink-500 text-xs"></i>
                                <span>Instagram</span>
                            </a>
                            <a href="https://www.pinterest.com/colormagic_techkreative/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-red-600 transition-colors">
                                <i class="bi bi-pinterest text-red-500 text-xs"></i>
                                <span>Pinterest</span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- ── RIGHT: Favorites, GitHub, User Profile ───────────────── -->
        <div class="flex items-center gap-2 sm:gap-3">
            
            <!-- Favorites Button -->
            <a href="<?= $base ?>/favorites" rel="nofollow"
                class="flex items-center justify-center w-9 h-9 rounded-full text-slate-600 dark:text-slate-300 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all"
                title="Saved Favorites" aria-label="Saved Favorites">
                <i class="bi bi-heart text-base"></i>
            </a>

            <!-- GitHub / Open Source -->
            <a href="<?= $base ?>/open-source"
                class="hidden sm:flex items-center justify-center w-9 h-9 rounded-full text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                title="Open Source on GitHub" aria-label="Open Source">
                <i class="bi bi-github text-base"></i>
            </a>

            <!-- User Auth Container -->
            <div id="navAuthSlot" class="relative">
                <?php if ($navUser): ?>
                    <!-- Logged In User Avatar Button with Dropdown -->
                    <div class="relative flex items-center" id="userMenuContainer">
                        <a href="<?= $base ?>/profile" rel="nofollow" id="userMenuBtn"
                            class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-primary/30 transition-all focus:outline-none"
                            title="View Profile" aria-label="User Profile">
                            <img id="navAvatarImg" src="<?= htmlspecialchars($navAvatarUrl) ?>" alt="Profile"
                                class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200 dark:border-slate-700">
                            <i id="userChevron" class="bi bi-chevron-down text-[10px] text-slate-400 transition-transform duration-200 p-0.5 hover:text-primary"></i>
                        </a>
                        <!-- User Dropdown Menu (with invisible hover bridge & grace delay) -->
                        <div id="userDropdownMenu"
                            class="absolute top-full right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl opacity-0 translate-y-1 pointer-events-none transition-all duration-200 z-50 p-2 text-left before:content-[''] before:block before:absolute before:-top-3 before:left-0 before:right-0 before:h-3">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <p id="navDropdownName" class="font-bold text-sm text-slate-900 dark:text-white truncate"><?= htmlspecialchars($navName) ?></p>
                                <p id="navDropdownEmail" class="text-xs text-slate-400 truncate"><?= htmlspecialchars($navEmail ?? '') ?></p>
                            </div>
                            <div class="py-1">
                                <a href="<?= $base ?>/profile" rel="nofollow" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <i class="bi bi-pencil-square text-sm text-slate-400"></i>
                                    <span>Edit Profile</span>
                                </a>
                                <a href="<?= $base ?>/favorites" rel="nofollow" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <i class="bi bi-heart text-sm text-rose-500"></i>
                                    <span>Saved Favorites</span>
                                </a>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800 pt-1">
                                <a href="<?= $base ?>/auth/logout" rel="nofollow" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    <i class="bi bi-box-arrow-right text-sm"></i>
                                    <span>Sign Out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Logged Out Sign In Button -->
                    <a id="navLoginBtn" href="<?= $base ?>/login" rel="nofollow"
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 rounded-full font-bold text-xs shadow-sm transition active:scale-95">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button type="button" id="mobileMenuToggle"
                class="md:hidden flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none"
                aria-label="Toggle Navigation Menu">
                <i class="bi bi-list text-xl"></i>
            </button>

        </div>

    </div>

    <!-- Mobile Drawer / Dropdown -->
    <div id="mobileMenuDrawer" class="hidden md:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-6 py-4 space-y-3">
        <a href="<?= $base ?>/palettes" class="block font-semibold text-sm text-slate-700 dark:text-slate-200 py-1.5">Color Palettes</a>
        <a href="<?= $base ?>/gradients" class="block font-semibold text-sm text-slate-700 dark:text-slate-200 py-1.5">CSS Gradients</a>
        <a href="<?= $base ?>/find-color" class="block font-semibold text-sm text-slate-700 dark:text-slate-200 py-1.5">Find Color</a>
        <a href="<?= $base ?>/generate-palette" class="block font-semibold text-sm text-slate-700 dark:text-slate-200 py-1.5">Generate Palette</a>
        <a href="<?= $base ?>/open-source" class="block font-semibold text-sm text-slate-700 dark:text-slate-200 py-1.5">Open Source</a>
    </div>

    <script>
        (function() {
            // Mobile Menu Toggle
            var toggleBtn = document.getElementById('mobileMenuToggle');
            var drawer = document.getElementById('mobileMenuDrawer');
            if (toggleBtn && drawer) {
                toggleBtn.addEventListener('click', function() {
                    drawer.classList.toggle('hidden');
                });
            }

            // User Menu Dropdown Handler with Smooth Grace Delay
            function initUserMenuDropdown() {
                var container = document.getElementById('userMenuContainer');
                var menu = document.getElementById('userDropdownMenu');
                var btn = document.getElementById('userMenuBtn');
                var chevron = document.getElementById('userChevron');
                if (!container || !menu) return;

                if (container.dataset.dropdownInit === 'true') return;
                container.dataset.dropdownInit = 'true';

                var closeTimer = null;
                var isOpen = false;

                function openMenu() {
                    if (closeTimer) {
                        clearTimeout(closeTimer);
                        closeTimer = null;
                    }
                    isOpen = true;
                    menu.classList.remove('opacity-0', 'translate-y-1', 'pointer-events-none');
                    menu.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                    if (chevron) chevron.classList.add('rotate-180');
                }

                function scheduleClose(delayMs) {
                    if (closeTimer) clearTimeout(closeTimer);
                    var wait = typeof delayMs === 'number' ? delayMs : 350;
                    closeTimer = setTimeout(function() {
                        isOpen = false;
                        menu.classList.add('opacity-0', 'translate-y-1', 'pointer-events-none');
                        menu.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    }, wait);
                }

                // Hover in: open immediately or cancel pending close
                container.addEventListener('mouseenter', openMenu);
                // Hover out: 350ms delay gives plenty of time to cross gap or briefly wander
                container.addEventListener('mouseleave', function() {
                    scheduleClose(350);
                });

                // Chevron click toggles dropdown menu on touch / click without navigating
                if (chevron) {
                    chevron.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (isOpen) {
                            scheduleClose(0);
                        } else {
                            openMenu();
                        }
                    });
                }

                // Outside click closes immediately
                document.addEventListener('click', function(e) {
                    if (!container.contains(e.target)) {
                        scheduleClose(0);
                    }
                });
            }

            // Hydrate client-side authentication if PHP session wasn't initialized
            try {
                var raw = localStorage.getItem('cm_user') || sessionStorage.getItem('cm_user');
                var slot = document.getElementById('navAuthSlot');
                var loginBtn = document.getElementById('navLoginBtn');
                if (raw && slot && loginBtn) {
                    var user = JSON.parse(raw);
                    if (user && user.email) {
                        var name = user.name || user.email.split('@')[0];
                        var av = user.avatar || '1.webp';
                        var picUrl = '<?= $base ?>/assets/avatars/' + av;

                        var adminLinkHtml = '';

                        slot.innerHTML = `
                            <div class="relative flex items-center" id="userMenuContainer">
                                <a href="<?= $base ?>/profile" rel="nofollow" id="userMenuBtn"
                                    class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-primary/30 transition-all focus:outline-none"
                                    title="View Profile" aria-label="User Profile">
                                    <img src="${picUrl}" alt="Profile"
                                        class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200 dark:border-slate-700">
                                    <i id="userChevron" class="bi bi-chevron-down text-[10px] text-slate-400 transition-transform duration-200 p-0.5 hover:text-primary"></i>
                                </a>
                                <div id="userDropdownMenu"
                                    class="absolute top-full right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl opacity-0 translate-y-1 pointer-events-none transition-all duration-200 z-50 p-2 text-left before:content-[''] before:block before:absolute before:-top-3 before:left-0 before:right-0 before:h-3">
                                    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                        <p class="font-bold text-sm text-slate-900 dark:text-white truncate">${name}</p>
                                        <p class="text-xs text-slate-400 truncate">${user.email}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="<?= $base ?>/profile" rel="nofollow" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                            <i class="bi bi-pencil-square text-sm text-slate-400"></i>
                                            <span>Edit Profile</span>
                                        </a>
                                        <a href="<?= $base ?>/favorites" rel="nofollow" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                            <i class="bi bi-heart text-sm text-rose-500"></i>
                                            <span>Saved Favorites</span>
                                        </a>
                                        ${adminLinkHtml}
                                    </div>
                                    <div class="border-t border-slate-100 dark:border-slate-800 pt-1">
                                        <a href="<?= $base ?>/auth/logout" rel="nofollow" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                            <i class="bi bi-box-arrow-right text-sm"></i>
                                            <span>Sign Out</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
            } catch(e) {}

            // Initialize user dropdown if container exists
            initUserMenuDropdown();
        })();
    </script>
</header>
