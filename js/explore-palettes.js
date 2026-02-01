let allPalettes = [];
let isLoading = false;
let loadError = null;
const paletteGrid = document.getElementById('paletteGrid');

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
    const query = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('#paletteGrid > div');

    cards.forEach(card => {
        const tags = card.dataset.tags || '';
        const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const colors = card.querySelectorAll('.swatch-hex');
        let colorMatch = false;

        colors.forEach(color => {
            if (color.textContent.toLowerCase().includes(query)) {
                colorMatch = true;
            }
        });

        if (tags.includes(query) || title.includes(query) || colorMatch || query === '') {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
});

let currentFilter = 'all';
document.querySelectorAll('.theme-filter').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.theme-filter').forEach(b => {
            b.classList.remove('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'font-bold');
            b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'font-medium');
        });

        this.classList.add('bg-primary', 'text-white', 'shadow-lg', 'shadow-primary/20', 'font-bold');
        this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'font-medium');

        const theme = this.dataset.theme;
        currentFilter = theme;

        paletteGrid.innerHTML = '';

        let filteredPalettes;
        if (theme === 'all') {
            filteredPalettes = [...allPalettes];
        } else if (theme === 'favorites') {
            const favorites = getFavorites();
            filteredPalettes = allPalettes.filter(palette => favorites.includes(palette.id));

            // Show message if no favorites
            if (filteredPalettes.length === 0) {
                paletteGrid.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                        <i class="bi bi-heart text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>
                        <p class="text-xl font-bold text-slate-700 dark:text-slate-300">No Favorites Yet</p>
                        <p class="text-sm text-slate-500 max-w-md mt-2">Start adding palettes to your favorites by clicking the heart icon on any palette card.</p>
                    </div>
                `;
                return;
            }
        } else {
            filteredPalettes = allPalettes.filter(palette =>
                palette.style.toLowerCase() === theme.toLowerCase()
            );
        }

        // Display all filtered palettes
        filteredPalettes.forEach(palette => {
            const card = createPaletteCard(palette);
            paletteGrid.appendChild(card);
        });
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
    const card = document.createElement('div');
    card.className = 'group flex flex-col gap-4';
    card.dataset.tags = `${palette.style.toLowerCase()} ${palette.name.toLowerCase()}`;
    card.dataset.paletteId = palette.id;

    const swatchesHTML = palette.colors.map(color => {
        const isLight = isLightColor(color);
        const textClass = isLight ? 'text-slate-800' : 'text-white';
        const bgClass = isLight ? 'bg-white/30' : 'bg-black/30';

        return `
            <div class="swatch flex-1 flex flex-col justify-end p-3 bg-[${color}] cursor-pointer hover:scale-105 transition-transform active:scale-95">
                <span class="swatch-hex text-[10px] font-bold ${textClass} ${bgClass} backdrop-blur-sm px-1.5 py-0.5 rounded text-center transition-all">${color}</span>
            </div>
        `;
    }).join('');

    // Check if this palette is favorited
    const isFav = isFavorite(palette.id);
    const heartIcon = isFav ? 'bi-heart-fill' : 'bi-heart';
    const heartColor = isFav ? 'text-red-500' : 'text-slate-400';

    card.innerHTML = `
        <div class="palette-card flex h-64 w-full rounded-2xl overflow-hidden shadow-xl shadow-black/5 dark:shadow-black/20 ring-1 ring-slate-200 dark:ring-slate-800">
            ${swatchesHTML}
        </div>
        <div class="flex items-center justify-between px-1">
            <div>
                <h3 class="font-bold text-lg">${palette.name}</h3>
                <p class="text-xs text-slate-500">By Color Studio • ${palette.style}</p>
            </div>
            <div class="flex items-center gap-4">
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
    paletteGrid.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-20">
            <i class="bi bi-hourglass-split text-6xl text-primary animate-pulse mb-4"></i>
            <p class="text-xl font-bold text-slate-700 dark:text-slate-300">Loading palettes...</p>
            <p class="text-sm text-slate-500">Please wait while we fetch the color data</p>
        </div>
    `;
}

function showErrorState(error) {
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

        paletteGrid.innerHTML = '';

        // Load all palettes at once
        allPalettes.forEach(palette => {
            const card = createPaletteCard(palette);
            paletteGrid.appendChild(card);
        });

        isLoading = false;

    } catch (error) {
        isLoading = false;
        loadError = error.message;
        showErrorState(error.message);
    }
}

fetchPalettes();
