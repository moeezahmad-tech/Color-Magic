/**
 * sidebar.js  – shared sidebar + mobile-menu logic for all pages
 *
 * Usage: include BEFORE closing </body> on every page.
 * Set window.CM_ACTIVE_PAGE to one of:
 *   'explore' | 'find-color' | 'generate' | 'open-source'
 * before this script loads so the right nav item gets highlighted.
 */
(function () {
    // ── Mobile menu ───────────────────────────────────────────────────────────
    const mobileMenuBtn     = document.getElementById('mobileMenuBtn');
    const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');
    const mobileMenuOverlay  = document.getElementById('mobileMenuOverlay');

    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenuOverlay.classList.remove('hidden');
        mobileMenuOverlay.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
    closeMobileMenuBtn?.addEventListener('click', () => {
        mobileMenuOverlay.classList.add('hidden');
        mobileMenuOverlay.classList.remove('flex');
        document.body.style.overflow = 'auto';
    });
    // Close mobile menu if resized past lg breakpoint
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024 && mobileMenuOverlay && !mobileMenuOverlay.classList.contains('hidden')) {
            mobileMenuOverlay.classList.add('hidden');
            mobileMenuOverlay.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    });

    // ── Active sidebar item highlighting ──────────────────────────────────────
    const activePage = window.CM_ACTIVE_PAGE || 'explore';

    const ACTIVE_CLASSES = [
        'bg-gradient-to-r','from-secondary','to-primary','text-white',
        'shadow-lg','shadow-primary/25'
    ];
    const INACTIVE_CLASSES = [
        'bg-slate-50','dark:bg-slate-800/60','hover:bg-pink-50',
        'dark:hover:bg-slate-700','text-slate-700','dark:text-slate-200',
        'border','border-slate-200','dark:border-slate-700'
    ];

    document.querySelectorAll('[data-nav-page]').forEach(el => {
        const page = el.dataset.navPage;
        if (page === activePage) {
            el.classList.remove(...INACTIVE_CLASSES);
            el.classList.add(...ACTIVE_CLASSES);
            // Make icon white in active state
            const icon = el.querySelector('i');
            if (icon) {
                icon.classList.remove('text-primary','text-secondary');
                icon.classList.add('text-white');
            }
        }
    });
})();
