<?php
/**
 * pages/admin.php
 * Color Magic - Administrative Portal
 * Protected by 12-hour JWT session authentication and Google Admin verification.
 */

require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../auth/AdminAuth.php';

$admin = AdminAuth::getCurrentAdmin();

// If not logged in or not an admin, handle redirect or prompt
$isAuthenticatedAdmin = ($admin !== null);
$adminEmail = $admin['email'] ?? ($_SESSION['user']['email'] ?? '');
$adminName  = $admin['name'] ?? ($_SESSION['user']['name'] ?? 'Administrator');
$adminPic   = $admin['picture'] ?? ($_SESSION['user']['picture'] ?? null);
$expTimestamp = $admin['exp'] ?? (time() + 43200);
$timeLeftSeconds = max(0, $expTimestamp - time());

if (!$isAuthenticatedAdmin) {
    // If not authenticated admin, redirect directly to the home screen
    header('Location: ' . ($base ?: '') . '/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Portal — Color Magic</title>
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#ec4899',
                        secondary: '#8b5cf6',
                        darkBg: '#0f172a',
                        cardDark: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Glassmorphism effects */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Pulse glow animation for session indicator */
        @keyframes pulseGlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.15); }
        }
        .pulse-indicator {
            animation: pulseGlow 2.5s infinite ease-in-out;
        }

        /* Modal transitions */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        .modal-active {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .modal-exit {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
            transition: all 0.22s ease-in;
        }

        /* Spinning gradient ring */
        @keyframes spinRing {
            to { transform: rotate(360deg); }
        }
        .spin-ring {
            animation: spinRing 1.8s linear infinite;
        }

        /* Slim, sleek scrollbar for sidebar */
        #adminSidebar::-webkit-scrollbar {
            width: 5px;
        }
        #adminSidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        #adminSidebar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.25);
            border-radius: 9999px;
        }
        #adminSidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.45);
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 dark:bg-darkBg text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200">

    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- ══ SIDEBAR ══════════════════════════════════════════════════════════ -->
        <!-- Fixed to viewport screen height on desktop (md:h-screen, md:sticky md:top-0) -->
        <aside id="adminSidebar" class="w-full md:w-64 md:h-screen md:sticky md:top-0 md:max-h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between flex-shrink-0 z-30 shadow-sm md:shadow-none overflow-y-auto">
            
            <!-- Top Section -->
            <div>
                <!-- Brand Header -->
                <div class="h-16 px-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                    <a href="<?= $base ?>/" class="flex items-center gap-2.5">
                        <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic Logo" class="w-7 h-7 object-contain" />
                        <span class="font-extrabold text-lg tracking-tight">
                            <span>Color</span><span class="bg-gradient-to-r from-purple-500 to-pink-500 bg-clip-text text-transparent">Magic</span>
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700 dark:bg-purple-900/60 dark:text-purple-300 rounded-md ml-1">Admin</span>
                    </a>
                    <!-- Mobile sidebar toggle -->
                    <button id="mobileMenuBtn" class="md:hidden text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 p-1">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5" id="sidebarNav">
                    <a href="<?= $base ?>/admin" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold text-sm transition-all shadow-sm shadow-purple-500/10">
                        <i class="bi bi-speedometer2 text-lg text-purple-600 dark:text-purple-400"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= $base ?>/palettes" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium text-sm transition-all">
                        <i class="bi bi-palette text-lg"></i>
                        <span>Color Palettes</span>
                    </a>
                    <a href="<?= $base ?>/gradients" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium text-sm transition-all">
                        <i class="bi bi-rainbow text-lg"></i>
                        <span>Gradients</span>
                    </a>
                    <a href="<?= $base ?>/profile" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium text-sm transition-all">
                        <i class="bi bi-person-badge text-lg"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="<?= $base ?>/" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium text-sm transition-all">
                        <i class="bi bi-globe text-lg"></i>
                        <span>Live Site</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Section: Session countdown & Logout -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <!-- 12-Hour Session Countdown Box -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-indicator"></span>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">12h Session</span>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded">Active</span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <span id="sessionCountdown" class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200">12h 00m 00s</span>
                        <span class="text-[10px] text-slate-400">JWT HS256</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-1 rounded-full mt-2 overflow-hidden">
                        <div id="sessionProgressBar" class="bg-gradient-to-r from-purple-500 to-pink-500 h-full w-full transition-all duration-1000"></div>
                    </div>
                </div>

                <!-- Admin User Info Pill -->
                <div class="flex items-center gap-3 px-2 py-1">
                    <?php if ($adminPic): ?>
                        <img src="<?= htmlspecialchars($adminPic) ?>" alt="Admin" class="w-9 h-9 rounded-full object-cover shadow-sm ring-2 ring-purple-500/20" />
                    <?php else: ?>
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                            <?= strtoupper(substr($adminEmail, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($adminName) ?></p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate"><?= htmlspecialchars($adminEmail) ?></p>
                    </div>
                </div>

                <!-- REQUIRED: Logout Button on Sidebar -->
                <a href="<?= $base ?>/auth/logout" id="sidebarLogoutBtn" class="w-full flex items-center justify-center gap-2.5 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/40 text-sm font-semibold transition-all group shadow-sm">
                    <i class="bi bi-box-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- ══ MAIN CONTENT WRAPPER ═════════════════════════════════════════════ -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Button -->
                    <button id="headerMobileMenuBtn" type="button" aria-label="Toggle menu" class="md:hidden text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    <span class="text-xs font-medium text-slate-400">Admin Portal</span>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Overview</span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Session indicator pill -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40 text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 pulse-indicator"></span>
                        <span>12h Session Active</span>
                    </div>

                    <!-- Theme toggle -->
                    <button id="themeToggleBtn" type="button" aria-label="Toggle dark mode" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="bi bi-sun-fill hidden dark:inline text-amber-400"></i>
                        <i class="bi bi-moon-stars-fill inline dark:hidden text-slate-600"></i>
                    </button>

                    <!-- View Live App -->
                    <a href="<?= $base ?>/" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold transition-all">
                        <span>View Live App</span>
                        <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                    </a>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-6 sm:p-8 max-w-7xl w-full mx-auto space-y-8">

                <!-- ══ HERO BANNER: "YOU ARE ON THE ADMIN PAGE" ═════════════════ -->
                <div class="glass-card relative overflow-hidden rounded-3xl p-7 sm:p-9 border border-purple-100 dark:border-slate-800 shadow-sm">
                    <!-- Decorative background gradient blurs -->
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-purple-500/10 dark:bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-pink-500/10 dark:bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 text-xs font-bold mb-3 border border-purple-200 dark:border-purple-800/40">
                                <i class="bi bi-shield-check"></i>
                                <span>Verified Admin Portal</span>
                            </div>
                            <!-- Core message requested by user -->
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                You are on the <span class="bg-gradient-to-r from-purple-600 via-fuchsia-600 to-pink-600 bg-clip-text text-transparent">admin page</span>
                            </h1>
                            <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base mt-2 max-w-2xl">
                                Welcome, <strong class="text-slate-800 dark:text-slate-200"><?= htmlspecialchars($adminName) ?></strong> (<span class="text-purple-600 dark:text-purple-400"><?= htmlspecialchars($adminEmail) ?></span>). Your administrative privileges are active with a secure 12-hour morning-to-evening JWT session.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="<?= $base ?>/profile" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all shadow-sm">
                                <i class="bi bi-person"></i>
                                <span>Profile</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ══ OVERVIEW STATS ROW ═══════════════════════════════════════ -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Stat 1 -->
                    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="bi bi-palette-fill"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Curated Palettes</span>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">400+ Palettes</h3>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="bi bi-eyedropper"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Named Colors</span>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">1,500+ Colors</h3>
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="bi bi-rainbow"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">CSS Gradients</span>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">200+ Gradients</h3>
                        </div>
                    </div>

                    <!-- Stat 4 -->
                    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Session Duration</span>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">12 Hours</h3>
                        </div>
                    </div>
                </div>

                <!-- ══ SECURITY & JWT SESSION DIAGNOSTICS CARD ══════════════════ -->
                <div class="glass-card rounded-2xl p-6 sm:p-7 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg">
                                <i class="bi bi-lock-fill"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-base">JWT Authentication &amp; Storage Architecture</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Industry-standard authentication and session resilience strategy</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 text-xs font-mono font-semibold">
                            HS256 Signature Verified
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Spec 1 -->
                        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Storage Strategy</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">HttpOnly Cookie + LocalStorage</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Protected against XSS via <code class="font-mono text-purple-600 dark:text-purple-400">cm_admin_jwt</code> cookie with client synchronization in <code class="font-mono">localStorage</code>.
                            </p>
                        </div>

                        <!-- Spec 2 -->
                        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Expiration Cycle</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">12 Hours (Morning to Evening)</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Configured for exactly 43,200 seconds. When elapsed, the inline UI popup checks your verified email and seamlessly re-authenticates.
                            </p>
                        </div>

                        <!-- Spec 3 -->
                        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">Identity &amp; Database</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($adminEmail) ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Verified in MySQL database with role <code class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">admin</code> and environment configuration.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ══ QUICK ADMINISTRATIVE SHORTCUTS ═══════════════════════════ -->
                <div class="glass-card rounded-2xl p-6 sm:p-7 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                        <i class="bi bi-grid-fill text-purple-500"></i>
                        Quick Navigation &amp; Controls
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        <a href="<?= $base ?>/profile" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50/30 dark:hover:bg-purple-950/20 transition-all flex flex-col gap-1">
                            <i class="bi bi-person-check text-xl text-purple-600 dark:text-purple-400"></i>
                            <span class="font-semibold text-sm text-slate-900 dark:text-white">Admin Profile</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Favorites &amp; settings</span>
                        </a>
                        <a href="<?= $base ?>/generate-palette" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50/30 dark:hover:bg-purple-950/20 transition-all flex flex-col gap-1">
                            <i class="bi bi-stars text-xl text-pink-600 dark:text-pink-400"></i>
                            <span class="font-semibold text-sm text-slate-900 dark:text-white">Generate Palette</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">AI palette creation</span>
                        </a>
                        <a href="<?= $base ?>/palette-from-image" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50/30 dark:hover:bg-purple-950/20 transition-all flex flex-col gap-1">
                            <i class="bi bi-image text-xl text-fuchsia-600 dark:text-fuchsia-400"></i>
                            <span class="font-semibold text-sm text-slate-900 dark:text-white">Palette from Image</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Image extraction</span>
                        </a>
                        <a href="<?= $base ?>/find-color" class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50/30 dark:hover:bg-purple-950/20 transition-all flex flex-col gap-1">
                            <i class="bi bi-search text-xl text-indigo-600 dark:text-indigo-400"></i>
                            <span class="font-semibold text-sm text-slate-900 dark:text-white">Find Color</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Named color search</span>
                        </a>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- ══ AUTHENTICATING... 12-HOUR SESSION RENEWAL POPUP MODAL ════════════════ -->
    <div id="authRenewalModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md hidden opacity-0 transition-opacity duration-300">
        
        <div id="authModalCard" class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-7 shadow-2xl border border-purple-100 dark:border-slate-800 text-center modal-enter">
            
            <!-- Animated Spinner Icon -->
            <div class="relative w-20 h-20 mx-auto mb-5 flex items-center justify-center">
                <!-- Glowing spinning halo ring -->
                <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-purple-600 via-pink-500 to-indigo-500 p-[3px] spin-ring">
                    <div class="w-full h-full bg-white dark:bg-slate-900 rounded-full"></div>
                </div>
                <!-- Center Shield Icon -->
                <div id="authModalIcon" class="relative z-10 w-14 h-14 rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-500/30">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
            </div>

            <!-- Title & Status -->
            <h3 id="authModalTitle" class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Authenticating...
            </h3>
            
            <p id="authModalSubtitle" class="text-sm text-slate-600 dark:text-slate-400 mt-2 px-2">
                Your 12-hour session expired. Verifying administrative authorization for <strong class="text-purple-600 dark:text-purple-400 font-mono"><?= htmlspecialchars($adminEmail) ?></strong>...
            </p>

            <!-- Animated Progress Bar -->
            <div id="authModalProgressWrap" class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full mt-6 overflow-hidden">
                <div id="authModalProgressBar" class="bg-gradient-to-r from-purple-600 to-pink-600 h-full w-0 transition-all duration-700 ease-out"></div>
            </div>

            <!-- Result Feedback Badge -->
            <div id="authModalBadge" class="mt-4 hidden text-xs font-semibold py-1.5 px-3 rounded-lg inline-block"></div>
        </div>
    </div>

    <!-- ══ CLIENT-SIDE SESSION MANAGEMENT & 12-HOUR RE-AUTH SCRIPT ══════════════ -->
    <script>
        // Server bootstrap parameters
        const ADMIN_EXP_TIMESTAMP = <?= (int)$expTimestamp ?>;
        const ADMIN_EMAIL = <?= json_encode($adminEmail) ?>;
        const BASE_URL = <?= json_encode($base) ?>;

        let sessionExpTime = ADMIN_EXP_TIMESTAMP;
        let isRenewing = false;

        // Initialize localStorage if missing
        try {
            if (!localStorage.getItem('cm_admin_exp') && ADMIN_EXP_TIMESTAMP) {
                localStorage.setItem('cm_admin_exp', ADMIN_EXP_TIMESTAMP.toString());
            }
        } catch(e) {}

        // ══ REAL-TIME CROSS-TAB & CROSS-PAGE LOGOUT SYNCHRONIZATION ══
        function handleImmediateLogout() {
            try {
                localStorage.removeItem('cm_user');
                localStorage.removeItem('cm_admin_token');
                localStorage.removeItem('cm_admin_exp');
                localStorage.removeItem('cm_admin_user');
                sessionStorage.clear();
            } catch(e) {}
            window.location.replace(`${BASE_URL}/`);
        }

        // 1. Storage event: fires instantly in this tab when user logs out from ANY other tab/page
        window.addEventListener('storage', (e) => {
            if (e.key === 'cm_logout_event' || e.key === 'cm_user' || e.key === 'cm_admin_token') {
                if (!localStorage.getItem('cm_user') || !localStorage.getItem('cm_admin_token')) {
                    handleImmediateLogout();
                }
            }
        });

        // 2. Focus event: when switching back to this tab, revalidate active admin session immediately
        window.addEventListener('focus', async () => {
            try {
                const res = await fetch(`${BASE_URL}/auth/admin-auth?action=check`, {
                    method: 'GET',
                    headers: { 'Cache-Control': 'no-cache, no-store, must-revalidate' }
                });
                const data = await res.json();
                if (!res.ok || !data.success || !data.is_admin) {
                    handleImmediateLogout();
                }
            } catch (err) {
                // Network or session invalid
            }
        });

        // 3. Periodic check: ensure client storage maintains an active user
        setInterval(() => {
            if (!localStorage.getItem('cm_user') && !sessionStorage.getItem('cm_user')) {
                handleImmediateLogout();
            }
        }, 2000);

        // Dark mode toggle
        const themeBtn = document.getElementById('themeToggleBtn');
        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                try {
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                } catch(e) {}
            });
        }
        // Load initial theme preference
        try {
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch(e) {}

        // Mobile sidebar toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const headerMobileMenuBtn = document.getElementById('headerMobileMenuBtn');
        const sidebar = document.getElementById('adminSidebar');
        function toggleSidebar() {
            if (sidebar) sidebar.classList.toggle('hidden');
        }
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleSidebar);
        }
        if (headerMobileMenuBtn) {
            headerMobileMenuBtn.addEventListener('click', toggleSidebar);
        }

        /**
         * Countdown timer updating every second
         */
        function updateSessionTimer() {
            const now = Math.floor(Date.now() / 1000);
            const remaining = Math.max(0, sessionExpTime - now);

            const hours = Math.floor(remaining / 3600);
            const minutes = Math.floor((remaining % 3600) / 60);
            const seconds = remaining % 60;

            const formatted = `${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
            const countdownEl = document.getElementById('sessionCountdown');
            if (countdownEl) {
                countdownEl.textContent = formatted;
            }

            // Update progress bar (43200 seconds = 12 hours)
            const percent = Math.min(100, Math.max(0, (remaining / 43200) * 100));
            const barEl = document.getElementById('sessionProgressBar');
            if (barEl) {
                barEl.style.width = percent + '%';
            }

            // When countdown reaches 0 and not already renewing, trigger the authenticating popup!
            if (remaining <= 0 && !isRenewing) {
                triggerSessionRenewal(false);
            }
        }

        setInterval(updateSessionTimer, 1000);
        updateSessionTimer();

        /**
         * Trigger UI-matched "Authenticating..." popup and regenerate session
         * @param {boolean} manualTrigger
         */
        async function triggerSessionRenewal(manualTrigger = false) {
            if (isRenewing) return;
            isRenewing = true;

            const modal = document.getElementById('authRenewalModal');
            const card  = document.getElementById('authModalCard');
            const title = document.getElementById('authModalTitle');
            const sub   = document.getElementById('authModalSubtitle');
            const bar   = document.getElementById('authModalProgressBar');
            const icon  = document.getElementById('authModalIcon');
            const badge = document.getElementById('authModalBadge');

            // Reset modal appearance
            title.textContent = "Authenticating...";
            sub.innerHTML = `Your 12-hour session has concluded. Revalidating administrative credentials for <strong class="text-purple-600 dark:text-purple-400 font-mono">${ADMIN_EMAIL}</strong>...`;
            icon.innerHTML = `<i class="bi bi-shield-lock-fill"></i>`;
            icon.className = "relative z-10 w-14 h-14 rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-500/30";
            bar.style.width = '35%';
            badge.className = "mt-4 hidden text-xs font-semibold py-1.5 px-3 rounded-lg inline-block";

            // Reveal modal with smooth animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('modal-enter');
                card.classList.add('modal-active');
            }, 10);

            // Animate progress to 65%
            setTimeout(() => {
                if (bar) bar.style.width = '70%';
            }, 400);

            try {
                // Request regeneration from /auth/admin-auth?action=refresh
                const response = await fetch(`${BASE_URL}/auth/admin-auth?action=refresh`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                bar.style.width = '100%';

                if (response.ok && data.success) {
                    // Update session expiry timestamp
                    sessionExpTime = data.exp || (Math.floor(Date.now() / 1000) + 43200);

                    // Update client storage
                    try {
                        localStorage.setItem('cm_admin_token', data.token);
                        localStorage.setItem('cm_admin_exp', sessionExpTime.toString());
                        sessionStorage.setItem('cm_admin_token', data.token);
                        sessionStorage.setItem('cm_admin_exp', sessionExpTime.toString());
                    } catch(e) {}

                    // Success presentation
                    setTimeout(() => {
                        title.textContent = "Authenticated!";
                        sub.textContent = "Your administrative authorization has been verified and extended for 12 hours.";
                        icon.innerHTML = `<i class="bi bi-check-lg"></i>`;
                        icon.className = "relative z-10 w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30";
                        
                        badge.textContent = "Session Valid for Next 12 Hours";
                        badge.className = "mt-4 text-xs font-semibold py-1.5 px-3 rounded-lg inline-block bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800";
                    }, 500);

                    // Smoothly dismiss modal
                    setTimeout(() => {
                        card.classList.remove('modal-active');
                        card.classList.add('modal-exit');
                        modal.classList.add('opacity-0');
                        setTimeout(() => {
                            modal.classList.add('hidden');
                            card.classList.remove('modal-exit');
                            card.classList.add('modal-enter');
                            isRenewing = false;
                        }, 250);
                    }, 1700);

                } else {
                    throw new Error(data.error || 'Admin verification failed');
                }
            } catch (err) {
                console.error('Session renewal error:', err);
                bar.style.width = '100%';
                title.textContent = "Session Expired";
                sub.textContent = "Your session could not be renewed automatically. Please sign in with Google again.";
                icon.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i>`;
                icon.className = "relative z-10 w-14 h-14 rounded-full bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-rose-500/30";

                badge.textContent = "Redirecting to Home...";
                badge.className = "mt-4 text-xs font-semibold py-1.5 px-3 rounded-lg inline-block bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800";

                setTimeout(() => {
                    window.location.href = `${BASE_URL}/`;
                }, 1500);
            }
        }
    </script>
</body>
</html>
