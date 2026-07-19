(function () {
    // ── Mobile Menu Toggle ───────────────────────────────────────────
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenuOverlay?.classList.remove('hidden');
        mobileMenuOverlay?.classList.add('flex');
        document.body.style.overflow = 'hidden';
    });
    closeMobileMenuBtn?.addEventListener('click', () => {
        mobileMenuOverlay?.classList.add('hidden');
        mobileMenuOverlay?.classList.remove('flex');
        document.body.style.overflow = 'auto';
    });
})();
