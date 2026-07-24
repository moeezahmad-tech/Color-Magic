/**
 * palette-from-image.js
 * Extracts dominant colors from an uploaded image using k-means clustering
 * and renders them as a copyable palette. All processing is client-side.
 */
(function () {
    // ── DOM refs ───────────────────────────────────────────────────────────────
    const dropZone            = document.getElementById('dropZone');
    const fileInput           = document.getElementById('fileInput');
    const dropZoneContent     = document.getElementById('dropZoneContent');
    const extractingOverlay   = document.getElementById('extractingOverlay');
    const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
    const imagePreview        = document.getElementById('imagePreview');
    const removeImageBtn      = document.getElementById('removeImageBtn');
    const colorCountSlider    = document.getElementById('colorCountSlider');
    const colorCountBadge     = document.getElementById('colorCountBadge');
    const extractBtn          = document.getElementById('extractBtn');
    const resultsPlaceholder  = document.getElementById('resultsPlaceholder');
    const paletteSwatchResults = document.getElementById('paletteSwatchResults');
    const paletteDetails      = document.getElementById('paletteDetails');
    const copyToast           = document.getElementById('copyToast');
    const copyToastText       = document.getElementById('copyToastText');

    // Hidden canvas for pixel sampling
    const canvas = document.createElement('canvas');
    const ctx    = canvas.getContext('2d', { willReadFrequently: true });

    let loadedImage = null;   // HTMLImageElement of the currently loaded image
    let toastTimer  = null;

    // ── Slider badge sync ──────────────────────────────────────────────────────
    colorCountSlider?.addEventListener('input', () => {
        colorCountBadge.textContent = colorCountSlider.value;
    });

    // ── Drag & drop ────────────────────────────────────────────────────────────
    ;['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('drag-over');
        });
    });

    ;['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('drag-over');
        });
    });

    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer?.files;
        if (files && files.length) handleFile(files[0]);
    });

    // ── File input change ──────────────────────────────────────────────────────
    fileInput?.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) {
            handleFile(fileInput.files[0]);
        }
    });

    // ── Remove image ───────────────────────────────────────────────────────────
    removeImageBtn?.addEventListener('click', () => {
        resetState();
    });

    // ── Extract button ─────────────────────────────────────────────────────────
    extractBtn?.addEventListener('click', () => {
        if (!loadedImage) return;
        const k = parseInt(colorCountSlider.value, 10) || 6;
        runExtraction(k);
    });

    // ── Handle incoming file ───────────────────────────────────────────────────
    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            showToast('Please select a valid image file.');
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            const img = new Image();
            img.onload = () => {
                loadedImage = img;
                imagePreview.src = e.target.result;
                dropZone.classList.add('hidden');
                imagePreviewWrapper.classList.remove('hidden');
                // Reset results
                resultsPlaceholder?.classList.remove('hidden');
                paletteSwatchResults?.classList.add('hidden');
                paletteDetails?.classList.add('hidden');
                if (paletteSwatchResults) paletteSwatchResults.innerHTML = '';
                if (paletteDetails) paletteDetails.innerHTML = '';
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // ── Reset to initial upload state ──────────────────────────────────────────
    function resetState() {
        loadedImage = null;
        imagePreview.src = '';
        imagePreviewWrapper.classList.add('hidden');
        dropZone.classList.remove('hidden');
        if (fileInput) fileInput.value = '';
        resultsPlaceholder?.classList.remove('hidden');
        paletteSwatchResults?.classList.add('hidden');
        paletteDetails?.classList.add('hidden');
        if (paletteSwatchResults) paletteSwatchResults.innerHTML = '';
        if (paletteDetails) paletteDetails.innerHTML = '';
    }

    // ── Main extraction pipeline ───────────────────────────────────────────────
    async function runExtraction(k) {
        showOverlay(true);

        // Defer heavy work so the overlay paints
        await sleep(80);

        try {
            const pixels = samplePixels(loadedImage, 200);  // max 200px on longest side
            const clusters = kMeans(pixels, k, 20);          // 20 iterations max

            // Sort clusters by size descending (most dominant first)
            clusters.sort((a, b) => b.count - a.count);

            renderResults(clusters, loadedImage);
        } catch (err) {
            console.error('Extraction error:', err);
            showToast('Something went wrong. Please try a different image.');
        }

        showOverlay(false);
    }

    // ── Pixel sampling ─────────────────────────────────────────────────────────
    function samplePixels(img, maxDim) {
        const scale = Math.min(1, maxDim / Math.max(img.naturalWidth, img.naturalHeight));
        const w = Math.round(img.naturalWidth  * scale);
        const h = Math.round(img.naturalHeight * scale);

        canvas.width  = w;
        canvas.height = h;
        ctx.drawImage(img, 0, 0, w, h);

        const imageData = ctx.getImageData(0, 0, w, h);
        const data = imageData.data; // RGBA flat array

        const pixels = [];
        for (let i = 0; i < data.length; i += 4) {
            const a = data[i + 3];
            // Skip fully transparent pixels
            if (a < 128) continue;
            pixels.push([data[i], data[i + 1], data[i + 2]]);
        }

        return pixels;
    }

    // ── K-Means clustering ─────────────────────────────────────────────────────
    function kMeans(pixels, k, maxIter) {
        if (!pixels.length) return [];

        // Smart initial centroids: spread across pixel range (k-means++)
        const centroids = initCentroidsPlusPlus(pixels, k);

        let assignments = new Uint16Array(pixels.length);

        for (let iter = 0; iter < maxIter; iter++) {
            let changed = false;

            // Assign each pixel to nearest centroid
            for (let i = 0; i < pixels.length; i++) {
                const p  = pixels[i];
                let best = 0;
                let bestDist = Infinity;
                for (let c = 0; c < centroids.length; c++) {
                    const d = colorDist(p, centroids[c]);
                    if (d < bestDist) { bestDist = d; best = c; }
                }
                if (assignments[i] !== best) {
                    assignments[i] = best;
                    changed = true;
                }
            }

            if (!changed) break;

            // Recompute centroids
            const sums   = centroids.map(() => [0, 0, 0]);
            const counts = new Float64Array(centroids.length);

            for (let i = 0; i < pixels.length; i++) {
                const c = assignments[i];
                sums[c][0] += pixels[i][0];
                sums[c][1] += pixels[i][1];
                sums[c][2] += pixels[i][2];
                counts[c]++;
            }

            for (let c = 0; c < centroids.length; c++) {
                if (counts[c] === 0) continue;
                centroids[c] = [
                    Math.round(sums[c][0] / counts[c]),
                    Math.round(sums[c][1] / counts[c]),
                    Math.round(sums[c][2] / counts[c])
                ];
            }
        }

        // Build cluster result objects
        const counts = new Float64Array(centroids.length);
        for (let i = 0; i < assignments.length; i++) counts[assignments[i]]++;

        return centroids.map((rgb, i) => ({
            r: rgb[0], g: rgb[1], b: rgb[2],
            count: counts[i]
        }));
    }

    // ── K-means++ initialisation ───────────────────────────────────────────────
    function initCentroidsPlusPlus(pixels, k) {
        const centroids = [];
        // First centroid: random pixel
        const first = pixels[Math.floor(Math.random() * pixels.length)];
        centroids.push([...first]);

        for (let c = 1; c < k; c++) {
            // Compute min distances to existing centroids
            let totalDist = 0;
            const dists = new Float64Array(pixels.length);
            for (let i = 0; i < pixels.length; i++) {
                let minD = Infinity;
                for (const cent of centroids) {
                    const d = colorDist(pixels[i], cent);
                    if (d < minD) minD = d;
                }
                dists[i] = minD;
                totalDist += minD;
            }

            // Weighted random selection
            let r = Math.random() * totalDist;
            for (let i = 0; i < pixels.length; i++) {
                r -= dists[i];
                if (r <= 0) { centroids.push([...pixels[i]]); break; }
            }
            if (centroids.length <= c) {
                // Fallback: pick random pixel
                centroids.push([...pixels[Math.floor(Math.random() * pixels.length)]]);
            }
        }

        return centroids;
    }

    // ── Squared Euclidean distance in RGB space ────────────────────────────────
    function colorDist(a, b) {
        const dr = a[0] - b[0];
        const dg = a[1] - b[1];
        const db = a[2] - b[2];
        return dr * dr + dg * dg + db * db;
    }

    // ── Color conversion helpers ───────────────────────────────────────────────
    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('').toUpperCase();
    }

    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h = 0, s = 0, l = (max + min) / 2;

        if (max !== min) {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            if (max === r)      h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
            else if (max === g) h = ((b - r) / d + 2) / 6;
            else                h = ((r - g) / d + 4) / 6;
        }
        return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
    }

    function getContrastYIQ(r, g, b) {
        const yiq = (r * 299 + g * 587 + b * 114) / 1000;
        return yiq >= 128 ? '#000000' : '#FFFFFF';
    }

    function getColorName(h, s, l) {
        h = ((Math.round(h) % 360) + 360) % 360;
        if (s < 10) {
            if (l > 90) return 'White';
            if (l > 70) return 'Light Gray';
            if (l > 50) return 'Gray';
            if (l > 30) return 'Dark Gray';
            return 'Charcoal';
        }
        if (h < 15)  return l > 60 ? 'Light Red'    : l > 40 ? 'Red'         : 'Dark Red';
        if (h < 45)  return l > 60 ? 'Peach'        : l > 40 ? 'Orange'      : 'Burnt Orange';
        if (h < 70)  return l > 60 ? 'Light Yellow' : l > 40 ? 'Yellow'      : 'Gold';
        if (h < 150) return l > 60 ? 'Light Green'  : l > 40 ? 'Green'       : 'Dark Green';
        if (h < 200) return l > 60 ? 'Mint'         : l > 40 ? 'Cyan'        : 'Teal';
        if (h < 250) return l > 60 ? 'Sky Blue'     : l > 40 ? 'Blue'        : 'Navy';
        if (h < 290) return l > 60 ? 'Lavender'     : l > 40 ? 'Purple'      : 'Deep Purple';
        if (h < 330) return l > 60 ? 'Pink'         : l > 40 ? 'Magenta'     : 'Maroon';
        return l > 60 ? 'Rose' : l > 40 ? 'Crimson' : 'Burgundy';
    }

    // ── Render extracted palette ───────────────────────────────────────────────
    function renderResults(clusters, img) {
        const totalPixels = clusters.reduce((s, c) => s + c.count, 0);

        const colors = clusters.map(c => {
            const hex  = rgbToHex(c.r, c.g, c.b);
            const hsl  = rgbToHsl(c.r, c.g, c.b);
            const pct  = totalPixels ? ((c.count / totalPixels) * 100).toFixed(1) : 0;
            return {
                hex,
                r: c.r, g: c.g, b: c.b,
                hsl,
                name: getColorName(hsl.h, hsl.s, hsl.l),
                pct,
                textColor: getContrastYIQ(c.r, c.g, c.b)
            };
        });

        resultsPlaceholder?.classList.add('hidden');
        paletteSwatchResults?.classList.remove('hidden');
        paletteDetails?.classList.remove('hidden');
        paletteSwatchResults.innerHTML = '';
        paletteDetails.innerHTML = '';

        // ── Swatch bar (matches explore palette card UI) ─────────────────
        const swatchBar = document.createElement('div');
        swatchBar.className = 'flex flex-col gap-4 anim-fade-in-up';

        const base = window.CM_BASE || '';

        const isLight = (hex) => {
            const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
            return ((r*299)+(g*587)+(b*114))/1000 >= 128;
        };

        const swatchesHTML = colors.map(c => {
            const light    = isLight(c.hex);
            const textCls  = light ? 'text-slate-800' : 'text-white';
            const bgCls    = light ? 'bg-white/30'    : 'bg-black/30';
            const btnBase  = light
                ? 'bg-white/70 hover:bg-white text-slate-800 backdrop-blur-sm'
                : 'bg-black/40 hover:bg-black/70 text-white backdrop-blur-sm';
            const hexBare  = c.hex.replace('#','');

            return '<div class="swatch min-w-0 relative flex flex-col justify-end p-2 hover:scale-[1.02] transition-transform active:scale-95" style="background-color:' + c.hex + '" data-hex="' + c.hex + '">'
                + '<div class="swatch-actions absolute top-2 left-2 flex flex-col gap-1 pointer-events-none">'
                +   '<button class="swatch-copy-hex swatch-icon-btn swatch-btn-1 ' + btnBase + ' w-6 h-6 rounded-md flex items-center justify-center shadow-sm pointer-events-auto" data-hex="' + c.hex + '" title="Copy ' + c.hex + '" type="button">'
                +     '<i class="bi bi-clipboard" style="font-size:11px;line-height:1"></i>'
                +   '</button>'
                +   '<a class="swatch-open-color swatch-icon-btn swatch-btn-2 ' + btnBase + ' w-6 h-6 rounded-md flex items-center justify-center shadow-sm pointer-events-auto" href="' + base + '/color/' + hexBare + '/" target="_blank" rel="noopener" title="Open color page">'
                +     '<i class="bi bi-box-arrow-up-right" style="font-size:11px;line-height:1"></i>'
                +   '</a>'
                + '</div>'
                + '<span class="swatch-hex text-[10px] font-bold ' + textCls + ' ' + bgCls + ' backdrop-blur-sm px-1.5 py-0.5 rounded text-center transition-all relative z-10">' + c.hex + '</span>'
                + '</div>';
        }).join('');

        const allHex = colors.map(c => c.hex).join(', ');

        swatchBar.innerHTML =
            '<div class="grid h-56 w-full rounded-2xl overflow-hidden shadow-xl shadow-black/5 dark:shadow-black/20 ring-1 ring-slate-200 dark:ring-slate-800" style="grid-template-columns:repeat(' + colors.length + ', minmax(0, 1fr));">'
            + swatchesHTML
            + '</div>'
            + '<div class="flex items-center justify-between px-1">'
            +   '<div>'
            +     '<h3 class="font-bold text-lg">Extracted Palette</h3>'
            +     '<p class="text-xs text-slate-500">' + colors.length + ' dominant colors &bull; K-Means clustering</p>'
            +   '</div>'
            +   '<div class="flex items-center gap-4">'
            +     '<button class="copy-all-btn p-1.5 text-slate-400 hover:text-primary transition-colors" data-colors="' + allHex + '" title="Copy all colors" aria-label="Copy all colors">'
            +       '<i class="bi bi-clipboard text-xl" aria-hidden="true"></i>'
            +     '</button>'
            +   '</div>'
            + '</div>';

        paletteSwatchResults.appendChild(swatchBar);

        // ── Color detail rows ─────────────────────────────────────────────────
        const detailCard = document.createElement('div');
        detailCard.className = 'bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-5 md:p-6 flex flex-col gap-3 anim-fade-in-up';
        detailCard.style.animationDelay = '0.08s';

        const rowsHTML = colors.map((c, i) => `
            <div class="color-row flex flex-wrap sm:flex-nowrap items-center gap-4 p-4 rounded-xl border border-slate-200 dark:border-slate-700 transition-all">
                <!-- Swatch + name -->
                <div class="flex items-center gap-3 min-w-[160px] flex-1">
                    <div class="w-12 h-12 rounded-xl shadow-sm ring-2 ring-white dark:ring-slate-700 shrink-0"
                         style="background-color:${c.hex};"></div>
                    <div>
                        <p class="font-bold text-sm">${c.name}</p>
                        <button class="text-xs text-slate-500 font-mono hover:text-fuchsia-500 transition-colors text-left"
                                onclick="window.__cmCopyText('${c.hex}', this)" title="Copy hex">
                            ${c.hex}
                        </button>
                    </div>
                </div>
                <!-- % bar -->
                <div class="flex items-center gap-2 min-w-[100px] flex-1">
                    <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width:${c.pct}%; background-color:${c.hex};"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 w-10 text-right">${c.pct}%</span>
                </div>
                <!-- RGB -->
                <div class="flex flex-col items-end text-xs min-w-[110px]">
                    <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider">RGB</span>
                    <button class="font-mono font-medium hover:text-fuchsia-500 transition-colors"
                            onclick="window.__cmCopyText('rgb(${c.r}, ${c.g}, ${c.b})', this)">
                        ${c.r}, ${c.g}, ${c.b}
                    </button>
                </div>
                <!-- HSL -->
                <div class="flex flex-col items-end text-xs min-w-[130px]">
                    <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider">HSL</span>
                    <button class="font-mono font-medium hover:text-fuchsia-500 transition-colors"
                            onclick="window.__cmCopyText('hsl(${c.hsl.h}, ${c.hsl.s}%, ${c.hsl.l}%)', this)">
                        ${c.hsl.h}°, ${c.hsl.s}%, ${c.hsl.l}%
                    </button>
                </div>
            </div>
        `).join('');

        detailCard.innerHTML = `
            <h3 class="text-base font-bold mb-1">Color Details</h3>
            ${rowsHTML}
        `;

        paletteDetails.appendChild(detailCard);

        // ── Attach event listeners ──────────────────────────────────────────────
        // Copy individual swatch hex
        swatchBar.querySelectorAll('.swatch-copy-hex').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                const hex = btn.dataset.hex;
                navigator.clipboard.writeText(hex).then(() => {
                    const hexLabel = btn.closest('.swatch')?.querySelector('.swatch-hex');
                    if (hexLabel) {
                        hexLabel.classList.add('copied-state');
                        hexLabel.textContent = 'Copied!';
                        setTimeout(() => {
                            hexLabel.classList.remove('copied-state');
                            hexLabel.textContent = hex;
                        }, 1200);
                    }
                }).catch(() => {});
            });
        });

        // Copy all hex
        swatchBar.querySelector('.copy-all-btn')?.addEventListener('click', function () {
            const colors = this.dataset.colors || '';
            navigator.clipboard.writeText(colors).then(() => {
                const icon = this.querySelector('i');
                if (icon) { icon.className = 'bi bi-clipboard-check text-xl text-green-500'; }
                setTimeout(() => {
                    if (icon) { icon.className = 'bi bi-clipboard text-xl'; }
                }, 1500);
            }).catch(() => {});
        });
    }

    // ── UI helpers ─────────────────────────────────────────────────────────────
    function showOverlay(show) {
        extractingOverlay?.classList.toggle('hidden', !show);
        extractingOverlay?.classList.toggle('flex', show);
        if (extractBtn) extractBtn.disabled = show;
    }

    function sleep(ms) {
        return new Promise(r => setTimeout(r, ms));
    }

    // ── Toast ──────────────────────────────────────────────────────────────────
    function showToast(msg) {
        if (!copyToast || !copyToastText) return;
        copyToastText.textContent = msg;
        copyToast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
        copyToast.classList.add('opacity-100', 'translate-y-0');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            copyToast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
            copyToast.classList.remove('opacity-100', 'translate-y-0');
        }, 1800);
    }

    // ── Global copy helpers (called from inline onclick) ───────────────────────
    window.__cmCopyText = function (text, el) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = el.textContent;
            el.textContent = 'Copied!';
            el.classList?.add('text-green-500');
            setTimeout(() => { el.textContent = orig; el.classList?.remove('text-green-500'); }, 1200);
        }).catch(() => {
            showToast('Copy failed — please copy manually.');
        });
    };

    window.__cmCopyAllHex = function (btn) {
        const allHex = btn.getAttribute('data-all-hex') || '';
        if (!allHex) return;
        navigator.clipboard.writeText(allHex).then(() => {
            const origHTML = btn.innerHTML;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-green-500', 'text-white');
            setTimeout(() => {
                btn.innerHTML = origHTML;
                btn.classList.remove('bg-green-500', 'text-white');
            }, 1400);
        }).catch(() => {
            showToast('Copy failed.');
        });
    };

})();
