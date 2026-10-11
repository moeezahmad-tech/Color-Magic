/**
 * explore-palettes.js
 * Coordinates the Explore Palettes page.
 *
 * Depends on (must be loaded before this script):
 *   assets/js/utils.js
 *   assets/js/services/favorites.js
 *   assets/js/components/palette-card.js
 */

(function () {
    'use strict';

    // ─── App State ────────────────────────────────────────────────────────────

    var App = {
        palettes: [],
        filtered: [],
        rendered: 0,
        filter:   'all',
        query:    '',
        loading:  false,
        error:    null
    };

    var PAGE_SIZE = 60;

    // ─── DOM References ───────────────────────────────────────────────────────

    var paletteGrid        = document.getElementById('paletteGrid');
    var loadMoreBtn        = document.getElementById('loadMoreBtn');
    var paletteCountStatus = document.getElementById('paletteCountStatus');
    var searchInput        = document.getElementById('searchInput');

    // ─── Rendering ────────────────────────────────────────────────────────────

    function showLoadingState() {
        if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
        if (paletteCountStatus) paletteCountStatus.textContent = '';
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
        paletteGrid.innerHTML = skeletonHtml;
    }

    function showErrorState(message) {
        if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
        if (paletteCountStatus) paletteCountStatus.textContent = '';
        paletteGrid.innerHTML =
            '<div class="col-span-full flex flex-col items-center justify-center py-20 text-center">'
            + '<i class="bi bi-exclamation-triangle text-6xl text-red-500 mb-4"></i>'
            + '<p class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">Failed to Load Palettes</p>'
            + '<p class="text-sm text-slate-500 mb-6 max-w-md">' + message + '</p>'
            + '<button onclick="window._cmRetry()" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all flex items-center gap-2">'
            + '<i class="bi bi-arrow-clockwise"></i> Retry</button>'
            + '</div>';
    }

    function showEmptyState() {
        var isFav = App.filter === 'favorites';
        paletteGrid.innerHTML =
            '<div class="col-span-full flex flex-col items-center justify-center py-20 text-center">'
            + '<i class="bi bi-heart text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>'
            + '<p class="text-xl font-bold text-slate-700 dark:text-slate-300">'
            + (isFav ? 'No Favorites Yet' : 'No Palettes Found') + '</p>'
            + '<p class="text-sm text-slate-500 max-w-md mt-2">'
            + (isFav
                ? 'Start adding palettes to your favorites by clicking the heart icon on any palette card.'
                : 'Try changing your filters or search query to discover more color palettes.')
            + '</p></div>';
    }

    function updatePaginationUI() {
        var total = App.filtered.length;
        if (paletteCountStatus) {
            paletteCountStatus.textContent = total === 0
                ? 'No palettes match your current filters.'
                : 'Showing ' + App.rendered + ' of ' + total + ' palettes';
        }
        if (loadMoreBtn) {
            var hasMore = App.rendered < App.filtered.length;
            loadMoreBtn.classList.toggle('hidden', !hasMore || total === 0);
        }
    }

    function renderNextBatch(reset) {
        if (reset) {
            paletteGrid.innerHTML = '';
            App.rendered = 0;
        }

        if (App.filtered.length === 0) {
            showEmptyState();
            updatePaginationUI();
            return;
        }

        var nextChunk = App.filtered.slice(App.rendered, App.rendered + PAGE_SIZE);
        var fragment  = document.createDocumentFragment();
        nextChunk.forEach(function (palette) {
            fragment.appendChild(window.ColorMagic.createPaletteCard(palette));
        });
        paletteGrid.appendChild(fragment);
        App.rendered += nextChunk.length;
        updatePaginationUI();
    }

    // ─── Filtering ────────────────────────────────────────────────────────────

    function applyFilters() {
        var result = App.palettes.slice();
        var favs = window.ColorMagic.Favorites.getFavorites();

        if (App.filter === 'favorites') {
            result = result.filter(function (p) { return favs.indexOf(p.id) !== -1; });
        } else if (App.filter !== 'all') {
            result = result.filter(function (p) {
                return p.style.toLowerCase() === App.filter.toLowerCase();
            });
        }

        if (App.query !== '') {
            result = result.filter(function (p) {
                return p.name.toLowerCase().indexOf(App.query) !== -1
                    || p.style.toLowerCase().indexOf(App.query) !== -1
                    || p.colors.join(' ').toLowerCase().indexOf(App.query) !== -1;
            });
        }

        App.filtered = result;
    }

    function applyFiltersAndRender() {
        applyFilters();
        renderNextBatch(true);
    }

    // ─── Data Fetching ────────────────────────────────────────────────────────

    function fetchPalettes() {
        if (App.loading) return;
        App.loading = true;
        App.error   = null;
        showLoadingState();

        var apiPromise = (window.ColorMagic && window.ColorMagic.api)
            ? window.ColorMagic.api.getPalettes({ limit: 1000 })
            : fetch('/api/palettes.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });

        apiPromise
            .then(function (result) {
                var data = (result && result.data) ? result.data : result;
                if (!Array.isArray(data) || data.length === 0) throw new Error('Invalid palette data format');

                // Shuffle then mark duplicate slugs
                App.palettes = data.sort(function () { return Math.random() - 0.5; });
                if (window.ColorMagic && window.ColorMagic.markDuplicateSlugs) {
                    window.ColorMagic.markDuplicateSlugs(App.palettes);
                }

                App.loading = false;
                applyFiltersAndRender();

                // Auto-activate URL filter param e.g. ?filter=favorites
                try {
                    var filterParam = new URLSearchParams(window.location.search).get('filter');
                    if (filterParam) {
                        var btn = document.querySelector('.theme-filter[data-theme="' + filterParam + '"]');
                        if (btn) btn.click();
                    }
                } catch (_) {}
            })
            .catch(function (err) {
                App.loading = false;
                App.error   = err.message;
                showErrorState(err.message);
            });
    }

    // Exposed for the inline retry button
    window._cmRetry = fetchPalettes;

    // ─── Event Listeners ──────────────────────────────────────────────────────

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            App.query = this.value.toLowerCase().trim();
            applyFiltersAndRender();
        });
    }

    document.querySelectorAll('.theme-filter').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.theme-filter').forEach(function (b) {
                b.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'font-bold');
                b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'font-medium');
            });
            this.classList.add('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-lg', 'shadow-primary/20', 'font-bold');
            this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'font-medium');
            App.filter = this.dataset.theme;
            applyFiltersAndRender();
        });
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () { renderNextBatch(false); });
    }

    // Event delegation: copy palette, favorite toggle, copy single swatch
    if (paletteGrid) {
        paletteGrid.addEventListener('click', function (e) {

            // ── Copy single swatch HEX ──────────────────────────────────────
            var hexCopyBtn = e.target.closest('.swatch-copy-hex');
            if (hexCopyBtn) {
                var hexVal    = hexCopyBtn.dataset.hex;
                var icon      = hexCopyBtn.querySelector('i');
                var origLabel = hexCopyBtn.innerHTML;
                navigator.clipboard.writeText(hexVal).then(function () {
                    if (icon) icon.className = 'bi bi-check-circle-fill text-[11px]';
                    hexCopyBtn.classList.add('text-green-600');
                    setTimeout(function () {
                        hexCopyBtn.innerHTML = origLabel;
                        hexCopyBtn.classList.remove('text-green-600');
                    }, 1500);
                }).catch(function (err) { console.error('Copy failed:', err); });
                return;
            }

            // ── Favorite single color ────────────────────────────────────────
            var favColorBtn = e.target.closest('.swatch-fav-color');
            if (favColorBtn) {
                var favHex  = favColorBtn.dataset.hex;
                var added   = window.ColorMagic.ColorFavorites.toggleFavorite(favHex);
                var favIcon = favColorBtn.querySelector('i');
                if (favIcon) {
                    favIcon.className = added
                        ? 'bi bi-heart-fill text-red-500 text-[11px]'
                        : 'bi bi-heart text-[11px]';
                }
                favColorBtn.title = added ? 'Remove from favorites' : 'Add to favorites';
                return;
            }

            // ── Copy entire palette ──────────────────────────────────────────
            var copyBtn = e.target.closest('.copy-palette-btn');
            if (copyBtn) {
                var colors = copyBtn.dataset.colors;
                var icon   = copyBtn.querySelector('i');
                var origClass = icon ? icon.className : '';
                navigator.clipboard.writeText(colors).then(function () {
                    if (icon) icon.className = 'bi bi-check-circle-fill text-xl';
                    copyBtn.classList.add('text-green-500');
                    setTimeout(function () {
                        if (icon) icon.className = origClass;
                        copyBtn.classList.remove('text-green-500');
                    }, 2000);
                }).catch(function (err) { console.error('Copy failed:', err); });
                return;
            }

            // ── Favorite toggle ──────────────────────────────────────────────
            var favBtn = e.target.closest('.favorite-btn');
            if (favBtn) {
                var paletteId = favBtn.dataset.paletteId;
                window.ColorMagic.Favorites.toggleFavorite(paletteId);
                window.ColorMagic.Favorites.updateFavoriteButton(favBtn, paletteId);
                if (App.filter === 'favorites') applyFiltersAndRender();
                return;
            }

        });
    }

    // ─── Init ─────────────────────────────────────────────────────────────────
    fetchPalettes();

})();
