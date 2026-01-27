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

    const getVibeTransform = (vibe, s, l) => {
        if (vibe === 'Soft & Muted') return { s: s * 0.6, l: Math.min(l + 20, 90) };
        if (vibe === 'Deep & Bold') return { s: Math.min(s + 30, 100), l: Math.max(l - 10, 30) };
        if (vibe === 'Pastel') return { s: 35, l: 85 };
        return { s, l };
    };

    const createPaletteVariation = (vibeName, baseHarmonyHues) => {
        const colors = baseHarmonyHues.map((h, i) => {
            let { s, l } = getVibeTransform(vibeName, base.s, base.l);

            const finalH = (selectedScheme === 'mono') ? h : h;
            const finalS = (selectedScheme === 'mono') ? Math.max(10, s - (i * 5)) : s;
            const finalL = (selectedScheme === 'mono') ? Math.max(15, Math.min(95, l + (i - 2) * 15)) : l;

            const hex = hslToHex(finalH % 360, finalS, finalL);
            return {
                hex: hex,
                name: `${vibeName.charAt(0).toUpperCase() + vibeName.slice(1)} ${hex}`,
                rgb: hexToRgb(hex.slice(1)),
                hsl: { h: Math.round(finalH % 360), s: Math.round(finalS), l: Math.round(finalL) },
                textColor: getContrastYIQ(hex)
            };
        });

        return {
            sourceName: `${vibeName.charAt(0).toUpperCase() + vibeName.slice(1)} ${selectedScheme.charAt(0).toUpperCase() + selectedScheme.slice(1)}`,
            colorCount: colors.length,
            colorPalette: colors,
            colorPaletteRaw: colors.map(c => c.hex.slice(1))
        };
    };

    const hexToRgb = (hex) => {
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        return { r, g, b };
    };

    let hues = [];
    if (selectedScheme === 'mono') hues = [base.h, base.h, base.h, base.h, base.h];
    else if (selectedScheme === 'contrast') hues = [base.h, base.h, base.h + 180, base.h + 180, base.h];
    else if (selectedScheme === 'triade') hues = [base.h, base.h + 120, base.h + 240, base.h, base.h + 120];
    else if (selectedScheme === 'tetrade') hues = [base.h, base.h + 90, base.h + 180, base.h + 270, base.h];
    else if (selectedScheme === 'analogic') hues = [base.h - 30, base.h - 15, base.h, base.h + 15, base.h + 30];

    results.push(createPaletteVariation('Classic', hues));
    results.push(createPaletteVariation('Soft & Muted', hues));
    results.push(createPaletteVariation('Deep & Bold', hues));

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
    resultCard.className = 'bg-slate-50 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-700 animate-fadeIn';

    const swatchesHTML = paletteData.colorPalette.map(color => `
        <div class="flex-1 h-24 flex items-end justify-center p-2" style="background-color: ${color.hex};">
            <span class="text-xs font-bold px-2 py-1 rounded" style="background-color: ${color.textColor === '#FFFFFF' ? 'rgba(0,0,0,0.3)' : 'rgba(255,255,255,0.3)'}; color: ${color.textColor};">
                ${color.hex}
            </span>
        </div>
    `).join('');

    const colorsDetailsHTML = paletteData.colorPalette.map((color, index) => {
        const hexToRgb = (hex) => {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : { r: 0, g: 0, b: 0 };
        };

        const rgb = color.rgb || hexToRgb(color.hex);
        const hsl = color.hsl || { h: 0, s: 0, l: 0 };

        return `
        <div class="bg-white dark:bg-slate-900 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full" style="background-color: ${color.hex};"></div>
                <div>
                    <h4 class="font-bold text-sm">${color.name}</h4>
                    <p class="text-xs text-slate-500 font-mono">${color.hex}</p>
                </div>
            </div>
            <div class="space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">RGB:</span>
                    <span class="font-mono">${rgb.r}, ${rgb.g}, ${rgb.b}</span>
                </div>
                ${color.hsl ? `
                <div class="flex justify-between">
                    <span class="text-slate-500">HSL:</span>
                    <span class="font-mono">${hsl.h}°, ${hsl.s}%, ${hsl.l}%</span>
                </div>
                ` : ''}
                ${color.luminance !== null && color.luminance !== undefined ? `
                <div class="flex justify-between">
                    <span class="text-slate-500">Luminance:</span>
                    <span>${color.luminance.toFixed(3)}</span>
                </div>
                ` : ''}
                ${color.accessibility ? `
                <div class="flex gap-1 mt-2">
                    ${color.accessibility.wcagAAA ? '<span class="px-2 py-0.5 bg-green-500 text-white rounded text-[10px] font-bold">AAA</span>' : ''}
                    ${color.accessibility.wcagAALarge ? '<span class="px-2 py-0.5 bg-blue-500 text-white rounded text-[10px] font-bold">AA Large</span>' : ''}
                    ${color.accessibility.wcagAANormal ? '<span class="px-2 py-0.5 bg-purple-500 text-white rounded text-[10px] font-bold">AA Normal</span>' : ''}
                </div>
                ` : ''}
            </div>
        </div>
    `}).join('');

    resultCard.innerHTML = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold">${paletteData.sourceName}</h3>
                    <p class="text-sm text-slate-500">
                        ${selectedScheme.charAt(0).toUpperCase() + selectedScheme.slice(1)} • 
                        ${selectedVariation.charAt(0).toUpperCase() + selectedVariation.slice(1)} • 
                        ${paletteData.colorCount} colors
                    </p>
                </div>
            </div>

            <div class="flex rounded-xl overflow-hidden mb-6 shadow-lg">
                ${swatchesHTML}
            </div>

            ${paletteData.image && paletteData.image.downloadURL ? `
                <div class="mb-6">
                    <img src="${paletteData.image.downloadURL}" alt="Palette Image" class="w-full h-32 object-cover rounded-lg shadow-md">
                </div>
            ` : ''}

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
                ${colorsDetailsHTML}
            </div>
        </div>
    `;

    paletteResults.insertBefore(resultCard, paletteResults.firstChild);

    const copyCSSBtn = resultCard.querySelector('.copy-css-btn');
    const copyHexBtn = resultCard.querySelector('.copy-hex-btn');

    if (copyCSSBtn) {
        copyCSSBtn.addEventListener('click', function () {
            const css = this.dataset.css;
            navigator.clipboard.writeText(css).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<span class="flex items-center gap-1"><i class="bi bi-check text-sm"></i>Copied!</span>';
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                }, 2000);
            }).catch(err => {
                // Silent fail
            });
        });
    }

    if (copyHexBtn) {
        copyHexBtn.addEventListener('click', function () {
            const colors = this.dataset.colors;
            navigator.clipboard.writeText(colors).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<span class="flex items-center gap-1"><i class="bi bi-check text-sm"></i>Copied!</span>';
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                }, 2000);
            }).catch(err => {
                // Silent fail
            });
        });
    }
}
