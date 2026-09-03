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

    // ─── Render functions ─────────────────────────────────────────────────────

    function renderGradient(g) {
        var detail = document.getElementById('gradientDetail');
        var error  = document.getElementById('gradientError');
        if (!detail || !error) return;

        detail.classList.remove('hidden');

        // ID text
        var idText = document.getElementById('gradientIdText');
        if (idText) idText.textContent = 'ID: ' + g.id;

        // Name & meta
        document.getElementById('gradientName').textContent = g.name;
        document.getElementById('gradientMeta').textContent =
            g.style + ' · ' + g.type.charAt(0).toUpperCase() + g.type.slice(1) + ' gradient';

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

        // Hero preview
        var hero = document.getElementById('heroGradientPreview');
        hero.style.background = g.css;

        // Color list (small chips in info panel)
        var colorList = document.getElementById('gradientColorList');
        colorList.innerHTML = '';
        g.colors.forEach(function (hex) {
            var rgb = hexToRgb(hex);
            var item = document.createElement('div');
            item.className = 'flex items-center gap-2 bg-slate-50 dark:bg-slate-800 rounded-lg px-3 py-2';
            item.innerHTML =
                '<span class="w-5 h-5 rounded-md shrink-0 shadow-sm" style="background:' + hex + '"></span>'
                + '<span class="text-sm font-mono font-semibold">' + hex + '</span>'
                + '<span class="text-xs text-slate-400 ml-auto">' + rgb.r + ',' + rgb.g + ',' + rgb.b + '</span>';
            colorList.appendChild(item);
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

        // Copy CSS button
        var copyCssBtn = document.getElementById('copyCssBtn');
        var copyCssOrig = copyCssBtn.innerHTML;
        copyCssBtn.addEventListener('click', function () {
            copyToClipboard(g.css, copyCssBtn, copyCssOrig);
        });

        // Copy CSS block button
        var copyBlockBtn = document.getElementById('copyCssBlockBtn');
        var copyBlockOrig = copyBlockBtn.innerHTML;
        copyBlockBtn.addEventListener('click', function () {
            copyToClipboard(cssCode, copyBlockBtn, copyBlockOrig);
        });

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
            if (icon) icon.className = 'bi ' + (isFav ? 'bi-heart-fill' : 'bi-heart');
            if (span) span.textContent = isFav ? 'Favorited' : 'Favorite';
            favBtn.classList.toggle('text-red-500', isFav);
        }
        updateFavBtn();
        favBtn.addEventListener('click', function () {
            if (window.ColorMagic.GradientFavorites) {
                window.ColorMagic.GradientFavorites.toggleFavorite(g.id);
                updateFavBtn();
            }
        });

        // Download PNG button
        var downloadPngBtn = document.getElementById('downloadGradientPngBtn');
        if (downloadPngBtn && window.ColorMagic && window.ColorMagic.exportGradientImage) {
            downloadPngBtn.addEventListener('click', function () {
                window.ColorMagic.exportGradientImage(g);
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

        var gradientBase = (document.querySelector('base') ? '' : '/') + 'gradient/';

        related.forEach(function (g) {
            var angleOrShape = g.type === 'linear' ? (g.angle + '°') : (g.type === 'mesh' ? 'mesh' : (g.shape || g.type));
            var isFav = window.ColorMagic && window.ColorMagic.GradientFavorites && window.ColorMagic.GradientFavorites.isFavorite(g.id);
            var heartIcon = isFav ? 'bi-heart-fill text-red-500' : 'bi-heart';

            var card = document.createElement('div');
            card.className = 'gradient-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col';

            card.innerHTML =
                '<div class="h-28 w-full" style="background:' + g.css + '"></div>'
                + '<div class="p-4 flex flex-col gap-2.5 flex-1">'
                +   '<p class="font-bold text-sm">' + g.name + '</p>'
                +   '<p class="text-xs text-slate-400">' + g.style + ' · ' + g.type + ' · ' + angleOrShape + '</p>'
                +   '<div class="flex gap-1 h-4 rounded-lg overflow-hidden mt-1">'
                +     g.colors.map(function (c) { return '<div class="flex-1" style="background:' + c + '"></div>'; }).join('')
                +   '</div>'
                +   '<div class="flex items-center justify-between gap-2 mt-auto pt-2 border-t border-slate-100 dark:border-slate-800">'
                +     '<button class="fav-gradient-btn p-1.5 text-slate-400 hover:text-red-500 transition-colors" data-gradient-id="' + g.id + '" title="' + (isFav ? 'Remove from' : 'Add to') + ' favorites">'
                +       '<i class="bi ' + heartIcon + ' text-base"></i>'
                +     '</button>'
                +     '<button class="copy-gradient-css-btn p-1.5 text-slate-400 hover:text-primary transition-colors" data-css="' + g.css.replace(/"/g, '&quot;') + '" title="Copy CSS">'
                +       '<i class="bi bi-clipboard text-lg"></i>'
                +     '</button>'
                +     '<a href="' + gradientBase + g.id + '/" class="p-1.5 text-slate-400 hover:text-secondary transition-colors" title="Open gradient" target="_blank" rel="noopener">'
                +       '<i class="bi bi-box-arrow-up-right text-base"></i>'
                +     '</a>'
                +   '</div>'
                + '</div>';

            grid.appendChild(card);
        });

        // Delegated click handlers for gradient cards
        grid.addEventListener('click', function (e) {
            // Favorite gradient
            var favBtn = e.target.closest('.fav-gradient-btn');
            if (favBtn && window.ColorMagic && window.ColorMagic.GradientFavorites) {
                var gid = favBtn.dataset.gradientId;
                window.ColorMagic.GradientFavorites.toggleFavorite(gid);
                var icon = favBtn.querySelector('i');
                var nowFav = window.ColorMagic.GradientFavorites.isFavorite(gid);
                if (icon) icon.className = 'bi ' + (nowFav ? 'bi-heart-fill text-red-500' : 'bi-heart') + ' text-base';
                return;
            }
            // Copy CSS
            var copyBtn = e.target.closest('.copy-gradient-css-btn');
            if (copyBtn) {
                var css = copyBtn.dataset.css;
                var icon = copyBtn.querySelector('i');
                var origClass = icon ? icon.className : '';
                navigator.clipboard.writeText(css).then(function () {
                    if (icon) icon.className = 'bi bi-check-circle-fill text-lg text-green-500';
                    setTimeout(function () { if (icon) icon.className = origClass; }, 2000);
                });
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
                document.getElementById('gradientError').classList.remove('hidden');
                return;
            }
            renderGradient(gradient);
            renderRelatedColors(gradient);
            renderRelatedGradients(gradient, data);
            renderRelatedPalettes(gradient);
        })
        .catch(function () {
            document.getElementById('gradientError').classList.remove('hidden');
        });

})();
