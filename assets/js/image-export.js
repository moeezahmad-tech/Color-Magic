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
        return getLuminance(hex) > 0.5 ? '#1e293b' : '#ffffff';
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

    /**
     * Renders a single color as a PNG image and triggers download.
     * @param {Object} colorData - { hex: string, name: string, rgb: {r,g,b}, hsl: {h,s,l} }
     * @param {string} [filename] - Optional filename
     */
    window.ColorMagic.exportColorImage = function (colorData, filename) {
        var canvas = document.createElement('canvas');
        var W = 4800, H = 3200;
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var hex = colorData.hex || '#000000';
        var name = colorData.name || hex;
        var rgb = colorData.rgb || hexToRgb(hex);
        var hsl = colorData.hsl || { h: 0, s: 0, l: 0 };

        // Color fill (top 60%)
        var colorH = 1920;
        ctx.fillStyle = hex;
        ctx.fillRect(0, 0, W, colorH);

        // Hex label on color area
        var textOnColor = bestTextColor(hex);
        ctx.fillStyle = textOnColor;
        ctx.font = 'bold 288px Inter, system-ui, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(hex.toUpperCase(), W / 2, colorH / 2 - 80);

        // Color name below hex
        ctx.font = '500 112px Inter, system-ui, sans-serif';
        ctx.globalAlpha = 0.85;
        ctx.fillText(name, W / 2, colorH / 2 + 160);
        ctx.globalAlpha = 1;

        // Bottom info area
        var infoY = colorH;

        // White background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, infoY, W, H - infoY);

        // Divider
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 5;
        ctx.beginPath();
        ctx.moveTo(160, infoY);
        ctx.lineTo(W - 160, infoY);
        ctx.stroke();

        // Color values in cards
        var cardW = 1360;
        var cardH = 720;
        var cardGap = 120;
        var cardY = infoY + 160;
        var startX = (W - (3 * cardW + 2 * cardGap)) / 2;

        var cards = [
            { label: 'HEX', value: hex.toUpperCase() },
            { label: 'RGB', value: rgb.r + ', ' + rgb.g + ', ' + rgb.b },
            { label: 'HSL', value: hsl.h + '\u00b0, ' + hsl.s + '%, ' + hsl.l + '%' }
        ];

        cards.forEach(function (card, i) {
            var x = startX + i * (cardW + cardGap);

            // Card background
            roundRect(ctx, x, cardY, cardW, cardH, 64);
            ctx.fillStyle = '#f8fafc';
            ctx.fill();
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 4;
            ctx.stroke();

            // Label
            ctx.fillStyle = '#64748b';
            ctx.font = '600 56px Inter, system-ui, sans-serif';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'top';
            ctx.fillText(card.label, x + 96, cardY + 96);

            // Value
            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 128px Inter, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(card.value, x + cardW / 2, cardY + cardH / 2 + 40);
        });

        // Branding
        ctx.fillStyle = '#94a3b8';
        ctx.font = '500 56px Inter, system-ui, sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'bottom';
        ctx.fillText('Color Magic \u00b7 colormagic.techkreative.com', W - 200, H - 120);

        // Download
        var fname = filename || hex.replace('#', '').toLowerCase() + '-color.png';
        downloadCanvas(canvas, fname);
    };

    // ─── Gradient Image Export ─────────────────────────────────────────────────

    /**
     * Renders a gradient as a PNG image and triggers download.
     * @param {Object} gradient - { id, name, css, colors, type, style, angle, shape }
     * @param {string} [filename] - Optional filename
     */
    window.ColorMagic.exportGradientImage = function (gradient, filename) {
        var canvas = document.createElement('canvas');
        var W = 4800, H = 3200;
        canvas.width = W;
        canvas.height = H;
        var ctx = canvas.getContext('2d');

        var name = gradient.name || 'CSS Gradient';
        var css = gradient.css || '';
        var colors = gradient.colors || [];
        var type = gradient.type || 'linear';
        var angle = gradient.angle || 135;

        // Gradient fill (top 60%)
        var gradH = 1920;
        var grad;
        if (type === 'linear') {
            var rad = (angle - 90) * Math.PI / 180;
            var cx = W / 2, cy = gradH / 2;
            var len = Math.max(W, gradH);
            var dx = Math.cos(rad) * len / 2;
            var dy = Math.sin(rad) * len / 2;
            grad = ctx.createLinearGradient(cx - dx, cy - dy, cx + dx, cy + dy);
        } else {
            grad = ctx.createRadialGradient(W / 2, gradH / 2, 0, W / 2, gradH / 2, Math.max(W, gradH) / 2);
        }

        colors.forEach(function (hex, i) {
            grad.addColorStop(i / Math.max(colors.length - 1, 1), hex);
        });

        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, gradH);

        // Gradient name on gradient area
        ctx.fillStyle = '#ffffff';
        ctx.shadowColor = 'rgba(0,0,0,0.5)';
        ctx.shadowBlur = 48;
        ctx.font = 'bold 168px Inter, system-ui, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(name, W / 2, gradH / 2 - 80);

        // CSS code below name
        ctx.shadowBlur = 32;
        ctx.font = '500 72px Inter, system-ui, sans-serif';
        ctx.globalAlpha = 0.9;
        var cssShort = css.length > 80 ? css.substring(0, 77) + '...' : css;
        ctx.fillText('background: ' + cssShort + ';', W / 2, gradH / 2 + 120);
        ctx.globalAlpha = 1;
        ctx.shadowBlur = 0;
        ctx.shadowColor = 'transparent';

        // Bottom info area
        var infoY = gradH;

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, infoY, W, H - infoY);

        // Divider
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 5;
        ctx.beginPath();
        ctx.moveTo(160, infoY);
        ctx.lineTo(W - 160, infoY);
        ctx.stroke();

        // Info text
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 112px Inter, system-ui, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        ctx.fillText(name, 200, infoY + 120);

        var angleOrShape = type === 'linear' ? (angle + '\u00b0') : (gradient.shape || type);
        ctx.fillStyle = '#64748b';
        ctx.font = '500 64px Inter, system-ui, sans-serif';
        ctx.fillText(gradient.style + ' \u00b7 ' + type + ' \u00b7 ' + angleOrShape + ' \u00b7 ' + colors.length + ' colors', 200, infoY + 280);

        // Color swatches
        var swatchSize = 280;
        var swatchGap = 64;
        var swatchesY = infoY + 480;
        colors.forEach(function (hex, i) {
            var x = 200 + i * (swatchSize + swatchGap);

            roundRect(ctx, x, swatchesY, swatchSize, swatchSize, 48);
            ctx.fillStyle = hex;
            ctx.fill();

            ctx.fillStyle = '#475569';
            ctx.font = '600 52px Inter, system-ui, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(hex.toUpperCase(), x + swatchSize / 2, swatchesY + swatchSize + 40);
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

})();
