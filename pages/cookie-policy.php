<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Cookie Policy | Color Magic</title>
    <meta name="description"
        content="Understand how Color Magic uses cookies and local storage to save your color preferences, themes, and authenticate sessions." />
    <meta name="keywords"
        content="cookie policy, local storage, color magic cookies, privacy, tracking, TechKreative" />
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
    <meta property="og:url" content="https://colormagic.techkreative.com/cookie-policy" />
    <meta property="og:title" content="Cookie Policy | Color Magic" />
    <meta property="og:description"
        content="Read about how Color Magic uses cookies and browser local storage to provide a seamless design tool experience." />
    <meta property="og:image" content="<?= $base ?>/assets/images/logo.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/cookie-policy" />
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
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="bi bi-cookie"></i>
                <span>Transparency &amp; Cookies</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">
                Cookie <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Policy</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto">
                Discover how Color Magic utilizes minimal cookies and local browser storage to personalize your color design experience.
            </p>
            <div class="mt-4 text-xs text-slate-500 dark:text-slate-500">
                Last updated: October 11, 2026 &bull; Effective immediately
            </div>
        </div>

        <!-- Policy Content -->
        <div class="space-y-10 text-slate-700 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
            <!-- Section 1 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">1</span>
                    What Are Cookies?
                </h2>
                <p>
                    Cookies are tiny text files stored on your computer or mobile device when you load websites. They are widely used to make web applications function reliably, remember your display preferences, and deliver anonymous analytics. In addition to HTTP cookies, modern websites use <strong>HTML5 LocalStorage</strong> to persist user preferences directly on your device without transmitting them across network requests.
                </p>
            </section>

            <!-- Section 2 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">2</span>
                    How We Use Storage &amp; Cookies
                </h2>
                <p class="mb-4">
                    Color Magic adheres to a strict minimal-data philosophy. We only utilize cookies and storage keys essential for platform utility:
                </p>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800 text-xs uppercase font-bold text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="p-4">Cookie / Key Name</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">Purpose</th>
                                <th class="p-4">Duration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <tr>
                                <td class="p-4 font-mono text-xs font-bold text-primary">theme</td>
                                <td class="p-4 text-xs">LocalStorage</td>
                                <td class="p-4 text-xs">Remembers whether you prefer Dark Mode or Light Mode.</td>
                                <td class="p-4 text-xs">Persistent</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-mono text-xs font-bold text-primary">color_magic_favs</td>
                                <td class="p-4 text-xs">LocalStorage</td>
                                <td class="p-4 text-xs">Saves your bookmarked colors &amp; custom generated palettes locally.</td>
                                <td class="p-4 text-xs">Persistent</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-mono text-xs font-bold text-secondary">PHPSESSID</td>
                                <td class="p-4 text-xs">HTTP Cookie</td>
                                <td class="p-4 text-xs">Maintains your authenticated session state when signing in via Google.</td>
                                <td class="p-4 text-xs">Session</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-mono text-xs font-bold text-slate-600 dark:text-slate-400">_ga, _ga_*</td>
                                <td class="p-4 text-xs">HTTP Cookie</td>
                                <td class="p-4 text-xs">Google Analytics metrics to gauge aggregated visitor traffic and tool usage.</td>
                                <td class="p-4 text-xs">Up to 2 years</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Section 3 -->
            <section class="border-b border-slate-200 dark:border-slate-800 pb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">3</span>
                    Managing &amp; Disabling Cookies
                </h2>
                <p class="mb-3">
                    You can restrict or block cookies at any time via your browser settings:
                </p>
                <ul class="list-disc pl-5 space-y-2 text-slate-600 dark:text-slate-400">
                    <li><strong>Google Chrome:</strong> Settings &gt; Privacy and security &gt; Third-party cookies.</li>
                    <li><strong>Mozilla Firefox:</strong> Settings &gt; Privacy &amp; Security &gt; Enhanced Tracking Protection.</li>
                    <li><strong>Apple Safari:</strong> Settings &gt; Privacy &gt; Block all cookies.</li>
                    <li><strong>Microsoft Edge:</strong> Settings &gt; Cookies and site permissions.</li>
                </ul>
                <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <em>Please note: Disabling cookies entirely may prevent Google login persistence and theme memory, but you can still search color names and generate palettes freely.</em>
                </p>
            </section>

            <!-- Section 4 -->
            <section>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">4</span>
                    Questions About Our Cookie Policy?
                </h2>
                <p class="mb-4">
                    For any questions regarding how we store client-side state, please contact our team:
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="<?= $base ?>/contact"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-pink-600 transition shadow-md shadow-pink-500/20">
                        <i class="bi bi-chat-dots"></i>
                        <span>Contact TechKreative</span>
                    </a>
                    <a href="<?= $base ?>/privacy-policy"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <i class="bi bi-shield-check"></i>
                        <span>Read Privacy Policy</span>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>
</body>

</html>
