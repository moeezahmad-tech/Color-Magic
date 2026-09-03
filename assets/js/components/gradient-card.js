/**
 * components/gradient-card.js
 * Reusable gradient card component matching the Explore Gradients design.
 * Attaches to window.ColorMagic.createGradientCard.
 * Must be loaded after utils.js and services/favorites.js.
 */

window.ColorMagic = window.ColorMagic || {};

/**
 * Creates a standard gradient card DOM element.
 * @param {Object} g - { id, name, type, style, css, colors, angle, shape }
 * @returns {HTMLElement}
 */
window.ColorMagic.createGradientCard = function (g) {
    var card = document.createElement('div');
    card.className =
        'gradient-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow-md transition-all duration-200';

    var gradientBase = (window.CM_GRADIENT_BASE || (document.querySelector('base') ? '' : '/') + 'gradient/');
    if (gradientBase.charAt(gradientBase.length - 1) !== '/') gradientBase += '/';

    // Preview area — links to detail page
    var previewLink = document.createElement('a');
    previewLink.href = gradientBase + g.id + '/';
    previewLink.className = 'gradient-preview h-44 w-full rounded-t-2xl relative block group';
    previewLink.style.background = g.css;
    previewLink.setAttribute('aria-label', 'View ' + g.name + ' gradient details');

    // Floating Copy CSS button (bottom-right inside preview)
    var copyBtn = document.createElement('button');
    copyBtn.type = 'button';
    copyBtn.className =
        'copy-css-btn border-none absolute bottom-3 right-3 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-900 shadow-sm backdrop-blur-sm';
    copyBtn.dataset.css = g.css;
    copyBtn.innerHTML =
        '<i class="bi bi-clipboard text-sm"></i>'
        + '<span>Copy CSS</span>';
    previewLink.appendChild(copyBtn);

    card.appendChild(previewLink);

    // Info body
    var body = document.createElement('div');
    body.className = 'p-4 flex flex-col gap-2.5 flex-1';

    // Header: Name + Badge + Links
    var header = document.createElement('div');
    header.className = 'flex items-start justify-between gap-2';

    var name = document.createElement('h3');
    name.className = 'text-base font-bold text-slate-800 dark:text-white leading-tight';
    name.textContent = g.name;

    var headerRight = document.createElement('div');
    headerRight.className = 'flex items-center gap-1.5 flex-shrink-0';

    // Type badge (→ LINEAR, ⭕ RADIAL, etc.)
    var typeBadge = document.createElement('span');
    var isLinear = (g.type || '').toLowerCase() === 'linear';
    var isRadial = (g.type || '').toLowerCase() === 'radial';
    typeBadge.className =
        'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border '
        + (isLinear
            ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-300 border-violet-200 dark:border-violet-700'
            : isRadial
            ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300 border-amber-200 dark:border-amber-700'
            : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700');
    var typeIcon = isLinear ? 'arrow-right' : (isRadial ? 'circle' : 'grid-3x3-gap');
    typeBadge.innerHTML =
        '<i class="bi bi-' + typeIcon + ' text-[9px]"></i>'
        + (g.type || 'linear');

    // External link to detail page
    var viewBtn = document.createElement('a');
    viewBtn.href = gradientBase + g.id + '/';
    viewBtn.className = 'p-1 rounded-md text-slate-400 hover:text-secondary transition-colors';
    viewBtn.title = 'View ' + g.name + ' details';
    viewBtn.setAttribute('aria-label', 'Open ' + g.name + ' gradient details');
    viewBtn.innerHTML = '<i class="bi bi-box-arrow-up-right text-base"></i>';

    // Favorite heart button
    var isFav = window.ColorMagic && window.ColorMagic.GradientFavorites && window.ColorMagic.GradientFavorites.isFavorite(g.id);
    var favBtn = document.createElement('button');
    favBtn.type = 'button';
    favBtn.className = 'gradient-fav-btn border-none bg-transparent p-1 rounded-md transition-colors ' + (isFav ? 'text-red-500' : 'text-slate-400 hover:text-red-500');
    favBtn.dataset.gradientId = g.id;
    favBtn.title = isFav ? 'Remove from favorites' : 'Add to favorites';
    favBtn.innerHTML = '<i class="bi ' + (isFav ? 'bi-heart-fill' : 'bi-heart') + ' text-base"></i>';

    headerRight.appendChild(typeBadge);
    headerRight.appendChild(viewBtn);
    headerRight.appendChild(favBtn);
    header.appendChild(name);
    header.appendChild(headerRight);
    body.appendChild(header);

    // Meta line: Style · N colors · angleOrShape
    var meta = document.createElement('p');
    meta.className = 'text-xs text-slate-400 dark:text-slate-500';
    var angleOrShape = isLinear ? ((g.angle !== undefined ? g.angle : 135) + '°') : (g.type === 'mesh' ? 'mesh' : (g.shape || 'radial'));
    var count = Array.isArray(g.colors) ? g.colors.length : 2;
    meta.textContent = (g.style || 'Modern') + ' · ' + count + ' colors · ' + angleOrShape;
    body.appendChild(meta);

    // Color swatches row
    var swatches = document.createElement('div');
    swatches.className = 'flex gap-1.5 h-5 rounded-lg overflow-hidden border border-slate-100 dark:border-slate-800 mt-auto';
    if (Array.isArray(g.colors)) {
        g.colors.forEach(function (hex) {
            var sw = document.createElement('div');
            sw.className = 'flex-1 cursor-pointer relative group/sw';
            sw.style.backgroundColor = hex;
            sw.title = hex;
            sw.innerHTML =
                '<span class="swatch-hex absolute inset-0 flex items-center justify-center text-[9px] font-mono font-bold text-white drop-shadow bg-black/30 opacity-0 group-hover/sw:opacity-100 transition-opacity rounded">'
                + hex + '</span>';
            swatches.appendChild(sw);
        });
    }
    body.appendChild(swatches);

    card.appendChild(body);
    return card;
};

// Global delegated handlers for standard gradient card interactions
document.addEventListener('click', function (e) {
    // Copy gradient CSS button
    var copyBtn = e.target.closest('.copy-css-btn');
    if (copyBtn) {
        e.preventDefault();
        e.stopPropagation();
        var css = copyBtn.dataset.css || '';
        if (window.ColorMagic && window.ColorMagic.animateCopy) {
            window.ColorMagic.animateCopy(copyBtn, css, 'Copied!');
        } else {
            var icon = copyBtn.querySelector('i');
            var label = copyBtn.querySelector('span');
            var origIcon = icon ? icon.className : '';
            var origLabel = label ? label.textContent : '';
            navigator.clipboard.writeText(css).then(function () {
                if (icon) icon.className = 'bi bi-check-circle-fill text-sm';
                if (label) label.textContent = 'Copied!';
                setTimeout(function () {
                    if (icon) icon.className = origIcon;
                    if (label) label.textContent = origLabel;
                }, 1600);
            });
        }
        return;
    }

    // Favorite button on card
    var favBtn = e.target.closest('.gradient-fav-btn');
    if (favBtn && window.ColorMagic && window.ColorMagic.GradientFavorites) {
        e.preventDefault();
        e.stopPropagation();
        var gid = favBtn.dataset.gradientId;
        window.ColorMagic.GradientFavorites.toggleFavorite(gid);
        var isFav = window.ColorMagic.GradientFavorites.isFavorite(gid);
        var fIcon = favBtn.querySelector('i');
        if (fIcon) {
            fIcon.className = 'bi ' + (isFav ? 'bi-heart-fill' : 'bi-heart') + ' text-base';
        }
        favBtn.className = 'gradient-fav-btn border-none bg-transparent p-1 rounded-md transition-colors ' + (isFav ? 'text-red-500' : 'text-slate-400 hover:text-red-500');
        favBtn.title = isFav ? 'Remove from favorites' : 'Add to favorites';
        return;
    }

    // Swatch hex copy
    var swatch = e.target.closest('.group\\/sw');
    if (swatch && swatch.title) {
        e.preventDefault();
        e.stopPropagation();
        navigator.clipboard.writeText(swatch.title).catch(function () {});
        var hexSpan = swatch.querySelector('.swatch-hex');
        if (hexSpan) {
            var orig = hexSpan.textContent;
            hexSpan.textContent = '✓';
            setTimeout(function () { hexSpan.textContent = orig; }, 1000);
        }
    }
});
