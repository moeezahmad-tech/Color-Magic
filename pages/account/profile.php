<?php 
require_once __DIR__ . '/../../components/config.php'; 
require_once __DIR__ . '/../../auth/AdminAuth.php';
require_once __DIR__ . '/../../auth/UserDb.php';
/** @var string $base */ 

$profileEmail = $_SESSION['user']['email'] ?? null;
$isAdmin = AdminAuth::isAdminEmail($profileEmail);

$dbUser = $profileEmail ? UserDb::getUserByEmail($profileEmail) : null;
$userAvatar = $dbUser['avatar'] ?? ($_SESSION['user']['avatar'] ?? '1.webp');
$avatarUrl = $base . '/assets/avatars/' . $userAvatar;
$userName = $dbUser['name'] ?? ($_SESSION['user']['name'] ?? '');
$userUsername = $dbUser['username'] ?? ($_SESSION['user']['username'] ?? '');
$userPronouns = $dbUser['pronouns'] ?? ($_SESSION['user']['pronouns'] ?? '');
$userBio = $dbUser['bio'] ?? ($_SESSION['user']['bio'] ?? '');
$userWebsite = $dbUser['website'] ?? ($_SESSION['user']['website'] ?? '');
$userCity = $dbUser['city'] ?? ($_SESSION['user']['city'] ?? '');
$userCountry = $dbUser['country'] ?? ($_SESSION['user']['country'] ?? '');
$userCreatedAt = $dbUser['created_at'] ?? null;
$userLastLogin = $dbUser['last_login'] ?? null;
$userRole = $dbUser['role'] ?? ($_SESSION['user']['role'] ?? ($isAdmin ? 'admin' : 'user'));
$createdFormatted = $userCreatedAt ? date('F j, Y', strtotime($userCreatedAt)) : null;
$lastLoginFormatted = $userLastLogin ? date('M j, Y, g:i A', strtotime($userLastLogin)) : null;
$isLoggedIn = !empty($profileEmail);

$avatarsFile = __DIR__ . '/../../data/avatars.json';
$avatarsList = file_exists($avatarsFile) ? json_decode((string)file_get_contents($avatarsFile), true) : [];
if (!is_array($avatarsList)) $avatarsList = [];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Edit Profile &amp; Settings | Color Magic</title>
    <meta name="description"
        content="Manage your personal details, choose your creative avatar, and explore all your saved colors, palettes, and gradients." />
    <meta name="keywords" content="edit profile, user settings, avatar, saved colors, favorite palettes, design system" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="noindex, nofollow" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/profile" />
    <meta property="og:title" content="Color Magic | Edit Profile &amp; Settings" />
    <meta property="og:description" content="Personalize your avatar, details, and access your saved collections." />
    <meta property="og:image" content="https://colormagic.techkreative.com/assets/og-preview.png" />
    <link rel="canonical" href="https://colormagic.techkreative.com/profile" />
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
        /* ── Pinterest-Style Sidebar Navigation ── */
        .profile-sidebar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #334155;
            text-align: left;
            white-space: nowrap;
        }

        .profile-sidebar-item:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .dark .profile-sidebar-item:hover {
            color: #f8fafc;
            background-color: #1e293b;
        }

        .profile-sidebar-item.active {
            color: #0f172a;
            font-weight: 800;
            background-color: transparent;
        }

        .dark .profile-sidebar-item.active {
            color: #ffffff;
            background-color: transparent;
        }

        .profile-sidebar-item.active span.tab-title {
            position: relative;
            display: inline-block;
        }

        .profile-sidebar-item.active span.tab-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #0f172a;
            border-radius: 9999px;
        }

        .dark .profile-sidebar-item.active span.tab-title::after {
            background-color: #ffffff;
        }

        .profile-sidebar-item .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            background: #e2e8f0;
            color: #475569;
            margin-left: 8px;
        }

        .dark .profile-sidebar-item .tab-badge {
            background: #334155;
            color: #cbd5e1;
        }

        .profile-sidebar-item.active .tab-badge {
            background: #0f172a;
            color: #ffffff;
        }

        .dark .profile-sidebar-item.active .tab-badge {
            background: #ffffff;
            color: #0f172a;
        }

        /* ── Pinterest-Style Rounded Input Boxes ── */
        .pinterest-input-box {
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .dark .pinterest-input-box {
            border-color: #334155;
            background: #0f172a;
        }

        .pinterest-input-box:focus-within {
            border-color: #0f172a;
            box-shadow: 0 0 0 1px #0f172a;
        }

        .dark .pinterest-input-box:focus-within {
            border-color: #f8fafc;
            box-shadow: 0 0 0 1px #f8fafc;
        }

        /* ── Card styles for Favorites ── */
        .color-fav-card {
            transition: all 0.25s ease;
        }

        .color-fav-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.15);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-fadeIn {
            animation: fadeIn 0.2s ease both;
        }
    </style>
</head>

<body class="bg-[#fbfbfb] dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include '../../components/navbar.php'; ?>

    <!-- ══ MAIN CONTENT WRAPPER ═════════════════════════════════════════════════ -->
    <main class="w-full max-w-full px-4 sm:px-8 md:px-12 lg:px-16 pt-6 sm:pt-8 pb-24">
        
        <div class="flex flex-col md:flex-row gap-8 lg:gap-16 items-start w-full">
            
            <!-- ── PINTEREST-STYLE SIDEBAR (LEFT) ────────────────────────────── -->
            <aside class="w-full md:w-60 lg:w-68 shrink-0 md:sticky md:top-24 z-10" aria-label="Profile Settings Navigation">
                <nav class="flex md:flex-col gap-1 overflow-x-auto pb-3 md:pb-0 scrollbar-none border-b md:border-b-0 border-slate-200 dark:border-slate-800">
                    <button type="button" class="profile-sidebar-item active" data-target="panelEditProfile">
                        <span class="tab-title">Edit profile</span>
                    </button>
                    <button type="button" class="profile-sidebar-item" data-target="panelAccount">
                        <span class="tab-title">Account management</span>
                    </button>
                    <a href="<?= $base ?>/favorites" class="profile-sidebar-item group">
                        <span class="tab-title flex items-center gap-1.5">
                            <i class="bi bi-heart-fill text-rose-500 text-xs"></i>
                            <span>Saved Favorites</span>
                        </span>
                        <i class="bi bi-arrow-up-right text-xs text-slate-400 group-hover:text-primary transition-colors"></i>
                    </a>
                </nav>
            </aside>

            <!-- ── RIGHT CONTENT PANELS ──────────────────────────────────────── -->
            <div class="flex-1 w-full min-w-0">

        <!-- ── 2. EDIT PROFILE PANEL (PINTEREST STYLE) ──────────────────────── -->
        <div id="panelEditProfile" class="profile-tab-panel">
            <!-- Title & Subtitle -->
            <div class="mb-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Edit profile
                        </h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed max-w-xl">
                            Keep your personal details private. Information you add here is visible to anyone who can view your profile.
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" id="resetProfileBtn" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition active:scale-95">
                            Reset
                        </button>
                        <button type="submit" form="pinterestProfileForm" id="saveProfileBtn" class="px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 disabled:opacity-75">
                            <i class="bi bi-floppy text-xs"></i>
                            <span id="saveProfileBtnText">Save</span>
                        </button>
                        <?php if ($isAdmin): ?>
                            <a id="adminHeroBtn" href="<?= $base ?>/admin" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-purple-600 via-fuchsia-600 to-pink-600 text-white rounded-full font-bold text-xs shadow-md shadow-purple-500/20 hover:scale-[1.02] transition-all">
                                <i class="bi bi-shield-check"></i>
                                <span>Admin Dashboard</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Photo / Avatar Row -->
            <div class="mb-8">
                <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Photo</span>
                <div class="flex items-center gap-4">
                    <img id="profilePhotoImg" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-18 h-18 sm:w-20 sm:h-20 rounded-full object-cover shadow-sm border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <button type="button" onclick="openAvatarModal()" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-bold text-sm transition">
                        Change
                    </button>
                </div>
            </div>

            <!-- Form Fields -->
            <form id="pinterestProfileForm" class="space-y-6 max-w-2xl">
                <!-- Name -->
                <div class="space-y-1">
                    <div class="pinterest-input-box">
                        <label for="pinterestName" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Name</label>
                        <input type="text" id="pinterestName" value="<?= htmlspecialchars($userName) ?>" placeholder="Moeez Ahmad"
                               class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5">
                    </div>
                </div>

                <!-- About / Bio -->
                <div class="space-y-1">
                    <div class="pinterest-input-box">
                        <label for="pinterestAbout" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">About</label>
                        <textarea id="pinterestAbout" rows="3" placeholder="Helping businesses 10x their design workflows..."
                                  class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5 resize-none"><?= htmlspecialchars($userBio) ?></textarea>
                    </div>
                </div>

                <!-- Pronouns -->
                <div class="space-y-1.5">
                    <div class="pinterest-input-box flex items-center justify-between">
                        <div class="flex-1">
                            <label for="pinterestPronouns" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Pronouns</label>
                            <input type="text" id="pinterestPronouns" list="pronounsList" value="<?= htmlspecialchars($userPronouns) ?>" placeholder="Add your pronouns"
                                   class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5">
                            <datalist id="pronounsList">
                                <option value="they/them">
                                <option value="she/her">
                                <option value="he/him">
                                <option value="she/they">
                                <option value="he/they">
                                <option value="they/she">
                                <option value="they/he">
                                <option value="prefer not to say">
                            </datalist>
                        </div>
                        <i class="bi bi-chevron-down text-slate-400 text-sm pointer-events-none"></i>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 leading-normal px-1">
                        Choose up to 2 sets of pronouns to appear on your profile so others know how to refer to you. You can edit or remove these any time.
                    </p>
                </div>

                <!-- Website -->
                <div class="space-y-1.5">
                    <div class="pinterest-input-box">
                        <label for="pinterestWebsite" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Website</label>
                        <input type="url" id="pinterestWebsite" value="<?= htmlspecialchars($userWebsite) ?>" placeholder="https://moeezahmad.techkreative.com"
                               class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5">
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 leading-normal px-1">
                        Add a link to drive traffic to your portfolio or brand site.
                    </p>
                </div>

                <!-- Username -->
                <div class="space-y-1.5">
                    <div class="pinterest-input-box">
                        <label for="pinterestUsername" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Username</label>
                        <input type="text" id="pinterestUsername" value="<?= htmlspecialchars($userUsername) ?>" placeholder="moeezahmadtech"
                               class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5">
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 leading-normal px-1">
                        colormagic.techkreative.com/@<span id="previewUsernameLabel" class="font-medium text-slate-600 dark:text-slate-300"><?= htmlspecialchars($userUsername ?: 'username') ?></span>
                    </p>
                </div>

                <!-- Location: City & Country (Side-by-Side) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="pinterest-input-box">
                        <label for="pinterestCity" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">City</label>
                        <input type="text" id="pinterestCity" value="<?= htmlspecialchars($userCity) ?>" placeholder="London"
                               class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5">
                    </div>

                    <div class="pinterest-input-box">
                        <label for="pinterestCountry" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Country</label>
                        <input type="text" id="pinterestCountry" value="<?= htmlspecialchars($userCountry) ?>" placeholder="United Kingdom"
                               class="w-full bg-transparent border-none text-slate-900 dark:text-white text-base font-medium focus:outline-none focus:ring-0 p-0 mt-0.5">
                    </div>
                </div>

            </form>
        </div>

        <!-- ── 3. ACCOUNT & SECURITY PANEL ─────────────────────────────────── -->
        <div id="panelAccount" class="profile-tab-panel hidden">
            <div class="mb-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Account management
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed max-w-xl">
                    View your verified account credentials, sign-in activity, and manage your data privacy settings.
                </p>
            </div>

            <!-- LOGGED-IN STATE CONTAINER -->
            <div id="authAccountState" class="<?= $isLoggedIn ? '' : 'hidden' ?> space-y-6 max-w-3xl">
                
                <!-- 1. Identity Hero Card -->
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="relative shrink-0">
                            <img id="accountAvatarImg" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover shadow-sm border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <button type="button" onclick="openAvatarModal()" title="Change avatar" class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-md flex items-center justify-center text-xs hover:scale-105 transition">
                                <i class="bi bi-camera"></i>
                            </button>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 id="accountNameHeading" class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                    <?= htmlspecialchars($userName ?: ($profileEmail ? explode('@', $profileEmail)[0] : 'User')) ?>
                                </h3>
                                <span id="accountRoleBadge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide <?= $isAdmin ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' ?>">
                                    <?= $isAdmin ? 'Admin' : 'Member' ?>
                                </span>
                            </div>
                            <p id="accountEmailSub" class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5 font-mono">
                                <?= htmlspecialchars($profileEmail ?? '') ?>
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Active Session</span>
                                </span>
                                <span id="accountUsernameBadge" class="text-xs font-mono text-slate-400 dark:text-slate-500">
                                    @<?= htmlspecialchars($userUsername ?: ($profileEmail ? explode('@', $profileEmail)[0] : 'user')) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="document.querySelector('[data-target=\'panelEditProfile\']').click()" class="px-5 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition self-stretch sm:self-auto text-center shrink-0">
                        Edit Profile
                    </button>
                </div>

                <!-- 2. Detailed Metadata Grid -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm divide-y divide-slate-100 dark:divide-slate-800/80 overflow-hidden">
                    
                    <!-- Account Name -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Account Name</span>
                            <span id="accountNameValue" class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                                <?= htmlspecialchars($userName ?: 'Not configured') ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Display name for shared palettes</span>
                    </div>

                    <!-- Account Email -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Account Email</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span id="accountEmailValue" class="text-base font-bold text-slate-900 dark:text-white font-mono">
                                    <?= htmlspecialchars($profileEmail ?: 'No email linked') ?>
                                </span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">
                                    <i class="bi bi-patch-check-fill"></i> Verified
                                </span>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Primary authentication credential</span>
                    </div>

                    <!-- Sign-in Provider -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Sign-in Provider</span>
                            <div class="flex items-center gap-2 mt-1">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Google OAuth 2.0</span>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Secured via Google Identity</span>
                    </div>

                    <!-- Account Created At -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Account Created</span>
                            <span id="accountCreatedAtValue" class="text-sm font-bold text-slate-900 dark:text-white mt-0.5 block">
                                <?= htmlspecialchars($createdFormatted ?: 'Active Member') ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Initial registration date</span>
                    </div>

                    <!-- Last Logged In -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Last Logged In</span>
                            <span id="accountLastLoginValue" class="text-sm font-bold text-slate-900 dark:text-white mt-0.5 block">
                                <?= htmlspecialchars($lastLoginFormatted ?: 'Active now') ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Most recent verified session</span>
                    </div>

                </div>

                <!-- 3. Security & Actions -->
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">Session &amp; Security</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                        Your design data and color harmonies are safely synchronized with our secure database. You can sign out anytime or permanently delete your account.
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="<?= $base ?>/auth/logout" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition flex items-center gap-2">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                        <button type="button" onclick="if(confirm('Are you sure you want to permanently delete your Color Magic account? This will permanently delete your profile and cloud-saved palettes.')) window.location.href='<?= $base ?>/auth/delete-account'" class="px-5 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-300 text-xs font-bold transition flex items-center gap-2">
                            <i class="bi bi-trash3"></i>
                            <span>Delete Account</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- GUEST STATE CONTAINER (Shown if no session and no localStorage) -->
            <div id="guestAccountState" class="<?= $isLoggedIn ? 'hidden' : '' ?> bg-white dark:bg-slate-900 p-8 sm:p-12 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm max-w-2xl text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-pink-500 to-rose-500 text-white flex items-center justify-center mx-auto text-2xl shadow-lg shadow-pink-500/20">
                    <i class="bi bi-person-fill-lock"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Guest Workspace</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-md mx-auto leading-relaxed">
                        You are currently browsing Color Magic as a guest. Sign in with Google to synchronize your saved palettes, custom gradients, and personal profile across all your devices.
                    </p>
                </div>
                <div class="pt-3 flex justify-center">
                    <a href="<?= $base ?>/login" class="px-8 py-3.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition inline-flex items-center gap-2.5 active:scale-95">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Sign In with Google</span>
                    </a>
                </div>
            </div>
        </div>

        </div><!-- /.flex-1 right column -->
        </div><!-- /.flex row -->
    </main>

    <!-- ══ AVATAR SELECTION MODAL ════════════════════════════════════════════ -->
    <div id="avatarModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm animate-fadeIn">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Choose Your Avatar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select a persona that fits your creative identity.</p>
                </div>
                <button type="button" onclick="closeAvatarModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 flex items-center justify-center transition">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <!-- Filter Categories -->
            <div class="px-6 py-3 bg-slate-50 dark:bg-slate-950/40 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-2">
                <button type="button" onclick="filterAvatars('all', this)" class="avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-white dark:bg-white dark:text-slate-900 transition">All (28)</button>
                <button type="button" onclick="filterAvatars('cool', this)" class="avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 transition">Cool</button>
                <button type="button" onclick="filterAvatars('decent', this)" class="avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 transition">Decent</button>
                <button type="button" onclick="filterAvatars('happy', this)" class="avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 transition">Happy</button>
                <button type="button" onclick="filterAvatars('old', this)" class="avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 transition">Sage / Elder</button>
            </div>

            <!-- Avatars Grid -->
            <div id="avatarsGrid" class="p-6 overflow-y-auto grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($avatarsList as $av): 
                    $fileName = $av['file_name'] ?? '1.webp';
                    $avName = $av['name'] ?? 'Avatar';
                    $avType = $av['type'] ?? 'cool';
                    $isSelected = ($fileName === $userAvatar);
                ?>
                    <div class="avatar-card group relative p-3 rounded-2xl border <?= $isSelected ? 'border-slate-900 dark:border-white ring-2 ring-slate-900 dark:ring-white bg-slate-50 dark:bg-slate-800' : 'border-slate-200 dark:border-slate-800 hover:border-slate-400' ?> flex flex-col items-center text-center cursor-pointer transition-all duration-200"
                         data-filename="<?= htmlspecialchars($fileName) ?>"
                         data-type="<?= htmlspecialchars($avType) ?>"
                         data-name="<?= htmlspecialchars($avName) ?>"
                         onclick="selectAvatarCard('<?= htmlspecialchars($fileName) ?>', '<?= addslashes($avName) ?>', this)">
                        <div class="w-16 h-16 rounded-full overflow-hidden mb-2 shadow-sm transition-transform group-hover:scale-105">
                            <img src="<?= $base ?>/assets/avatars/<?= htmlspecialchars($fileName) ?>" alt="<?= htmlspecialchars($avName) ?>" class="w-full h-full object-cover">
                        </div>
                        <span class="font-bold text-xs text-slate-900 dark:text-white truncate w-full"><?= htmlspecialchars($avName) ?></span>
                        <span class="text-[10px] text-slate-400 capitalize mt-0.5"><?= htmlspecialchars($avType) ?></span>
                        <span class="avatar-check-icon absolute top-2 right-2 w-5 h-5 rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-[10px] flex items-center justify-center shadow-md <?= $isSelected ? 'flex' : 'hidden' ?>">
                            <i class="bi bi-check-lg"></i>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 flex items-center justify-between">
                <span id="modalSelectedLabel" class="text-xs font-semibold text-slate-500">
                    Selected: <strong id="modalSelectedName"><?= htmlspecialchars($userAvatar) ?></strong>
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeAvatarModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition">Cancel</button>
                    <button type="button" id="confirmAvatarBtn" onclick="saveSelectedAvatar()" class="px-6 py-2 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE   = "profile";
        window.CM_CURRENT_AVATAR = "<?= htmlspecialchars($userAvatar) ?>";
        window.CM_BASE = "<?= $base ?>";
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=<?= time() ?>" defer></script>

    <script>
        var selectedAvatarFile = window.CM_CURRENT_AVATAR || '1.webp';

        // ── 1. Pinterest-Style Sidebar Navigation Switching ────────────────
        document.querySelectorAll('.profile-sidebar-item').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.profile-sidebar-item').forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');

                var targetId = tab.getAttribute('data-target');
                document.querySelectorAll('.profile-tab-panel').forEach(function(panel) {
                    panel.classList.add('hidden');
                });
                var targetPanel = document.getElementById(targetId);
                if (targetPanel) {
                    targetPanel.classList.remove('hidden');
                }
            });
        });

        // Live preview of username
        var usernameInput = document.getElementById('pinterestUsername');
        var previewLabel = document.getElementById('previewUsernameLabel');
        if (usernameInput && previewLabel) {
            usernameInput.addEventListener('input', function() {
                var clean = this.value.replace(/[^a-zA-Z0-9_-]/g, '').toLowerCase();
                this.value = clean;
                previewLabel.textContent = clean || 'username';
            });
        }

        // ── 2. Avatar Modal Helpers ─────────────────────────────────────────
        function openAvatarModal() {
            var modal = document.getElementById('avatarModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeAvatarModal() {
            var modal = document.getElementById('avatarModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function filterAvatars(type, btn) {
            document.querySelectorAll('.avatar-filter-pill').forEach(function(b) {
                b.className = 'avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-300 transition';
            });
            btn.className = 'avatar-filter-pill px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-white dark:bg-white dark:text-slate-900 transition';

            var cards = document.querySelectorAll('.avatar-card');
            cards.forEach(function(c) {
                if (type === 'all' || c.getAttribute('data-type') === type) {
                    c.style.display = 'flex';
                } else {
                    c.style.display = 'none';
                }
            });
        }

        function selectAvatarCard(fileName, name, el) {
            selectedAvatarFile = fileName;
            document.querySelectorAll('.avatar-card').forEach(function(c) {
                c.classList.remove('border-slate-900', 'dark:border-white', 'ring-2', 'ring-slate-900', 'dark:ring-white', 'bg-slate-50', 'dark:bg-slate-800');
                c.classList.add('border-slate-200', 'dark:border-slate-800');
                var check = c.querySelector('.avatar-check-icon');
                if (check) { check.classList.add('hidden'); check.classList.remove('flex'); }
            });

            el.classList.add('border-slate-900', 'dark:border-white', 'ring-2', 'ring-slate-900', 'dark:ring-white', 'bg-slate-50', 'dark:bg-slate-800');
            el.classList.remove('border-slate-200', 'dark:border-slate-800');
            var check = el.querySelector('.avatar-check-icon');
            if (check) { check.classList.remove('hidden'); check.classList.add('flex'); }

            var labelName = document.getElementById('modalSelectedName');
            if (labelName) labelName.textContent = name + ' (' + fileName + ')';
        }

        function saveSelectedAvatar() {
            var btn = document.getElementById('confirmAvatarBtn');
            if (btn) btn.disabled = true;

            function applyAvatarLocal() {
                window.CM_CURRENT_AVATAR = selectedAvatarFile;
                var newUrl = '<?= $base ?>/assets/avatars/' + selectedAvatarFile;

                var photoImg = document.getElementById('profilePhotoImg');
                if (photoImg) photoImg.src = newUrl;

                var navImgs = document.querySelectorAll('header img[alt="Profile"]');
                navImgs.forEach(function(img) { img.src = newUrl; });

                try {
                    var raw = localStorage.getItem('cm_user');
                    var u = raw ? JSON.parse(raw) : {};
                    u.avatar = selectedAvatarFile;
                    u.picture = newUrl;
                    localStorage.setItem('cm_user', JSON.stringify(u));
                } catch(e) {}

                closeAvatarModal();
            }

            fetch('<?= $base ?>/auth/user-profile.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ avatar: selectedAvatarFile })
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (btn) btn.disabled = false;
                applyAvatarLocal();
            })
            .catch(function() {
                if (btn) btn.disabled = false;
                applyAvatarLocal();
            });
        }

        // ── 3. Pinterest Form Submit (Save / Reset) ─────────────────────────
        var form = document.getElementById('pinterestProfileForm');
        var initialFormData = null;

        function recordInitialForm() {
            initialFormData = {
                name: (document.getElementById('pinterestName').value || '').trim(),
                about: (document.getElementById('pinterestAbout').value || '').trim(),
                pronouns: (document.getElementById('pinterestPronouns').value || '').trim(),
                website: (document.getElementById('pinterestWebsite').value || '').trim(),
                username: (document.getElementById('pinterestUsername').value || '').trim(),
                city: (document.getElementById('pinterestCity').value || '').trim(),
                country: (document.getElementById('pinterestCountry').value || '').trim(),
                avatar: selectedAvatarFile
            };
        }
        recordInitialForm();

        var resetBtn = document.getElementById('resetProfileBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (!initialFormData) return;
                document.getElementById('pinterestName').value = initialFormData.name;
                document.getElementById('pinterestAbout').value = initialFormData.about;
                document.getElementById('pinterestPronouns').value = initialFormData.pronouns;
                document.getElementById('pinterestWebsite').value = initialFormData.website;
                document.getElementById('pinterestUsername').value = initialFormData.username;
                document.getElementById('pinterestCity').value = initialFormData.city;
                document.getElementById('pinterestCountry').value = initialFormData.country;
                if (previewLabel) previewLabel.textContent = initialFormData.username || 'username';
            });
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var saveBtn = document.getElementById('saveProfileBtn');
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.className = "px-6 py-2.5 rounded-full bg-slate-900 text-white font-bold text-xs shadow-md transition flex items-center gap-2 active:scale-95 disabled:opacity-75";
                    saveBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Saving...</span>';
                }

                var payload = {
                    name: (document.getElementById('pinterestName').value || '').trim(),
                    bio: (document.getElementById('pinterestAbout').value || '').trim(),
                    pronouns: (document.getElementById('pinterestPronouns').value || '').trim(),
                    website: (document.getElementById('pinterestWebsite').value || '').trim(),
                    username: (document.getElementById('pinterestUsername').value || '').trim(),
                    city: (document.getElementById('pinterestCity').value || '').trim(),
                    country: (document.getElementById('pinterestCountry').value || '').trim(),
                    avatar: selectedAvatarFile
                };

                function updateLocalAndUI(userObj) {
                    recordInitialForm();
                    try {
                        var raw = localStorage.getItem('cm_user');
                        var u = raw ? JSON.parse(raw) : {};
                        Object.assign(u, userObj);
                        localStorage.setItem('cm_user', JSON.stringify(u));
                    } catch(err) {}

                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.className = "px-6 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5 active:scale-95";
                        saveBtn.innerHTML = '<i class="bi bi-check2 text-sm font-black"></i><span>Saved!</span>';
                        setTimeout(function() {
                            saveBtn.className = "px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition flex items-center gap-1.5 active:scale-95 disabled:opacity-75";
                            saveBtn.innerHTML = '<i class="bi bi-floppy text-xs"></i><span id="saveProfileBtnText">Save</span>';
                        }, 2500);
                    }
                }

                fetch('<?= $base ?>/auth/user-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res && res.success && res.user) {
                        updateLocalAndUI(res.user);
                    } else {
                        updateLocalAndUI(payload);
                    }
                })
                .catch(function() {
                    updateLocalAndUI(payload);
                });
            });
        }

        function hydrateAccountManagement(u) {
            if (!u || !u.email) return;

            var authCard = document.getElementById('authAccountState');
            var guestCard = document.getElementById('guestAccountState');
            if (authCard) authCard.classList.remove('hidden');
            if (guestCard) guestCard.classList.add('hidden');

            var name = u.name || (u.email ? u.email.split('@')[0] : 'User');
            var email = u.email;
            var username = u.username || (u.email ? u.email.split('@')[0] : 'user');

            var nameHeading = document.getElementById('accountNameHeading');
            if (nameHeading) nameHeading.textContent = name;

            var nameVal = document.getElementById('accountNameValue');
            if (nameVal) nameVal.textContent = name;

            var emailSub = document.getElementById('accountEmailSub');
            if (emailSub) emailSub.textContent = email;

            var emailVal = document.getElementById('accountEmailValue');
            if (emailVal) emailVal.textContent = email;

            var userBadge = document.getElementById('accountUsernameBadge');
            if (userBadge) userBadge.textContent = '@' + username;

            var avImg = document.getElementById('accountAvatarImg');
            if (avImg && u.avatar) {
                avImg.src = '<?= $base ?>/assets/avatars/' + u.avatar;
            }

            if (u.created_at) {
                var createdVal = document.getElementById('accountCreatedAtValue');
                if (createdVal) {
                    try {
                        var d = new Date(u.created_at);
                        if (!isNaN(d.getTime())) {
                            createdVal.textContent = d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
                        }
                    } catch(e) {}
                }
            }

            if (u.last_login) {
                var lastLoginVal = document.getElementById('accountLastLoginValue');
                if (lastLoginVal) {
                    try {
                        var d = new Date(u.last_login);
                        if (!isNaN(d.getTime())) {
                            lastLoginVal.textContent = d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) + ', ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
                        }
                    } catch(e) {}
                }
            }

            var roleBadge = document.getElementById('accountRoleBadge');
            if (roleBadge) {
                var isAdmin = Boolean(u.role === 'admin' || u.is_admin || localStorage.getItem('cm_admin_token'));
                roleBadge.textContent = isAdmin ? 'Admin' : 'Member';
                roleBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide ' +
                    (isAdmin ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300');
            }
        }

        // Hydrate from localStorage immediately, then sync latest from server
        (function() {
            try {
                var raw = localStorage.getItem('cm_user');
                if (raw) {
                    var u = JSON.parse(raw);
                    if (u) {
                        hydrateAccountManagement(u);
                        if (u.name && !document.getElementById('pinterestName').value) {
                            document.getElementById('pinterestName').value = u.name;
                        }
                        if (u.username && !document.getElementById('pinterestUsername').value) {
                            document.getElementById('pinterestUsername').value = u.username;
                            if (previewLabel) previewLabel.textContent = u.username;
                        }
                        if (u.pronouns && !document.getElementById('pinterestPronouns').value) {
                            document.getElementById('pinterestPronouns').value = u.pronouns;
                        }
                        if (u.website && !document.getElementById('pinterestWebsite').value) {
                            document.getElementById('pinterestWebsite').value = u.website;
                        }
                        if (u.city && !document.getElementById('pinterestCity').value) {
                            document.getElementById('pinterestCity').value = u.city;
                        }
                        if (u.country && !document.getElementById('pinterestCountry').value) {
                            document.getElementById('pinterestCountry').value = u.country;
                        }
                        if (u.bio && !document.getElementById('pinterestAbout').value) {
                            document.getElementById('pinterestAbout').value = u.bio;
                        }
                        if (u.avatar) {
                            var photoImg = document.getElementById('profilePhotoImg');
                            var avUrl = '<?= $base ?>/assets/avatars/' + u.avatar;
                            if (photoImg) photoImg.src = avUrl;
                        }
                        recordInitialForm();
                    }
                }
            } catch(e) {}

            // Background fetch to ensure latest DB record (created_at, last_login)
            fetch('<?= $base ?>/auth/user-profile.php')
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res && res.success && res.user) {
                        hydrateAccountManagement(res.user);
                        try {
                            var raw = localStorage.getItem('cm_user');
                            var u = raw ? JSON.parse(raw) : {};
                            Object.assign(u, res.user);
                            localStorage.setItem('cm_user', JSON.stringify(u));
                        } catch(e) {}
                    }
                })
                .catch(function() {});
        })();
    </script>
</body>

</html>
