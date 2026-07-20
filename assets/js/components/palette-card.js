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

    const card = document.createElement('article');
    card.className = 'group flex flex-col gap-4';
    card.dataset.tags = palette.style.toLowerCase() + ' ' + palette.name.toLowerCase();
    card.dataset.paletteId = palette.id;

    const swatchesHTML = palette.colors.map(function (color) {
        const light = isLightColor(color);
        const textClass = light ? 'text-slate-800' : 'text-white';
        const bgClass   = light ? 'bg-white/30'    : 'bg-black/30';
        return '<div class="swatch min-w-0 flex flex-col justify-end p-2 cursor-pointer hover:scale-[1.02] transition-transform active:scale-95" style="background-color:' + color + '">'
            + '<span class="swatch-hex text-[10px] font-bold ' + textClass + ' ' + bgClass + ' backdrop-blur-sm px-1.5 py-0.5 rounded text-center transition-all">' + color + '</span>'
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
        +        ' href="palette/' + slug + '/"'
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
