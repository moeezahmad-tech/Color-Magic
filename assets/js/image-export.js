/**
 * image-export.js
 * Shared Canvas-based image export utilities for palettes, colors, and gradients.
 * 
 * Provides functions to render color data onto an HTML5 Canvas and download as PNG.
 */

(function () {
    'use strict';

    window.ColorMagic = window.ColorMagic || {};

    // ─── Helpers ──────────────────────────────────────────────────────────────

    function hexToRgb(hex) {
        var h = hex.replace('#', '');
        if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
        var n = parseInt(h, 16);
        return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
    }

    function getLuminance(hex) {
        var c = hexToRgb(hex);
        return (0.299 * c.r + 0.587 * c.g + 0.114 * c.b) / 255;
    }

    function bestTextColor(hex) {
        return getLuminance(hex) > 0.62 ? '#1e293b' : '#ffffff';
    }

    function downloadCanvas(canvas, filename) {
        canvas.toBlob(function (blob) {
            if (!blob) return;
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(function () { URL.revokeObjectURL(url); }, 100);
        }, 'image/png', 1.0);
    }

    function roundRect(ctx, x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + w - r, y);
        ctx.quadraticCurveTo(x + w, y, x + w, y + r);
        ctx.lineTo(x + w, y + h - r);
        ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        ctx.lineTo(x + r, y + h);
        ctx.quadraticCurveTo(x, y + h, x, y + h - r);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
    }

    // ─── Palette Image Export ──────────────────────────────────────────────────

    /**
     * Renders a palette as a PNG image and triggers download.
     * @param {Object} palette - { name: string, colors: string[], style: string }
     * @param {string} [filename] - Optional filename (defaults to palette-name.png)
     */
    window.ColorMagic.exportPaletteImage = function (palette, filename) {
        var canvas = document.createElement('canvas');
        var W = 4800, H = 3200;
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var colors = palette.colors || [];
        var name = palette.name || 'Color Palette';
        var style = palette.style || 'curated';

        // Background
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, W, H);

        // Color strips (top 65%)
        var stripWidth = W / colors.length;
        var stripHeight = 2080;
        colors.forEach(function (hex, i) {
            ctx.fillStyle = hex;
            ctx.fillRect(i * stripWidth, 0, stripWidth + 1, stripHeight);

            // Hex label at bottom of strip
            var textColor = bestTextColor(hex);
            ctx.fillStyle = textColor;
            ctx.font = 'bold 64px Inter, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            ctx.fillText(hex.toUpperCase(), i * stripWidth + stripWidth / 2, stripHeight - 80);
        });

        // Bottom info area
        var infoY = stripHeight;

        // White background for info area
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, infoY, W, H - infoY);

        // Divider line
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 5;
        ctx.beginPath();
        ctx.moveTo(160, infoY);
        ctx.lineTo(W - 160, infoY);
        ctx.stroke();

        // Palette name
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 144px Inter, system-ui, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        ctx.fillText(name, 200, infoY + 120);

        // Style tag
        ctx.fillStyle = '#64748b';
        ctx.font = '500 72px Inter, system-ui, sans-serif';
        ctx.fillText(style + ' palette \u00b7 ' + colors.length + ' colors', 200, infoY + 320);

        // Color swatches row
        var swatchSize = 240;
        var swatchGap = 48;
        var swatchesY = infoY + 520;
        colors.forEach(function (hex, i) {
            var x = 200 + i * (swatchSize + swatchGap);
            roundRect(ctx, x, swatchesY, swatchSize, swatchSize, 40);
            ctx.fillStyle = hex;
            ctx.fill();

            ctx.fillStyle = '#475569';
            ctx.font = '600 52px Inter, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(hex.toUpperCase(), x + swatchSize / 2, swatchesY + swatchSize + 32);
        });

        // Branding
        ctx.fillStyle = '#94a3b8';
        ctx.font = '500 56px Inter, system-ui, sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'bottom';
        ctx.fillText('Color Magic \u00b7 colormagic.techkreative.com', W - 200, H - 120);

        // Download
        var fname = filename || name.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '.png';
        downloadCanvas(canvas, fname);
    };

    // ─── Color Image Export ────────────────────────────────────────────────────

    function rgbToCmyk(r, g, b) {
        var c = 1 - (r / 255);
        var m = 1 - (g / 255);
        var y = 1 - (b / 255);
        var k = Math.min(c, Math.min(m, y));
        if (k >= 1) return { c: 0, m: 0, y: 0, k: 100 };
        return {
            c: Math.round(((c - k) / (1 - k)) * 100),
            m: Math.round(((m - k) / (1 - k)) * 100),
            y: Math.round(((y - k) / (1 - k)) * 100),
            k: Math.round(k * 100)
        };
    }

    function getColorMoodKeywords(hex, rgb, hsl, name) {
        var h = (hsl && typeof hsl.h === 'number') ? hsl.h : 0;
        var s = (hsl && typeof hsl.s === 'number') ? hsl.s : 0;
        var l = (hsl && typeof hsl.l === 'number') ? hsl.l : 50;
        var n = (name || '').toLowerCase();

        if (n.indexOf('merlot') !== -1 || n.indexOf('wine') !== -1 || n.indexOf('burgundy') !== -1 || ((h >= 335 || h <= 15) && l < 35 && s > 20)) {
            return ['RICH', 'MOODY', 'TIMELESS', 'ELEGANCE'];
        }
        if (n.indexOf('inferno') !== -1 || n.indexOf('crimson') !== -1 || n.indexOf('ruby') !== -1 || n.indexOf('scarlet') !== -1) {
            return ['INTENSE', 'PASSION', 'DRAMATIC', 'VELVET'];
        }
        if (l < 15) {
            return ['DEEP', 'MYSTERIOUS', 'MODERN', 'PRECISION'];
        }
        if (l > 85) {
            return ['ETHEREAL', 'LUMINOUS', 'SERENE', 'PURITY'];
        }
        if (s < 18) {
            return ['MINIMAL', 'SUBTLE', 'ORGANIC', 'UNDERSTATED'];
        }
        if (h >= 35 && h <= 65) {
            return ['RADIANT', 'WARMTH', 'OPTIMISTIC', 'VIBRANT'];
        }
        if (h > 65 && h <= 165) {
            return ['BOTANICAL', 'GROUNDED', 'TRANQUIL', 'ORGANIC'];
        }
        if (h > 165 && h <= 200) {
            return ['COASTAL', 'REFRESHING', 'TRANQUIL', 'MODERN'];
        }
        if (h > 200 && h <= 255) {
            return l < 40 ? ['REGAL', 'DEEP', 'TIMELESS', 'CONFIDENT'] : ['AIRY', 'SERENE', 'CALM', 'BALANCED'];
        }
        if (h > 255 && h <= 300) {
            return ['MYSTICAL', 'LUXURIOUS', 'CREATIVE', 'ENCHANTING'];
        }
        if (h > 300 && h < 335) {
            return ['EXPRESSIVE', 'PLAYFUL', 'ALLURING', 'CONTEMPORARY'];
        }
        if (h >= 15 && h < 35) {
            return l < 40 ? ['EARTHEN', 'RUSTIC', 'HERITAGE', 'WARMTH'] : ['VIVID', 'SUNLIT', 'ENERGETIC', 'DYNAMIC'];
        }

        return ['TIMELESS', 'MODERN', 'HARMONIOUS', 'ELEGANCE'];
    }

    function drawSpacedText(ctx, text, x, y, letterSpacing, align) {
        if (!text) return;
        align = align || 'left';
        if (typeof ctx.letterSpacing !== 'undefined') {
            ctx.letterSpacing = letterSpacing + 'px';
            ctx.textAlign = align;
            ctx.fillText(text, x, y);
            ctx.letterSpacing = '0px';
            return;
        }
        var chars = text.split('');
        var widths = [];
        var totalW = 0;
        for (var i = 0; i < chars.length; i++) {
            var w = ctx.measureText(chars[i]).width;
            widths.push(w);
            totalW += w + (i < chars.length - 1 ? letterSpacing : 0);
        }
        var curX = x;
        if (align === 'right') curX = x - totalW;
        else if (align === 'center') curX = x - totalW / 2;
        ctx.textAlign = 'left';
        for (var j = 0; j < chars.length; j++) {
            ctx.fillText(chars[j], curX, y);
            curX += widths[j] + letterSpacing;
        }
    }

    /**
     * Renders a color swatch canvas in Pantone Swatch style (Image 2).
     * @param {Object} colorData - { hex: string, name: string, rgb: {r,g,b}, hsl: {h,s,l} }
     * @returns {HTMLCanvasElement}
     */
    window.ColorMagic.renderColorCanvas = function (colorData) {
        var canvas = document.createElement('canvas');
        var W = 2000, H = 2500; // 4:5 vertical portrait ratio
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var hex = colorData.hex || '#000000';
        var cleanHex = hex.replace('#', '').toUpperCase();
        var name = (colorData.name || ('#' + cleanHex)).replace(/^Hex\s*#/i, '');
        var rgb = colorData.rgb || hexToRgb(hex);
        var hsl = colorData.hsl || { h: 0, s: 0, l: 0 };
        var cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);

        // Full-bleed color background
        ctx.fillStyle = hex;
        ctx.fillRect(0, 0, W, H);

        // Contrast calculation for high legibility
        var isLight = getLuminance(hex) > 0.62;
        var textPrimary = isLight ? '#111827' : '#FFFFFF';
        var textSecondary = isLight ? 'rgba(17, 24, 39, 0.88)' : 'rgba(255, 255, 255, 0.92)';
        var textMuted = isLight ? 'rgba(17, 24, 39, 0.55)' : 'rgba(255, 255, 255, 0.60)';

        // ─── Swatch Card Layout ─────────────────────────────────────────────
        var startX = 220;

        // 1. Brand Tag (moderate size, clean letter-spacing, NO (R) symbol)
        ctx.fillStyle = textSecondary;
        ctx.textBaseline = 'alphabetic';
        ctx.font = '700 48px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        drawSpacedText(ctx, 'COLOR MAGIC', startX, 1500, 6, 'left');

        // 2. Large Color Name / Color Code
        var hasDistinctName = name && name.trim() !== '' && 
            name.toLowerCase() !== ('#' + cleanHex).toLowerCase() && 
            name.toLowerCase() !== cleanHex.toLowerCase() &&
            name.toLowerCase() !== ('hex #' + cleanHex).toLowerCase();

        if (hasDistinctName) {
            // Show Color Name in LARGE size
            ctx.fillStyle = textPrimary;
            var heroName = name.toUpperCase();
            var maxHeroW = 1560;
            ctx.font = '900 136px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            if (ctx.measureText(heroName).width > maxHeroW) {
                var scale = maxHeroW / ctx.measureText(heroName).width;
                ctx.font = '900 ' + Math.max(64, Math.floor(136 * scale)) + 'px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            }
            drawSpacedText(ctx, heroName, startX, 1660, 2, 'left');

            // Color Code in prominent size
            ctx.fillStyle = textSecondary;
            ctx.font = '700 72px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            drawSpacedText(ctx, '#' + cleanHex, startX, 1770, 4, 'left');
        } else {
            // If no custom name, show the Color Code in LARGE size
            ctx.fillStyle = textPrimary;
            ctx.font = '900 150px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            drawSpacedText(ctx, '#' + cleanHex, startX, 1700, 3, 'left');
        }

        // 3. Technical Specs Block (HEX, CMYK, RGB, HSL)
        var specY = 1920;
        var lineH = 74;
        var labelX = startX;
        var valueX = startX + 280;

        // Labels Column
        ctx.font = '700 50px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillStyle = textSecondary;
        drawSpacedText(ctx, 'HEX', labelX, specY, 4, 'left');
        drawSpacedText(ctx, 'CMYK', labelX, specY + lineH, 4, 'left');
        drawSpacedText(ctx, 'RGB', labelX, specY + lineH * 2, 4, 'left');
        drawSpacedText(ctx, 'HSL', labelX, specY + lineH * 3, 4, 'left');

        // Values Column
        ctx.font = '500 50px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillStyle = textPrimary;
        drawSpacedText(ctx, '#' + cleanHex, valueX, specY, 2, 'left');
        drawSpacedText(ctx, cmyk.c + '  ' + cmyk.m + '  ' + cmyk.y + '  ' + cmyk.k, valueX, specY + lineH, 2, 'left');
        drawSpacedText(ctx, rgb.r + '  ' + rgb.g + '  ' + rgb.b, valueX, specY + lineH * 2, 2, 'left');
        drawSpacedText(ctx, Math.round(hsl.h) + '\u00b0  ' + Math.round(hsl.s) + '%  ' + Math.round(hsl.l) + '%', valueX, specY + lineH * 3, 2, 'left');

        // 4. Subtle Branding URL (Bottom Right)
        ctx.font = '500 26px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillStyle = textMuted;
        drawSpacedText(ctx, 'colormagic.techkreative.com', 1780, 2360, 3, 'right');

        return canvas;
    };

    /**
     * Renders a single color as a high-res swatch PNG image and triggers download.
     * @param {Object} colorData - { hex: string, name: string, rgb: {r,g,b}, hsl: {h,s,l} }
     * @param {string} [filename] - Optional filename
     */
    window.ColorMagic.exportColorImage = function (colorData, filename) {
        var canvas = window.ColorMagic.renderColorCanvas(colorData);

        var hex = colorData.hex || '#000000';
        var cleanHex = hex.replace('#', '').toLowerCase();
        var fname = filename || cleanHex + '-color.png';

        downloadCanvas(canvas, fname);
    };

    // ─── Gradient Image Export ─────────────────────────────────────────────────

    /**
     * Renders a gradient card canvas in Pantone Swatch / high-res portrait style (4:5).
     * @param {Object} gradient - { id, name, css, colors, type, style, angle, shape }
     * @returns {HTMLCanvasElement}
     */
    window.ColorMagic.renderGradientCanvas = function (gradient) {
        var canvas = document.createElement('canvas');
        var W = 2000, H = 2500; // 4:5 vertical portrait ratio
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var name = (gradient.name || 'Gradient').toUpperCase();
        var colors = gradient.colors || ['#000000', '#ffffff'];
        var type = (gradient.type || 'linear').toLowerCase();
        var angle = (typeof gradient.angle === 'number') ? gradient.angle : 135;

        // Render high-res gradient
        var grad;
        if (type === 'linear') {
            var rad = (angle - 90) * Math.PI / 180;
            var cx = W / 2, cy = H / 2;
            var len = Math.sqrt(W * W + H * H);
            var dx = Math.cos(rad) * len / 2;
            var dy = Math.sin(rad) * len / 2;
            grad = ctx.createLinearGradient(cx - dx, cy - dy, cx + dx, cy + dy);
        } else {
            grad = ctx.createRadialGradient(W / 2, H / 2, 0, W / 2, H / 2, Math.max(W, H) / 1.5);
        }

        colors.forEach(function (hex, i) {
            grad.addColorStop(i / Math.max(colors.length - 1, 1), hex);
        });

        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, H);

        // Calculate average perceived luminance
        var totalLum = 0;
        colors.forEach(function (hex) {
            totalLum += getLuminance(hex);
        });
        var avgLum = totalLum / Math.max(colors.length, 1);
        var isLight = avgLum > 0.62;

        var textPrimary = isLight ? '#111827' : '#FFFFFF';
        var textSecondary = isLight ? 'rgba(17, 24, 39, 0.88)' : 'rgba(255, 255, 255, 0.92)';
        var textMuted = isLight ? 'rgba(17, 24, 39, 0.55)' : 'rgba(255, 255, 255, 0.60)';

        // ─── Swatch Card Layout ─────────────────────────────────────────────
        var startX = 220;

        // 1. Brand Tag (moderate size, clean letter-spacing, NO (R) symbol)
        ctx.fillStyle = textSecondary;
        ctx.textBaseline = 'alphabetic';
        ctx.font = '700 48px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        drawSpacedText(ctx, 'COLOR MAGIC', startX, 1500, 6, 'left');

        // 2. Large Gradient Name
        ctx.fillStyle = textPrimary;
        var maxHeroW = 1560;
        ctx.font = '900 136px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        if (ctx.measureText(name).width > maxHeroW) {
            var scale = maxHeroW / ctx.measureText(name).width;
            ctx.font = '900 ' + Math.max(64, Math.floor(136 * scale)) + 'px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        }
        drawSpacedText(ctx, name, startX, 1660, 2, 'left');

        // 3. Color stops subtitle
        ctx.fillStyle = textSecondary;
        ctx.font = '700 68px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        var stopsSummary = colors.map(function (c) { return c.toUpperCase(); }).join('  \u2192  ');
        if (ctx.measureText(stopsSummary).width > maxHeroW) {
            var sScale = maxHeroW / ctx.measureText(stopsSummary).width;
            ctx.font = '700 ' + Math.max(36, Math.floor(68 * sScale)) + 'px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        }
        drawSpacedText(ctx, stopsSummary, startX, 1770, 3, 'left');

        // 3b. Visual Color Swatch Chips with Color Names
        var swatchY = 1836;
        var swatchR = 18;
        var curSwatchX = startX + swatchR;
        colors.forEach(function (cHex, idx) {
            if (curSwatchX > startX + maxHeroW - 100) return; // avoid overflow
            ctx.beginPath();
            ctx.arc(curSwatchX, swatchY, swatchR, 0, Math.PI * 2);
            ctx.fillStyle = cHex;
            ctx.fill();
            ctx.lineWidth = 3;
            ctx.strokeStyle = isLight ? 'rgba(0, 0, 0, 0.25)' : 'rgba(255, 255, 255, 0.5)';
            ctx.stroke();

            var cName = (gradient.colorNames && gradient.colorNames[idx]) ? gradient.colorNames[idx] : cHex.toUpperCase();
            ctx.font = '600 36px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            ctx.fillStyle = textSecondary;
            ctx.textBaseline = 'middle';
            ctx.textAlign = 'left';
            ctx.fillText(cName, curSwatchX + swatchR + 14, swatchY);

            var textW = ctx.measureText(cName).width;
            curSwatchX += swatchR * 2 + textW + 48;
        });

        // 4. Technical Specs Block (TYPE, STOPS, STYLE)
        var specY = 1950;
        var lineH = 78;
        var labelX = startX;
        var valueX = startX + 280;

        // Labels Column
        ctx.font = '700 50px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillStyle = textSecondary;
        drawSpacedText(ctx, 'TYPE', labelX, specY, 4, 'left');
        drawSpacedText(ctx, 'STOPS', labelX, specY + lineH, 4, 'left');
        drawSpacedText(ctx, 'STYLE', labelX, specY + lineH * 2, 4, 'left');

        // Values Column
        ctx.font = '500 50px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillStyle = textPrimary;
        var typeValue = (type === 'linear' ? (type.toUpperCase() + ' \u00b7 ' + angle + '\u00b0') : (type.toUpperCase() + ' \u00b7 CIRCLE'));
        drawSpacedText(ctx, typeValue, valueX, specY, 2, 'left');
        drawSpacedText(ctx, colors.length + ' COLORS (' + colors.join(', ') + ')', valueX, specY + lineH, 2, 'left');
        drawSpacedText(ctx, (gradient.style || 'MODERN').toUpperCase(), valueX, specY + lineH * 2, 2, 'left');

        // 5. Subtle Branding URL (Bottom Right)
        ctx.font = '500 26px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillStyle = textMuted;
        drawSpacedText(ctx, 'colormagic.techkreative.com', 1780, 2360, 3, 'right');

        return canvas;
    };

    /**
     * Renders a gradient as a high-res PNG image and triggers download.
     * @param {Object} gradient - { id, name, css, colors, type, style, angle, shape }
     * @param {string} [filename] - Optional filename
     */
    window.ColorMagic.exportGradientImage = function (gradient, filename) {
        var canvas = window.ColorMagic.renderGradientCanvas(gradient);
        var name = gradient.name || 'gradient';
        var cleanName = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        var fname = filename || cleanName + '-gradient.png';
        downloadCanvas(canvas, fname);
    };

})();
