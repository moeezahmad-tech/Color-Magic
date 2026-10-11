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

    function toTitleCase(str) {
        if (!str) return '';
        if (str.charAt(0) === '#') return str.toUpperCase();
        return str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
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
     * Renders a palette as a high-res PNG image matching the horizontal bands poster design
     * (full-bleed horizontal color bands, HEX in each band, website name at top, URL at bottom).
     * @param {Object} palette - { name: string, colors: string[], style: string, colorNames?: string[] }
     * @param {string} [filename] - Optional filename
     */
    window.ColorMagic.exportPaletteImage = function (palette, filename) {
        var canvas = document.createElement('canvas');
        var W = 1500, H = 2000; // 3:4 portrait ratio (1500x2000)
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var colors = palette.colors || ['#000000', '#ffffff'];
        var name = (palette.name || 'Color Palette').toUpperCase();
        var n = colors.length;
        var bandH = H / n;

        // 1. Draw full-bleed horizontal color bands
        colors.forEach(function (hex, i) {
            var y0 = Math.floor(i * bandH);
            var y1 = (i === n - 1) ? H : Math.floor((i + 1) * bandH);
            ctx.fillStyle = hex;
            ctx.fillRect(0, y0, W, y1 - y0);

            // Text contrast for this band
            var lum = getLuminance(hex);
            var isLight = lum > 0.60;
            var textColor = isLight ? '#111827' : '#FFFFFF';

            // Clean HEX code near bottom-left of band
            var cleanHex = hex.replace('#', '').toUpperCase();
            var padX = 90;
            var hexY = y1 - 85;

            ctx.save();
            ctx.shadowColor = isLight ? 'rgba(255, 255, 255, 0.4)' : 'rgba(0, 0, 0, 0.25)';
            ctx.shadowBlur = 6;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 2;
            ctx.fillStyle = textColor;
            ctx.font = '800 56px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            ctx.textBaseline = 'alphabetic';
            drawSpacedText(ctx, cleanHex, padX, hexY, 2, 'left');
            ctx.restore();
        });

        // 2. Website Name at the top (and Palette Name on top-right)
        var topPadX = 90;
        var topPadY = 68;
        var firstColor = colors[0] || '#ffffff';
        var firstLum = getLuminance(firstColor);
        var firstIsLight = firstLum > 0.60;
        var topTextColor = firstIsLight ? '#111827' : '#FFFFFF';

        ctx.save();
        ctx.shadowColor = firstIsLight ? 'rgba(255, 255, 255, 0.4)' : 'rgba(0, 0, 0, 0.25)';
        ctx.shadowBlur = 6;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 2;
        ctx.fillStyle = topTextColor;
        ctx.textBaseline = 'top';

        // Top-Left: Website Name
        ctx.font = '800 30px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        drawSpacedText(ctx, 'COLOR MAGIC', topPadX, topPadY, 4, 'left');

        // Top-Right: Palette Name
        if (name) {
            ctx.font = '700 30px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            var maxNameW = W - topPadX * 2 - 340;
            var measuredNameW = ctx.measureText(name).width;
            if (measuredNameW > maxNameW) {
                var scaledSize = Math.max(18, Math.floor(30 * (maxNameW / measuredNameW)));
                ctx.font = '700 ' + scaledSize + 'px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            }
            drawSpacedText(ctx, name, W - topPadX, topPadY, 2, 'right');
        }
        ctx.restore();

        // 3. Website URL at the bottom
        var lastColor = colors[colors.length - 1] || '#000000';
        var lastLum = getLuminance(lastColor);
        var lastIsLight = lastLum > 0.60;
        var urlTextColor = lastIsLight ? 'rgba(17, 24, 39, 0.72)' : 'rgba(255, 255, 255, 0.78)';

        ctx.save();
        ctx.shadowColor = lastIsLight ? 'rgba(255, 255, 255, 0.4)' : 'rgba(0, 0, 0, 0.25)';
        ctx.shadowBlur = 6;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 2;
        ctx.fillStyle = urlTextColor;
        ctx.font = '600 24px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.textBaseline = 'alphabetic';
        drawSpacedText(ctx, 'colormagic.techkreative.com', W - topPadX, H - 85, 2, 'right');
        ctx.restore();

        // Download
        var cleanName = (palette.name || 'palette').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        var fname = filename || cleanName + '-palette.png';
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
     * Renders a gradient card canvas matching the poster reference style
     * (top ~66.8% gradient with uppercase title, bottom ~33.2% solid color blocks with names & hex).
     * @param {Object} gradient - { id, name, css, colors, type, style, angle, shape, colorNames }
     * @returns {HTMLCanvasElement}
     */
    window.ColorMagic.renderGradientCanvas = function (gradient) {
        var canvas = document.createElement('canvas');
        var W = 1648, H = 2048; // High-res 2x matching 824x1024 reference poster proportions
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var name = (gradient.name || 'Gradient').toUpperCase();
        var colors = gradient.colors || ['#000000', '#ffffff'];
        var type = (gradient.type || 'linear').toLowerCase();
        var angle = (typeof gradient.angle === 'number') ? gradient.angle : 135;

        // 1. Top Section: Gradient (top ~66.8% of total height)
        var gradientH = Math.round(H * 0.668);

        var grad;
        if (type === 'linear') {
            var rad = (angle - 90) * Math.PI / 180;
            var cx = W / 2, cy = gradientH / 2;
            var len = Math.sqrt(W * W + gradientH * gradientH);
            var dx = Math.cos(rad) * len / 2;
            var dy = Math.sin(rad) * len / 2;
            grad = ctx.createLinearGradient(cx - dx, cy - dy, cx + dx, cy + dy);
        } else {
            grad = ctx.createRadialGradient(W / 2, gradientH / 2, 0, W / 2, gradientH / 2, Math.max(W, gradientH) / 1.4);
        }

        colors.forEach(function (hex, i) {
            grad.addColorStop(i / Math.max(colors.length - 1, 1), hex);
        });

        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, gradientH);

        // 1b. Brand Tag & Gradient Title (Top-Left of Gradient Box)
        var topPadX = 92;
        var topPadY = 104;
        var firstColorLum = getLuminance(colors[0]);
        var topTitleColor = firstColorLum > 0.85 ? '#111827' : '#FFFFFF';

        ctx.save();
        ctx.shadowColor = firstColorLum > 0.85 ? 'rgba(255, 255, 255, 0.4)' : 'rgba(0, 0, 0, 0.25)';
        ctx.shadowBlur = 8;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 2;
        ctx.fillStyle = topTitleColor;
        ctx.textBaseline = 'top';

        // Line 1: COLOR MAGIC on top (brand kicker)
        ctx.font = '700 28px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        drawSpacedText(ctx, 'COLOR MAGIC', topPadX, topPadY, 4, 'left');

        // Line 2: Large text for the gradient name
        var nameY = topPadY + 48;
        var nameFontSize = 76;
        ctx.font = '900 ' + nameFontSize + 'px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        var maxNameW = W - topPadX * 2;
        var measuredNameW = ctx.measureText(name).width;
        if (measuredNameW > maxNameW) {
            nameFontSize = Math.max(36, Math.floor(nameFontSize * (maxNameW / measuredNameW)));
            ctx.font = '900 ' + nameFontSize + 'px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        }
        drawSpacedText(ctx, name, topPadX, nameY, 2, 'left');
        ctx.restore();

        // 1c. Website Link on the bottom-right corner of the gradient color box (NOT on the solid color bottom box)
        var lastColor = colors[colors.length - 1] || '#000000';
        var lastColorLum = getLuminance(lastColor);
        var urlColor = lastColorLum > 0.80 ? 'rgba(17, 24, 39, 0.75)' : 'rgba(255, 255, 255, 0.82)';

        ctx.save();
        ctx.shadowColor = lastColorLum > 0.80 ? 'rgba(255, 255, 255, 0.5)' : 'rgba(0, 0, 0, 0.3)';
        ctx.shadowBlur = 6;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 2;
        ctx.fillStyle = urlColor;
        ctx.font = '600 24px Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.textBaseline = 'bottom';
        drawSpacedText(ctx, 'colormagic.techkreative.com', W - topPadX, gradientH - 42, 2, 'right');
        ctx.restore();

        // 2. Bottom Section: Color Blocks (bottom ~33.2% of total height)
        var blockAreaY = gradientH;
        var blockAreaH = H - gradientH;
        var rects = [];

        if (colors.length === 3) {
            // Exactly matching user reference poster (3 colors):
            // Left block: ~57.3% width, full bottom height
            // Right top block: ~42.7% width, ~54% height
            // Right bottom block: ~42.7% width, ~46% height
            var leftW = Math.round(W * 0.573);
            var rightW = W - leftW;
            var topRightH = Math.round(blockAreaH * 0.54);
            var bottomRightH = blockAreaH - topRightH;

            rects.push({ x: 0, y: blockAreaY, w: leftW, h: blockAreaH });
            rects.push({ x: leftW, y: blockAreaY, w: rightW, h: topRightH });
            rects.push({ x: leftW, y: blockAreaY + topRightH, w: rightW, h: bottomRightH });
        } else if (colors.length === 2) {
            var colW = Math.round(W * 0.5);
            rects.push({ x: 0, y: blockAreaY, w: colW, h: blockAreaH });
            rects.push({ x: colW, y: blockAreaY, w: W - colW, h: blockAreaH });
        } else if (colors.length === 4) {
            var colW = Math.round(W * 0.5);
            var rowH = Math.round(blockAreaH * 0.5);
            rects.push({ x: 0, y: blockAreaY, w: colW, h: rowH });
            rects.push({ x: colW, y: blockAreaY, w: W - colW, h: rowH });
            rects.push({ x: 0, y: blockAreaY + rowH, w: colW, h: blockAreaH - rowH });
            rects.push({ x: colW, y: blockAreaY + rowH, w: W - colW, h: blockAreaH - rowH });
        } else {
            var colW = W / colors.length;
            for (var i = 0; i < colors.length; i++) {
                rects.push({
                    x: Math.round(i * colW),
                    y: blockAreaY,
                    w: Math.round(colW) + 1,
                    h: blockAreaH
                });
            }
        }

        rects.forEach(function (rect, i) {
            var hex = colors[i] || '#000000';

            // Draw solid block
            ctx.fillStyle = hex;
            ctx.fillRect(rect.x, rect.y, rect.w, rect.h);

            // Text contrast: luminance > 0.50 is light background => dark text
            var lum = getLuminance(hex);
            var isDarkText = lum > 0.50;
            var textColor = isDarkText ? '#111827' : '#FFFFFF';
            var labelColor = isDarkText ? 'rgba(17, 24, 39, 0.72)' : 'rgba(255, 255, 255, 0.75)';

            // Generous padding matching reference
            var padX = Math.max(36, Math.min(76, Math.round(rect.w * 0.08)));
            var padY = Math.max(36, Math.min(74, Math.round(rect.h * 0.18)));
            var textX = rect.x + padX;
            var textY = rect.y + padY;

            // Color name in Title Case
            var rawName = (gradient.colorNames && gradient.colorNames[i]) ? gradient.colorNames[i] : '';
            if (!rawName && window.ColorMagic.colorDict && window.ColorMagic.findColorName) {
                rawName = window.ColorMagic.findColorName(hex, window.ColorMagic.colorDict);
            }
            if (!rawName) {
                rawName = hex.toUpperCase();
            }
            var colorName = toTitleCase(rawName);

            // Measure and fit color name on single line
            var maxTextW = rect.w - padX * 1.4;
            var nameFontSize = 42;
            ctx.font = '800 ' + nameFontSize + 'px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            var measuredW = ctx.measureText(colorName).width;
            if (measuredW > maxTextW) {
                nameFontSize = Math.max(22, Math.floor(nameFontSize * (maxTextW / measuredW)));
                ctx.font = '800 ' + nameFontSize + 'px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            }

            ctx.fillStyle = textColor;
            ctx.textBaseline = 'top';
            ctx.textAlign = 'left';
            ctx.fillText(colorName, textX, textY);

            // Line 2: HEX #XXXXXX
            var line2Y = textY + nameFontSize + 16;
            ctx.font = '700 24px Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            ctx.fillStyle = labelColor;
            ctx.fillText('HEX', textX, line2Y);

            var hexLabelW = ctx.measureText('HEX').width;
            ctx.fillStyle = textColor;
            ctx.fillText(hex.toUpperCase(), textX + hexLabelW + 28, line2Y);
        });

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
