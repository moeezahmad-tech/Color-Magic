/**
 * gradients-page.js
 * Renders and manages the Explore Gradients page.
 *
 * Self-contained — no external component dependencies required.
 */

(function () {
    'use strict';

    // ─── App State ────────────────────────────────────────────────────────────

    var App = {
        gradients: [],
        filtered:  [],
        rendered:  0,
        typeFilter:  'all',
        styleFilter: 'all',
        query:    '',
        loading:  false,
        styles:   []
    };

    var PAGE_SIZE = 30;

    // Fisher-Yates shuffle
    function shuffle(arr) {
        var a = arr.slice();
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var t = a[i]; a[i] = a[j]; a[j] = t;
        }
        return a;
    }

    // ─── DOM References ───────────────────────────────────────────────────────

    var gradientGrid         = document.getElementById('gradientGrid');
    var loadMoreBtn          = document.getElementById('loadMoreGradientsBtn');
    var gradientCountStatus  = document.getElementById('gradientCountStatus');
    var gradientCountEl      = document.getElementById('gradientCount');
    var searchInput          = document.getElementById('gradientSearchInput');
    var styleFilterContainer = document.getElementById('styleFilterContainer');

    // ─── Card Builder ─────────────────────────────────────────────────────────

    function buildGradientCard(g) {
        if (window.ColorMagic && window.ColorMagic.createGradientCard) {
            return window.ColorMagic.createGradientCard(g);
        }
        var card = document.createElement('div');
        card.className =
            'gradient-card bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col';
        return card;
    }

    // ─── Rendering ────────────────────────────────────────────────────────────

    function showLoadingState() {
        if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
        if (gradientCountStatus) gradientCountStatus.textContent = '';
        var skeletonHtml = '';
        for (var i = 0; i < 12; i++) {
            skeletonHtml +=
                '<div class="col-span-1 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-4 flex flex-col justify-between h-[230px] animate-pulse">'
                + '<div class="h-32 rounded-2xl bg-slate-100 dark:bg-slate-800 w-full mb-3"></div>'
                + '<div class="flex items-center justify-between">'
                + '<div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-1/3"></div>'
                + '<div class="flex gap-2"><div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800"></div><div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800"></div></div>'
                + '</div></div>';
        }
        gradientGrid.innerHTML = skeletonHtml;
    }

    function showErrorState(message) {
        if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
        if (gradientCountStatus) gradientCountStatus.textContent = '';
        gradientGrid.innerHTML =
            '<div class="col-span-full flex flex-col items-center justify-center py-20 text-center">'
            + '<i class="bi bi-exclamation-triangle text-6xl text-red-500 mb-4"></i>'
            + '<p class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">Failed to Load Gradients</p>'
            + '<p class="text-sm text-slate-500 mb-6 max-w-md">' + message + '</p>'
            + '<button onclick="window._cmGradientRetry()" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all flex items-center gap-2">'
            + '<i class="bi bi-arrow-clockwise"></i> Retry</button>'
            + '</div>';
    }

    function showEmptyState() {
        gradientGrid.innerHTML =
            '<div class="col-span-full flex flex-col items-center justify-center py-20 text-center">'
            + '<i class="bi bi-rainbow text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>'
            + '<p class="text-xl font-bold text-slate-700 dark:text-slate-300">No Gradients Found</p>'
            + '<p class="text-sm text-slate-500 max-w-md mt-2">Try changing your filters or search query.</p>'
            + '</div>';
    }

    function updatePaginationUI() {
        var total = App.filtered.length;
        if (gradientCountEl) gradientCountEl.textContent = total + ' gradient' + (total !== 1 ? 's' : '');
        if (gradientCountStatus) {
            gradientCountStatus.textContent = total === 0
                ? 'No gradients match your current filters.'
                : 'Showing ' + App.rendered + ' of ' + total + ' gradients';
        }
        if (loadMoreBtn) {
            loadMoreBtn.classList.toggle('hidden', App.rendered >= App.filtered.length || total === 0);
        }
    }

    function renderNextBatch(reset) {
        if (reset) {
            gradientGrid.innerHTML = '';
            App.rendered = 0;
        }

        if (App.filtered.length === 0) {
            showEmptyState();
            updatePaginationUI();
            return;
        }

        var chunk    = App.filtered.slice(App.rendered, App.rendered + PAGE_SIZE);
        var fragment = document.createDocumentFragment();
        chunk.forEach(function (g) {
            fragment.appendChild(buildGradientCard(g));
        });
        gradientGrid.appendChild(fragment);
        App.rendered += chunk.length;
        updatePaginationUI();
    }

    // ─── Filtering ────────────────────────────────────────────────────────────

    function applyFilters() {
        var result = App.gradients.slice();

        if (App.typeFilter !== 'all') {
            result = result.filter(function (g) { return g.type === App.typeFilter; });
        }

        if (App.styleFilter !== 'all') {
            result = result.filter(function (g) {
                return g.style.toLowerCase() === App.styleFilter.toLowerCase();
            });
        }

        if (App.query !== '') {
            result = result.filter(function (g) {
                return g.name.toLowerCase().indexOf(App.query) !== -1
                    || g.style.toLowerCase().indexOf(App.query) !== -1
                    || g.type.toLowerCase().indexOf(App.query) !== -1
                    || g.colors.join(' ').toLowerCase().indexOf(App.query) !== -1;
            });
        }

        App.filtered = result;
    }

    function applyFiltersAndRender() {
        applyFilters();
        renderNextBatch(true);
    }

    // ─── Style filter button builder ──────────────────────────────────────────

    function buildStyleButtons() {
        if (!styleFilterContainer) return;
        var styleIcons = {
            'Warm':     'bi-sun-fill',
            'Cool':     'bi-snow',
            'Purple':   'bi-flower2',
            'Nature':   'bi-tree-fill',
            'Pink':     'bi-heart-fill',
            'Dark':     'bi-moon-fill',
            'Pastel':   'bi-cloud-fill',
            'Neon':     'bi-lightning-fill',
            'Earth':    'bi-globe-americas',
            'Mono':     'bi-circle-half',
            'Aurora':   'bi-stars',
            'Sunset':   'bi-sunset-fill',
            'Ocean':    'bi-water',
            'Galaxy':   'bi-stars',
            'Midnight': 'bi-moon-stars-fill',
            'Luxury':   'bi-gem',
            'Forest':   'bi-tree-fill',
            'Sky':      'bi-cloud-sun-fill',
            'Royal':    'bi-award-fill',
            'Rose':     'bi-flower1',
            'Emerald':  'bi-hexagon-fill',
            'Lavender': 'bi-flower3',
            'Peach':    'bi-circle-fill',
            'Candy':    'bi-balloon-fill',
            'Ice':      'bi-snow2',
            'Coffee':   'bi-cup-hot-fill',
            'Volcano':  'bi-fire',
            'Autumn':   'bi-leaf-fill',
            'Spring':   'bi-flower2',
            'Summer':   'bi-brightness-high-fill',
            'Winter':   'bi-snow',
            'Gold':     'bi-coin',
            'Glass':    'bi-window',
            'Cyber':    'bi-cpu-fill',
            'Minimal':  'bi-circle-half'
        };

        App.styles.forEach(function (style) {
            var btn = document.createElement('button');
            btn.className =
                'style-filter flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-pink-50 dark:hover:bg-slate-700 text-xs font-semibold transition-all border border-slate-200 dark:border-slate-700';
            btn.dataset.style = style.toLowerCase();
            var icon = styleIcons[style] || 'bi-tag-fill';
            btn.innerHTML = '<i class="bi ' + icon + '"></i> ' + style;
            styleFilterContainer.appendChild(btn);
        });

        // Attach listeners
        styleFilterContainer.addEventListener('click', function (e) {
            var btn = e.target.closest('.style-filter');
            if (!btn) return;
            document.querySelectorAll('.style-filter').forEach(function (b) {
                b.classList.remove('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-md', 'shadow-primary/20', 'font-bold', 'border-transparent');
                b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'font-semibold', 'border-slate-200', 'dark:border-slate-700');
            });
            btn.classList.add('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-md', 'shadow-primary/20', 'font-bold', 'border-transparent');
            btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'font-semibold', 'border-slate-200', 'dark:border-slate-700');
            App.styleFilter = btn.dataset.style;

            // Show/clear clear-filter button
            var clearBtn = document.getElementById('clearStyleBtn');
            if (clearBtn) clearBtn.classList.toggle('hidden', App.styleFilter === 'all');

            applyFiltersAndRender();
        });

        // Clear style filter button
        var clearStyleBtn = document.getElementById('clearStyleBtn');
        if (clearStyleBtn) {
            clearStyleBtn.addEventListener('click', function () {
                document.querySelectorAll('.style-filter').forEach(function (b) {
                    b.classList.remove('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-md', 'shadow-primary/20', 'font-bold', 'border-transparent');
                    b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'font-semibold', 'border-slate-200', 'dark:border-slate-700');
                });
                var allBtn = document.querySelector('.style-filter[data-style="all"]');
                if (allBtn) {
                    allBtn.classList.add('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-md', 'shadow-primary/20', 'font-bold', 'border-transparent');
                    allBtn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'font-semibold', 'border-slate-200', 'dark:border-slate-700');
                }
                App.styleFilter = 'all';
                clearStyleBtn.classList.add('hidden');
                applyFiltersAndRender();
            });
        }
    }

    // ─── Data Fetching ────────────────────────────────────────────────────────

    function fetchGradients() {
        if (App.loading) return;
        App.loading = true;
        showLoadingState();

        var apiPromise = (window.ColorMagic && window.ColorMagic.api)
            ? window.ColorMagic.api.getGradients({ limit: 1000 })
            : fetch('/api/gradients.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });

        apiPromise
            .then(function (result) {
                var data = (result && result.data) ? result.data : result;
                if (!Array.isArray(data) || data.length === 0) throw new Error('Invalid gradient data format');
                App.gradients = shuffle(data);

                // Extract unique styles (preserve order from JSON)
                var seen = {};
                App.styles = [];
                data.forEach(function (g) {
                    if (!seen[g.style]) {
                        seen[g.style] = true;
                        App.styles.push(g.style);
                    }
                });
                buildStyleButtons();

                App.loading = false;
                applyFiltersAndRender();
            })
            .catch(function (err) {
                App.loading = false;
                showErrorState(err.message);
            });
    }

    window._cmGradientRetry = fetchGradients;

    // ─── Event Listeners ──────────────────────────────────────────────────────

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            App.query = this.value.toLowerCase().trim();
            applyFiltersAndRender();
        });
    }

    // Type filter buttons (All / Linear / Radial)
    document.querySelectorAll('.type-filter').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.type-filter').forEach(function (b) {
                b.classList.remove('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-md', 'shadow-primary/20', 'font-bold', 'border-transparent');
                b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'font-semibold', 'border-slate-200', 'dark:border-slate-700');
            });
            this.classList.add('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-md', 'shadow-primary/20', 'font-bold', 'border-transparent');
            this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'font-semibold', 'border-slate-200', 'dark:border-slate-700');
            App.typeFilter = this.dataset.type;
            applyFiltersAndRender();
        });
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () { renderNextBatch(false); });
    }

    // Event delegation: copy CSS, copy swatch hex
    if (gradientGrid) {
        gradientGrid.addEventListener('click', function (e) {

            // Gradient favorite toggle
            var gradFavBtn = e.target.closest('.gradient-fav-btn');
            if (gradFavBtn) {
                var gid   = gradFavBtn.dataset.gradientId;
                var added = window.ColorMagic.GradientFavorites.toggleFavorite(gid);
                var icon  = gradFavBtn.querySelector('i');
                if (icon) {
                    icon.className = 'bi ' + (added ? 'bi-heart-fill' : 'bi-heart') + ' text-base';
                }
                gradFavBtn.className = 'gradient-fav-btn border-none bg-transparent p-1 rounded-md transition-colors ' + (added ? 'text-red-500' : 'text-slate-400 hover:text-red-500');
                gradFavBtn.title = added ? 'Remove from favorites' : 'Add to favorites';
                return;
            }

            // Copy gradient CSS — stop propagation to prevent navigating to detail page
            var copyBtn = e.target.closest('.copy-css-btn');
            if (copyBtn) {
                e.preventDefault();
                e.stopPropagation();
                var css    = copyBtn.dataset.css;
                var icon   = copyBtn.querySelector('i');
                var label  = copyBtn.querySelector('span');
                var origIcon  = icon ? icon.className : '';
                var origLabel = label ? label.textContent : '';
                navigator.clipboard.writeText(css).then(function () {
                    if (icon)  icon.className = 'bi bi-check-circle-fill text-sm';
                    if (label) label.textContent = 'Copied!';
                    copyBtn.classList.add('copied-state');
                    setTimeout(function () {
                        if (icon)  icon.className = origIcon;
                        if (label) label.textContent = origLabel;
                        copyBtn.classList.remove('copied-state');
                    }, 2000);
                }).catch(function (err) { console.error('Copy failed:', err); });
                return;
            }

            // Copy individual swatch hex
            var sw = e.target.closest('.flex-1.cursor-pointer');
            if (sw && sw.title) {
                var hex = sw.title;
                navigator.clipboard.writeText(hex).catch(function (err) {
                    console.error('Copy failed:', err);
                });
            }
        });
    }

    // ─── Init ─────────────────────────────────────────────────────────────────
    fetchGradients();

})();
