/**
 * gradient-page.js
 * Renders a single gradient detail page at /gradient/{id}/
 */

(function () {
    'use strict';

    // ─── Helpers ──────────────────────────────────────────────────────────────

    function hexToRgb(hex) {
        var h = hex.replace('#', '');
        if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
        var n = parseInt(h, 16);
        return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
    }

    function hexToHsl(hex) {
        var c = hexToRgb(hex);
        var r = c.r / 255, g = c.g / 255, b = c.b / 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;
        if (max === min) { h = s = 0; }
        else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
    }

    function getLuminance(hex) {
        var c = hexToRgb(hex);
        return Math.round((0.299 * c.r + 0.587 * c.g + 0.114 * c.b) / 2.55);
    }

    function copyToClipboard(text, btn, origHTML) {
        navigator.clipboard.writeText(text).then(function () {
            var icon = btn.querySelector('i');
            if (icon) icon.className = 'bi bi-check-circle-fill';
            setTimeout(function () {
                if (origHTML) btn.innerHTML = origHTML;
            }, 2000);
        }).catch(function () {});
    }

    // ─── URL ID extraction ───────────────────────────────────────────────────

    function getGradientId() {
        // /gradient/gradient_1/ or /gradient/gradient_1
        var match = window.location.pathname.match(/\/gradient\/([^\/\?]+)/);
        if (match) return decodeURIComponent(match[1]);
        // Fallback to query param
        var params = new URLSearchParams(window.location.search);
        return params.get('id') || '';
    }

    var colorDictCache = null;
    function loadColorDict() {
        if (colorDictCache) return Promise.resolve(colorDictCache);
        return fetch('/api/color-names.json')
            .then(function (r) { return r.json(); })
            .then(function (dict) {
                colorDictCache = dict;
                return dict;
            })
            .catch(function () { return null; });
    }

    function findColorName(hex, dict) {
        if (!hex) return '';
        var clean = hex.replace('#', '').toUpperCase();
        if (dict && dict[clean]) {
            var entry = dict[clean];
            return typeof entry === 'object' ? (entry.name || clean) : String(entry);
        }
        if (!dict) return hex;
        var target = hexToRgb(clean);
        var minD = Infinity;
        var bestName = hex;
        for (var k in dict) {
            var rgb = hexToRgb(k);
            var d = (rgb.r - target.r) * (rgb.r - target.r) + (rgb.g - target.g) * (rgb.g - target.g) + (rgb.b - target.b) * (rgb.b - target.b);
            if (d < minD) {
                minD = d;
                var e = dict[k];
                bestName = typeof e === 'object' ? (e.name || k) : String(e);
                if (d === 0) break;
            }
        }
        return bestName;
    }

    function showCopyFeedback(msg) {
        var fb = document.getElementById('copyFeedback');
        if (!fb) return;
        fb.textContent = msg || 'Copied to clipboard';
        fb.classList.remove('opacity-0', '-translate-y-1');
        fb.classList.add('opacity-100', 'translate-y-0');
        clearTimeout(fb._timer);
        fb._timer = setTimeout(function () {
            fb.classList.remove('opacity-100', 'translate-y-0');
            fb.classList.add('opacity-0', '-translate-y-1');
        }, 2000);
    }

    // ─── Render functions ─────────────────────────────────────────────────────

    function renderGradient(g) {
        var detail = document.getElementById('gradientDetail');
        var error  = document.getElementById('gradientError');
        var skeleton = document.getElementById('gradientSkeleton');
        if (!detail || !error) return;

        if (skeleton) skeleton.classList.add('hidden');
        detail.classList.remove('hidden');

        // ID text
        var idText = document.getElementById('gradientIdText');
        if (idText) idText.textContent = 'ID: ' + g.id;

        // Name & meta
        var nameEl = document.getElementById('gradientName');
        if (nameEl) nameEl.textContent = g.name;

        var breadcrumbEl = document.getElementById('gradientBreadcrumb');
        if (breadcrumbEl) breadcrumbEl.textContent = g.name;

        var subheadEl = document.getElementById('gradientSubheading');
        if (subheadEl) {
            subheadEl.textContent = (g.style || 'Modern') + ' · ' + g.type.charAt(0).toUpperCase() + g.type.slice(1) + ' Gradient';
        }

        var descEl = document.getElementById('gradientDescription');
        if (descEl) {
            var stopCount = Array.isArray(g.colors) ? g.colors.length : 2;
            descEl.textContent = 'CSS ' + g.type + ' gradient blending ' + stopCount + ' curated colors with copy-ready code and visual stops.';
        }

        var metaEl = document.getElementById('gradientMeta');
        if (metaEl) {
            metaEl.textContent = 'Gradient ID: ' + g.id + ' · ' + g.style;
        }

        // Type chip
        document.getElementById('gradientType').textContent = g.type.charAt(0).toUpperCase() + g.type.slice(1);

        // Color count
        document.getElementById('gradientColorCount').textContent = g.colors.length;

        // Angle or shape
        var angleShapeLabel = document.getElementById('angleShapeLabel');
        var angleShapeValue = document.getElementById('gradientAngleShape');
        if (g.type === 'linear' && g.angle !== undefined) {
            angleShapeLabel.textContent = 'Angle';
            angleShapeValue.textContent = g.angle + '°';
        } else if (g.shape) {
            angleShapeLabel.textContent = 'Shape';
            angleShapeValue.textContent = g.shape.charAt(0).toUpperCase() + g.shape.slice(1);
        } else {
            angleShapeLabel.textContent = 'Style';
            angleShapeValue.textContent = g.type.charAt(0).toUpperCase() + g.type.slice(1);
        }

        // Hero preview background
        var hero = document.getElementById('heroGradientPreview');
        hero.style.background = g.css;

        // Contrast calculation for hero text overlay
        var totalLum = 0;
        g.colors.forEach(function (hex) {
            totalLum += getLuminance(hex);
        });
        var avgLum = totalLum / Math.max(g.colors.length, 1);
        var isLight = avgLum > 62;
        var heroOverlay = document.getElementById('heroOverlayText');
        if (heroOverlay) {
            if (isLight) {
                heroOverlay.style.color = '#0f172a';
                heroOverlay.style.textShadow = '0 1px 3px rgba(255,255,255,0.75)';
            } else {
                heroOverlay.style.color = '#FFFFFF';
                heroOverlay.style.textShadow = '0 2px 10px rgba(0,0,0,0.55)';
            }
        }

        // Write Gradient Name on the gradient (matches color page style)
        var heroNameEl = document.getElementById('heroGradientName');
        if (heroNameEl) {
            heroNameEl.textContent = g.name;
        }

        // Small text with color names on the gradient
        function renderHeroColorStops(names) {
            var heroColorsEl = document.getElementById('heroGradientColors');
            if (!heroColorsEl) return;
            heroColorsEl.innerHTML = '';
            g.colors.forEach(function (hex, idx) {
                var name = (names && names[idx]) ? names[idx] : hex;
                var item = document.createElement('span');
                item.className = 'inline-flex items-center gap-1.5';
                item.innerHTML =
                    '<span class="w-2.5 h-2.5 rounded-full inline-block shadow-sm ring-1 ring-white/50" style="background:' + hex + '"></span>'
                    + '<span class="font-bold">' + name + '</span>'
                    + (name !== hex ? '<span class="font-mono text-xs opacity-85">(' + hex + ')</span>' : '');
                heroColorsEl.appendChild(item);
                if (idx < g.colors.length - 1) {
                    var arrow = document.createElement('span');
                    arrow.className = 'opacity-60 text-xs mx-0.5';
                    arrow.innerHTML = '\u2192';
                    heroColorsEl.appendChild(arrow);
                }
            });
        }
        renderHeroColorStops(g.colorNames || null);

        // Color swatches breakdown (1-click copy chips with toast feedback)
        function renderColorList(names) {
            var colorList = document.getElementById('gradientColorList');
            if (!colorList) return;
            colorList.innerHTML = '';
            g.colors.forEach(function (hex, idx) {
                var rgb = hexToRgb(hex);
                var name = (names && names[idx]) ? names[idx] : '';
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'copy-swatch-chip group flex items-center gap-2.5 bg-slate-50 hover:bg-white dark:bg-slate-800/60 dark:hover:bg-slate-800 rounded-xl px-3 py-2.5 border border-slate-200 hover:border-primary/50 dark:border-slate-700/60 dark:hover:border-primary/50 transition-all shadow-sm hover:shadow text-left min-w-0';
                btn.title = 'Click to copy ' + hex;
                btn.innerHTML =
                    '<span class="w-6 h-6 rounded-lg shrink-0 shadow-sm border border-black/10" style="background:' + hex + '"></span>'
                    + '<div class="min-w-0 flex-1">'
                    +   '<div class="flex items-center gap-1.5">'
                    +     '<span class="text-xs font-mono font-bold text-slate-800 dark:text-slate-100">' + hex + '</span>'
                    +     (name && name !== hex ? '<span class="text-[11px] text-slate-400 truncate font-medium">' + name + '</span>' : '')
                    +   '</div>'
                    +   '<span class="text-[10px] text-slate-400 font-mono">RGB(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ')</span>'
                    + '</div>'
                    + '<i class="bi bi-clipboard text-slate-400 group-hover:text-primary text-xs transition-colors shrink-0"></i>';
                btn.addEventListener('click', function () {
                    navigator.clipboard.writeText(hex).then(function () {
                        showCopyFeedback('Copied ' + hex + '!');
                        btn.classList.add('ring-2', 'ring-emerald-500');
                        setTimeout(function () { btn.classList.remove('ring-2', 'ring-emerald-500'); }, 1200);
                    });
                });
                colorList.appendChild(btn);
            });
        }
        renderColorList(g.colorNames || null);

        // Async resolve color names to populate gradient overlay and swatches
        loadColorDict().then(function (dict) {
            if (!dict) return;
            window.ColorMagic = window.ColorMagic || {};
            window.ColorMagic.colorDict = dict;
            window.ColorMagic.findColorName = findColorName;
            var names = g.colors.map(function (hex) {
                return findColorName(hex, dict);
            });
            g.colorNames = names;
            renderHeroColorStops(names);
            renderColorList(names);
        });

        // CSS code block
        var cssCode = 'background: ' + g.css + ';';
        document.getElementById('cssCodeBlock').textContent = cssCode;

        // Color info grid
        var colorInfoGrid = document.getElementById('colorInfoGrid');
        colorInfoGrid.innerHTML = '';
        var colorRoute = (document.querySelector('base') ? '' : '/') + 'color/';
        g.colors.forEach(function (hex) {
            var rgb = hexToRgb(hex);
            var hsl = hexToHsl(hex);
            var lum = getLuminance(hex);
            var bareHex = hex.replace('#', '');
            var card = document.createElement('div');
            card.className = 'bg-slate-50 dark:bg-slate-800 rounded-xl overflow-hidden flex flex-col';
            card.innerHTML =
                '<div class="h-16 w-full" style="background:' + hex + '"></div>'
                + '<div class="p-4 flex flex-col gap-3 flex-1">'
                +   '<div class="flex items-center justify-between">'
                +     '<p class="font-mono font-bold text-base">' + hex + '</p>'
                +     '<a href="' + colorRoute + bareHex + '/" class="p-1.5 rounded-lg text-slate-400 hover:text-secondary hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="View ' + hex + ' details">'
                +       '<i class="bi bi-box-arrow-up-right text-sm"></i>'
                +     '</a>'
                +   '</div>'
                +   '<div class="space-y-1.5 text-sm">'
                +     '<div class="flex items-center justify-between">'
                +       '<span class="text-slate-400 text-xs uppercase tracking-wider">RGB</span>'
                +       '<span class="font-mono text-xs font-semibold">' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + '</span>'
                +     '</div>'
                +     '<div class="flex items-center justify-between">'
                +       '<span class="text-slate-400 text-xs uppercase tracking-wider">HSL</span>'
                +       '<span class="font-mono text-xs font-semibold">' + hsl.h + '°, ' + hsl.s + '%, ' + hsl.l + '%</span>'
                +     '</div>'
                +     '<div class="flex items-center justify-between">'
                +       '<span class="text-slate-400 text-xs uppercase tracking-wider">Brightness</span>'
                +       '<span class="font-mono text-xs font-semibold">' + lum + '%</span>'
                +     '</div>'
                +   '</div>'
                +   '<div class="flex items-center gap-2 mt-auto pt-1">'
                +     '<button class="copy-color-hex-btn flex-1 px-3 py-2 bg-white dark:bg-slate-700 hover:bg-primary hover:text-white text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all border border-slate-200 dark:border-slate-600 flex items-center justify-center gap-1.5" data-hex="' + hex + '">'
                +       '<i class="bi bi-clipboard text-xs"></i> HEX'
                +     '</button>'
                +     '<button class="copy-color-rgb-btn flex-1 px-3 py-2 bg-white dark:bg-slate-700 hover:bg-primary hover:text-white text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold transition-all border border-slate-200 dark:border-slate-600 flex items-center justify-center gap-1.5" data-rgb="rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')">'
                +       '<i class="bi bi-clipboard text-xs"></i> RGB'
                +     '</button>'
                +   '</div>'
                + '</div>';
            colorInfoGrid.appendChild(card);
        });

        // Copy CSS (hero click)
        hero.addEventListener('click', function () {
            copyToClipboard(g.css, hero, null);
            var overlay = hero.querySelector('.group-hover\\:opacity-100 span');
            if (overlay) {
                overlay.innerHTML = '<i class="bi bi-check-circle-fill"></i> Copied!';
                setTimeout(function () {
                    overlay.innerHTML = '<i class="bi bi-clipboard"></i> Click to copy CSS';
                }, 2000);
            }
        });


        // Copy CSS block button
        var copyBlockBtn = document.getElementById('copyCssBlockBtn');
        if (copyBlockBtn) {
            var copyBlockOrig = copyBlockBtn.innerHTML;
            copyBlockBtn.addEventListener('click', function () {
                copyToClipboard(cssCode, copyBlockBtn, copyBlockOrig);
            });
        }

        // Copy color HEX / RGB buttons (delegated on color info grid)
        var colorInfoGrid = document.getElementById('colorInfoGrid');
        if (colorInfoGrid) {
            colorInfoGrid.addEventListener('click', function (e) {
                var hexBtn = e.target.closest('.copy-color-hex-btn');
                if (hexBtn) {
                    var origHTML = hexBtn.innerHTML;
                    copyToClipboard(hexBtn.dataset.hex, hexBtn, origHTML);
                    return;
                }
                var rgbBtn = e.target.closest('.copy-color-rgb-btn');
                if (rgbBtn) {
                    var origRGB = rgbBtn.innerHTML;
                    copyToClipboard(rgbBtn.dataset.rgb, rgbBtn, origRGB);
                }
            });
        }

        // Favorite button
        var favBtn = document.getElementById('favGradientBtn');
        function updateFavBtn() {
            var isFav = window.ColorMagic.GradientFavorites && window.ColorMagic.GradientFavorites.isFavorite(g.id);
            var icon = favBtn.querySelector('i');
            var span = favBtn.querySelector('span');
            if (icon) icon.className = 'bi ' + (isFav ? 'bi-heart-fill text-red-500' : 'bi-heart');
            if (span) span.textContent = isFav ? 'Favorited' : 'Favorite';
            favBtn.classList.toggle('text-red-500', isFav);
        }
        updateFavBtn();
        favBtn.addEventListener('click', function () {
            if (window.ColorMagic.GradientFavorites) {
                window.ColorMagic.GradientFavorites.toggleFavorite(g.id);
                var isFav = window.ColorMagic.GradientFavorites.isFavorite(g.id);
                if (window.ColorMagic.animateFavorite) {
                    window.ColorMagic.animateFavorite(favBtn, isFav);
                } else {
                    updateFavBtn();
                }
            }
        });

        // Download PNG button
        var downloadPngBtn = document.getElementById('downloadGradientPngBtn');
        if (downloadPngBtn && window.ColorMagic && window.ColorMagic.exportGradientImage) {
            downloadPngBtn.addEventListener('click', function () {
                var triggerExport = function () {
                    if (window.ColorMagic.animateDownload) {
                        window.ColorMagic.animateDownload(downloadPngBtn, function () {
                            window.ColorMagic.exportGradientImage(g);
                        });
                    } else {
                        window.ColorMagic.exportGradientImage(g);
                    }
                };

                if (!g.colorNames || g.colorNames.length === 0) {
                    loadColorDict().then(function (dict) {
                        if (dict) {
                            window.ColorMagic = window.ColorMagic || {};
                            window.ColorMagic.colorDict = dict;
                            window.ColorMagic.findColorName = findColorName;
                            g.colorNames = g.colors.map(function (hex) {
                                return findColorName(hex, dict);
                            });
                        }
                        triggerExport();
                    }).catch(triggerExport);
                } else {
                    triggerExport();
                }
            });
        }

        // Update page title
        document.title = g.name + ' Gradient | Color Magic';
    }

    function renderRelatedColors(g) {
        var grid = document.getElementById('relatedColorsGrid');
        if (!grid) return;

        var colorRoute = (document.querySelector('base') ? '' : '/') + 'color/';
        var seen = {};
        var relatedColors = [];

        g.colors.forEach(function (hex) {
            var hsl = hexToHsl(hex);

            // Complementary (180° rotation)
            var compH = (hsl.h + 180) % 360;
            var compHex = hslToHex(compH, hsl.s, hsl.l);
            if (!seen[compHex]) { seen[compHex] = true; relatedColors.push({ hex: compHex, label: 'Complementary' }); }

            // Analogous (+30°, -30°)
            var ana1H = (hsl.h + 30) % 360;
            var ana1Hex = hslToHex(ana1H, hsl.s, hsl.l);
            if (!seen[ana1Hex]) { seen[ana1Hex] = true; relatedColors.push({ hex: ana1Hex, label: 'Analogous' }); }

            var ana2H = (hsl.h + 330) % 360;
            var ana2Hex = hslToHex(ana2H, hsl.s, hsl.l);
            if (!seen[ana2Hex]) { seen[ana2Hex] = true; relatedColors.push({ hex: ana2Hex, label: 'Analogous' }); }

            // Triadic (+120°, +240°)
            var tri1H = (hsl.h + 120) % 360;
            var tri1Hex = hslToHex(tri1H, hsl.s, hsl.l);
            if (!seen[tri1Hex]) { seen[tri1Hex] = true; relatedColors.push({ hex: tri1Hex, label: 'Triadic' }); }

            var tri2H = (hsl.h + 240) % 360;
            var tri2Hex = hslToHex(tri2H, hsl.s, hsl.l);
            if (!seen[tri2Hex]) { seen[tri2Hex] = true; relatedColors.push({ hex: tri2Hex, label: 'Triadic' }); }
        });

        relatedColors.slice(0, 12).forEach(function (c) {
            var rgb = hexToRgb(c.hex);
            var card = document.createElement('a');
            card.href = colorRoute + c.hex.replace('#', '') + '/';
            card.className = 'group block rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all';
            card.innerHTML =
                '<div class="h-20 relative" style="background:' + c.hex + '">'
                + '<div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20">'
                + '<span class="px-2 py-1 bg-white/90 dark:bg-slate-900/90 rounded-lg text-[10px] font-bold shadow">' + c.label + '</span>'
                + '</div>'
                + '</div>'
                + '<div class="bg-white dark:bg-slate-900 p-2.5">'
                + '<p class="font-mono text-xs font-bold">' + c.hex + '</p>'
                + '<p class="text-[10px] text-slate-400 mt-0.5">RGB(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')</p>'
                + '</div>';
            grid.appendChild(card);
        });
    }

    function hslToHex(h, s, l) {
        s /= 100; l /= 100;
        var c = (1 - Math.abs(2 * l - 1)) * s;
        var x = c * (1 - Math.abs((h / 60) % 2 - 1));
        var m = l - c / 2;
        var r, g, b;
        if (h < 60) { r=c; g=x; b=0; }
        else if (h < 120) { r=x; g=c; b=0; }
        else if (h < 180) { r=0; g=c; b=x; }
        else if (h < 240) { r=0; g=x; b=c; }
        else if (h < 300) { r=x; g=0; b=c; }
        else { r=c; g=0; b=x; }
        var toHex = function(v) { var hex = Math.round((v + m) * 255).toString(16); return hex.length === 1 ? '0' + hex : hex; };
        return '#' + toHex(r) + toHex(g) + toHex(b);
    }

    function renderRelatedGradients(current, all) {
        var grid = document.getElementById('relatedGradientsGrid');
        if (!grid) return;

        // Find related: same style first, then same type, exclude current
        var related = all.filter(function (g) {
            return g.id !== current.id && (g.style === current.style || g.type === current.type);
        });

        // Sort: same style first
        related.sort(function (a, b) {
            var aStyle = a.style === current.style ? 0 : 1;
            var bStyle = b.style === current.style ? 0 : 1;
            return aStyle - bStyle;
        });

        related = related.slice(0, 6);

        if (related.length === 0) {
            grid.innerHTML = '<p class="text-sm text-slate-400 col-span-full">No related gradients found.</p>';
            return;
        }

        related.forEach(function (g) {
            if (window.ColorMagic && window.ColorMagic.createGradientCard) {
                grid.appendChild(window.ColorMagic.createGradientCard(g));
            }
        });
    }

    function colorDistSq(a, b) {
        var ar = parseInt(a.substring(0, 2), 16), ag = parseInt(a.substring(2, 4), 16), ab = parseInt(a.substring(4, 6), 16);
        var br = parseInt(b.substring(0, 2), 16), bg = parseInt(b.substring(2, 4), 16), bb = parseInt(b.substring(4, 6), 16);
        var dr = ar - br, dg = ag - bg, db = ab - bb;
        return dr * dr + dg * dg + db * db;
    }

    function renderRelatedPalettes(gradient) {
        var grid = document.getElementById('relatedPalettesGrid');
        if (!grid || !window.ColorMagic || !window.ColorMagic.createPaletteCard) return;

        var gradientHexes = gradient.colors.map(function (c) { return c.replace('#', '').toLowerCase(); });

        var palPromise = (window.ColorMagic && window.ColorMagic.api)
            ? window.ColorMagic.api.getPalettes({ limit: 1000 })
            : fetch('/api/palettes.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });

        palPromise
            .then(function (res) {
                var palettes = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
                var scored = [];
                palettes.forEach(function (p) {
                    if (!Array.isArray(p.colors)) return;
                    var matchCount = 0;
                    var totalDist = 0;

                    gradientHexes.forEach(function (gh) {
                        var minD = Infinity;
                        p.colors.forEach(function (pc) {
                            var ph = pc.replace('#', '').toLowerCase();
                            if (gh === ph) { matchCount++; minD = 0; }
                            else {
                                var d = colorDistSq(gh, ph);
                                if (d < minD) minD = d;
                            }
                        });
                        totalDist += minD;
                    });

                    if (matchCount > 0 || totalDist / gradientHexes.length < 50000) {
                        scored.push({ palette: p, score: matchCount * 100000 - totalDist });
                    }
                });

                scored.sort(function (a, b) { return b.score - a.score; });
                var top = scored.slice(0, 6);

                if (top.length === 0) {
                    // Fallback: just pick the 6 closest palettes regardless of threshold
                    var allScored = [];
                    palettes.forEach(function (p) {
                        if (!Array.isArray(p.colors)) return;
                        var td = 0;
                        gradientHexes.forEach(function (gh) {
                            var minD = Infinity;
                            p.colors.forEach(function (pc) {
                                var ph = pc.replace('#', '').toLowerCase();
                                var d = colorDistSq(gh, ph);
                                if (d < minD) minD = d;
                            });
                            td += minD;
                        });
                        allScored.push({ palette: p, score: -td });
                    });
                    allScored.sort(function (a, b) { return b.score - a.score; });
                    top = allScored.slice(0, 6);
                }

                window.ColorMagic.markDuplicateSlugs(top.map(function (s) { return s.palette; }));
                var fragment = document.createDocumentFragment();
                top.forEach(function (s) {
                    fragment.appendChild(window.ColorMagic.createPaletteCard(s.palette));
                });
                grid.appendChild(fragment);

                // Delegated handlers for palette cards in this grid
                grid.addEventListener('click', function (e) {
                    var copyBtn = e.target.closest('.copy-palette-btn');
                    if (copyBtn) {
                        var colors = copyBtn.dataset.colors;
                        var icon = copyBtn.querySelector('i');
                        var origClass = icon ? icon.className : '';
                        navigator.clipboard.writeText(colors).then(function () {
                            if (icon) icon.className = 'bi bi-check-circle-fill text-xl';
                            copyBtn.classList.add('text-green-500');
                            setTimeout(function () {
                                if (icon) icon.className = origClass;
                                copyBtn.classList.remove('text-green-500');
                            }, 2000);
                        });
                        return;
                    }
                    var swatchBtn = e.target.closest('.swatch-copy-hex');
                    if (swatchBtn) {
                        var hex = swatchBtn.dataset.hex;
                        var swIcon = swatchBtn.querySelector('i');
                        var swOrig = swatchBtn.innerHTML;
                        navigator.clipboard.writeText(hex).then(function () {
                            if (swIcon) swIcon.className = 'bi bi-check-circle-fill text-[11px]';
                            swatchBtn.classList.add('text-green-600');
                            setTimeout(function () { swatchBtn.innerHTML = swOrig; swatchBtn.classList.remove('text-green-600'); }, 1500);
                        });
                        return;
                    }
                    var favColorBtn = e.target.closest('.swatch-fav-color');
                    if (favColorBtn && window.ColorMagic.ColorFavorites) {
                        var favHex = favColorBtn.dataset.hex;
                        var added = window.ColorMagic.ColorFavorites.toggleFavorite(favHex);
                        var fIcon = favColorBtn.querySelector('i');
                        if (fIcon) fIcon.className = added ? 'bi bi-heart-fill text-red-500' : 'bi bi-heart';
                        return;
                    }
                    var favPaletteBtn = e.target.closest('.favorite-btn');
                    if (favPaletteBtn && window.ColorMagic.Favorites) {
                        var paletteId = favPaletteBtn.dataset.paletteId;
                        window.ColorMagic.Favorites.toggleFavorite(paletteId);
                        window.ColorMagic.Favorites.updateFavoriteButton(favPaletteBtn, paletteId);
                    }
                });
            })
            .catch(function () {
                grid.innerHTML = '<p class="text-sm text-slate-400 col-span-full">Failed to load palettes.</p>';
            });
    }

    // ─── Init ─────────────────────────────────────────────────────────────────

    var gradientId = getGradientId();
    if (!gradientId) {
        document.getElementById('gradientError').classList.remove('hidden');
        return;
    }

    var gradPromise = (window.ColorMagic && window.ColorMagic.api)
        ? window.ColorMagic.api.getGradients({ limit: 1000 })
        : fetch('/api/gradients.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });

    gradPromise
        .then(function (res) {
            var data = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
            var gradient = null;
            for (var i = 0; i < data.length; i++) {
                if (data[i].id === gradientId) { gradient = data[i]; break; }
            }
            if (!gradient) {
                var skel1 = document.getElementById('gradientSkeleton');
                if (skel1) skel1.classList.add('hidden');
                document.getElementById('gradientError').classList.remove('hidden');
                return;
            }
            renderGradient(gradient);
            renderRelatedColors(gradient);
            renderRelatedGradients(gradient, data);
            renderRelatedPalettes(gradient);
        })
        .catch(function () {
            var skel2 = document.getElementById('gradientSkeleton');
            if (skel2) skel2.classList.add('hidden');
            document.getElementById('gradientError').classList.remove('hidden');
        });

})();
