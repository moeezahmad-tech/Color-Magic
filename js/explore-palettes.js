let allPalettes = [];
let isLoading = false;
let loadError = null;
let displayedCount = 0;
const palettesPerLoad = 12;
const paletteGrid = document.getElementById('paletteGrid');
const loadMoreBtn = document.getElementById('loadMoreBtn');

document.querySelectorAll('.copy-palette-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const colors = this.dataset.colors;
        navigator.clipboard.writeText(colors).then(() => {
            const icon = this.querySelector('i');
            const originalClass = icon.className;
            icon.className = icon.className.replace(/bi-[\w-]+/, 'bi-check');
            this.classList.add('text-green-500');

            setTimeout(() => {
                icon.className = originalClass;
                this.classList.remove('text-green-500');
            }, 2000);
        }).catch(err => {
            // Silent fail
        });
    });
});

document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const icon = this.querySelector('i');
        const countSpan = this.querySelector('.like-count');
        const currentCount = parseInt(countSpan.textContent.replace('k', '00').replace('.', ''));

        if (this.classList.contains('text-red-500')) {
            this.classList.remove('text-red-500');
            this.classList.add('text-slate-400');
            const newCount = currentCount - 1;
            countSpan.textContent = formatCount(newCount);
        } else {
            this.classList.remove('text-slate-400');
            this.classList.add('text-red-500');
            const newCount = currentCount + 1;
            countSpan.textContent = formatCount(newCount);
        }
    });
});

function formatCount(num) {
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'k';
    }
    return num.toString();
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
        } else {
            filteredPalettes = allPalettes.filter(palette =>
                palette.style.toLowerCase() === theme.toLowerCase()
            );
        }

        displayedCount = 0;
        filteredPalettes.forEach(palette => {
            const card = createPaletteCard(palette);
            paletteGrid.appendChild(card);
        });

        displayedCount = filteredPalettes.length;

        if (theme === 'all' && displayedCount < allPalettes.length) {
            loadMoreBtn.style.display = 'flex';
        } else {
            loadMoreBtn.style.display = 'none';
        }
    });
});

function isLightColor(hex) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness > 155;
}

function generateLikeCount() {
    const formats = ['k', 'k'];
    const useK = Math.random() > 0.3;
    if (useK) {
        const base = (Math.random() * 4.5 + 0.5).toFixed(1);
        return `${base}k`;
    } else {
        return Math.floor(Math.random() * 900 + 100).toString();
    }
}

function createPaletteCard(palette) {
    const card = document.createElement('div');
    card.className = 'group flex flex-col gap-4';
    card.dataset.tags = `${palette.style.toLowerCase()} ${palette.name.toLowerCase()}`;

    const swatchesHTML = palette.colors.map(color => {
        const isLight = isLightColor(color);
        const textClass = isLight ? 'text-slate-800' : 'text-white';
        const bgClass = isLight ? 'bg-white/30' : 'bg-black/30';

        return `
            <div class="swatch flex-1 flex flex-col justify-end p-3 bg-[${color}]">
                <span class="swatch-hex text-[10px] font-bold ${textClass} ${bgClass} backdrop-blur-sm px-1.5 py-0.5 rounded text-center">${color}</span>
            </div>
        `;
    }).join('');

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
                <button class="like-btn flex items-center gap-1 text-slate-400 hover:text-red-500 transition-colors">
                    <i class="bi bi-heart-fill text-lg"></i>
                    <span class="like-count text-sm font-bold">${generateLikeCount()}</span>
                </button>
                <button class="copy-palette-btn p-1.5 text-slate-400 hover:text-primary transition-colors" data-colors="${palette.colors.join(',')}">
                    <i class="bi bi-clipboard text-xl"></i>
                </button>
            </div>
        </div>
    `;

    return card;
}

function loadPalettes(count) {
    const palettesToShow = allPalettes.slice(displayedCount, displayedCount + count);

    palettesToShow.forEach(palette => {
        const card = createPaletteCard(palette);
        paletteGrid.appendChild(card);
    });

    displayedCount += palettesToShow.length;

    if (displayedCount >= allPalettes.length) {
        loadMoreBtn.style.display = 'none';
    }
}

function showLoadingState() {
    paletteGrid.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-20">
            <i class="bi bi-hourglass-split text-6xl text-primary animate-pulse mb-4"></i>
            <p class="text-xl font-bold text-slate-700 dark:text-slate-300">Loading palettes...</p>
            <p class="text-sm text-slate-500">Please wait while we fetch the color data</p>
        </div>
    `;
    loadMoreBtn.style.display = 'none';
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
    loadMoreBtn.style.display = 'none';
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
        displayedCount = 0;
        loadPalettes(palettesPerLoad);

        // Show Load More button if there are more palettes to load
        if (displayedCount < allPalettes.length) {
            loadMoreBtn.style.display = 'flex';
        } else {
            loadMoreBtn.style.display = 'none';
        }

        isLoading = false;

    } catch (error) {
        isLoading = false;
        loadError = error.message;
        showErrorState(error.message);
    }
}

loadMoreBtn?.addEventListener('click', function () {
    loadPalettes(palettesPerLoad);
});

fetchPalettes();
