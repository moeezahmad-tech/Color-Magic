/**
 * generate-palette-page.js
 * Powers the standalone Generate Palette page (generate-palette.html)
 */

(function () {
    // ── DOM refs ──────────────────────────────────────────────────────────────
    const paletteColorInput = document.getElementById('paletteColorInput');
    const paletteColorPicker = document.getElementById('paletteColorPicker');
    const generatePaletteBtn = document.getElementById('generatePaletteBtn');
    const paletteErrorMessage = document.getElementById('paletteErrorMessage');
    const paletteResults = document.getElementById('paletteResults');
    const palettePlaceholder = document.getElementById('palettePlaceholder');
    const schemeInfoText = document.getElementById('schemeInfoText');

    let selectedScheme = 'mono';

    // ── Scheme descriptions ───────────────────────────────────────────────────
    const schemeDescriptions = {
        mono: '<strong class="text-slate-800 dark:text-slate-200">Monochromatic:</strong> Variations of the same hue with different lightness and saturation for a cohesive look.',
        contrast: '<strong class="text-slate-800 dark:text-slate-200">Complementary:</strong> Opposite hues on the color wheel create high-contrast and energetic combinations.',
        triade: '<strong class="text-slate-800 dark:text-slate-200">Triadic:</strong> Three evenly-spaced hues form a balanced and vibrant palette with great visual richness.',
        tetrade: '<strong class="text-slate-800 dark:text-slate-200">Tetradic:</strong> Four hues forming a rectangle on the color wheel, best for complex and versatile projects.',
        analogic: '<strong class="text-slate-800 dark:text-slate-200">Analogous:</strong> Neighbouring hues on the wheel create serene and harmonious schemes found in nature.',
    };

    // ── Scheme tab buttons ────────────────────────────────────────────────────
    document.querySelectorAll('.scheme-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.scheme-btn').forEach(b => {
                b.classList.remove('scheme-active');
                b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
            });
            this.classList.add('scheme-active');
            this.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
            selectedScheme = this.dataset.scheme;
            if (schemeInfoText) schemeInfoText.innerHTML = schemeDescriptions[selectedScheme] || '';
        });
    });

    // ── Sync text input → picker + preview ───────────────────────────────────
    paletteColorInput?.addEventListener('input', function () {
        let val = this.value.trim().replace('#', '');
        if (val.length === 3) {
            val = val.split('').map(c => c + c).join('');
        }
        if (/^[0-9A-Fa-f]{6}$/.test(val)) {
            const full = '#' + val.toUpperCase();
            if (paletteColorPicker) paletteColorPicker.value = full;
        }   
    });

    // ── Sync picker → text input + preview ───────────────────────────────────
    paletteColorPicker?.addEventListener('input', function () {
        const hex = this.value.replace('#', '').toUpperCase();
        if (paletteColorInput) paletteColorInput.value = hex;
        
    });

    // ── Generate button ───────────────────────────────────────────────────────
    generatePaletteBtn?.addEventListener('click', generatePalette);

    // Also allow Enter from text input
    paletteColorInput?.addEventListener('keypress', e => {
        if (e.key === 'Enter') generatePalette();
    });

    // ── Color math helpers ────────────────────────────────────────────────────
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

    function getContrastYIQ(hex) {
        hex = hex.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return yiq >= 128 ? '#000000' : '#FFFFFF';
    }

    function hexToRgb(hex) {
        return {
            r: parseInt(hex.substring(0, 2), 16),
            g: parseInt(hex.substring(2, 4), 16),
            b: parseInt(hex.substring(4, 6), 16)
        };
    }

    function getColorName(h, s, l) {
        h = Math.round(h) % 360;
        if (s < 10) {
            if (l > 90) return 'White';
            if (l > 70) return 'Light Gray';
            if (l > 50) return 'Gray';
            if (l > 30) return 'Dark Gray';
            return 'Charcoal';
        }
        if (h >= 0 && h < 15) return l > 60 ? 'Light Red' : l > 40 ? 'Red' : 'Dark Red';
        if (h >= 15 && h < 45) return l > 60 ? 'Peach' : l > 40 ? 'Orange' : 'Burnt Orange';
        if (h >= 45 && h < 70) return l > 60 ? 'Light Yellow' : l > 40 ? 'Yellow' : 'Gold';
        if (h >= 70 && h < 150) return l > 60 ? 'Light Green' : l > 40 ? 'Green' : 'Dark Green';
        if (h >= 150 && h < 200) return l > 60 ? 'Mint' : l > 40 ? 'Cyan' : 'Teal';
        if (h >= 200 && h < 250) return l > 60 ? 'Sky Blue' : l > 40 ? 'Blue' : 'Navy';
        if (h >= 250 && h < 290) return l > 60 ? 'Lavender' : l > 40 ? 'Purple' : 'Deep Purple';
        if (h >= 290 && h < 330) return l > 60 ? 'Pink' : l > 40 ? 'Magenta' : 'Maroon';
        return l > 60 ? 'Rose' : l > 40 ? 'Crimson' : 'Burgundy';
    }

    // ── Palette generation ────────────────────────────────────────────────────
    async function generatePalette() {
        const btnText = generatePaletteBtn.querySelector('span');
        const btnIcon = generatePaletteBtn.querySelector('i');

        btnText.textContent = 'Generating…';
        btnIcon.className = btnIcon.className.replace(/bi-[\w-]+/, 'bi-hourglass-split');
        generatePaletteBtn.disabled = true;
        if (paletteColorInput) paletteColorInput.disabled = true;
        paletteErrorMessage.classList.add('hidden');

        let colorCode = (paletteColorInput?.value || '').trim().replace('#', '');

        if (!colorCode || !/^[0-9A-F]{6}$/i.test(colorCode)) {
            showError('Please enter a valid 6-character hex code (e.g., EC4899)');
            resetBtn();
            return;
        }

        await new Promise(r => setTimeout(r, 500));

        try {
            const baseHsl = hexToHsl(colorCode);
            const palette1 = buildPalette(baseHsl, colorCode, 'Primary');

            const variantHsl2 = { h: baseHsl.h, s: Math.max(0, baseHsl.s - 20), l: Math.min(100, baseHsl.l + 15) };
            const palette2 = buildPalette(variantHsl2, hslToHex(variantHsl2.h, variantHsl2.s, variantHsl2.l), 'Lighter');

            const variantHsl3 = { h: baseHsl.h, s: Math.min(100, baseHsl.s + 10), l: Math.max(0, baseHsl.l - 20) };
            const palette3 = buildPalette(variantHsl3, hslToHex(variantHsl3.h, variantHsl3.s, variantHsl3.l), 'Darker');

            palettePlaceholder.classList.add('hidden');
            paletteResults.classList.remove('hidden');
            paletteResults.innerHTML = '';

            displayPaletteResult(palette1);
            displayPaletteResult(palette2);
            displayPaletteResult(palette3);

            paletteResults.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } catch (err) {
            showError('Something went wrong. Please try another color.');
        }

        resetBtn();
    }

    function buildPalette(base, baseHex, variantName) {
        let hues = [];
        if (selectedScheme === 'mono') hues = [base.h, base.h, base.h, base.h, base.h];
        else if (selectedScheme === 'contrast') hues = [base.h, base.h + 60, base.h + 180, base.h + 240, base.h + 300];
        else if (selectedScheme === 'triade') hues = [base.h, base.h + 120, base.h + 240, base.h + 60, base.h + 180];
        else if (selectedScheme === 'tetrade') hues = [base.h, base.h + 90, base.h + 180, base.h + 270, base.h + 45];
        else if (selectedScheme === 'analogic') hues = [base.h - 30, base.h - 15, base.h, base.h + 15, base.h + 30];

        const schemeNames = {
            mono: 'Monochromatic', contrast: 'Complementary',
            triade: 'Triadic', tetrade: 'Tetradic', analogic: 'Analogous'
        };

        const colors = hues.map((h, i) => {
            let s = base.s, l = base.l;
            if (selectedScheme === 'mono') {
                s = Math.max(10, base.s - (i * 8));
                l = Math.max(15, Math.min(95, base.l + (i - 2) * 18));
            } else {
                s = Math.max(20, Math.min(100, s + (i % 2 === 0 ? 10 : -5)));
                l = Math.max(25, Math.min(85, l + (i - 2) * 8));
            }
            const finalH = ((h % 360) + 360) % 360;
            const hex = hslToHex(finalH, s, l);
            return {
                hex,
                name: getColorName(finalH, s, l),
                rgb: hexToRgb(hex.slice(1)),
                hsl: { h: Math.round(finalH), s: Math.round(s), l: Math.round(l) },
                textColor: getContrastYIQ(hex)
            };
        });

        return {
            sourceName: `${schemeNames[selectedScheme]} Palette - ${variantName}`,
            colorCount: colors.length,
            colorPalette: colors,
            colorPaletteRaw: colors.map(c => c.hex.slice(1))
        };
    }

    function resetBtn() {
        const btnText = generatePaletteBtn.querySelector('span');
        const btnIcon = generatePaletteBtn.querySelector('i');
        btnText.textContent = 'Generate Palette';
        btnIcon.className = btnIcon.className.replace(/bi-[\w-]+/, 'bi-stars');
        generatePaletteBtn.disabled = false;
        if (paletteColorInput) paletteColorInput.disabled = false;
    }

    function showError(msg) {
        paletteErrorMessage.textContent = msg;
        paletteErrorMessage.classList.remove('hidden');
    }

    // ── Render result card ────────────────────────────────────────────────────
    function displayPaletteResult(data) {
        const card = document.createElement('div');
        card.className = 'bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-700 animate-[fadeInUp_.4s_ease_both]';

        const swatchesHTML = data.colorPalette.map(c => `
            <div class="flex-1 min-h-[140px] flex items-end justify-center p-3 group/sw cursor-pointer transition-all"
                 style="background-color:${c.hex};"
                 title="Click to copy ${c.hex}"
                 onclick="copySwatchHex(this,'${c.hex}')">
                <span class="swatch-label text-xs font-bold px-2 py-1 rounded-lg opacity-0 group-hover/sw:opacity-100 transition-all select-none"
                    style="background-color:${c.textColor === '#FFFFFF' ? 'rgba(0,0,0,0.45)' : 'rgba(255,255,255,0.55)'}; color:${c.textColor}; backdrop-filter:blur(4px);">
                    ${c.hex}
                </span>
            </div>
        `).join('');

        const detailsHTML = data.colorPalette.map(c => `
            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700 group/detail flex flex-wrap sm:flex-nowrap items-center justify-between gap-4 transition-all hover:border-pink-200 dark:hover:border-slate-600">
                <div class="flex items-center gap-3 md:gap-4 w-full sm:w-auto min-w-[200px]">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg shadow-sm shrink-0 ring-2 ring-white dark:ring-slate-700" style="background-color:${c.hex};"></div>
                    <div>
                        <p class="font-bold text-sm md:text-base">${c.name}</p>
                        <button class="text-xs text-slate-500 font-mono hover:text-primary transition-colors text-left" onclick="copyText('${c.hex}', this)" title="Copy hex">
                            ${c.hex}
                        </button>
                    </div>
                </div>
                <div class="flex gap-4 md:gap-8 text-xs sm:text-right w-full sm:w-auto mt-2 sm:mt-0 pt-3 sm:pt-0 border-t border-slate-200 dark:border-slate-700 sm:border-t-0">
                    <div class="flex-1 sm:flex-none flex flex-col gap-1 sm:items-end">
                        <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider">RGB</span>
                        <button class="font-mono font-medium hover:text-primary transition-colors" onclick="copyText('rgb(${c.rgb.r}, ${c.rgb.g}, ${c.rgb.b})',this)">${c.rgb.r}, ${c.rgb.g}, ${c.rgb.b}</button>
                    </div>
                    <div class="flex-1 sm:flex-none flex flex-col gap-1 sm:items-end">
                        <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider">HSL</span>
                        <button class="font-mono font-medium hover:text-primary transition-colors" onclick="copyText('hsl(${c.hsl.h}, ${c.hsl.s}%, ${c.hsl.l}%)',this)">${c.hsl.h}°, ${c.hsl.s}%, ${c.hsl.l}%</button>
                    </div>
                </div>
            </div>
        `).join('');

        const allHex = data.colorPalette.map(c => c.hex).join(', ');

        card.innerHTML = `
            <div class="p-5 md:p-7">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight mb-0.5">${data.sourceName}</h2>
                        <p class="text-sm text-slate-500">${data.colorCount} colors · Color theory harmony</p>
                    </div>
                    <button onclick="copyAllHex('${allHex}', this)"
                        class="flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-xl text-sm font-semibold transition-all group/copy">
                        <i class="bi bi-clipboard text-base group-hover/copy:hidden"></i>
                        <i class="bi bi-clipboard-check text-base hidden group-hover/copy:block"></i>
                        Copy All
                    </button>
                </div>

                <div class="flex rounded-2xl overflow-hidden mb-6 shadow-sm border border-slate-200 dark:border-slate-700 h-32 md:h-40">
                    ${swatchesHTML}
                </div>

                <div class="flex flex-col items-stretch gap-3">
                    ${detailsHTML}
                </div>
            </div>
        `;

        paletteResults.insertBefore(card, paletteResults.firstChild);
    }

    // ── Global copy helpers (called from inline onclick) ──────────────────────
    window.copySwatchHex = function (el, hex) {
        navigator.clipboard.writeText(hex).then(() => {
            const label = el.querySelector('.swatch-label');
            if (!label) return;
            const orig = label.textContent;
            label.textContent = 'Copied!';
            label.style.opacity = '1';
            setTimeout(() => { label.textContent = orig; label.style.opacity = ''; }, 1500);
        }).catch(() => { });
    };

    window.copyText = function (text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('text-green-500');
            setTimeout(() => { btn.textContent = orig; btn.classList.remove('text-green-500'); }, 1300);
        }).catch(() => { });
    };

    window.copyAllHex = function (text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-green-500', 'text-white');
            setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('bg-green-500', 'text-white'); }, 1500);
        }).catch(() => { });
    };

    // ── Seed with the default picker color ───────────────────────────────────
    if (paletteColorInput && !paletteColorInput.value) {
        paletteColorInput.value = 'EC4899';
    }

})();
