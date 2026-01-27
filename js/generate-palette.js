const paletteColorInput = document.getElementById('paletteColorInput');
const generatePaletteBtn = document.getElementById('generatePaletteBtn');
const paletteErrorMessage = document.getElementById('paletteErrorMessage');
const paletteResults = document.getElementById('paletteResults');
const paletteColorPreview = document.getElementById('paletteColorPreview');

let selectedScheme = 'mono';
let selectedVariation = 'default';

document.querySelectorAll('.scheme-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.scheme-btn').forEach(b => {
            b.classList.remove('scheme-active', 'bg-primary', 'text-white');
            b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
        });
        this.classList.add('scheme-active', 'bg-primary', 'text-white');
        this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
        selectedScheme = this.dataset.scheme;
    });
});

document.querySelectorAll('.variation-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.variation-btn').forEach(b => {
            b.classList.remove('variation-active', 'bg-primary', 'text-white');
            b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
        });
        this.classList.add('variation-active', 'bg-primary', 'text-white');
        this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
        selectedVariation = this.dataset.variation;
    });
});

paletteColorInput?.addEventListener('input', function () {
    let color = this.value.trim();
    if (color.startsWith('#')) {
        color = color.slice(1);
    }
    if (color.length === 3 || color.length === 6) {
        paletteColorPreview.style.backgroundColor = '#' + color;
    }
});

generatePaletteBtn?.addEventListener('click', generatePalette);

function hexToHsl(hex) {
    let r = parseInt(hex.substring(0, 2), 16) / 255;
    let g = parseInt(hex.substring(2, 4), 16) / 255;
    let b = parseInt(hex.substring(4, 6), 16) / 255;
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

function hslToHex(h, s, l) {
    l /= 100;
    const a = s * Math.min(l, 1 - l) / 100;
    const f = n => {
        const k = (n + h / 30) % 12;
        const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
        return Math.round(255 * color).toString(16).padStart(2, '0');
    };
    return `#${f(0)}${f(8)}${f(4)}`.toUpperCase();
}

function getContrastYIQ(hexcolor) {
    hexcolor = hexcolor.replace("#", "");
    const r = parseInt(hexcolor.substr(0, 2), 16);
    const g = parseInt(hexcolor.substr(2, 2), 16);
    const b = parseInt(hexcolor.substr(4, 2), 16);
    const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    return (yiq >= 128) ? '#000000' : '#FFFFFF';
}

async function generatePalette() {
    const btn = generatePaletteBtn;
    const btnText = btn.querySelector('span:last-child');
    const btnIcon = btn.querySelector('i');

    btnText.textContent = 'Generating...';
    btnIcon.className = btnIcon.className.replace(/bi-[\w-]+/, 'bi-hourglass-split');
    btn.disabled = true;
    paletteColorInput.disabled = true;
    paletteErrorMessage.classList.add('hidden');
    paletteErrorMessage.textContent = '';

    let colorCode = paletteColorInput.value.trim();
    if (colorCode.startsWith('#')) colorCode = colorCode.slice(1);

    if (colorCode === '' || !/^[0-9A-F]{6}$/i.test(colorCode)) {
        showPaletteError('Please enter a valid 6-character hex code');
        resetPaletteButton();
        return;
    }

    await new Promise(r => setTimeout(r, 600));

    try {
        const baseHsl = hexToHsl(colorCode);
        const palettes = generateColorTheoreticPalettes(baseHsl, colorCode);

        document.getElementById('palettePlaceholder').classList.add('hidden');
        paletteResults.classList.remove('hidden');
        paletteResults.innerHTML = '';

        palettes.forEach(p => displayPaletteResult(p));

        if (window.innerWidth < 1024) {
            paletteResults.scrollIntoView({ behavior: 'smooth' });
        }

    } catch (error) {
        alert('Error generating palette. Please try again.');
        showPaletteError('Something went wrong. Please try another color.');
    }

    resetPaletteButton();
}

function generateColorTheoreticPalettes(base, baseHex) {
    const results = [];

    // Function to get color name based on hue
    const getColorName = (h, s, l) => {
        h = Math.round(h) % 360;

        // Achromatic colors (low saturation)
        if (s < 10) {
            if (l > 90) return 'White';
            if (l > 70) return 'Light Gray';
            if (l > 50) return 'Gray';
            if (l > 30) return 'Dark Gray';
            return 'Charcoal';
        }

        // Chromatic colors based on hue
        if (h >= 0 && h < 15) return l > 60 ? 'Light Red' : l > 40 ? 'Red' : 'Dark Red';
        if (h >= 15 && h < 45) return l > 60 ? 'Peach' : l > 40 ? 'Orange' : 'Burnt Orange';
        if (h >= 45 && h < 70) return l > 60 ? 'Light Yellow' : l > 40 ? 'Yellow' : 'Gold';
        if (h >= 70 && h < 150) return l > 60 ? 'Light Green' : l > 40 ? 'Green' : 'Dark Green';
        if (h >= 150 && h < 200) return l > 60 ? 'Mint' : l > 40 ? 'Cyan' : 'Teal';
        if (h >= 200 && h < 250) return l > 60 ? 'Sky Blue' : l > 40 ? 'Blue' : 'Navy';
        if (h >= 250 && h < 290) return l > 60 ? 'Lavender' : l > 40 ? 'Purple' : 'Deep Purple';
        if (h >= 290 && h < 330) return l > 60 ? 'Pink' : l > 40 ? 'Magenta' : 'Maroon';
        return l > 60 ? 'Rose' : l > 40 ? 'Crimson' : 'Burgundy';
    };

    const hexToRgb = (hex) => {
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        return { r, g, b };
    };

    // Generate hues based on selected scheme
    let hues = [];
    if (selectedScheme === 'mono') {
        hues = [base.h, base.h, base.h, base.h, base.h];
    } else if (selectedScheme === 'contrast') {
        hues = [base.h, base.h + 60, base.h + 180, base.h + 240, base.h + 300];
    } else if (selectedScheme === 'triade') {
        hues = [base.h, base.h + 120, base.h + 240, base.h + 60, base.h + 180];
    } else if (selectedScheme === 'tetrade') {
        hues = [base.h, base.h + 90, base.h + 180, base.h + 270, base.h + 45];
    } else if (selectedScheme === 'analogic') {
        hues = [base.h - 30, base.h - 15, base.h, base.h + 15, base.h + 30];
    }

    // Create ONE palette with varied lightness and saturation
    const colors = hues.map((h, i) => {
        let s = base.s;
        let l = base.l;

        // Vary saturation and lightness for visual interest
        if (selectedScheme === 'mono') {
            s = Math.max(10, base.s - (i * 8));
            l = Math.max(15, Math.min(95, base.l + (i - 2) * 18));
        } else {
            // Subtle variations for non-mono schemes
            s = Math.max(20, Math.min(100, s + (i % 2 === 0 ? 10 : -5)));
            l = Math.max(25, Math.min(85, l + (i - 2) * 8));
        }

        const finalH = h % 360;
        const hex = hslToHex(finalH, s, l);
        const colorName = getColorName(finalH, s, l);

        return {
            hex: hex,
            name: colorName,
            rgb: hexToRgb(hex.slice(1)),
            hsl: { h: Math.round(finalH), s: Math.round(s), l: Math.round(l) },
            textColor: getContrastYIQ(hex)
        };
    });

    // Get scheme name
    const schemeNames = {
        'mono': 'Monochromatic',
        'contrast': 'Complementary',
        'triade': 'Triadic',
        'tetrade': 'Tetradic',
        'analogic': 'Analogous'
    };

    results.push({
        sourceName: `${schemeNames[selectedScheme]} Palette`,
        colorCount: colors.length,
        colorPalette: colors,
        colorPaletteRaw: colors.map(c => c.hex.slice(1))
    });

    return results;
}

function resetPaletteButton() {
    const btn = generatePaletteBtn;
    const btnText = btn.querySelector('span:last-child');
    const btnIcon = btn.querySelector('i');

    btnText.textContent = 'Generate Palette';
    btnIcon.className = btnIcon.className.replace(/bi-[\w-]+/, 'bi-stars');
    btn.disabled = false;
    paletteColorInput.disabled = false;
}

function showPaletteError(message) {
    paletteErrorMessage.textContent = message;
    paletteErrorMessage.classList.remove('hidden');
}

function displayPaletteResult(paletteData) {
    const resultCard = document.createElement('div');
    resultCard.className = 'bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-700';
    resultCard.style.fontFamily = "'Space Grotesk', sans-serif";

    const swatchesHTML = paletteData.colorPalette.map(color => `
        <div class="flex-1 h-24 min-[700px]:h-32 flex items-end justify-center p-2 min-[700px]:p-3" style="background-color: ${color.hex};">
            <span class="text-xs min-[700px]:text-sm font-bold px-2 min-[700px]:px-3 py-1 min-[700px]:py-1.5 rounded-lg" style="background-color: ${color.textColor === '#FFFFFF' ? 'rgba(0,0,0,0.4)' : 'rgba(255,255,255,0.4)'}; color: ${color.textColor}; backdrop-filter: blur(4px);">
                ${color.hex}
            </span>
        </div>
    `).join('');

    const colorsDetailsHTML = paletteData.colorPalette.map((color, index) => {
        const rgb = color.rgb;
        const hsl = color.hsl;

        return `
        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg min-[700px]:rounded-xl p-3 min-[700px]:p-4 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg shadow-md" style="background-color: ${color.hex};"></div>
                <div class="flex-1">
                    <h4 class="font-bold text-base tracking-tight">${color.name}</h4>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">${color.hex}</p>
                </div>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">RGB</span>
                    <span class="font-mono font-semibold">${rgb.r}, ${rgb.g}, ${rgb.b}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">HSL</span>
                    <span class="font-mono font-semibold">${hsl.h}°, ${hsl.s}%, ${hsl.l}%</span>
                </div>
            </div>
        </div>
    `}).join('');

    resultCard.innerHTML = `
        <div class="p-4 min-[700px]:p-6">
            <div class="flex items-center justify-between mb-3 min-[700px]:mb-5">
                <div>
                    <h3 class="text-2xl font-bold tracking-tight mb-1">${paletteData.sourceName}</h3>
                    <p class="text-sm text-slate-500">
                        ${paletteData.colorCount} colors • Professional color harmony
                    </p>
                </div>
            </div>

            <div class="flex flex-col min-[700px]:flex-row rounded-lg min-[700px]:rounded-xl overflow-hidden mb-4 min-[700px]:mb-6 shadow-lg border border-slate-200 dark:border-slate-700">
                ${swatchesHTML}
            </div>

            <div class="grid grid-cols-1 min-[700px]:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                ${colorsDetailsHTML}
            </div>
        </div>
    `;

    paletteResults.insertBefore(resultCard, paletteResults.firstChild);
}
