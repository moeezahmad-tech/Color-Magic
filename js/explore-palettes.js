let allPalettes = [];
let filteredPalettes = [];
let isLoading = false;
let loadError = null;
const PAGE_SIZE = 60;
let renderedCount = 0;
let currentQuery = '';
const paletteGrid = document.getElementById('paletteGrid');
const loadMoreBtn = document.getElementById('loadMoreBtn');
const paletteCountStatus = document.getElementById('paletteCountStatus');

// Event delegation for copy palette button
paletteGrid?.addEventListener('click', function (e) {
    // Handle copy palette button
    const copyBtn = e.target.closest('.copy-palette-btn');
    if (copyBtn) {
        const colors = copyBtn.dataset.colors;
        navigator.clipboard.writeText(colors).then(() => {
            const icon = copyBtn.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'bi bi-check-circle-fill text-xl';
            copyBtn.classList.add('text-green-500');

            setTimeout(() => {
                icon.className = originalClass;
                copyBtn.classList.remove('text-green-500');
            }, 2000);
        }).catch(err => {
            console.error('Copy failed:', err);
        });
        return;
    }

    // Handle favorite button
    const favoriteBtn = e.target.closest('.favorite-btn');
    if (favoriteBtn) {
        const paletteId = favoriteBtn.dataset.paletteId;
        toggleFavorite(paletteId);
        updateFavoriteButton(favoriteBtn, paletteId);
        return;
    }

    // Handle individual color swatch copy
    const swatch = e.target.closest('.swatch');
    if (swatch && !e.target.closest('.copy-palette-btn')) {
        const hexSpan = swatch.querySelector('.swatch-hex');
        const colorCode = hexSpan.textContent;

        navigator.clipboard.writeText(colorCode).then(() => {
            const originalText = hexSpan.textContent;
            hexSpan.textContent = 'Copied!';
            hexSpan.classList.add('copied-state');

            setTimeout(() => {
                hexSpan.textContent = originalText;
                hexSpan.classList.remove('copied-state');
            }, 1500);
        }).catch(err => {
            console.error('Copy failed:', err);
        });
    }
});

// Local Storage Functions for Favorites
function getFavorites() {
    const favorites = localStorage.getItem('colorMagicFavorites');
    return favorites ? JSON.parse(favorites) : [];
}

function saveFavorites(favorites) {
    localStorage.setItem('colorMagicFavorites', JSON.stringify(favorites));
}

function toggleFavorite(paletteId) {
    const favorites = getFavorites();
    const index = favorites.indexOf(paletteId);

    if (index > -1) {
        favorites.splice(index, 1); // Remove from favorites
    } else {
        favorites.push(paletteId); // Add to favorites
    }

    saveFavorites(favorites);
}

function isFavorite(paletteId) {
    const favorites = getFavorites();
    return favorites.includes(paletteId);
}

function updateFavoriteButton(button, paletteId) {
    const icon = button.querySelector('i');

    if (isFavorite(paletteId)) {
        button.classList.remove('text-slate-400');
        button.classList.add('text-red-500');
        icon.className = 'bi bi-heart-fill text-lg';
    } else {
        button.classList.remove('text-red-500');
        button.classList.add('text-slate-400');
        icon.className = 'bi bi-heart text-lg';
    }
}

const searchInput = document.getElementById('searchInput');
searchInput?.addEventListener('input', function (e) {
    currentQuery = e.target.value.toLowerCase().trim();
    applyFiltersAndRender();
});

let currentFilter = 'all';
document.querySelectorAll('.theme-filter').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.theme-filter').forEach(b => {
            b.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'font-bold');
            b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'font-medium');
        });

        this.classList.add('bg-primary', 'hover:bg-primary/90', 'text-white', 'shadow-lg', 'shadow-primary/20', 'font-bold');
        this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'font-medium');

        const theme = this.dataset.theme;
        currentFilter = theme;
        applyFiltersAndRender();
    });
});

function isLightColor(hex) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness > 155;
}



function createPaletteCard(palette) {
    const card = document.createElement('article');
    card.className = 'group flex flex-col gap-4';
    card.dataset.tags = `${palette.style.toLowerCase()} ${palette.name.toLowerCase()}`;
    card.dataset.paletteId = palette.id;

    const swatchesHTML = palette.colors.map(color => {
        const isLight = isLightColor(color);
        const textClass = isLight ? 'text-slate-800' : 'text-white';
        const bgClass = isLight ? 'bg-white/30' : 'bg-black/30';

        return `
            <div class="swatch min-w-0 flex flex-col justify-end p-2 cursor-pointer hover:scale-[1.02] transition-transform active:scale-95" style="background-color:${color}">
                <span class="swatch-hex text-[10px] font-bold ${textClass} ${bgClass} backdrop-blur-sm px-1.5 py-0.5 rounded text-center transition-all">${color}</span>
            </div>
        `;
    }).join('');

    // Check if this palette is favorited
    const isFav = isFavorite(palette.id);
    const heartIcon = isFav ? 'bi-heart-fill' : 'bi-heart';
    const heartColor = isFav ? 'text-red-500' : 'text-slate-400';

    card.innerHTML = `
        <div class="palette-card grid h-56 w-full rounded-2xl overflow-hidden shadow-xl shadow-black/5 dark:shadow-black/20 ring-1 ring-slate-200 dark:ring-slate-800" style="grid-template-columns:repeat(${palette.colors.length}, minmax(0, 1fr));">
            ${swatchesHTML}
        </div>
        <div class="flex items-center justify-between px-1">
            <div>
                <h3 class="font-bold text-lg">${palette.name}</h3>
                <p class="text-xs text-slate-500">By Color Studio • ${palette.style}</p>
            </div>
            <div class="flex items-center gap-4">
                <a class="open-palette-btn p-1.5 text-slate-400 hover:text-secondary transition-colors"
                   href="palette?id=${encodeURIComponent(palette.id)}"
                   target="_blank"
                   rel="noopener"
                   title="Open in new tab"
                   aria-label="Open palette in new tab">
                    <i class="bi bi-box-arrow-up-right text-lg"></i>
                </a>
                <button class="favorite-btn ${heartColor} hover:text-red-500 transition-colors" data-palette-id="${palette.id}" title="Add to favorites">
                    <i class="${heartIcon} text-lg"></i>
                </button>
                <button class="copy-palette-btn p-1.5 text-slate-400 hover:text-primary transition-colors" data-colors="${palette.colors.join(',')}" title="Copy all colors">
                    <i class="bi bi-clipboard text-xl"></i>
                </button>
            </div>
        </div>
    `;

    return card;
}



function showLoadingState() {
    if (loadMoreBtn) {
        loadMoreBtn.classList.add('hidden');
    }
    if (paletteCountStatus) {
        paletteCountStatus.textContent = '';
    }
    paletteGrid.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-20">
            <i class="bi bi-hourglass-split text-6xl text-primary animate-pulse mb-4"></i>
            <p class="text-xl font-bold text-slate-700 dark:text-slate-300">Loading palettes...</p>
            <p class="text-sm text-slate-500">Please wait while we fetch the color data</p>
        </div>
    `;
}

function showErrorState(error) {
    if (loadMoreBtn) {
        loadMoreBtn.classList.add('hidden');
    }
    if (paletteCountStatus) {
        paletteCountStatus.textContent = '';
    }
    paletteGrid.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
            <i class="bi bi-exclamation-triangle text-6xl text-red-500 mb-4"></i>
            <p class="text-xl font-bold text-slate-700 dark:text-slate-300 mb-2">Failed to Load Palettes</p>
            <p class="text-sm text-slate-500 mb-6 max-w-md">${error}</p>
            <button onclick="fetchPalettes()" class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all flex items-center gap-2">
                <i class="bi bi-arrow-clockwise"></i>
                Retry
            </button>
        </div>
    `;
}

function getFilteredPalettes() {
    let scoped = [...allPalettes];

    if (currentFilter === 'favorites') {
        const favorites = getFavorites();
        scoped = scoped.filter(palette => favorites.includes(palette.id));
    } else if (currentFilter !== 'all') {
        scoped = scoped.filter(palette =>
            palette.style.toLowerCase() === currentFilter.toLowerCase()
        );
    }

    if (currentQuery !== '') {
        scoped = scoped.filter(palette => {
            const title = palette.name.toLowerCase();
            const style = palette.style.toLowerCase();
            const colors = palette.colors.join(' ').toLowerCase();
            return title.includes(currentQuery) || style.includes(currentQuery) || colors.includes(currentQuery);
        });
    }

    return scoped;
}

function updatePaginationState() {
    if (paletteCountStatus) {
        const total = filteredPalettes.length;
        paletteCountStatus.textContent = total === 0
            ? 'No palettes match your current filters.'
            : `Showing ${renderedCount} of ${total} palettes`;
    }

    if (loadMoreBtn) {
        const hasMore = renderedCount < filteredPalettes.length;
        loadMoreBtn.classList.toggle('hidden', !hasMore || filteredPalettes.length === 0);
    }
}

function renderNextBatch(reset = false) {
    if (reset) {
        paletteGrid.innerHTML = '';
        renderedCount = 0;
    }

    if (filteredPalettes.length === 0) {
        const emptyTitle = currentFilter === 'favorites'
            ? 'No Favorites Yet'
            : 'No Palettes Found';
        const emptyMessage = currentFilter === 'favorites'
            ? 'Start adding palettes to your favorites by clicking the heart icon on any palette card.'
            : 'Try changing your filters or search query to discover more color palettes.';

        paletteGrid.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                <i class="bi bi-heart text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>
                <p class="text-xl font-bold text-slate-700 dark:text-slate-300">${emptyTitle}</p>
                <p class="text-sm text-slate-500 max-w-md mt-2">${emptyMessage}</p>
            </div>
        `;
        updatePaginationState();
        return;
    }

    const nextChunk = filteredPalettes.slice(renderedCount, renderedCount + PAGE_SIZE);
    const fragment = document.createDocumentFragment();

    nextChunk.forEach(palette => {
        const card = createPaletteCard(palette);
        fragment.appendChild(card);
    });

    paletteGrid.appendChild(fragment);
    renderedCount += nextChunk.length;
    updatePaginationState();
}

function applyFiltersAndRender() {
    filteredPalettes = getFilteredPalettes();
    renderNextBatch(true);
}


loadMoreBtn?.addEventListener('click', function () {
    renderNextBatch(false);
});

async function fetchPalettes() {
    if (isLoading) return;

    isLoading = true;
    loadError = null;
    showLoadingState();

    try {
        const response = await fetch('data/colors.json');

        if (!response.ok) {
            throw new Error(`Failed to fetch palettes (Status: ${response.status})`);
        }

        const data = await response.json();

        if (!Array.isArray(data) || data.length === 0) {
            throw new Error('Invalid palette data format');
        }

        allPalettes = data.sort(() => Math.random() - 0.5);
        applyFiltersAndRender();

        isLoading = false;

    } catch (error) {
        isLoading = false;
        loadError = error.message;
        showErrorState(error.message);
    }
}

fetchPalettes().then(() => {
    // Auto-activate a filter set in the URL: e.g. /?filter=favorites
    try {
        const params = new URLSearchParams(window.location.search);
        const filterParam = params.get('filter');
        if (filterParam) {
            const btn = document.querySelector(`.theme-filter[data-theme="${filterParam}"]`);
            if (btn) btn.click();
        }
    } catch (_) {}
});
