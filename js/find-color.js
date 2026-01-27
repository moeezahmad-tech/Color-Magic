let colorNames = {};
let isColorNamesLoaded = false;
let isLoadingColorNames = false;

const colorCodeInput = document.getElementById('colorCodeInput');
const findColorBtn = document.getElementById('findColorBtn');
const errorMessage = document.getElementById('errorMessage');
const colorResultsGrid = document.getElementById('colorResultsGrid');

async function fetchColorNames() {
    if (isLoadingColorNames || isColorNamesLoaded) return;

    isLoadingColorNames = true;

    try {
        const response = await fetch('data/color-names.json');

        if (!response.ok) {
            throw new Error(`Failed to fetch color names (Status: ${response.status})`);
        }

        const data = await response.json();

        if (typeof data !== 'object' || data === null) {
            throw new Error('Invalid color names data format');
        }

        colorNames = data;
        isColorNamesLoaded = true;

    } catch (error) {
        showError(`Failed to load color database: ${error.message}`);
    } finally {
        isLoadingColorNames = false;
    }
}

fetchColorNames();

function getColorName(hex) {
    const cleanHex = hex.toLowerCase().replace('#', '');
    if (colorNames[cleanHex]) {
        return colorNames[cleanHex];
    }
    return hex.toUpperCase();
}

colorCodeInput?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        findColorByCode();
    }
});

findColorBtn?.addEventListener('click', findColorByCode);

function hexToRgbLocal(hex) {
    const cleanHex = hex.replace('#', '');
    return {
        r: parseInt(cleanHex.substring(0, 2), 16),
        g: parseInt(cleanHex.substring(2, 4), 16),
        b: parseInt(cleanHex.substring(4, 6), 16)
    };
}

function hexToHslLocal(hex) {
    const cleanHex = hex.replace('#', '');
    let r = parseInt(cleanHex.substring(0, 2), 16) / 255;
    let g = parseInt(cleanHex.substring(2, 4), 16) / 255;
    let b = parseInt(cleanHex.substring(4, 6), 16) / 255;

    let max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
        h = s = 0;
    } else {
        let d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    return { h: h * 360, s: s * 100, l: l * 100 };
}

function calculateLuminance(hex) {
    const rgb = hexToRgbLocal(hex);
    const a = [rgb.r, rgb.g, rgb.b].map(v => {
        v /= 255;
        return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
    });
    return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

function getBestContrast(hex) {
    const luminance = calculateLuminance(hex);
    return luminance > 0.179 ? 'Black' : 'White';
}

async function findColorByCode() {
    if (!isColorNamesLoaded) {
        showError('Color database is still loading. Please wait a moment and try again.');
        return;
    }

    const btn = findColorBtn;
    const originalBtnHTML = btn.innerHTML;

    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
    btn.disabled = true;
    colorCodeInput.disabled = true;
    errorMessage.classList.add('hidden');
    errorMessage.textContent = '';

    let colorCode = colorCodeInput.value.trim();

    if (colorCode.startsWith('#')) {
        colorCode = colorCode.slice(1);
    }

    if (colorCode === '' || !/^[0-9A-F]{6}$/i.test(colorCode)) {
        showError('Please enter a valid 6-character hex code (e.g., FF5733)');
        btn.innerHTML = originalBtnHTML;
        btn.disabled = false;
        colorCodeInput.disabled = false;
        return;
    }

    const fullHex = '#' + colorCode.toUpperCase();

    setTimeout(() => {
        try {
            const rgb = hexToRgbLocal(fullHex);
            const hsl = hexToHslLocal(fullHex);
            const colorName = getColorName(fullHex);
            const bestContrast = getBestContrast(fullHex);

            displayColorInfo({
                hex: fullHex,
                rgb: rgb,
                hsl: hsl,
                name: colorName,
                contrast: bestContrast
            });

            saveToHistory({
                hex: fullHex,
                name: colorName,
                timestamp: Date.now()
            });

            loadColorHistory();

        } catch (error) {
            showError('Something went wrong. Please try another color code.');
        }

        btn.innerHTML = originalBtnHTML;
        btn.disabled = false;
        colorCodeInput.disabled = false;
    }, 600);
}

function resetButton() {
    const btn = findColorBtn;
    btn.innerHTML = '<i class="bi bi-search"></i> Find Name';
    btn.disabled = false;
    colorCodeInput.disabled = false;
}

function showError(message) {
    errorMessage.textContent = message;
    errorMessage.classList.remove('hidden');
}

function displayColorInfo(colorData) {
    const infoCard = document.createElement('div');
    infoCard.className = 'col-span-full bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-700 animate-fadeIn';

    infoCard.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
            <div class="p-8 flex flex-col justify-center" style="background-color: ${colorData.hex};">
                <div class="text-center" style="color: ${colorData.contrast === 'White' ? '#FFFFFF' : '#000000'};">
                    <h2 class="text-4xl font-bold mb-2">${colorData.name}</h2>
                    <p class="text-2xl font-mono opacity-80">${colorData.hex}</p>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Color Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">HEX</span>
                            <span class="font-mono font-bold">${colorData.hex}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">RGB</span>
                            <span class="font-mono">${colorData.rgb.r}, ${colorData.rgb.g}, ${colorData.rgb.b}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">HSL</span>
                            <span class="font-mono">${Math.round(colorData.hsl.h)}°, ${Math.round(colorData.hsl.s)}%, ${Math.round(colorData.hsl.l)}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Best Contrast</span>
                            <span class="font-bold">${colorData.contrast}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    colorResultsGrid.innerHTML = '';
    colorResultsGrid.appendChild(infoCard);
}

function saveToHistory(colorData) {
    let history = JSON.parse(localStorage.getItem('colorHistory') || '[]');

    history = history.filter(item => item.hex.toLowerCase() !== colorData.hex.toLowerCase());

    history.unshift(colorData);

    if (history.length > 10) {
        history = history.slice(0, 10);
    }

    localStorage.setItem('colorHistory', JSON.stringify(history));
}

function loadColorHistory() {
    const history = JSON.parse(localStorage.getItem('colorHistory') || '[]');

    if (history.length === 0) {
        colorResultsGrid.innerHTML = `
            <div class="col-span-full text-center py-12 text-slate-500">
                <i class="bi bi-palette text-6xl mb-4 opacity-50"></i>
                <p class="text-lg">No search history yet</p>
                <p class="text-sm">Enter a color code above to get started!</p>
            </div>
        `;
        return;
    }
}
