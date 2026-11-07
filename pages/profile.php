<?php 
require_once __DIR__ . '/../components/config.php'; 
require_once __DIR__ . '/../auth/AdminAuth.php';
require_once __DIR__ . '/../auth/UserDb.php';
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

$avatarsFile = __DIR__ . '/../data/avatars.json';
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
    <?php include '../components/navbar.php'; ?>

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
                    <button type="button" class="profile-sidebar-item" data-target="panelColors">
                        <span class="tab-title">Saved Colors</span>
                        <span class="tab-badge" id="favColorsCount">0</span>
                    </button>
                    <button type="button" class="profile-sidebar-item" data-target="panelPalettes">
                        <span class="tab-title">Saved Palettes</span>
                        <span class="tab-badge" id="favPalettesCount">0</span>
                    </button>
                    <button type="button" class="profile-sidebar-item" data-target="panelGradients">
                        <span class="tab-title">Saved Gradients</span>
                        <span class="tab-badge" id="favGradientsCount">0</span>
                    </button>
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
                        <button type="button" onclick="document.getElementById('resetProfileBtn').click()" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition active:scale-95">
                            Reset
                        </button>
                        <button type="button" onclick="document.getElementById('saveProfileBtn').click()" class="px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition flex items-center gap-1.5 active:scale-95">
                            <span>Save</span>
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

                <!-- Pinterest Bottom Action Buttons (Non-floating, placed at end of form) -->
                <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" id="resetProfileBtn" class="px-6 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm transition active:scale-95">
                        Reset
                    </button>
                    <button type="submit" id="saveProfileBtn" class="px-7 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-sm shadow-md transition flex items-center gap-2 active:scale-95">
                        <span>Save</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ── 3. SAVED COLORS PANEL ────────────────────────────────────────── -->
        <div id="panelColors" class="profile-tab-panel hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Saved Colors</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Your liked hexadecimal swatches and verified color profiles.</p>
                </div>
            </div>
            <div id="favColorsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
        </div>

        <!-- ── 4. SAVED PALETTES PANEL ──────────────────────────────────────── -->
        <div id="panelPalettes" class="profile-tab-panel hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Saved Palettes</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Curated color harmony combinations saved to your profile.</p>
                </div>
            </div>
            <div id="favPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 gap-5"></div>
        </div>

        <!-- ── 5. SAVED GRADIENTS PANEL ─────────────────────────────────────── -->
        <div id="panelGradients" class="profile-tab-panel hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Saved Gradients</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">CSS gradients ready for your user interface projects.</p>
                </div>
            </div>
            <div id="favGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-5"></div>
        </div>

        <!-- ── 6. ACCOUNT & SECURITY PANEL ─────────────────────────────────── -->
        <div id="panelAccount" class="profile-tab-panel hidden">
            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Account Management</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage your account credentials, sessions, and data privacy.</p>
            </div>

            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Signed in as</p>
                    <p class="text-base font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($profileEmail ?? 'Guest User') ?></p>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-3">
                    <a href="<?= $base ?>/auth/logout" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition flex items-center gap-2">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Log out</span>
                    </a>
                    <button type="button" onclick="if(confirm('Are you sure you want to delete your account? This will permanently remove your favorites and personal data.')) window.location.href='<?= $base ?>/auth/delete-account'" class="px-5 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-300 text-xs font-bold transition flex items-center gap-2">
                        <i class="bi bi-trash3"></i>
                        <span>Delete account</span>
                    </button>
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

    <?php include '../components/footer.php'; ?>

    <script>
        window.CM_ACTIVE_PAGE   = "favorites";
        window.CM_COLOR_BASE    = "color/";
        window.CM_PALETTE_BASE  = "palette/";
        window.CM_GRADIENT_BASE = "gradient/";
        window.CM_CURRENT_AVATAR = "<?= htmlspecialchars($userAvatar) ?>";
        window.CM_BASE = "<?= $base ?>";
    </script>
    <script src="<?= $base ?>/assets/js/utils.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/services/favorites.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/palette-card.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/components/gradient-card.js?v=<?= time() ?>" defer></script>
    <script src="<?= $base ?>/assets/js/favorites-page.js?v=<?= time() ?>" defer></script>

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
                var origText = saveBtn ? saveBtn.innerHTML : '';
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span>Saving...</span>';
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
                        saveBtn.innerHTML = '<span>Saved!</span>';
                        setTimeout(function() { saveBtn.innerHTML = origText; }, 2000);
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

        // Hydrate from localStorage if present
        (function() {
            try {
                var raw = localStorage.getItem('cm_user');
                if (raw) {
                    var u = JSON.parse(raw);
                    if (u) {
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
        })();
    </script>
</body>

</html>
