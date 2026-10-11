<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Privacy Policy | Color Magic</title>
    <meta name="description"
        content="Read the Color Magic Privacy Policy. Learn how we collect, protect, and handle your data, local storage preferences, and privacy rights." />
    <meta name="keywords"
        content="privacy policy, color magic privacy, data policy, terms, cookies, privacy rights, TechKreative" />
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
    <meta property="og:url" content="https://colormagic.techkreative.com/privacy-policy" />
    <meta property="og:title" content="Privacy Policy | Color Magic" />
    <meta property="og:description"
        content="Understand how Color Magic collects, stores, and protects your information across our color tools and palette generator." />
    <meta property="og:image" content="<?= $base ?>/assets/images/logo.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/privacy-policy" />
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
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-primary text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Legal & Trust</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">
                Privacy <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Policy</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto">
                Your privacy is fundamental to how we build Color Magic. Learn what information we collect, how we protect it, and your rights.
            </p>
            <div class="mt-4 text-xs text-slate-500 dark:text-slate-500">
                Last updated: October 11, 2026 &bull; Effective immediately
            </div>
        </div>

        <!-- Quick Summary Box -->
        <div class="bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-12 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary"></i>
                Summary in Plain English
            </h2>
            <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 font-bold mt-0.5"></i>
                    <span><strong>No registration required:</strong> You can explore 30,000+ color names, generate palettes, and convert color codes anonymously without creating an account.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 font-bold mt-0.5"></i>
                    <span><strong>We don't sell your data:</strong> We do not sell, rent, or monetize your personal information to third parties or advertisers.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 font-bold mt-0.5"></i>
                    <span><strong>Your favorites stay on your device:</strong> Palettes you save locally remain in your browser's Local Storage unless you sign in with Google to sync them.</span>
                </li>
            </ul>
        </div>

        <!-- Policy Content -->
        <div class="space-y-10 text-slate-700 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
            <!-- Section 1 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">1</span>
                    Who We Are
                </h2>
                <p class="mb-3">
                    Color Magic (<a href="https://colormagic.techkreative.com/" class="text-primary hover:underline">colormagic.techkreative.com</a>) is an open-source digital color exploration and palette generator suite developed and maintained by <strong>TechKreative</strong> ("we", "us", or "our").
                </p>
                <p>
                    For questions regarding this policy or data practices, you can contact us at <a href="mailto:support@techkreative.com" class="text-primary hover:underline">support@techkreative.com</a>.
                </p>
            </section>

            <!-- Section 2 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">2</span>
                    Information We Collect
                </h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-1">A. Information You Provide Directly</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            If you choose to sign in using Google OAuth, we receive basic profile details authorized by Google: your full name, email address, and profile picture avatar. This information is strictly used to authenticate your session and associate your saved custom palettes.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-1">B. Local Browser Storage</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            We store client-side preferences (such as your chosen UI color theme: Dark Mode or Light Mode) and locally-saved color palettes using HTML5 LocalStorage. This data resides on your physical machine and is not sent to our servers when browsing logged-out.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 dark:text-white mb-1">C. Usage Data &amp; Web Analytics</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            We use Google Analytics (GA4) to collect aggregated, anonymized metrics (such as page views, browser type, device category, referring URL, and interaction events). This data helps us optimize platform loading speed and identify popular tools.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 3 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">3</span>
                    How We Use Your Information
                </h2>
                <p class="mb-3">We use collected information solely for legitimate service purposes:</p>
                <ul class="list-disc pl-5 space-y-2 text-slate-600 dark:text-slate-400">
                    <li>To provide, operate, and maintain our color calculators, palettes, and gradient tools.</li>
                    <li>To authenticate users and enable cross-device palette syncing for signed-in accounts.</li>
                    <li>To understand how visitors navigate the platform in order to improve user experience and fix bugs.</li>
                    <li>To prevent fraudulent activity, automated abuse, or security incidents.</li>
                </ul>
            </section>

            <!-- Section 4 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">4</span>
                    Third-Party Services
                </h2>
                <p class="mb-3">We partner with trustworthy third-party infrastructure providers:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <div class="font-semibold text-slate-900 dark:text-white mb-1">Google Identity &amp; OAuth</div>
                        <p class="text-xs text-slate-500">Provides secure single-sign-on without us ever seeing or storing your password.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <div class="font-semibold text-slate-900 dark:text-white mb-1">Google Analytics</div>
                        <p class="text-xs text-slate-500">Collects anonymous visitor statistics to measure platform performance.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <div class="font-semibold text-slate-900 dark:text-white mb-1">Content Delivery Networks (CDNs)</div>
                        <p class="text-xs text-slate-500">Delivers static assets (Tailwind, Google Fonts, icons) with low latency worldwide.</p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <div class="font-semibold text-slate-900 dark:text-white mb-1">Social Platforms (Pinterest &amp; Instagram)</div>
                        <p class="text-xs text-slate-500">Links to our verified creative channels for color inspiration sharing.</p>
                    </div>
                </div>
            </section>

            <!-- Section 5 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">5</span>
                    Your Data Rights (GDPR &amp; CCPA)
                </h2>
                <p class="mb-3">
                    Regardless of your geographic location, we respect your rights regarding your personal information:
                </p>
                <ul class="list-disc pl-5 space-y-2 text-slate-600 dark:text-slate-400">
                    <li><strong>Right to Access:</strong> You can view all information associated with your profile anytime.</li>
                    <li><strong>Right to Rectification:</strong> You can request corrections to any inaccurate data.</li>
                    <li><strong>Right to Erasure (Delete Account):</strong> You can delete your account and all associated saved palettes at any time directly through your <a href="<?= $base ?>/profile" class="text-primary hover:underline">Profile settings</a> or by contacting us.</li>
                    <li><strong>Right to Opt-Out:</strong> You can block cookies via your browser settings or use privacy-focused ad/tracker blockers.</li>
                </ul>
            </section>

            <!-- Section 6 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">6</span>
                    Data Retention &amp; Security
                </h2>
                <p class="mb-3">
                    All communication with Color Magic is encrypted in transit using industry-standard Transport Layer Security (HTTPS/SSL). We retain authenticated profile data only for as long as your account is active. When you delete your account, your data is permanently removed from our active database.
                </p>
            </section>

            <!-- Section 7 -->
            <section>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-sm font-bold">7</span>
                    Contact &amp; Policy Updates
                </h2>
                <p class="mb-4">
                    We may occasionally update this Privacy Policy to reflect enhancements or legal adjustments. Any modifications will be posted here with a refreshed "Last updated" date.
                </p>
                <div class="bg-gradient-to-r from-pink-500/10 to-purple-500/10 border border-primary/20 rounded-2xl p-6">
                    <div class="font-bold text-slate-900 dark:text-white mb-1">Have a question or request?</div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Our privacy team at TechKreative will gladly assist you with any inquiries or data deletion requests.
                    </p>
                    <a href="<?= $base ?>/contact"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-pink-600 transition shadow-md shadow-pink-500/20">
                        <i class="bi bi-envelope"></i>
                        <span>Contact Privacy Team</span>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
