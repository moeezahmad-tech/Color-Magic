<?php include '../components/config.php'; /** @var string $base */ ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Contact Us — Get in Touch | Color Magic</title>
    <meta name="description"
        content="Have questions, suggestions, or feedback for Color Magic? Contact our team at TechKreative or connect with us on social media." />
    <meta name="keywords"
        content="contact color magic, support, color suggestions, feature requests, TechKreative support" />
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
    <meta property="og:url" content="https://colormagic.techkreative.com/contact" />
    <meta property="og:title" content="Contact Us | Color Magic" />
    <meta property="og:description"
        content="Send us feedback, report bugs, or propose custom color palettes to the Color Magic team." />
    <meta property="og:image" content="<?= $base ?>/assets/images/logo.png" />

    <link rel="canonical" href="https://colormagic.techkreative.com/contact" />
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

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-primary text-xs font-semibold uppercase tracking-wider mb-4">
                <i class="bi bi-chat-heart-fill"></i>
                <span>We'd Love to Hear From You</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight mb-4">
                Get in <span class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Touch</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-xl mx-auto">
                Got a color name suggestion, bug report, or feature idea? We typically respond within 24 to 48 hours.
            </p>
        </div>

        <!-- Contact Channels Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/40 text-primary flex items-center justify-center text-xl mb-4">
                    <i class="bi bi-envelope-at"></i>
                </div>
                <h2 class="font-bold text-slate-900 dark:text-white mb-1">Email Support</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Direct help for accounts, bugs, &amp; data</p>
                <a href="mailto:support@techkreative.com" class="text-sm font-semibold text-primary hover:underline">
                    support@techkreative.com
                </a>
            </div>

            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-secondary flex items-center justify-center text-xl mb-4">
                    <i class="bi bi-code-slash"></i>
                </div>
                <h2 class="font-bold text-slate-900 dark:text-white mb-1">GitHub Issues</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Report codebase bugs &amp; PRs</p>
                <a href="https://github.com/moeezahmad-tech/Color-Magic/issues" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-secondary hover:underline">
                    Open an Issue <i class="bi bi-arrow-up-right text-xs"></i>
                </a>
            </div>

            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-500 flex items-center justify-center text-xl mb-4">
                    <i class="bi bi-camera"></i>
                </div>
                <h2 class="font-bold text-slate-900 dark:text-white mb-1">Social &amp; DM</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Daily palette drops &amp; updates</p>
                <div class="flex items-center gap-3">
                    <a href="https://www.instagram.com/colormagic.techkreative/" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-pink-500 hover:underline">
                        Instagram
                    </a>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <a href="https://www.pinterest.com/colormagic_techkreative/" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-red-500 hover:underline">
                        Pinterest
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Form Card -->
        <div class="bg-white dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-sm mb-16">
            <div class="max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 text-center">Send Us a Message</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-8">
                    Fill out the form below and we'll route your message to the appropriate developer or designer.
                </p>

                <form id="contactForm" onsubmit="handleContactSubmit(event)" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="nameInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                                Your Name <span class="text-primary">*</span>
                            </label>
                            <input type="text" id="nameInput" required placeholder="Jane Doe"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm transition" />
                        </div>
                        <div>
                            <label for="emailInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                                Your Email <span class="text-primary">*</span>
                            </label>
                            <input type="email" id="emailInput" required placeholder="jane@example.com"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm transition" />
                        </div>
                    </div>

                    <div>
                        <label for="topicSelect" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Topic / Category
                        </label>
                        <select id="topicSelect"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm transition">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Color Name Suggestion">Color Name Suggestion / Dataset Feedback</option>
                            <option value="Bug Report">Bug Report</option>
                            <option value="Feature Request">Feature Request</option>
                            <option value="Partnership / Open Source">Partnership / Open Source</option>
                        </select>
                    </div>

                    <div>
                        <label for="messageInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Message <span class="text-primary">*</span>
                        </label>
                        <textarea id="messageInput" rows="5" required placeholder="How can we help you today?"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm transition"></textarea>
                    </div>

                    <div id="formStatus" class="hidden p-4 rounded-xl text-sm font-medium"></div>

                    <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-secondary to-primary hover:opacity-95 text-white font-semibold text-sm transition shadow-lg shadow-pink-500/25">
                        <i class="bi bi-send-fill"></i>
                        <span>Send Message</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- FAQ Quick Section -->
        <div class="border-t border-slate-200 dark:border-slate-800 pt-12">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-6 text-center">
                Frequently Asked Questions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Is Color Magic completely free?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Yes! Every feature — palette generation, image extraction, and 30,000+ color name lookups — is 100% free with no subscriptions.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Can I use palettes commercially?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Absolutely. You have full permission to use all palettes, gradients, and hex codes in personal, commercial, and client design projects.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Can I contribute color names?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Yes! Check out our <a href="<?= $base ?>/open-source" class="text-primary hover:underline">Open Source</a> page or submit a pull request on GitHub to expand our curated datasets.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">How fast do you respond to inquiries?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Our engineering and design team at TechKreative typically reviews submissions within 24 to 48 hours during business days.</p>
                </div>
            </div>
        </div>
    </main>

    <?php include '../components/footer.php'; ?>

    <script>
        function handleContactSubmit(e) {
            e.preventDefault();
            const name = document.getElementById('nameInput').value.trim();
            const email = document.getElementById('emailInput').value.trim();
            const topic = document.getElementById('topicSelect').value;
            const message = document.getElementById('messageInput').value.trim();
            const statusEl = document.getElementById('formStatus');

            if (!name || !email || !message) {
                statusEl.className = "p-4 rounded-xl text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200";
                statusEl.textContent = "Please fill in all required fields.";
                statusEl.classList.remove('hidden');
                return;
            }

            // Compose direct mailto link as reliable fallback for immediate transmission
            const subjectEncoded = encodeURIComponent(`[Color Magic] ${topic} from ${name}`);
            const bodyEncoded = encodeURIComponent(`Name: ${name}\nEmail: ${email}\nTopic: ${topic}\n\nMessage:\n${message}`);
            const mailtoUrl = `mailto:support@techkreative.com?subject=${subjectEncoded}&body=${bodyEncoded}`;

            statusEl.className = "p-4 rounded-xl text-sm font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200";
            statusEl.innerHTML = `<i class="bi bi-check-circle-fill mr-1"></i> Thank you, <strong>${name}</strong>! Opening your email client to send to <code>support@techkreative.com</code>... If your client didn't open, you can email us directly at <a href="mailto:support@techkreative.com" class="underline font-bold">support@techkreative.com</a>.`;
            statusEl.classList.remove('hidden');

            setTimeout(() => {
                window.location.href = mailtoUrl;
            }, 600);
        }
    </script>
</body>

</html>
