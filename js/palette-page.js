(async function () {
    const params = new URLSearchParams(window.location.search);
    const paletteId = params.get('id');

    const paletteDetail = document.getElementById('paletteDetail');
    const paletteError = document.getElementById('paletteError');
    const paletteName = document.getElementById('paletteName');
    const paletteMeta = document.getElementById('paletteMeta');
    const paletteType = document.getElementById('paletteType');
    const paletteCount = document.getElementById('paletteCount');
    const heroPaletteStrips = document.getElementById('heroPaletteStrips');
    const detailColorList = document.getElementById('detailColorList');
    const contrastCardA = document.getElementById('contrastCardA');
    const contrastCardB = document.getElementById('contrastCardB');
    const darkColorParagraph = document.getElementById('darkColorParagraph');
    const paletteSwatches = document.getElementById('paletteSwatches');
    const colorInfoGrid = document.getElementById('colorInfoGrid');
    const brightnessChart = document.getElementById('brightnessChart');
    const paletteIdText = document.getElementById('paletteIdText');
    const copyAllBtn = document.getElementById('copyAllBtn');
    const copyCssVarsBtn = document.getElementById('copyCssVarsBtn');
    const exportPreview = document.getElementById('exportPreview');
    const exportCode = document.getElementById('exportCode');

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function hexToHsl(hex) {
        let r, g, b;
        const rgb = hexToRgb(hex);
        r = rgb.r / 255;
        g = rgb.g / 255;
        b = rgb.b / 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }

        return {
            h: Math.round(h * 360),
            s: Math.round(s * 100),
            l: Math.round(l * 100)
        };
    }

    function getLuminance(hex) {
        const rgb = hexToRgb(hex);
        const [r, g, b] = [rgb.r, rgb.g, rgb.b].map(x => {
            x = x / 255;
            return x <= 0.03928 ? x / 12.92 : Math.pow((x + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    }

    function showCopyFeedback(button, successText, failText, resetText) {
        if (!button) return;
        button.innerHTML = successText;
        setTimeout(() => {
            button.innerHTML = resetText;
        }, 1200);
    }

    async function copyText(text, button, successText, failText, resetText) {
        try {
            await navigator.clipboard.writeText(text);
            showCopyFeedback(button, successText, failText, resetText);
        } catch (_) {
            if (button) {
                button.innerHTML = failText;
                setTimeout(() => {
                    button.innerHTML = resetText;
                }, 1200);
            }
        }
    }

    if (paletteIdText && paletteId) {
        paletteIdText.textContent = `id: ${paletteId}`;
    }

    // Get export elements
    const exportPreviewEl = document.getElementById('exportPreview');
    const copyExportBtn = document.getElementById('copyExportBtn');
    const exportBtns = document.querySelectorAll('.export-btn');

    if (!paletteId) {
        paletteError?.classList.remove('hidden');
        return;
    }

    try {
        const response = await fetch('data/colors.json');
        if (!response.ok) {
            throw new Error(`Failed to fetch palettes (Status: ${response.status})`);
        }

        const palettes = await response.json();
        const palette = Array.isArray(palettes)
            ? palettes.find((item) => String(item.id) === paletteId)
            : null;

        if (!palette) {
            paletteError?.classList.remove('hidden');
            return;
        }

        document.title = `${palette.name} | Color Magic`;
        paletteName.textContent = palette.name;

        const colorList = Array.isArray(palette.colors) ? palette.colors : [];
        const firstColor = colorList[0] || '#111111';
        const lastColor = colorList[colorList.length - 1] || '#f8fafc';
        const allColors = colorList.join(', ');
        const cssVars = colorList.map((color, index) => `--palette-color-${index + 1}: ${color};`).join('\n');

        paletteMeta.textContent = `Palette ID: ${palette.id}`;
        paletteType.textContent = palette.style || 'General';
        paletteCount.textContent = String(colorList.length);

        heroPaletteStrips.style.gridTemplateColumns = `repeat(${colorList.length}, minmax(0, 1fr))`;
        heroPaletteStrips.innerHTML = colorList.map((color) => `
            <div class="h-full flex items-end justify-center p-3" style="background-color:${color};">
                <span class="text-[10px] md:text-xs font-bold font-mono px-2 py-1 rounded-lg bg-black/25 text-white">${color}</span>
            </div>
        `).join('');

        detailColorList.innerHTML = colorList.map((color) => `
            <div class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 dark:bg-slate-800 px-3 py-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-6 h-6 rounded-md shrink-0" style="background:${color}"></span>
                    <span class="font-mono text-xs md:text-sm truncate">${color}</span>
                </div>
                <button type="button"
                        class="copy-single-color text-xs font-semibold px-2 py-1 rounded-lg bg-white dark:bg-slate-700 hover:bg-primary hover:text-white transition-colors"
                        data-color="${color}">
                    Copy
                </button>
            </div>
        `).join('');

        contrastCardA.style.backgroundColor = firstColor;
        contrastCardA.style.color = lastColor;
        contrastCardA.innerHTML = `
            <p class="text-xs uppercase tracking-wider opacity-80">Contrast Preview A</p>
            <p class="text-2xl md:text-3xl font-bold">Aa</p>
            <p class="text-sm">Background: ${firstColor} · Text: ${lastColor}</p>
        `;

        contrastCardB.style.backgroundColor = lastColor;
        contrastCardB.style.color = firstColor;
        contrastCardB.innerHTML = `
            <p class="text-xs uppercase tracking-wider opacity-80">Contrast Preview B</p>
            <p class="text-2xl md:text-3xl font-bold">Aa</p>
            <p class="text-sm">Background: ${lastColor} · Text: ${firstColor}</p>
        `;

        darkColorParagraph.style.color = firstColor;
        darkColorParagraph.textContent = `${palette.name} starts with ${firstColor} as the anchor tone, giving the palette a strong visual base. As the colors progress to ${lastColor}, the composition opens into brighter accents that are great for UI highlights, typography emphasis, and layered backgrounds. This progression creates a clear rhythm that helps designs feel intentional instead of random.`;

        paletteSwatches.innerHTML = colorList.map((color) => `
            <a href="/color/${color.replace('#', '')}" target="_blank" rel="noopener"
                    class="group rounded-xl p-4 text-left bg-white dark:bg-slate-900 shadow-sm hover:shadow-md transition-all block"
                    title="Open ${color} details">
                <span class="block w-full h-20 rounded-lg mb-3" style="background-color:${color}"></span>
                <span class="font-mono text-sm font-bold">${color}</span>
                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                    <i class="bi bi-box-arrow-up-right"></i>Open
                </span>
            </a>
        `).join('');

        // Color Information Grid
        colorInfoGrid.innerHTML = colorList.map((color, index) => {
            const rgb = hexToRgb(color);
            const hsl = hexToHsl(color);
            const lum = getLuminance(color);
            const brightness = lum > 0.5 ? 'Light' : 'Dark';
            return `
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-8 h-8 rounded-lg" style="background:${color}"></span>
                        <span class="font-bold text-sm">${color}</span>
                    </div>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-slate-500 dark:text-slate-400">RGB:</span> ${rgb.r}, ${rgb.g}, ${rgb.b}</p>
                        <p><span class="text-slate-500 dark:text-slate-400">HSL:</span> ${hsl.h}°, ${hsl.s}%, ${hsl.l}%</p>
                        <p><span class="text-slate-500 dark:text-slate-400">Brightness:</span> ${brightness}</p>
                    </div>
                </div>
            `;
        }).join('');

        // Brightness Chart
        const maxBrightness = Math.max(...colorList.map(c => getLuminance(c)));
        brightnessChart.innerHTML = colorList.map((color) => {
            const brightness = (getLuminance(color) / maxBrightness) * 100;
            return `
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs w-16 shrink-0">${color}</span>
                    <div class="flex-1 bg-slate-200 dark:bg-slate-700 rounded-full h-6 overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="background:${color}; width:${brightness}%"></div>
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 w-12 text-right">${Math.round(brightness)}%</span>
                </div>
            `;
        }).join('');

        detailColorList.addEventListener('click', async (event) => {
            const btn = event.target.closest('.copy-single-color');
            if (!btn) return;

            const color = btn.dataset.color || '';
            if (!color) return;

            const resetText = 'Copy';
            await copyText(color, btn, 'Copied', 'Failed', resetText);
        });

        // Export Format Handlers
        const hexArrayFormat = `[\n  ${colorList.map(c => `'${c}'`).join(',\n  ')}\n]`;
        const jsonFormat = JSON.stringify({ name: palette.name, colors: colorList, style: palette.style }, null, 2);
        const scssFormat = colorList.map((c, i) => `$palette-${i + 1}: ${c};`).join('\n');
        const tailwindFormat = `colors: {\n  ${colorList.map((c, i) => `palette-${i + 1}: '${c}'`).join(',\n  ')}\n}`;

        const exportFormats = {
            hex: hexArrayFormat,
            json: jsonFormat,
            scss: scssFormat,
            tailwind: tailwindFormat
        };

        let currentFormat = 'hex';

        function updateExportDisplay(format) {
            currentFormat = format;
            exportCode.textContent = exportFormats[format];
            exportPreviewEl.classList.remove('hidden');
            copyExportBtn.classList.remove('hidden');

            // Update button styles
            exportBtns.forEach(btn => {
                if (btn.dataset.format === format) {
                    btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'hover:bg-primary', 'hover:text-white');
                    btn.classList.add('bg-primary', 'text-white');
                } else {
                    btn.classList.remove('bg-primary', 'text-white');
                    btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'hover:bg-primary', 'hover:text-white');
                }
            });
        }

        exportBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const format = btn.dataset.format;
                updateExportDisplay(format);
            });
        });

        copyExportBtn?.addEventListener('click', async () => {
            const code = exportFormats[currentFormat];
            await copyText(code, copyExportBtn, '<i class="bi bi-check-circle me-1"></i>Copied', '<i class="bi bi-x-circle me-1"></i>Failed', '<i class="bi bi-clipboard me-1"></i>Copy Code');
        });

        // Show first export format by default
        updateExportDisplay('hex');

        copyAllBtn?.addEventListener('click', async () => {
            const resetText = '<i class="bi bi-clipboard me-1"></i>Copy All Colors';
            await copyText(allColors, copyAllBtn, '<i class="bi bi-check-circle me-1"></i>Copied', '<i class="bi bi-x-circle me-1"></i>Failed', resetText);
        });

        copyCssVarsBtn?.addEventListener('click', async () => {
            const resetText = '<i class="bi bi-code-slash me-1"></i>Copy CSS Variables';
            await copyText(cssVars, copyCssVarsBtn, '<i class="bi bi-check-circle me-1"></i>Copied', '<i class="bi bi-x-circle me-1"></i>Failed', resetText);
        });

        paletteDetail?.classList.remove('hidden');
    } catch (_) {
        paletteError?.classList.remove('hidden');
    }
})();
