<?php
require_once __DIR__ . '/../../components/config.php';
require_once __DIR__ . '/../../components/security-gate.php';

$redirect = $_GET['redirect'] ?? ($base !== '' ? $base : '/');
// Sanitize redirect to prevent open redirect vulnerabilities
if (!is_string($redirect) || strpos($redirect, '/') !== 0 || strpos($redirect, '//') === 0 || preg_match('/[<>"\'\s]/', $redirect)) {
    $redirect = $base !== '' ? $base : '/';
}

$reason = $_GET['reason'] ?? 'default';
$subtitle = 'Please select the requested color swatch below to verify you are human and enter Color Magic.';
if ($reason === 'speed') {
    $subtitle = 'Unusually rapid requests detected. Please solve this quick check to verify you are human.';
} elseif ($reason === 'limit') {
    $subtitle = 'You have enjoyed 5 free page views! Solve this quick check to unlock 30 days of unlimited browsing.';
}

// 1. If already verified via cookie, redirect right away
if (cm_is_human_verified()) {
    header('Location: ' . $redirect, true, 302);
    exit;
}

// 2. Handle POST / AJAX verification requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'] ?? ($_GET['action'] ?? '');

    // Action A: Restore from LocalStorage
    if ($action === 'restore') {
        $lsVerified = $_POST['ls_verified'] ?? '';
        if ($lsVerified === '1') {
            cm_issue_human_cookie();
            echo json_encode([
                'success' => true,
                'restored' => true,
                'redirect' => $redirect
            ]);
            exit;
        }
    }

    // Action B: Verify Challenge Answer
    if ($action === 'verify_answer') {
        $token = $_POST['challenge_token'] ?? '';
        $selectedId = $_POST['selected_id'] ?? '';

        if (empty($token) || empty($selectedId) || strpos($token, '.') === false) {
            echo json_encode(['success' => false, 'message' => 'Invalid challenge token']);
            exit;
        }

        list($payloadB64, $sig) = explode('.', $token, 2);
        $expectedSig = hash_hmac('sha256', $payloadB64, cm_get_security_secret());
        if (!hash_equals($expectedSig, $sig)) {
            echo json_encode(['success' => false, 'message' => 'Challenge verification failed']);
            exit;
        }

        $payload = json_decode(base64_decode($payloadB64), true);
        if (!$payload || !isset($payload['target_id']) || !isset($payload['exp'])) {
            echo json_encode(['success' => false, 'message' => 'Malformed challenge']);
            exit;
        }

        if (time() > (int)$payload['exp']) {
            echo json_encode(['success' => false, 'message' => 'Challenge expired. Please refresh the page.']);
            exit;
        }

        if ($payload['target_id'] !== $selectedId) {
            echo json_encode(['success' => false, 'message' => 'Incorrect color selected. Please try again!']);
            exit;
        }

        // Passed challenge! Issue 30-day cookie
        cm_issue_human_cookie();

        echo json_encode([
            'success' => true,
            'message' => 'Human verified successfully!',
            'redirect' => $redirect
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

// 3. Generate dynamic Color Magic challenge
$paletteOptions = [
    [
        ['id' => 'c1', 'name' => 'Royal Purple', 'hex' => '#7C3AED'],
        ['id' => 'c2', 'name' => 'Emerald Green', 'hex' => '#10B981'],
        ['id' => 'c3', 'name' => 'Sunset Coral', 'hex' => '#F43F5E'],
        ['id' => 'c4', 'name' => 'Electric Blue', 'hex' => '#3B82F6']
    ],
    [
        ['id' => 'c1', 'name' => 'Amber Gold', 'hex' => '#F59E0B'],
        ['id' => 'c2', 'name' => 'Cyan Wave', 'hex' => '#06B6D4'],
        ['id' => 'c3', 'name' => 'Neon Pink', 'hex' => '#EC4899'],
        ['id' => 'c4', 'name' => 'Deep Indigo', 'hex' => '#6366F1']
    ],
    [
        ['id' => 'c1', 'name' => 'Teal Oasis', 'hex' => '#14B8A6'],
        ['id' => 'c2', 'name' => 'Vivid Orange', 'hex' => '#F97316'],
        ['id' => 'c3', 'name' => 'Amethyst Violet', 'hex' => '#8B5CF6'],
        ['id' => 'c4', 'name' => 'Rose Crimson', 'hex' => '#E11D48']
    ]
];

$chosenSet = $paletteOptions[array_rand($paletteOptions)];
shuffle($chosenSet);
$targetColor = $chosenSet[array_rand($chosenSet)];

// Generate signed challenge token (valid for 10 minutes)
$challengePayload = [
    'target_id' => $targetColor['id'],
    'exp' => time() + 600,
    'nonce' => bin2hex(random_bytes(8))
];
$payloadB64 = base64_encode(json_encode($challengePayload));
$challengeToken = $payloadB64 . '.' . hash_hmac('sha256', $payloadB64, cm_get_security_secret());
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quick Security Check | Color Magic</title>
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <style>
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #FAFAFC;
            color: #0F172A;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }
        .cm-card {
            background: #FFFFFF;
            border: 1px solid #F1F5F9;
            box-shadow: 0 20px 45px -15px rgba(124, 58, 237, 0.08), 0 0 1px 1px rgba(0, 0, 0, 0.03);
            margin: auto;
        }
        .swatch-card {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .swatch-card:hover {
            transform: translateY(-2px);
            border-color: #C084FC;
            box-shadow: 0 8px 16px -4px rgba(124, 58, 237, 0.12);
        }
        .swatch-card:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body class="min-h-screen w-full p-4 sm:p-6 relative">
    <!-- Ambient Background Lighting (contained so it never causes scroll or clip) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-purple-200/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-pink-200/40 rounded-full blur-3xl"></div>
    </div>

    <!-- Centered Verification Card -->
    <main class="w-full max-w-[420px] cm-card rounded-3xl p-6 sm:p-7 relative z-10 text-center">
        <!-- Logo & Brand Header matching Homepage Navbar -->
        <div class="flex items-center justify-center gap-2.5 mb-4">
            <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic" class="w-8 h-8 object-contain" />
            <span class="text-xl font-extrabold tracking-tight text-slate-900">
                Color <span class="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">Magic</span>
            </span>
        </div>

        <!-- Security Shield Icon Badge -->
        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-purple-50 to-pink-50 border border-purple-100 flex items-center justify-center text-purple-600 text-xl shadow-sm">
            <i class="bi bi-shield-check"></i>
        </div>

        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mb-1.5 tracking-tight">Quick Human Check</h1>
        <p class="text-xs sm:text-sm text-slate-500 mb-5 leading-relaxed">
            <?= htmlspecialchars($subtitle) ?>
        </p>

        <!-- Dynamic Color Challenge Box -->
        <div id="challengeBox" class="bg-slate-50/80 border border-slate-200/70 rounded-2xl p-4 mb-4 text-left">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Target Color</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white border border-slate-200 text-slate-700 shadow-sm font-mono">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: <?= $targetColor['hex'] ?>;"></span>
                    <?= htmlspecialchars($targetColor['hex']) ?>
                </span>
            </div>

            <p class="text-xs sm:text-sm font-medium text-slate-700 mb-3">
                Click on the <strong class="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent font-bold"><?= htmlspecialchars($targetColor['name']) ?></strong> swatch:
            </p>

            <!-- 4 Color Choices -->
            <div class="grid grid-cols-2 gap-2.5" id="swatchGrid">
                <?php foreach ($chosenSet as $color): ?>
                    <button type="button"
                            class="swatch-card group flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-purple-50/30 focus:outline-none"
                            data-id="<?= htmlspecialchars($color['id']) ?>"
                            onclick="selectColor('<?= htmlspecialchars($color['id']) ?>')">
                        <span class="w-full h-10 rounded-lg mb-2 shadow-sm border border-black/5"
                              style="background-color: <?= htmlspecialchars($color['hex']) ?>;"></span>
                        <span class="text-xs font-semibold text-slate-800 group-hover:text-purple-700 transition-colors">
                            <?= htmlspecialchars($color['name']) ?>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">
                            <?= htmlspecialchars($color['hex']) ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Feedback & Status Message -->
        <div id="statusAlert" class="hidden text-xs sm:text-sm rounded-xl p-3 mb-3 font-medium transition-all"></div>

        <!-- LocalStorage Auto-Verify Notice -->
        <div id="restoringNotice" class="hidden items-center justify-center gap-2 text-xs sm:text-sm text-purple-600 py-2 mb-2 font-medium">
            <svg class="animate-spin h-4 w-4 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Verifying remembered session...</span>
        </div>

        <!-- Privacy & Expiration note -->
        <div class="text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
            <i class="bi bi-shield-lock text-purple-500"></i>
            <span>Verified humans are remembered for 30 days.</span>
        </div>
    </main>

    <script>
        const challengeToken = <?= json_encode($challengeToken) ?>;
        const redirectUrl = <?= json_encode($redirect) ?>;
        const statusAlert = document.getElementById('statusAlert');
        const restoringNotice = document.getElementById('restoringNotice');
        const challengeBox = document.getElementById('challengeBox');

        // Check if previously human-verified in localStorage for instant seamless bypass
        (function checkLocalStorage() {
            try {
                if (localStorage.getItem('cm_human_verified') === '1') {
                    restoringNotice.classList.remove('hidden');
                    restoringNotice.classList.add('flex');
                    challengeBox.classList.add('opacity-40', 'pointer-events-none');

                    const formData = new FormData();
                    formData.append('action', 'restore');
                    formData.append('ls_verified', '1');

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect || redirectUrl;
                        } else {
                            restoringNotice.classList.add('hidden');
                            restoringNotice.classList.remove('flex');
                            challengeBox.classList.remove('opacity-40', 'pointer-events-none');
                        }
                    })
                    .catch(() => {
                        restoringNotice.classList.add('hidden');
                        restoringNotice.classList.remove('flex');
                        challengeBox.classList.remove('opacity-40', 'pointer-events-none');
                    });
                }
            } catch (e) {
                // LocalStorage blocked/private mode
            }
        })();

        function selectColor(colorId) {
            statusAlert.className = 'text-xs sm:text-sm rounded-xl p-2.5 mb-3 font-medium bg-purple-50 border border-purple-200 text-purple-700 block';
            statusAlert.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-3.5 w-3.5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Verifying selection...</span>';

            const formData = new FormData();
            formData.append('action', 'verify_answer');
            formData.append('challenge_token', challengeToken);
            formData.append('selected_id', colorId);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    try {
                        localStorage.setItem('cm_human_verified', '1');
                    } catch (e) {}

                    statusAlert.className = 'text-xs sm:text-sm rounded-xl p-2.5 mb-3 font-medium bg-emerald-50 border border-emerald-200 text-emerald-700 block';
                    statusAlert.innerHTML = '<i class="bi bi-check-circle-fill mr-1.5 text-emerald-600"></i> Verification successful! Entering Color Magic...';

                    setTimeout(() => {
                        window.location.href = data.redirect || redirectUrl;
                    }, 500);
                } else {
                    statusAlert.className = 'text-xs sm:text-sm rounded-xl p-2.5 mb-3 font-medium bg-rose-50 border border-rose-200 text-rose-700 block';
                    statusAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill mr-1.5 text-rose-500"></i> ' + (data.message || 'Incorrect choice. Please try again.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                }
            })
            .catch(() => {
                statusAlert.className = 'text-xs sm:text-sm rounded-xl p-2.5 mb-3 font-medium bg-rose-50 border border-rose-200 text-rose-700 block';
                statusAlert.innerHTML = 'Network error. Please reload the page.';
            });
        }
    </script>
</body>
</html>
