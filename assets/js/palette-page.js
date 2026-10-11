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
    const brightnessChart = document.getElementById('brightnessChart');
    const paletteIdText = document.getElementById('paletteIdText');


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

    let colorDictCache = null;
    async function loadColorDict() {
        if (colorDictCache) return colorDictCache;
        try {
            const res = await fetch('/api/color-names.json');
            if (res.ok) {
                colorDictCache = await res.json();
                return colorDictCache;
            }
        } catch (_) {}
        return null;
    }

    function findColorName(hex, dict) {
        if (!hex) return '';
        const clean = hex.replace('#', '').toUpperCase();
        if (dict && dict[clean]) {
            const entry = dict[clean];
            return typeof entry === 'object' ? (entry.name || clean) : String(entry);
        }
        if (!dict) return hex;
        const target = hexToRgb(clean);
        let minD = Infinity;
        let bestName = hex;
        for (const k in dict) {
            const rgb = hexToRgb(k);
            const d = (rgb.r - target.r) * (rgb.r - target.r) + (rgb.g - target.g) * (rgb.g - target.g) + (rgb.b - target.b) * (rgb.b - target.b);
            if (d < minD) {
                minD = d;
                const e = dict[k];
                bestName = typeof e === 'object' ? (e.name || k) : String(e);
                if (d === 0) break;
            }
        }
        return bestName;
    }

    function showFloatingToast(msg) {
        const fb = document.getElementById('copyFeedback');
        if (!fb) return;
        fb.textContent = msg || 'Copied to clipboard';
        fb.classList.remove('opacity-0', '-translate-y-1');
        fb.classList.add('opacity-100', 'translate-y-0');
        clearTimeout(fb._timer);
        fb._timer = setTimeout(() => {
            fb.classList.remove('opacity-100', 'translate-y-0');
            fb.classList.add('opacity-0', '-translate-y-1');
        }, 2000);
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
        if (paletteType) paletteType.textContent = palette.style || 'Curated';
        if (paletteCount) paletteCount.textContent = String(colorList.length);

        // Render Hero Preview (clean vertical color strips with hex label at bottom)
        if (heroPaletteStrips) {
            heroPaletteStrips.style.gridTemplateColumns = `repeat(${colorList.length}, minmax(0, 1fr))`;
            heroPaletteStrips.innerHTML = colorList.map((color) => `
                <div class="h-full flex items-end justify-center p-3 cursor-pointer group/strip transition-opacity hover:opacity-95" style="background-color:${color};" data-color="${color}" title="Click to copy ${color}">
                    <span class="text-[10px] md:text-xs font-bold font-mono px-2 py-1 rounded-lg bg-black/25 text-white shadow-sm group-hover/strip:bg-black/45 transition-colors">${color}</span>
                </div>
            `).join('');

            heroPaletteStrips.addEventListener('click', (e) => {
                const strip = e.target.closest('[data-color]');
                if (!strip) return;
                const c = strip.dataset.color;
                if (c) {
                    navigator.clipboard.writeText(c).then(() => {
                        showFloatingToast(`Copied ${c}!`);
                        const span = strip.querySelector('span');
                        if (span) {
                            const orig = span.textContent;
                            span.textContent = 'Copied!';
                            setTimeout(() => { span.textContent = orig; }, 1200);
                        }
                    });
                }
            });
        }

        // Color Swatches list with 1-click interactive copy chips
        function renderDetailSwatches(names) {
            if (!detailColorList) return;
            detailColorList.innerHTML = '';
            colorList.forEach((color, idx) => {
                const rgb = hexToRgb(color);
                const name = (names && names[idx]) ? names[idx] : '';
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'copy-swatch-chip group flex items-center gap-2.5 bg-slate-50 hover:bg-white dark:bg-slate-800/60 dark:hover:bg-slate-800 rounded-xl px-3 py-2.5 border border-slate-200 hover:border-primary/50 dark:border-slate-700/60 dark:hover:border-primary/50 transition-all shadow-sm hover:shadow text-left min-w-0';
                btn.title = `Click to copy ${color}`;
                btn.innerHTML =
                    `<span class="w-6 h-6 rounded-lg shrink-0 shadow-sm border border-black/10" style="background:${color}"></span>`
                    + `<div class="min-w-0 flex-1">`
                    +   `<div class="flex items-center gap-1.5">`
                    +     `<span class="text-xs font-mono font-bold text-slate-800 dark:text-slate-100">${color}</span>`
                    +     (name && name !== color ? `<span class="text-[11px] text-slate-400 truncate font-medium">${name}</span>` : '')
                    +   `</div>`
                    +   `<span class="text-[10px] text-slate-400 font-mono">RGB(${rgb.r}, ${rgb.g}, ${rgb.b})</span>`
                    + `</div>`
                    + `<i class="bi bi-clipboard text-slate-400 group-hover:text-primary text-xs transition-colors shrink-0"></i>`;
                btn.addEventListener('click', () => {
                    navigator.clipboard.writeText(color).then(() => {
                        showFloatingToast(`Copied ${color}!`);
                        btn.classList.add('ring-2', 'ring-emerald-500');
                        setTimeout(() => { btn.classList.remove('ring-2', 'ring-emerald-500'); }, 1200);
                    });
                });
                detailColorList.appendChild(btn);
            });
        }
        renderDetailSwatches(palette.colorNames || null);

        // Async resolve color names to populate swatch list
        loadColorDict().then(dict => {
            if (!dict) return;
            const names = colorList.map(hex => findColorName(hex, dict));
            palette.colorNames = names;
            renderDetailSwatches(names);
        });

        // Contrast Preview Cards
        // Check if both colors are dark; if so, pick a white/light color from the palette,
        // or fall back to white (#FFFFFF). Symmetrically handle if both colors are light.
        const isFirstDark = getLuminance(firstColor) < 0.45;
        const isLastDark = getLuminance(lastColor) < 0.45;

        let textA = lastColor;
        let textB = firstColor;

        if (isFirstDark && isLastDark) {
            const lightColorInPalette = colorList
                .filter(c => getLuminance(c) >= 0.60)
                .sort((a, b) => getLuminance(b) - getLuminance(a))[0];

            const replacementLight = lightColorInPalette || '#FFFFFF';
            textA = replacementLight;
            textB = replacementLight;
        } else if (!isFirstDark && !isLastDark) {
            const darkColorInPalette = colorList
                .filter(c => getLuminance(c) <= 0.35)
                .sort((a, b) => getLuminance(a) - getLuminance(b))[0];

            const replacementDark = darkColorInPalette || '#0F172A';
            textA = replacementDark;
            textB = replacementDark;
        }

        if (contrastCardA) {
            contrastCardA.style.backgroundColor = firstColor;
            contrastCardA.style.color = textA;
            contrastCardA.innerHTML = `
                <p class="text-xs uppercase tracking-wider opacity-80">Contrast Preview A</p>
                <p class="text-2xl md:text-3xl font-bold">Aa</p>
                <p class="text-sm">Background: ${firstColor} · Text: ${textA}</p>
            `;
        }

        if (contrastCardB) {
            contrastCardB.style.backgroundColor = lastColor;
            contrastCardB.style.color = textB;
            contrastCardB.innerHTML = `
                <p class="text-xs uppercase tracking-wider opacity-80">Contrast Preview B</p>
                <p class="text-2xl md:text-3xl font-bold">Aa</p>
                <p class="text-sm">Background: ${lastColor} · Text: ${textB}</p>
            `;
        }

        if (darkColorParagraph) {
            darkColorParagraph.style.color = firstColor;
            darkColorParagraph.textContent = `${palette.name} starts with ${firstColor} as the anchor tone, giving the palette a strong visual base. As the colors progress to ${lastColor}, the composition opens into brighter accents that are great for UI highlights, typography emphasis, and layered backgrounds. This progression creates a clear rhythm that helps designs feel intentional instead of random.`;
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

        // Tabbed Export Data & Switcher
        const exportFormats = {
            json: {
                desc: 'Structured JSON format for APIs, AI prompts, and data exchange',
                code: jsonFormat
            },
            hex: {
                desc: 'Standard hex color array for JavaScript and CSS',
                code: hexArrayFormat
            },
            css: {
                desc: 'CSS custom properties for use in stylesheets with :root declaration',
                code: cssVarsFullFormat
            },
            tailwind: {
                desc: 'Extend your tailwind.config.js theme with custom palette colors',
                code: tailwindFormat
            },
            scss: {
                desc: 'Sass/SCSS variable declarations for preprocessor workflows',
                code: scssFormat
            },
            rgb: {
                desc: 'RGB functional notation for CSS and canvas rendering',
                code: rgbArrayFormat
            },
            hsl: {
                desc: 'HSL notation for dynamic color manipulation in CSS',
                code: hslArrayFormat
            },
            android: {
                desc: 'Android resource file format for colors.xml',
                code: androidFormat
            },
            swift: {
                desc: 'Swift extension for iOS/macOS UIColor declarations',
                code: swiftFormat
            }
        };

        let activeTab = 'json';
        const codePre = document.getElementById('paletteExportActiveCode');
        const descEl = document.getElementById('paletteExportDescription');
        const copyBtn = document.getElementById('copyPaletteActiveExportBtn');
        const tabBtns = document.querySelectorAll('#paletteExportTabs .export-tab-btn');

        function updateExportTab(key) {
            if (!exportFormats[key]) return;
            activeTab = key;
            if (codePre) codePre.textContent = exportFormats[key].code;
            if (descEl) descEl.textContent = exportFormats[key].desc;

            tabBtns.forEach(btn => {
                const isActive = btn.dataset.tab === key;
                if (isActive) {
                    btn.className = 'export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap bg-primary text-white shadow-sm flex items-center gap-1.5';
                } else {
                    btn.className = 'export-tab-btn px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/80 flex items-center gap-1.5';
                }
            });
        }

        // Initialize with JSON
        updateExportTab('json');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                updateExportTab(btn.dataset.tab);
            });
        });

        // Copy button copies active code
        if (copyBtn) {
            const origCopyHTML = copyBtn.innerHTML;
            copyBtn.addEventListener('click', async () => {
                const activeData = exportFormats[activeTab] ? exportFormats[activeTab].code : '';
                await copyText(
                    activeData,
                    copyBtn,
                    '<i class="bi bi-check-circle-fill"></i><span>Copied!</span>',
                    '<i class="bi bi-x-circle"></i><span>Failed</span>',
                    origCopyHTML
                );
            });
        }

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
                : fetch('/api/gradients.json').then(r => r.json()).then(d => ({ data: d }));

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
