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
            var card = document.createElement('a');
            card.href = (document.querySelector('base') ? '' : '/') + 'gradient/' + g.id + '/';
            card.className = 'group bg-slate-50 dark:bg-slate-800 rounded-xl overflow-hidden hover:shadow-lg transition-all';
            var angleOrShape = g.type === 'linear' ? (g.angle + '°') : (g.type === 'mesh' ? 'mesh' : (g.shape || g.type));
            card.innerHTML =
                '<div class="h-28 w-full" style="background:' + g.css + '"></div>'
                + '<div class="p-3">'
                + '<p class="font-bold text-sm">' + g.name + '</p>'
                + '<p class="text-xs text-slate-400 mt-0.5">' + g.style + ' · ' + g.type + ' · ' + angleOrShape + '</p>'
                + '<div class="flex gap-1 mt-2 h-3 rounded overflow-hidden">'
                + g.colors.map(function (c) { return '<div class="flex-1" style="background:' + c + '"></div>'; }).join('')
                + '</div>'
                + '</div>';
            grid.appendChild(card);
        });
    }

    // ─── Init ─────────────────────────────────────────────────────────────────

    var gradientId = getGradientId();
    if (!gradientId) {
        document.getElementById('gradientError').classList.remove('hidden');
        return;
    }

    fetch('data/gradients.json?t=' + Date.now())
        .then(function (res) {
            if (!res.ok) throw new Error('Failed to fetch');
            return res.json();
        })
        .then(function (data) {
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
        })
        .catch(function () {
            document.getElementById('gradientError').classList.remove('hidden');
        });

})();
