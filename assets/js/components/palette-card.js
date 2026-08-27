/**
 * components/palette-card.js
 * Reusable palette card component.
 * Plain script — attaches to window.ColorMagic.createPaletteCard.
 * Must be loaded after utils.js and services/favorites.js.
 */

window.ColorMagic = window.ColorMagic || {};

/**
 * Creates a palette card DOM element.
 * @param {Object} palette - { id, name, style, colors, _isDuplicate? }
 * @returns {HTMLElement}
 */
window.ColorMagic.createPaletteCard = function (palette) {
    const { isLightColor, getPaletteSlug } = window.ColorMagic;
    const { isFavorite } = window.ColorMagic.Favorites;

    // Base paths — overridden per-page when relative URLs would be wrong
    const colorBase   = (window.CM_COLOR_BASE   || 'color/');
    const paletteBase = (window.CM_PALETTE_BASE || 'palette/');

    const card = document.createElement('article');
    card.className = 'group flex flex-col gap-4';
    card.dataset.tags = palette.style.toLowerCase() + ' ' + palette.name.toLowerCase();
    card.dataset.paletteId = palette.id;

    const swatchesHTML = palette.colors.map(function (color) {
        const light    = isLightColor(color);
        const textClass = light ? 'text-slate-800' : 'text-white';
        const bgClass   = light ? 'bg-white/30'    : 'bg-black/30';
        const btnBase   = light
            ? 'bg-white/70 hover:bg-white text-slate-800 backdrop-blur-sm'
            : 'bg-black/40 hover:bg-black/70 text-white backdrop-blur-sm';
        const hexBare   = color.replace('#', '');
        const isFav     = window.ColorMagic.ColorFavorites && window.ColorMagic.ColorFavorites.isFavorite(color);
        const heartIcon = isFav ? 'bi-heart-fill text-red-500' : 'bi-heart';

        return '<div class="swatch group/swatch min-w-0 relative flex flex-col justify-end p-2 hover:scale-[1.02] transition-transform active:scale-95" style="background-color:' + color + '" data-hex="' + color + '">'
            // Icon buttons — top-left corner, staggered slide-down on hover
            + '<div class="swatch-actions absolute top-2 left-2 flex flex-col gap-1 pointer-events-none">'
            +   '<button class="swatch-copy-hex swatch-icon-btn swatch-btn-1 ' + btnBase + ' w-6 h-6 rounded-md flex items-center justify-center shadow-sm pointer-events-auto" data-hex="' + color + '" title="Copy ' + color + '" type="button">'
            +     '<i class="bi bi-clipboard" style="font-size:11px;line-height:1"></i>'
            +   '</button>'
            +   '<a class="swatch-open-color swatch-icon-btn swatch-btn-2 ' + btnBase + ' w-6 h-6 rounded-md flex items-center justify-center shadow-sm pointer-events-auto" href="' + colorBase + hexBare + '/" target="_blank" rel="noopener" title="Open color page">'
            +     '<i class="bi bi-box-arrow-up-right" style="font-size:11px;line-height:1"></i>'
            +   '</a>'
            +   '<button class="swatch-fav-color swatch-icon-btn swatch-btn-3 ' + btnBase + ' w-6 h-6 rounded-md flex items-center justify-center shadow-sm pointer-events-auto" data-hex="' + color + '" title="' + (isFav ? 'Remove from favorites' : 'Add to favorites') + '" type="button">'
            +     '<i class="bi ' + heartIcon + '" style="font-size:11px;line-height:1"></i>'
            +   '</button>'
            + '</div>'
            // HEX label at the bottom — hidden on narrow swatches via CSS, always shown on hover
            + '<span class="swatch-hex text-[10px] font-bold ' + textClass + ' ' + bgClass + ' backdrop-blur-sm px-1.5 py-0.5 rounded text-center relative z-10">' + color + '</span>'
            + '</div>';
    }).join('');

    const isFav      = isFavorite(palette.id);
    const heartIcon  = isFav ? 'bi-heart-fill' : 'bi-heart';
    const heartColor = isFav ? 'text-red-500'  : 'text-slate-400';
    const slug       = getPaletteSlug(palette);

    card.innerHTML =
        '<div class="palette-card grid h-56 w-full rounded-2xl overflow-hidden shadow-xl shadow-black/5 dark:shadow-black/20 ring-1 ring-slate-200 dark:ring-slate-800" style="grid-template-columns:repeat(' + palette.colors.length + ', minmax(0, 1fr));">'
        + swatchesHTML
        + '</div>'
        + '<div class="flex items-center justify-between px-1">'
        +   '<div>'
        +     '<h3 class="font-bold text-lg">' + palette.name + '</h3>'
        +     '<p class="text-xs text-slate-500">By Color Studio &bull; ' + palette.style + '</p>'
        +   '</div>'
        +   '<div class="flex items-center gap-4">'
        +     '<a class="open-palette-btn p-1.5 text-slate-400 hover:text-secondary transition-colors"'
        +        ' href="' + paletteBase + slug + '/"'
        +        ' target="_blank" rel="noopener"'
        +        ' title="Open palette: ' + palette.name + '"'
        +        ' aria-label="Open ' + palette.name + ' palette in new tab">'
        +       '<i class="bi bi-box-arrow-up-right text-lg" aria-hidden="true"></i>'
        +     '</a>'
        +     '<button class="favorite-btn ' + heartColor + ' hover:text-red-500 transition-colors"'
        +             ' data-palette-id="' + palette.id + '"'
        +             ' title="' + (isFav ? 'Remove from' : 'Add to') + ' favorites"'
        +             ' aria-label="' + (isFav ? 'Remove' : 'Add') + ' ' + palette.name + ' ' + (isFav ? 'from' : 'to') + ' favorites"'
        +             ' aria-pressed="' + isFav + '">'
        +       '<i class="bi ' + heartIcon + ' text-lg" aria-hidden="true"></i>'
        +     '</button>'
        +     '<button class="copy-palette-btn p-1.5 text-slate-400 hover:text-primary transition-colors"'
        +             ' data-colors="' + palette.colors.join(',') + '"'
        +             ' title="Copy all colors"'
        +             ' aria-label="Copy all colors for ' + palette.name + '">'
        +       '<i class="bi bi-clipboard text-xl" aria-hidden="true"></i>'
        +     '</button>'
        +   '</div>'
        + '</div>';

    return card;
};
