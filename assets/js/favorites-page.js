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

    var activeTab = 'colors';

    // ─── Tab switching ──────────────────────────────────────────────────────────
    if (favTabs) {
        favTabs.addEventListener('click', function (e) {
            var btn = e.target.closest('.fav-tab');
            if (!btn) return;
            activeTab = btn.dataset.tab;
            document.querySelectorAll('.fav-tab').forEach(function (b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            [panelColors, panelPalettes, panelGradients].forEach(function (p) {
                if (p) p.classList.add('hidden');
            });
            var target = activeTab === 'colors' ? panelColors
                       : activeTab === 'palettes' ? panelPalettes
                       : panelGradients;
            if (target) target.classList.remove('hidden');
        });
    }

    // ─── Empty state builder ────────────────────────────────────────────────────
    function buildEmptyState(icon, title, message) {
        var div = document.createElement('div');
        div.className = 'col-span-full flex flex-col items-center justify-center py-16 text-center';
        div.innerHTML =
            '<i class="bi ' + icon + ' text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>'
            + '<p class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">' + title + '</p>'
            + '<p class="text-sm text-slate-500 max-w-md">' + message + '</p>';
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
        var fullHex = '#' + hex;
        var light   = window.ColorMagic.isLightColor(fullHex);
        var textCls = light ? 'text-slate-800' : 'text-white';

        var card = document.createElement('div');
        card.className = 'color-fav-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col';

        card.innerHTML =
            '<div class="h-28 w-full relative flex items-end p-2.5" style="background-color:' + fullHex + '">'
            +   '<div class="flex items-center justify-between w-full">'
            +     '<span class="text-xs font-mono font-bold ' + textCls + ' drop-shadow">' + fullHex.toUpperCase() + '</span>'
            +     '<button class="fav-color-remove-btn border-none bg-transparent p-1 rounded-md transition-colors ' + textCls + ' hover:text-red-400" data-hex="' + hex + '" title="Remove from favorites">'
            +       '<i class="bi bi-heart-fill text-sm drop-shadow"></i>'
            +     '</button>'
            +   '</div>'
            + '</div>'
            + '<div class="p-3 flex flex-col gap-1.5">'
            +   '<a href="color/' + (slug || hex) + '/" class="text-sm font-bold text-slate-800 dark:text-white hover:text-primary transition-colors leading-tight truncate" title="' + (name || hex) + '">'
            +     (name || 'Unknown color')
            +   '</a>'
            +   '<div class="flex items-center justify-between gap-2">'
            +     '<span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">' + fullHex.toUpperCase() + '</span>'
            +     '<button class="fav-color-copy-btn border-none bg-transparent text-[11px] font-semibold text-primary hover:underline flex items-center gap-1" data-hex="' + fullHex + '">'
            +       '<i class="bi bi-clipboard text-xs"></i> Copy'
            +     '</button>'
            +   '</div>'
            + '</div>';

        return card;
    }

    // ─── Gradient card builder ──────────────────────────────────────────────────
    function buildGradientCard(g) {
        var gradientBase = (document.querySelector('base') ? '' : '/') + 'gradient/';
        var typeBadgeCls = g.type === 'linear'
            ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-300 border-violet-200 dark:border-violet-700'
            : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300 border-amber-200 dark:border-amber-700';
        var typeIcon = g.type === 'linear' ? 'arrow-right' : 'circle';
        var angleOrShape = g.type === 'linear' ? (g.angle + '°') : g.shape;
        var swatchesHtml = g.colors.map(function (hex) {
            return '<div class="flex-1 cursor-pointer relative group/sw" style="background-color:' + hex + '" title="' + hex + '">'
                + '<span class="swatch-hex absolute inset-0 flex items-center justify-center text-[9px] font-mono font-bold text-white drop-shadow bg-black/30 opacity-0 group-hover/sw:opacity-100 transition-opacity rounded">' + hex + '</span>'
                + '</div>';
        }).join('');

        var card = document.createElement('div');
        card.className = 'gradient-card bg-white dark:bg-slate-900 border border-pink-100 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col';

        card.innerHTML =
            '<div class="h-44 w-full" style="background:' + g.css + '"></div>'
            + '<div class="p-4 flex flex-col gap-2.5 flex-1">'
            +   '<div class="flex items-start justify-between gap-2">'
            +     '<h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">' + g.name + '</h3>'
            +     '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border ' + typeBadgeCls + '"><i class="bi bi-' + typeIcon + ' text-[9px]"></i>' + g.type + '</span>'
            +   '</div>'
            +   '<p class="text-xs text-slate-400 dark:text-slate-500">' + g.style + ' · ' + g.colors.length + ' colors · ' + angleOrShape + '</p>'
            +   '<div class="flex gap-1.5 h-5 rounded-lg overflow-hidden border border-slate-100 dark:border-slate-800 mt-auto">' + swatchesHtml + '</div>'
            +   '<div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">'
            +     '<button class="fav-gradient-btn p-1.5 text-red-500 hover:text-slate-400 transition-colors" data-gradient-id="' + g.id + '" title="Remove from favorites">'
            +       '<i class="bi bi-heart-fill text-base"></i>'
            +     '</button>'
            +     '<button class="copy-gradient-css-btn p-1.5 text-slate-400 hover:text-primary transition-colors" data-css="' + g.css.replace(/"/g, '&quot;') + '" title="Copy CSS">'
            +       '<i class="bi bi-clipboard text-lg"></i>'
            +     '</button>'
            +     '<a href="' + gradientBase + g.id + '/" class="p-1.5 text-slate-400 hover:text-secondary transition-colors" title="Open gradient" target="_blank" rel="noopener">'
            +       '<i class="bi bi-box-arrow-up-right text-base"></i>'
            +     '</a>'
            +   '</div>'
            + '</div>';

        return card;
    }

    // ─── Render Colors ──────────────────────────────────────────────────────────
    function renderColors(colorNames) {
        if (!favColorsGrid) return 0;
        favColorsGrid.innerHTML = '';

        var favHexes = window.ColorMagic.ColorFavorites.getFavorites();
        if (favHexes.length === 0) {
            favColorsGrid.appendChild(
                buildEmptyState('bi-eyedropper', 'No Saved Colors',
                    'Start saving colors by clicking the heart icon on any color swatch across the app.')
            );
            return 0;
        }

        var fragment = document.createDocumentFragment();
        favHexes.forEach(function (hex) {
            var entry = colorNames[hex];
            var name  = entry ? entry.name : null;
            var slug  = entry ? entry.slug : null;
            fragment.appendChild(buildColorCard(hex, name, slug));
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
                buildEmptyState('bi-palette', 'No Saved Palettes',
                    'Start saving palettes by clicking the heart icon on any palette card.')
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
                buildEmptyState('bi-rainbow', 'No Saved Gradients',
                    'Start saving gradients by clicking the heart icon on any gradient card.')
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
        var gradRemoveBtn = e.target.closest('.fav-gradient-btn');
        if (gradRemoveBtn) {
            var gid = gradRemoveBtn.dataset.gradientId;
            window.ColorMagic.GradientFavorites.toggleFavorite(gid);
            var icon = gradRemoveBtn.querySelector('i');
            var nowFav = window.ColorMagic.GradientFavorites.isFavorite(gid);
            if (icon) icon.className = 'bi ' + (nowFav ? 'bi-heart-fill text-red-500' : 'bi-heart') + ' text-base';
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
