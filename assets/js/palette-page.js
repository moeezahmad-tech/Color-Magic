(async function () {
    const params = new URLSearchParams(window.location.search);

    // Extract slug from clean URL path: /palette/soft-steel/ or /ColorMagic/palette/soft-steel/
    let paletteSlug = params.get('slug');
    if (!paletteSlug) {
        const pathMatch = window.location.pathname.match(/\/palette\/([a-z0-9-]+)\/?$/i);
        if (pathMatch) {
            paletteSlug = pathMatch[1];
        }
    }

    const paletteDetail = document.getElementById('paletteDetail');
    const paletteSkeleton = document.getElementById('paletteSkeleton');
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
    const colorInfoGrid = document.getElementById('colorInfoGrid');
    const brightnessChart = document.getElementById('brightnessChart');
    const paletteIdText = document.getElementById('paletteIdText');
    const copyAllBtn = document.getElementById('copyAllBtn');
    const copyCssVarsBtn = document.getElementById('copyCssVarsBtn');

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

    if (paletteIdText && paletteSlug) {
        paletteIdText.textContent = `slug: ${paletteSlug}`;
    }


    if (!paletteSlug) {
        paletteError?.classList.remove('hidden');
        return;
    }

    let palette = null;
    let allPalettes = [];

    try {
        // Try direct API lookup by slug or ID first
        if (window.ColorMagic && window.ColorMagic.api) {
            try {
                palette = await window.ColorMagic.api.getPaletteById(paletteSlug);
            } catch (e) {
                // Direct lookup failed, will try bulk fetch fallback below
            }
        }

        // Fallback: fetch all palettes and match by slug client-side
        if (!palette) {
            try {
                const palPromise = (window.ColorMagic && window.ColorMagic.api)
                    ? window.ColorMagic.api.getPalettes({ limit: 1000 })
                    : fetch('/api/palettes.json').then(r => r.json()).then(d => ({ data: d }));

                const result = await palPromise;
                allPalettes = (result && result.data) ? result.data : (Array.isArray(result) ? result : []);

                // Helper: generate slug from palette name
                function slugify(name) {
                    return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                }

                // Detect duplicate names for slug-with-id resolution
                const nameCount = {};
                allPalettes.forEach(p => {
                    const s = slugify(p.name || '');
                    nameCount[s] = (nameCount[s] || 0) + 1;
                });

                // Match slug (with duplicate ID suffix for duplicate names) or exact ID
                palette = allPalettes.find((item) => {
                    if (item.id === paletteSlug) return true;
                    let s = slugify(item.name || '');
                    if (nameCount[s] > 1) {
                        s = s + '-' + item.id.replace('palette_', '');
                    }
                    return s === paletteSlug;
                }) || null;
            } catch (fallbackErr) {
                console.warn("Fallback palette search failed:", fallbackErr);
            }
        }

        if (!palette) {
            paletteSkeleton?.classList.add('hidden');
            paletteError?.classList.remove('hidden');
            return;
        }

        document.title = `${palette.name} Color Palette | Color Magic`;
        if (paletteName) paletteName.textContent = palette.name;

        const paletteBreadcrumb = document.getElementById('paletteBreadcrumb');
        if (paletteBreadcrumb) paletteBreadcrumb.textContent = palette.name;

        const paletteSubheading = document.getElementById('paletteSubheading');
        if (paletteSubheading) paletteSubheading.textContent = `Curated ${(palette.style || 'Custom')} Color Scheme`;

        const paletteDescription = document.getElementById('paletteDescription');
        if (paletteDescription) {
            const count = Array.isArray(palette.colors) ? palette.colors.length : 0;
            paletteDescription.textContent = `Complete ${count}-color palette profile with copy-ready HEX, RGB, and CSS variable values.`;
        }

        const colorList = Array.isArray(palette.colors) ? palette.colors : [];
        const firstColor = colorList[0] || '#111111';
        const lastColor = colorList[colorList.length - 1] || '#f8fafc';
        const allColors = colorList.join(', ');
        const cssVars = colorList.map((color, index) => `--palette-color-${index + 1}: ${color};`).join('\n');

        if (paletteMeta) paletteMeta.textContent = `Palette ID: ${palette.id}`;
        if (paletteType) paletteType.textContent = palette.style || 'General';
        if (paletteCount) paletteCount.textContent = String(colorList.length);

        if (heroPaletteStrips) {
            heroPaletteStrips.style.gridTemplateColumns = `repeat(${colorList.length}, minmax(0, 1fr))`;
            heroPaletteStrips.innerHTML = colorList.map((color) => `
                <div class="h-full flex items-end justify-center p-3" style="background-color:${color};">
                    <span class="text-[10px] md:text-xs font-bold font-mono px-2 py-1 rounded-lg bg-black/25 text-white">${color}</span>
                </div>
            `).join('');
        }

        if (detailColorList) {
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
        }

        if (contrastCardA) {
            contrastCardA.style.backgroundColor = firstColor;
            contrastCardA.style.color = lastColor;
            contrastCardA.innerHTML = `
                <p class="text-xs uppercase tracking-wider opacity-80">Contrast Preview A</p>
                <p class="text-2xl md:text-3xl font-bold">Aa</p>
                <p class="text-sm">Background: ${firstColor} · Text: ${lastColor}</p>
            `;
        }

        if (contrastCardB) {
            contrastCardB.style.backgroundColor = lastColor;
            contrastCardB.style.color = firstColor;
            contrastCardB.innerHTML = `
                <p class="text-xs uppercase tracking-wider opacity-80">Contrast Preview B</p>
                <p class="text-2xl md:text-3xl font-bold">Aa</p>
                <p class="text-sm">Background: ${lastColor} · Text: ${firstColor}</p>
            `;
        }

        if (darkColorParagraph) {
            darkColorParagraph.style.color = firstColor;
            darkColorParagraph.textContent = `${palette.name} starts with ${firstColor} as the anchor tone, giving the palette a strong visual base. As the colors progress to ${lastColor}, the composition opens into brighter accents that are great for UI highlights, typography emphasis, and layered backgrounds. This progression creates a clear rhythm that helps designs feel intentional instead of random.`;
        }

        // Color Information Grid
        const colorRoute = (window.CM_COLOR_BASE || 'color/');
        if (colorInfoGrid) {
            colorInfoGrid.innerHTML = colorList.map((color) => {
            const rgb = hexToRgb(color);
            const hsl = hexToHsl(color);
            const lum = Math.round(getLuminance(color) * 100);
            const bareHex = color.replace('#', '');
            return `
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl overflow-hidden flex flex-col">
                    <div class="h-16 w-full" style="background:${color}"></div>
                    <div class="p-4 flex flex-col gap-3 flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-mono font-bold text-base">${color}</p>
                            <a href="${colorRoute}${bareHex}/" class="p-1.5 rounded-lg text-slate-400 hover:text-secondary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="View ${color} details">
                                <i class="bi bi-box-arrow-up-right text-sm"></i>
                            </a>
                        </div>
                        <div class="space-y-1.5 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 text-xs uppercase tracking-wider">RGB</span>
                                <span class="font-mono text-xs font-semibold">${rgb.r}, ${rgb.g}, ${rgb.b}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 text-xs uppercase tracking-wider">HSL</span>
                                <span class="font-mono text-xs font-semibold">${hsl.h}°, ${hsl.s}%, ${hsl.l}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 text-xs uppercase tracking-wider">Brightness</span>
                                <span class="font-mono text-xs font-semibold">${lum}%</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-auto pt-1">
                            <button class="copy-color-hex-btn flex-1 px-3 py-2 bg-white dark:bg-slate-700 hover:bg-primary hover:text-white text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all border border-slate-200 dark:border-slate-600 flex items-center justify-center gap-1.5" data-hex="${color}">
                                <i class="bi bi-clipboard text-xs"></i> HEX
                            </button>
                            <button class="copy-color-rgb-btn flex-1 px-3 py-2 bg-white dark:bg-slate-700 hover:bg-primary hover:text-white text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all border border-slate-200 dark:border-slate-600 flex items-center justify-center gap-1.5" data-rgb="rgb(${rgb.r}, ${rgb.g}, ${rgb.b})">
                                <i class="bi bi-clipboard text-xs"></i> RGB
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

        // Brightness Chart
        if (brightnessChart && colorList.length > 0) {
            const maxBrightness = Math.max(...colorList.map(c => getLuminance(c))) || 1;
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
        }

        detailColorList?.addEventListener('click', async (event) => {
            const btn = event.target.closest('.copy-single-color');
            if (!btn) return;

            const color = btn.dataset.color || '';
            if (!color) return;

            const resetText = 'Copy';
            await copyText(color, btn, 'Copied', 'Failed', resetText);
        });

        // Copy color HEX / RGB buttons in color info grid
        colorInfoGrid?.addEventListener('click', async (event) => {
            const hexBtn = event.target.closest('.copy-color-hex-btn');
            if (hexBtn) {
                const origHTML = hexBtn.innerHTML;
                await copyText(hexBtn.dataset.hex, hexBtn, '<i class="bi bi-check-circle text-xs"></i> HEX', '<i class="bi bi-x-circle text-xs"></i> HEX', origHTML);
                return;
            }
            const rgbBtn = event.target.closest('.copy-color-rgb-btn');
            if (rgbBtn) {
                const origHTML = rgbBtn.innerHTML;
                await copyText(rgbBtn.dataset.rgb, rgbBtn, '<i class="bi bi-check-circle text-xs"></i> RGB', '<i class="bi bi-x-circle text-xs"></i> RGB', origHTML);
            }
        });

        // --- Export Format Data ---
        const hexArrayFormat = `[\n  ${colorList.map(c => `'${c}'`).join(',\n  ')}\n]`;
        const cssVarsFullFormat = `:root {\n${colorList.map((c, i) => `  --palette-color-${i + 1}: ${c};`).join('\n')}\n}`;
        const jsonFormat = JSON.stringify({ name: palette.name, colors: colorList, style: palette.style }, null, 2);
        const scssFormat = colorList.map((c, i) => `$palette-${i + 1}: ${c};`).join('\n');
        const tailwindFormat = `colors: {\n  ${colorList.map((c, i) => `palette-${i + 1}: '${c}'`).join(',\n  ')}\n}`;
        const rgbArrayFormat = `[\n${colorList.map(c => {
            const rgb = hexToRgb(c);
            return `  rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;
        }).join(',\n')}\n]`;
        const hslArrayFormat = `[\n${colorList.map(c => {
            const hsl = hexToHsl(c);
            return `  hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`;
        }).join(',\n')}\n]`;
        const androidFormat = `<?xml version="1.0" encoding="utf-8"?>\n<resources>\n${colorList.map((c, i) => `  <color name="palette_${i + 1}">${c}</color>`).join('\n')}\n</resources>`;
        const swiftFormat = `import UIKit\n\nextension UIColor {\n${colorList.map((c, i) => {
            const rgb = hexToRgb(c);
            return `    static let palette${i + 1} = UIColor(red: ${(rgb.r / 255).toFixed(3)}, green: ${(rgb.g / 255).toFixed(3)}, blue: ${(rgb.b / 255).toFixed(3)}, alpha: 1.0)`;
        }).join('\n')}\n}`;

        // Populate each export section's code block
        const exportSections = [
            { codeEl: 'hexArrayCode', copyBtnId: 'copyHexArrayBtn', data: hexArrayFormat },
            { codeEl: 'cssVarsCode',  copyBtnId: 'copyCssVarsExportBtn', data: cssVarsFullFormat },
            { codeEl: 'jsonCode',     copyBtnId: 'copyJsonBtn', data: jsonFormat },
            { codeEl: 'scssCode',     copyBtnId: 'copyScssBtn', data: scssFormat },
            { codeEl: 'tailwindCode', copyBtnId: 'copyTailwindBtn', data: tailwindFormat },
            { codeEl: 'rgbArrayCode', copyBtnId: 'copyRgbArrayBtn', data: rgbArrayFormat },
            { codeEl: 'hslArrayCode', copyBtnId: 'copyHslArrayBtn', data: hslArrayFormat },
            { codeEl: 'androidCode',  copyBtnId: 'copyAndroidBtn', data: androidFormat },
            { codeEl: 'swiftCode',    copyBtnId: 'copySwiftBtn', data: swiftFormat }
        ];

        const copyIconHTML = '<i class="bi bi-clipboard me-1"></i>Copy';

        exportSections.forEach(({ codeEl, copyBtnId, data }) => {
            const codeBlock = document.getElementById(codeEl);
            const btn = document.getElementById(copyBtnId);
            if (codeBlock) codeBlock.textContent = data;
            btn?.addEventListener('click', async () => {
                await copyText(data, btn, '<i class="bi bi-check-circle me-1"></i>Copied', '<i class="bi bi-x-circle me-1"></i>Failed', copyIconHTML);
            });
        });

        copyAllBtn?.addEventListener('click', () => {
            window.ColorMagic.animateCopy(copyAllBtn, allColors, 'Copied All!');
        });

        copyCssVarsBtn?.addEventListener('click', () => {
            window.ColorMagic.animateCopy(copyCssVarsBtn, cssVars, 'Copied CSS Vars!');
        });

        // Related Colors — derive complementary, analogous, triadic from palette colors
        const relatedColorsGrid = document.getElementById('relatedColorsGrid');
        if (relatedColorsGrid) {
            function hslToHex(h, s, l) {
                s /= 100; l /= 100;
                const c = (1 - Math.abs(2 * l - 1)) * s;
                const x = c * (1 - Math.abs((h / 60) % 2 - 1));
                const m = l - c / 2;
                let r, g, b;
                if (h < 60) { r=c; g=x; b=0; }
                else if (h < 120) { r=x; g=c; b=0; }
                else if (h < 180) { r=0; g=c; b=x; }
                else if (h < 240) { r=0; g=x; b=c; }
                else if (h < 300) { r=x; g=0; b=c; }
                else { r=c; g=0; b=x; }
                const toHex = v => { const hex = Math.round((v + m) * 255).toString(16); return hex.length === 1 ? '0' + hex : hex; };
                return '#' + toHex(r) + toHex(g) + toHex(b);
            }

            const seen = {};
            const relatedColors = [];
            colorList.forEach(color => {
                const hsl = hexToHsl(color);
                const rules = [
                    { label: 'Complementary', h: (hsl.h + 180) % 360 },
                    { label: 'Analogous',     h: (hsl.h + 30) % 360 },
                    { label: 'Analogous',     h: (hsl.h + 330) % 360 },
                    { label: 'Triadic',       h: (hsl.h + 120) % 360 },
                    { label: 'Triadic',       h: (hsl.h + 240) % 360 }
                ];
                rules.forEach(rule => {
                    const hex = hslToHex(rule.h, hsl.s, hsl.l);
                    if (!seen[hex]) { seen[hex] = true; relatedColors.push({ hex, label: rule.label }); }
                });
            });

            const colorRoute = (window.CM_COLOR_BASE || 'color/');
            relatedColors.slice(0, 6).forEach(c => {
                const rgb = hexToRgb(c.hex);
                const card = document.createElement('a');
                card.href = colorRoute + c.hex.replace('#', '') + '/';
                card.className = 'group block rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all';
                card.innerHTML =
                    '<div class="h-20 relative" style="background:' + c.hex + '">'
                    + '<div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20">'
                    + '<span class="px-2 py-1 bg-white/90 dark:bg-slate-900/90 rounded-lg text-[10px] font-bold shadow">' + c.label + '</span>'
                    + '</div></div>'
                    + '<div class="bg-white dark:bg-slate-900 p-2.5">'
                    + '<p class="font-mono text-xs font-bold">' + c.hex + '</p>'
                    + '<p class="text-[10px] text-slate-400 mt-0.5">RGB(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')</p>'
                    + '</div>';
                relatedColorsGrid.appendChild(card);
            });
        }

        // ── Related Gradients ─────────────────────────────────────────────
        const relatedGradientsGrid = document.getElementById('relatedGradientsGrid');
        if (relatedGradientsGrid) {
            const gradPromise = (window.ColorMagic && window.ColorMagic.api)
                ? window.ColorMagic.api.getGradients({ limit: 100 })
                : fetch('https://colormagic-api.techkreative.com/data/gradients.json').then(r => r.json()).then(d => ({ data: d }));

            gradPromise
                .then(res => {
                    const gradients = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
                    const gradientBase = (window.location.pathname.indexOf('/ColorMagic') === 0) ? '/ColorMagic/gradient/' : '/gradient/';
                    const exact = [];
                    const near = [];

                    function colorDistSq(a, b) {
                        const ar = parseInt(a.substring(0, 2), 16), ag = parseInt(a.substring(2, 4), 16), ab = parseInt(a.substring(4, 6), 16);
                        const br = parseInt(b.substring(0, 2), 16), bg = parseInt(b.substring(2, 4), 16), bb = parseInt(b.substring(4, 6), 16);
                        const dr = ar - br, dg = ag - bg, db = ab - bb;
                        return dr * dr + dg * dg + db * db;
                    }

                    const paletteHexes = colorList.map(c => c.replace('#', '').toLowerCase());

                    gradients.forEach(g => {
                        if (g.id === palette.id) return;
                        let hasExact = false;
                        let minDist = Infinity;

                        g.colors.forEach(gc => {
                            const gHex = gc.replace('#', '').toLowerCase();
                            paletteHexes.forEach(ph => {
                                if (gHex === ph) hasExact = true;
                                else {
                                    const d = colorDistSq(gHex, ph);
                                    if (d < minDist) minDist = d;
                                }
                            });
                        });

                        if (hasExact) exact.push(g);
                        else if (minDist < 15000) { g._dist = minDist; near.push(g); }
                    });

                    near.sort((a, b) => a._dist - b._dist);
                    const results = exact.concat(near).slice(0, 6);

                    results.forEach(g => {
                        if (window.ColorMagic && window.ColorMagic.createGradientCard) {
                            relatedGradientsGrid.appendChild(window.ColorMagic.createGradientCard(g));
                        }
                    });
                })
                .catch(() => {});
        }

        // ── Related Palettes ──────────────────────────────────────────────
        const relatedPalettesGrid = document.getElementById('relatedPalettesGrid');
        if (relatedPalettesGrid) {
            function renderRelated(palList) {
                if (!Array.isArray(palList) || palList.length === 0) return;
                function colorDistSqP(a, b) {
                    const ar = parseInt(a.substring(0, 2), 16), ag = parseInt(a.substring(2, 4), 16), ab = parseInt(a.substring(4, 6), 16);
                    const br = parseInt(b.substring(0, 2), 16), bg = parseInt(b.substring(2, 4), 16), bb = parseInt(b.substring(4, 6), 16);
                    const dr = ar - br, dg = ag - bg, db = ab - bb;
                    return dr * dr + dg * dg + db * db;
                }

                const paletteHexes = colorList.map(c => c.replace('#', '').toLowerCase());
                const scored = [];

                palList.forEach(p => {
                    if (!p || p.id === palette.id) return;
                    const pHexes = (p.colors || []).map(c => c.replace('#', '').toLowerCase());
                    let matchCount = 0;
                    let totalDist = 0;

                    paletteHexes.forEach(ph => {
                        let minD = Infinity;
                        pHexes.forEach(ch => {
                            if (ph === ch) { matchCount++; minD = 0; }
                            else {
                                const d = colorDistSqP(ph, ch);
                                if (d < minD) minD = d;
                            }
                        });
                        totalDist += minD;
                    });

                    if (matchCount > 0 || totalDist / paletteHexes.length < 20000) {
                        scored.push({ palette: p, score: matchCount * 100000 - totalDist });
                    }
                });

                scored.sort((a, b) => b.score - a.score);
                const relatedPaletteResults = scored.slice(0, 6).map(s => s.palette);

                if (relatedPaletteResults.length > 0 && window.ColorMagic && window.ColorMagic.createPaletteCard) {
                    window.ColorMagic.markDuplicateSlugs(relatedPaletteResults);
                    const fragment = document.createDocumentFragment();
                    relatedPaletteResults.forEach(p => {
                        fragment.appendChild(window.ColorMagic.createPaletteCard(p));
                    });
                    relatedPalettesGrid.innerHTML = '';
                    relatedPalettesGrid.appendChild(fragment);
                }
            }

            try {
                if (allPalettes && allPalettes.length > 0) {
                    renderRelated(allPalettes);
                } else if (window.ColorMagic && window.ColorMagic.api) {
                    window.ColorMagic.api.getPalettes({ limit: 60 })
                        .then(res => {
                            const list = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
                            renderRelated(list);
                        })
                        .catch(() => {});
                }
            } catch (e) {
                console.warn("Related palettes failed:", e);
            }
        }

        // Favorite palette button
        const favPaletteBtn = document.getElementById('favPaletteBtn');
        if (favPaletteBtn && window.ColorMagic && window.ColorMagic.Favorites) {
            const { Favorites } = window.ColorMagic;
            function updateFavPaletteBtn() {
                const isFav = Favorites.isFavorite(palette.id);
                const icon = favPaletteBtn.querySelector('i');
                const span = favPaletteBtn.querySelector('span');
                if (icon) icon.className = 'bi ' + (isFav ? 'bi-heart-fill text-red-500' : 'bi-heart');
                if (span) span.textContent = isFav ? 'Favorited' : 'Favorite';
                favPaletteBtn.classList.toggle('text-red-500', isFav);
            }
            updateFavPaletteBtn();
            favPaletteBtn.addEventListener('click', () => {
                Favorites.toggleFavorite(palette.id);
                const isFav = Favorites.isFavorite(palette.id);
                if (window.ColorMagic.animateFavorite) {
                    window.ColorMagic.animateFavorite(favPaletteBtn, isFav);
                } else {
                    updateFavPaletteBtn();
                }
            });
        }

        // Download PNG button
        const downloadPngBtn = document.getElementById('downloadPalettePngBtn');
        if (downloadPngBtn && window.ColorMagic && window.ColorMagic.exportPaletteImage) {
            downloadPngBtn.addEventListener('click', () => {
                if (window.ColorMagic.animateDownload) {
                    window.ColorMagic.animateDownload(downloadPngBtn, () => {
                        window.ColorMagic.exportPaletteImage(palette);
                    });
                } else {
                    window.ColorMagic.exportPaletteImage(palette);
                }
            });
        }

        paletteSkeleton?.classList.add('hidden');
        paletteDetail?.classList.remove('hidden');
    } catch (err) {
        console.error("Error loading palette page:", err);
        paletteSkeleton?.classList.add('hidden');
        paletteError?.classList.remove('hidden');
    }
})();
