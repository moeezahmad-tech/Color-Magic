const sections = {
    'explore': document.getElementById('explore-section'),
    'find-color': document.getElementById('find-color-section'),
    'generate': document.getElementById('generate-section')
};

const navButtons = document.querySelectorAll('.nav-btn, .nav-btn-mobile');
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

navButtons.forEach(btn => {
    btn.addEventListener('click', function () {
        const targetSection = this.dataset.section;
        updateNavigationState(targetSection);

        if (mobileMenuOverlay && !mobileMenuOverlay.classList.contains('hidden')) {
            mobileMenuOverlay.classList.add('hidden');
            mobileMenuOverlay.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    });
});

function updateNavigationState(targetSection) {
    localStorage.setItem('activeSection', targetSection);

    navButtons.forEach(b => {
        const isMobile = b.classList.contains('nav-btn-mobile');

        if (isMobile) {
            b.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
            b.classList.add('hover:bg-slate-100', 'dark:hover:bg-slate-800');
            const icon = b.querySelector('i');
            const subtext = b.querySelector('.text-sm');

            if (icon) {
                icon.classList.remove('bg-white/20');
                icon.classList.add('bg-primary/10', 'text-primary');
            }
            if (subtext) subtext.classList.remove('text-white/70');
            if (subtext) subtext.classList.add('text-slate-500');
        } else {
            b.classList.remove('bg-gradient-to-r', 'from-primary', 'to-blue-500', 'text-white', 'shadow-lg', 'shadow-primary/25');
            b.classList.add('bg-white', 'dark:bg-slate-800', 'border', 'border-slate-200', 'dark:border-slate-700');

            const iconContainer = b.querySelector('.w-10');
            if (iconContainer) {
                iconContainer.classList.remove('bg-white/20', 'backdrop-blur-sm');
                iconContainer.classList.add('bg-primary/10');
            }

            const icon = b.querySelector('i');
            if (icon) {
                icon.classList.add('text-primary');
                icon.classList.remove('text-white');
            }

            const titleSpan = b.querySelector('span.block.text-sm');
            if (titleSpan) {
                titleSpan.classList.remove('font-bold', 'text-white');
                titleSpan.classList.add('font-semibold', 'text-slate-700', 'dark:text-slate-200');
            }

            const subtitleSpan = b.querySelector('span.block.text-xs');
            if (subtitleSpan) {
                subtitleSpan.classList.remove('opacity-80', 'text-white');
                subtitleSpan.classList.add('text-slate-500', 'dark:text-slate-400');
            }
        }

        if (b.dataset.section === targetSection) {
            if (isMobile) {
                b.classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20');
                b.classList.remove('hover:bg-slate-100', 'dark:hover:bg-slate-800');
                const icon = b.querySelector('i');
                const subtext = b.querySelector('.text-sm');

                if (icon) {
                    icon.classList.remove('bg-primary/10', 'text-primary');
                    icon.classList.add('bg-white/20');
                }
                if (subtext) subtext.classList.add('text-white/70');
                if (subtext) subtext.classList.remove('text-slate-500');
            } else {
                b.classList.remove('bg-white', 'dark:bg-slate-800', 'border', 'border-slate-200', 'dark:border-slate-700');
                b.classList.add('bg-gradient-to-r', 'from-primary', 'to-blue-500', 'text-white', 'shadow-lg', 'shadow-primary/25');

                const iconContainer = b.querySelector('.w-10');
                if (iconContainer) {
                    iconContainer.classList.remove('bg-primary/10');
                    iconContainer.classList.add('bg-white/20', 'backdrop-blur-sm');
                }

                const icon = b.querySelector('i');
                if (icon) {
                    icon.classList.remove('text-primary');
                    icon.classList.add('text-white');
                }

                const titleSpan = b.querySelector('span.block.text-sm');
                if (titleSpan) {
                    titleSpan.classList.remove('font-semibold', 'text-slate-700', 'dark:text-slate-200');
                    titleSpan.classList.add('font-bold', 'text-white');
                }

                const subtitleSpan = b.querySelector('span.block.text-xs');
                if (subtitleSpan) {
                    subtitleSpan.classList.remove('text-slate-500', 'dark:text-slate-400');
                    subtitleSpan.classList.add('opacity-80', 'text-white');
                }
            }
        }
    });

    Object.values(sections).forEach(section => {
        if (section) {
            section.classList.add('hidden');
            section.classList.remove('flex');
        }
    });

    if (sections[targetSection]) {
        sections[targetSection].classList.remove('hidden');
        sections[targetSection].classList.add('flex');
    }

    if (targetSection === 'find-color') {
        if (typeof loadColorHistory === 'function' && document.getElementById('colorResultsGrid')) {
            loadColorHistory();
        }
    }
}

const savedSection = localStorage.getItem('activeSection') || 'explore';
updateNavigationState(savedSection);
