/**
 * index-tabs.js
 * Handles the Explore Palettes / Find Color tab switching on index.html
 */

(function () {
    const tabs = document.querySelectorAll('.page-tab');
    const sections = {};

    tabs.forEach(tab => {
        const sectionId = tab.dataset.tab;
        sections[sectionId] = document.getElementById(sectionId);
    });

    function activate(targetId) {
        tabs.forEach(t => {
            const isTarget = t.dataset.tab === targetId;
            t.classList.toggle('active', isTarget);
            t.setAttribute('aria-selected', isTarget ? 'true' : 'false');
        });

        Object.entries(sections).forEach(([id, el]) => {
            if (!el) return;
            el.classList.toggle('active', id === targetId);
        });

        // Pre-load color history when switching to Find Color
        if (targetId === 'find-section') {
            if (typeof loadColorHistory === 'function' && document.getElementById('colorResultsGrid')) {
                loadColorHistory();
            }
        }

        try {
            localStorage.setItem('cm_activeTab', targetId);
        } catch (_) {}
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => activate(tab.dataset.tab));
    });

    // Restore last-visited tab
    try {
        const saved = localStorage.getItem('cm_activeTab');
        if (saved && sections[saved]) {
            activate(saved);
        } else {
            activate('explore-section');
        }
    } catch (_) {
        activate('explore-section');
    }

    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

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
})();
