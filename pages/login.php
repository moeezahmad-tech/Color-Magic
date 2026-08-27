<?php 
session_start();
include '../components/config.php'; 
/** @var string $base */ 
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Login / Sign Up — Color Magic</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <img src="<?= $base ?>/assets/images/logo.png" alt="Color Magic Logo" class="w-16 h-16 mx-auto mb-4 rounded-xl shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900">Welcome to Color Magic</h2>
                <p class="text-gray-500 mt-2 text-sm">Sign in or create an account to save your favorite palettes and colors.</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'auth_failed'): ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 text-sm border border-red-100 text-center">
                    Authentication failed. Please try again.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user'])): ?>
                <div class="text-center">
                    <div class="mb-4">
                        <?php if(!empty($_SESSION['user']['picture'])): ?>
                            <img src="<?= htmlspecialchars($_SESSION['user']['picture']) ?>" alt="Profile" class="w-20 h-20 rounded-full mx-auto shadow-md">
                        <?php else: ?>
                            <div class="w-20 h-20 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto shadow-md font-bold">
                                <?= strtoupper(substr($_SESSION['user']['email'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Signed in as</h3>
                    <p class="text-gray-600 mb-6 font-medium"><?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                    <a href="<?= $base ?>/auth/logout" class="inline-flex justify-center items-center w-full px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Sign Out
                    </a>
                    <a href="<?= $base ?>/" class="block mt-4 text-sm text-indigo-600 hover:text-indigo-500 font-medium">Return to Dashboard</a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <a href="<?= $base ?>/auth/google-login" class="relative flex items-center justify-center w-full px-4 py-3.5 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 group">
                        <div class="absolute left-4">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                        </div>
                        <span class="ml-6">Continue with Google</span>
                    </a>
                </div>
                
                <div class="mt-8 relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500">Secure & simple</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
            <span>&copy; <?= date('Y') ?> Color Magic</span>
            <div class="space-x-3">
                <a href="#" class="hover:text-gray-900 transition-colors">Privacy</a>
                <a href="#" class="hover:text-gray-900 transition-colors">Terms</a>
            </div>
        </div>
    </div>
</body>
</html>
