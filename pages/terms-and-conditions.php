<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Terms and Conditions | Color Magic</title>
    <meta name="description"
        content="Read the Terms and Conditions for Color Magic. Understand terms of service, acceptable use, intellectual property, and color data usage." />
    <meta name="keywords"
        content="terms and conditions, terms of service, acceptable use, color magic terms, license, TechKreative" />
    <meta name="author" content="TechKreative" />
    <meta name="robots" content="index, follow" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag("js", new Date());
        gtag("config", "G-537L4MR968");
    </script>
    <script src="<?= $base ?>/assets/js/app.js" defer></script>

    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://colormagic.techkreative.com/terms-and-conditions" />
    <meta property="og:title" content="Terms and Conditions | Color Magic" />
    <meta property="og:description"
        content="Terms of Service and guidelines for using Color Magic's tools, color generators, and color name directories." />
    <meta property="og:image" content="<?= $base ?>/assets/images/logo.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/terms-and-conditions" />
    <link rel="manifest" href="<?= $base ?>/manifest.json" />
    <link rel="icon" type="image/png" href="<?= $base ?>/assets/images/logo.png" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= $base ?>/assets/css/main.css" />
    <script id="tailwind-config" src="<?= $base ?>/assets/js/tailwind-config.js"></script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen selection:bg-primary/20 selection:text-primary">
    <!-- ══ HEADER ════════════════════════════════════════════════════════════════ -->
    <?php include '../components/navbar.php'; ?>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <!-- Hero Section -->
        <div class="mb-12 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-secondary text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="bi bi-file-earmark-text-fill"></i>
                <span>Terms of Service</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">
                Terms &amp; <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Conditions</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto">
                Welcome to Color Magic. By using our website, tools, and services, you agree to comply with the terms set forth below.
            </p>
            <div class="mt-4 text-xs text-slate-500 dark:text-slate-500">
                Last updated: October 11, 2026 &bull; Effective immediately
            </div>
        </div>

        <!-- Terms Content -->
        <div class="space-y-10 text-slate-700 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
            <!-- Section 1 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">1</span>
                    Acceptance of Terms
                </h2>
                <p>
                    By accessing or using Color Magic (<a href="https://colormagic.techkreative.com/" class="text-primary hover:underline">colormagic.techkreative.com</a>), you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree to these terms, please do not use our services.
                </p>
            </section>

            <!-- Section 2 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">2</span>
                    License &amp; Use of Color Data
                </h2>
                <p class="mb-3">
                    Color Magic is dedicated to making color discovery accessible for everyone:
                </p>
                <div class="space-y-3">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <strong class="text-slate-900 dark:text-white block mb-1">Color Codes &amp; Palettes:</strong>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">
                            You are free to use, copy, export, and incorporate any color names, hex codes, RGB coordinates, and color palettes generated or discovered on Color Magic into your personal, educational, and commercial design projects without royalty fees or attribution requirements.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <strong class="text-slate-900 dark:text-white block mb-1">Open Source Source Code:</strong>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">
                            The source code of the Color Magic project is licensed under the permissive MIT Open Source License. You can review and contribute to the code on our <a href="<?= $base ?>/open-source" class="text-primary hover:underline">Open Source page</a> or on GitHub.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 3 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">3</span>
                    Acceptable Use Policy
                </h2>
                <p class="mb-3">You agree not to engage in any of the following prohibited behaviors:</p>
                <ul class="list-disc pl-5 space-y-2 text-slate-600 dark:text-slate-400">
                    <li>Using automated tools, scrapers, bots, or scripts to flood or overload our servers at destructive request rates.</li>
                    <li>Attempting to bypass authentication mechanisms or compromise user sessions.</li>
                    <li>Uploading malicious payloads, harmful scripts, or corrupted files via image upload or form fields.</li>
                    <li>Reverse engineering, re-selling, or redistributing the service as a closed-source paid clone without respecting open-source licenses.</li>
                </ul>
            </section>

            <!-- Section 4 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">4</span>
                    User Accounts &amp; Data Security
                </h2>
                <p class="mb-3">
                    When you sign in with Google OAuth to sync favorites and personal palettes:
                </p>
                <ul class="list-disc pl-5 space-y-2 text-slate-600 dark:text-slate-400">
                    <li>You are responsible for safeguarding your Google account credentials.</li>
                    <li>You may terminate your account and wipe all stored palette favorites at any time through your Profile dashboard.</li>
                    <li>We reserve the right to suspend or terminate accounts that violate our acceptable use standards.</li>
                </ul>
            </section>

            <!-- Section 5 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">5</span>
                    Disclaimer of Warranties
                </h2>
                <p>
                    Color Magic is provided on an <strong>"AS IS"</strong> and <strong>"AS AVAILABLE"</strong> basis without warranties of any kind, whether express, implied, or statutory. While we strive for extreme mathematical precision in color conversions and Delta-E color naming matches, we do not warrant that color values will appear identically across all monitor calibrations, color profiles (sRGB, Display P3, CMYK), or operating systems.
                </p>
            </section>

            <!-- Section 6 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">6</span>
                    Limitation of Liability
                </h2>
                <p>
                    To the maximum extent permitted by applicable law, TechKreative, its developers, and contributors shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.
                </p>
            </section>

            <!-- Section 7 -->
            <section>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-sm font-bold">7</span>
                    Changes to Terms &amp; Inquiries
                </h2>
                <p class="mb-4">
                    We may revise these Terms and Conditions periodically. Continued use of Color Magic following updates signifies your acceptance of the amended terms.
                </p>
                <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-secondary/20 rounded-2xl p-6">
                    <div class="font-bold text-slate-900 dark:text-white mb-1">Questions about these Terms?</div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        If you have questions regarding these conditions or licensing details, please reach out to us.
                    </p>
                    <a href="<?= $base ?>/contact"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-secondary text-white text-sm font-semibold hover:bg-purple-700 transition shadow-md shadow-purple-500/20">
                        <i class="bi bi-chat-dots"></i>
                        <span>Contact Support</span>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
