/**
 * favorites-page.js
 * Renders the unified Favorites page (colors, palettes, gradients).
 *
 * Depends on (must be loaded before this script):
 *   assets/js/utils.js
 *   assets/js/services/favorites.js
 *   assets/js/components/palette-card.js
 */

(function () {
    'use strict';

    // ─── DOM References ─────────────────────────────────────────────────────────
    var favTabs            = document.getElementById('favTabs');
    var favColorsGrid      = document.getElementById('favColorsGrid');
    var favPalettesGrid    = document.getElementById('favPalettesGrid');
    var favGradientsGrid   = document.getElementById('favGradientsGrid');
    var favColorsCount     = document.getElementById('favColorsCount');
    var favPalettesCount   = document.getElementById('favPalettesCount');
    var favGradientsCount  = document.getElementById('favGradientsCount');
    var favTotalCount      = document.getElementById('favTotalCount');
    var panelColors        = document.getElementById('favPanelColors');
    var panelPalettes      = document.getElementById('favPanelPalettes');
    var panelGradients     = document.getElementById('favPanelGradients');

    var activeTab = 'palettes';

    // ─── Tab switching ──────────────────────────────────────────────────────────
    function switchTab(tabName) {
        if (!tabName) return;
        activeTab = tabName;
        document.querySelectorAll('.fav-tab').forEach(function (b) {
            if (b.dataset.tab === tabName) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
        [panelColors, panelPalettes, panelGradients].forEach(function (p) {
            if (p) p.classList.add('hidden');
        });
        var target = activeTab === 'colors' ? panelColors
                   : activeTab === 'palettes' ? panelPalettes
                   : panelGradients;
        if (target) target.classList.remove('hidden');

        try {
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#' + tabName);
            }
        } catch(e) {}
    }

    if (favTabs) {
        favTabs.addEventListener('click', function (e) {
            var btn = e.target.closest('.fav-tab');
            if (!btn) return;
            switchTab(btn.dataset.tab);
        });

        // Initialize based on URL hash if provided
        var hash = (window.location.hash || '').replace('#', '').toLowerCase();
        if (hash === 'colors' || hash === 'gradients' || hash === 'palettes') {
            switchTab(hash);
        } else {
            switchTab('palettes');
        }
    }

    // ─── Empty state builder ────────────────────────────────────────────────────
    function buildEmptyState(icon, title, message, ctaText, ctaHref) {
        var div = document.createElement('div');
        div.className = 'col-span-full flex flex-col items-center justify-center py-20 text-center px-4';
        var ctaHtml = (ctaText && ctaHref)
            ? '<a href="' + ctaHref + '" class="mt-5 px-6 py-2.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition-all active:scale-95 inline-flex items-center gap-2">'
              + '<i class="bi bi-compass"></i><span>' + ctaText + '</span></a>'
            : '';
        div.innerHTML =
            '<div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-4 shadow-inner">'
            +   '<i class="bi ' + icon + ' text-3xl"></i>'
            + '</div>'
            + '<p class="text-xl font-extrabold text-slate-800 dark:text-white mb-2 tracking-tight">' + title + '</p>'
            + '<p class="text-sm text-slate-500 dark:text-slate-400 max-w-md leading-relaxed">' + message + '</p>'
            + ctaHtml;
        return div;
    }

    // ─── Update counts ──────────────────────────────────────────────────────────
    function updateCounts(colorCount, paletteCount, gradientCount) {
        var total = colorCount + paletteCount + gradientCount;
        if (favColorsCount)    favColorsCount.textContent    = colorCount;
        if (favPalettesCount)  favPalettesCount.textContent  = paletteCount;
        if (favGradientsCount) favGradientsCount.textContent = gradientCount;
        if (favTotalCount)     favTotalCount.textContent     = total + ' item' + (total !== 1 ? 's' : '') + ' saved';
    }

    // ─── Color card builder ─────────────────────────────────────────────────────
    function buildColorCard(hex, name, slug) {
        var cleanHex = String(hex || '').replace('#', '').trim();
        var fullHex = '#' + cleanHex;
        var light   = window.ColorMagic.isLightColor(fullHex);
        var textCls = light ? 'text-slate-800' : 'text-white';

        var isKnownName = name && typeof name === 'string' &&
            name.toLowerCase() !== 'unknown color' &&
            name.toLowerCase() !== 'unknown' &&
            name.trim().length > 0;

        var familyShade = (window.ColorMagic && window.ColorMagic.getColorFamilyShade)
            ? window.ColorMagic.getColorFamilyShade(fullHex)
            : (fullHex.toUpperCase() + ' Shade');

        var displayName = isKnownName ? name : familyShade;
        var displaySlug = slug || cleanHex.toLowerCase();
        var colorUrl    = (window.CM_COLOR_BASE || 'color/') + displaySlug + '/';

        var card = document.createElement('div');
        card.className = 'color-fav-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col';

        card.innerHTML =
            '<div class="h-28 w-full relative flex items-end p-2.5" style="background-color:' + fullHex + '">'
            +   '<div class="flex items-center justify-between w-full">'
            +     '<span class="text-xs font-mono font-bold ' + textCls + ' drop-shadow">' + fullHex.toUpperCase() + '</span>'
            +     '<button class="fav-color-remove-btn border-none bg-transparent p-1 rounded-md transition-colors ' + textCls + ' hover:text-red-400" data-hex="' + cleanHex + '" title="Remove from favorites">'
            +       '<i class="bi bi-heart-fill text-sm drop-shadow"></i>'
            +     '</button>'
            +   '</div>'
            + '</div>'
            + '<div class="p-3 flex flex-col gap-1.5">'
            +   '<a href="' + colorUrl + '" class="text-sm font-bold text-slate-800 dark:text-white hover:text-primary transition-colors leading-tight truncate" title="' + displayName + ' (' + fullHex.toUpperCase() + ')">'
            +     displayName
            +   '</a>'
            +   '<div class="flex items-center justify-between gap-2">'
            +     '<span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono font-medium">' + fullHex.toUpperCase() + '</span>'
            +     '<button class="fav-color-copy-btn border-none bg-transparent text-[11px] font-semibold text-primary hover:underline flex items-center gap-1" data-hex="' + fullHex.toUpperCase() + '">'
            +       '<i class="bi bi-clipboard text-xs"></i> Copy'
            +     '</button>'
            +   '</div>'
            + '</div>';

        return card;
    }

    // ─── Gradient card builder ──────────────────────────────────────────────────
    function buildGradientCard(g) {
        if (window.ColorMagic && window.ColorMagic.createGradientCard) {
            return window.ColorMagic.createGradientCard(g);
        }
        var card = document.createElement('div');
        card.className = 'gradient-card bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col';
        return card;
    }

    // ─── Render Colors ──────────────────────────────────────────────────────────
    function renderColors(colorNames) {
        if (!favColorsGrid) return 0;
        favColorsGrid.innerHTML = '';

        var favHexes = window.ColorMagic.ColorFavorites.getFavorites();
        if (favHexes.length === 0) {
            favColorsGrid.appendChild(
                buildEmptyState('bi-eyedropper', 'No Saved Colors Yet',
                    'Start saving colors by clicking the heart icon on any color swatch across the app.',
                    'Find Color Details', 'find-color')
            );
            return 0;
        }

        var fragment = document.createDocumentFragment();
        favHexes.forEach(function (hex) {
            var cleanHex = String(hex || '').replace('#', '').trim();
            var entry = null;
            if (colorNames) {
                entry = colorNames[cleanHex.toUpperCase()] || colorNames[cleanHex.toLowerCase()] || colorNames[cleanHex];
            }
            var name  = entry ? entry.name : null;
            var slug  = entry ? entry.slug : null;
            fragment.appendChild(buildColorCard(cleanHex, name, slug));
        });
        favColorsGrid.appendChild(fragment);
        return favHexes.length;
    }

    // ─── Render Palettes ────────────────────────────────────────────────────────
    function renderPalettes(allPalettes) {
        if (!favPalettesGrid) return 0;
        favPalettesGrid.innerHTML = '';

        var favIds    = window.ColorMagic.Favorites.getFavorites();
        var favIdSet  = {};
        favIds.forEach(function (id) { favIdSet[id] = true; });

        var favPalettes = allPalettes.filter(function (p) { return favIdSet[p.id]; });

        if (favPalettes.length === 0) {
            favPalettesGrid.appendChild(
                buildEmptyState('bi-palette', 'No Saved Palettes Yet',
                    'Start saving palettes by clicking the heart icon on any palette card.',
                    'Browse 800+ Palettes', 'palettes')
            );
            return 0;
        }

        window.ColorMagic.markDuplicateSlugs(favPalettes);

        var fragment = document.createDocumentFragment();
        favPalettes.forEach(function (p) {
            fragment.appendChild(window.ColorMagic.createPaletteCard(p));
        });
        favPalettesGrid.appendChild(fragment);
        return favPalettes.length;
    }

    // ─── Render Gradients ───────────────────────────────────────────────────────
    function renderGradients(allGradients) {
        if (!favGradientsGrid) return 0;
        favGradientsGrid.innerHTML = '';

        var favIds    = window.ColorMagic.GradientFavorites.getFavorites();
        var favIdSet  = {};
        favIds.forEach(function (id) { favIdSet[id] = true; });

        var favGradients = allGradients.filter(function (g) { return favIdSet[g.id]; });

        if (favGradients.length === 0) {
            favGradientsGrid.appendChild(
                buildEmptyState('bi-rainbow', 'No Saved Gradients Yet',
                    'Start saving gradients by clicking the heart icon on any gradient card.',
                    'Explore 300+ Gradients', 'gradients')
            );
            return 0;
        }

        var fragment = document.createDocumentFragment();
        favGradients.forEach(function (g) {
            fragment.appendChild(buildGradientCard(g));
        });
        favGradientsGrid.appendChild(fragment);
        return favGradients.length;
    }

    // ─── Load all data and render ───────────────────────────────────────────────
    var colorPromise = (window.ColorMagic && window.ColorMagic.api)
        ? window.ColorMagic.api.getColors({ format: 'dict' })
        : fetch('/api/color-names.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });
    var palPromise = (window.ColorMagic && window.ColorMagic.api)
        ? window.ColorMagic.api.getPalettes({ limit: 1000 })
        : fetch('/api/palettes.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });
    var gradPromise = (window.ColorMagic && window.ColorMagic.api)
        ? window.ColorMagic.api.getGradients({ limit: 1000 })
        : fetch('/api/gradients.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });

    Promise.all([colorPromise, palPromise, gradPromise]).then(function (results) {
        var colorNames     = (results[0] && results[0].data) ? results[0].data : (results[0] || {});
        var allPalettes    = (results[1] && results[1].data) ? results[1].data : (Array.isArray(results[1]) ? results[1] : []);
        var allGradients   = (results[2] && results[2].data) ? results[2].data : (Array.isArray(results[2]) ? results[2] : []);

        var colorCount    = renderColors(colorNames);
        var paletteCount  = renderPalettes(allPalettes);
        var gradientCount = renderGradients(allGradients);
        updateCounts(colorCount, paletteCount, gradientCount);
    }).catch(function (err) {
        console.error('Failed to load favorites data:', err);
        updateCounts(0, 0, 0);
    });

    // ─── Event delegation: remove from favorites / copy hex / palette / gradient actions ─
    document.addEventListener('click', function (e) {

        // Remove color from favorites
        var colorRemoveBtn = e.target.closest('.fav-color-remove-btn');
        if (colorRemoveBtn) {
            var hex = colorRemoveBtn.dataset.hex;
            window.ColorMagic.ColorFavorites.toggleFavorite(hex);
            // Re-render colors
            var cnPromise = (window.ColorMagic && window.ColorMagic.api)
                ? window.ColorMagic.api.getColors({ format: 'dict' })
                : fetch('/api/color-names.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });
            cnPromise.then(function (res) {
                var data = (res && res.data) ? res.data : res;
                var count = renderColors(data);
                var paletteCount  = window.ColorMagic.Favorites.getFavorites().length;
                var gradientCount = window.ColorMagic.GradientFavorites.getFavorites().length;
                updateCounts(count, paletteCount, gradientCount);
            });
            return;
        }

        // Copy color hex
        var colorCopyBtn = e.target.closest('.fav-color-copy-btn');
        if (colorCopyBtn) {
            var hexVal = colorCopyBtn.dataset.hex;
            navigator.clipboard.writeText(hexVal).catch(function (err) { console.error('Copy failed:', err); });
            return;
        }

        // Remove gradient from favorites
        var gradRemoveBtn = e.target.closest('.gradient-fav-btn') || e.target.closest('.fav-gradient-btn');
        if (gradRemoveBtn && favGradientsGrid && favGradientsGrid.contains(gradRemoveBtn)) {
            var gid = gradRemoveBtn.dataset.gradientId;
            window.ColorMagic.GradientFavorites.toggleFavorite(gid);
            // Re-render gradients
            var gPromise = (window.ColorMagic && window.ColorMagic.api)
                ? window.ColorMagic.api.getGradients({ limit: 1000 })
                : fetch('/api/gradients.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });
            gPromise.then(function (res) {
                var data = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
                var count = renderGradients(Array.isArray(data) ? data : []);
                var colorCount   = window.ColorMagic.ColorFavorites.getFavorites().length;
                var paletteCount = window.ColorMagic.Favorites.getFavorites().length;
                updateCounts(colorCount, paletteCount, count);
            });
            return;
        }

        // Remove palette from favorites (uses .favorite-btn from palette-card component)
        var paletteFavBtn = e.target.closest('.favorite-btn');
        if (paletteFavBtn && favPalettesGrid && favPalettesGrid.contains(paletteFavBtn)) {
            var paletteId = paletteFavBtn.dataset.paletteId;
            window.ColorMagic.Favorites.toggleFavorite(paletteId);
            // Re-render palettes
            var pPromise = (window.ColorMagic && window.ColorMagic.api)
                ? window.ColorMagic.api.getPalettes({ limit: 1000 })
                : fetch('/api/palettes.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });
            pPromise.then(function (res) {
                var data = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
                var count = renderPalettes(Array.isArray(data) ? data : []);
                var colorCount    = window.ColorMagic.ColorFavorites.getFavorites().length;
                var gradientCount = window.ColorMagic.GradientFavorites.getFavorites().length;
                updateCounts(colorCount, count, gradientCount);
            });
            return;
        }

        // Copy palette
        var copyPaletteBtn = e.target.closest('.copy-palette-btn');
        if (copyPaletteBtn && favPalettesGrid && favPalettesGrid.contains(copyPaletteBtn)) {
            var colors = copyPaletteBtn.dataset.colors;
            var icon   = copyPaletteBtn.querySelector('i');
            var origClass = icon ? icon.className : '';
            navigator.clipboard.writeText(colors).then(function () {
                if (icon) icon.className = 'bi bi-check-circle-fill text-xl';
                copyPaletteBtn.classList.add('text-green-500');
                setTimeout(function () {
                    if (icon) icon.className = origClass;
                    copyPaletteBtn.classList.remove('text-green-500');
                }, 2000);
            }).catch(function (err) { console.error('Copy failed:', err); });
            return;
        }

        // Copy single palette swatch hex
        var swatchCopyBtn = e.target.closest('.swatch-copy-hex');
        if (swatchCopyBtn && favPalettesGrid && favPalettesGrid.contains(swatchCopyBtn)) {
            var swHex  = swatchCopyBtn.dataset.hex;
            var swIcon  = swatchCopyBtn.querySelector('i');
            var swOrig  = swatchCopyBtn.innerHTML;
            navigator.clipboard.writeText(swHex).then(function () {
                if (swIcon) swIcon.className = 'bi bi-check-circle-fill text-[11px]';
                swatchCopyBtn.classList.add('text-green-600');
                setTimeout(function () {
                    swatchCopyBtn.innerHTML = swOrig;
                    swatchCopyBtn.classList.remove('text-green-600');
                }, 1500);
            }).catch(function (err) { console.error('Copy failed:', err); });
            return;
        }

        // Copy gradient CSS
        var cssBtn = e.target.closest('.copy-gradient-css-btn');
        if (cssBtn && favGradientsGrid && favGradientsGrid.contains(cssBtn)) {
            var css     = cssBtn.dataset.css;
            var cssIcon = cssBtn.querySelector('i');
            var origIcon  = cssIcon ? cssIcon.className : '';
            navigator.clipboard.writeText(css).then(function () {
                if (cssIcon) cssIcon.className = 'bi bi-check-circle-fill text-lg text-green-500';
                setTimeout(function () {
                    if (cssIcon) cssIcon.className = origIcon;
                }, 2000);
            }).catch(function (err) { console.error('Copy failed:', err); });
            return;
        }
    });

})();
