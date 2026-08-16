/**
 * find-color-page.js
 * Powers the standalone Find Color page (find-color.php)
 */

(function () {
    // ── DOM refs ──────────────────────────────────────────────────────────────
    const colorCodeInput   = document.getElementById('colorCodeInput');
    const findColorBtn     = document.getElementById('findColorBtn');
    const errorMessage     = document.getElementById('errorMessage');
    const colorResultsGrid = document.getElementById('colorResultsGrid');
    const historyRow       = document.getElementById('historyRow');
    const historyChips     = document.getElementById('historyChips');

    // ── Color names DB ────────────────────────────────────────────────────────
    let colorNames = {};
    let isColorNamesLoaded = false;

    async function fetchColorNames() {
        try {
            let res = await fetch("https://api.colormagic.techkreative.com/color-names.json");
            if (!res.ok) throw new Error('Failed to load colors (Status: ' + res.status + ')');
            
            let data = await res.json();
            colorNames = (data && data.data !== undefined) ? data.data : data;
            isColorNamesLoaded = true;
        } catch (err) {
            // Non-fatal — fall back to hex as name
            console.warn('Color names not loaded:', err.message);
        }
    }

    const fetchColorNamesPromise = fetchColorNames();
    fetchColorNamesPromise.then(() => {
        // Render history chips after DB is ready
        renderHistoryChips();
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function getColorName(hex) {
        // Keys in color-names.json are uppercase hex without '#'
        const key = hex.toUpperCase().replace('#', '');
        const entry = colorNames[key];
        if (entry && typeof entry === 'object') return entry.name || hex.toUpperCase();
        return hex.toUpperCase();
    }

    function hexToRgb(hex) {
        const h = hex.replace('#', '');
        return {
            r: parseInt(h.substring(0, 2), 16),
            g: parseInt(h.substring(2, 4), 16),
            b: parseInt(h.substring(4, 6), 16),
        };
    }

    function hexToHsl(hex) {
        const { r, g, b } = hexToRgb(hex);
        let rn = r / 255, gn = g / 255, bn = b / 255;
        const max = Math.max(rn, gn, bn), min = Math.min(rn, gn, bn);
        let h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case rn: h = (gn - bn) / d + (gn < bn ? 6 : 0); break;
                case gn: h = (bn - rn) / d + 2; break;
                case bn: h = (rn - gn) / d + 4; break;
            }
            h /= 6;
        }
        return { h: h * 360, s: s * 100, l: l * 100 };
    }

    function getLuminance(hex) {
        const { r, g, b } = hexToRgb(hex);
        const a = [r, g, b].map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function getBestContrast(hex) {
        return getLuminance(hex) > 0.179 ? 'Black' : 'White';
    }

    // ── History ───────────────────────────────────────────────────────────────
    function getHistory() {
        try { return JSON.parse(localStorage.getItem('colorHistory') || '[]'); }
        catch (_) { return []; }
    }

    function saveToHistory(data) {
        let history = getHistory();
        history = history.filter(item => item.hex.toLowerCase() !== data.hex.toLowerCase());
        history.unshift(data);
        if (history.length > 10) history = history.slice(0, 10);
        try { localStorage.setItem('colorHistory', JSON.stringify(history)); } catch (_) {}
    }

    function renderHistoryChips() {
        const history = getHistory();
        if (history.length === 0) {
            historyRow.classList.add('hidden');
            return;
        }
        historyRow.classList.remove('hidden');
        historyChips.innerHTML = '';
        history.forEach(item => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'history-chip flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium shadow-sm hover:border-primary transition-all';
            chip.innerHTML = `
                <span class="w-5 h-5 rounded-md shrink-0 border border-white/30 shadow-sm" style="background-color:${item.hex};"></span>
                <span class="font-mono text-xs font-bold">${item.hex.toUpperCase()}</span>
                ${item.name && item.name !== item.hex.toUpperCase() ? `<span class="text-slate-400 text-xs hidden sm:inline">· ${item.name}</span>` : ''}
            `;
            chip.addEventListener('click', () => {
                colorCodeInput.value = item.hex.replace('#', '').toUpperCase();
                // Update live preview
                document.getElementById('livePreview').style.backgroundColor = item.hex;
                document.getElementById('liveHexLabel').textContent = item.hex.toUpperCase();
                findColor();
            });
            historyChips.appendChild(chip);
        });
    }

    // ── Main find logic ───────────────────────────────────────────────────────
    async function findColor() {
        // Wait for DB if still loading instead of blocking the user
        if (!isColorNamesLoaded) {
            findColorBtn.innerHTML = '<i class="bi bi-hourglass-split text-base"></i> Loading DB…';
            findColorBtn.disabled = true;
            await fetchColorNamesPromise;
            findColorBtn.disabled = false;
        }

        const originalHTML = findColorBtn.innerHTML;
        findColorBtn.innerHTML = '<i class="bi bi-hourglass-split text-base"></i> Loading…';
        findColorBtn.disabled = true;
        colorCodeInput.disabled = true;
        errorMessage.classList.add('hidden');

        let val = colorCodeInput.value.trim().replace('#', '');

        if (!val || !/^[0-9A-F]{6}$/i.test(val)) {
            showError('Please enter a valid 6-character hex code (e.g., FF5733)');
            findColorBtn.innerHTML = originalHTML;
            findColorBtn.disabled = false;
            colorCodeInput.disabled = false;
            return;
        }

        const fullHex = '#' + val.toUpperCase();

        await new Promise(r => setTimeout(r, 400));

        try {
            const rgb  = hexToRgb(fullHex);
            const hsl  = hexToHsl(fullHex);
            const name = getColorName(fullHex);
            const contrast = getBestContrast(fullHex);

            displayColorInfo({ hex: fullHex, rgb, hsl, name, contrast });
            saveToHistory({ hex: fullHex, name, timestamp: Date.now() });
            renderHistoryChips();

            // Update live preview
            document.getElementById('livePreview').style.backgroundColor = fullHex;
            document.getElementById('liveHexLabel').textContent = fullHex;

        } catch (err) {
            showError('Something went wrong. Please try another hex code.');
        }

        findColorBtn.innerHTML = originalHTML;
        findColorBtn.disabled = false;
        colorCodeInput.disabled = false;
    }

    findColorBtn?.addEventListener('click', findColor);

    function showError(msg) {
        errorMessage.textContent = msg;
        errorMessage.classList.remove('hidden');
    }

    // ── Display result card ───────────────────────────────────────────────────
    function displayColorInfo(colorData) {
        const { hex, rgb, hsl, name, contrast } = colorData;
        const cleanHex     = hex.replace('#', '').toLowerCase();
        const colorPageUrl = `color/${cleanHex}/`;
        const colorImageUrl = `https://colormagic.techkreative.com/colorImage.php?hex=${cleanHex}`;
        const rgbText  = `${rgb.r}, ${rgb.g}, ${rgb.b}`;
        const hslText  = `${Math.round(hsl.h)}°, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%`;
        const contrastColor = contrast === 'White' ? '#FFFFFF' : '#000000';

        colorResultsGrid.innerHTML = '';

        const card = document.createElement('div');
        card.className = 'result-card bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-xl border border-slate-200 dark:border-slate-700';

        card.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

                <!-- Color swatch panel -->
                <div class="relative min-h-[300px] flex flex-col justify-end overflow-hidden" style="background-color:${hex};">
                    <img src="${colorImageUrl}"
                        alt="${name} color swatch"
                        class="absolute inset-0 w-full h-full object-cover opacity-80"
                        loading="eager" fetchpriority="high" />
                    <div class="relative z-10 p-8" style="color:${contrastColor};">
                        <p class="text-sm font-semibold opacity-70 mb-1">${name}</p>
                        <h2 class="text-4xl font-bold font-mono tracking-wide">${hex}</h2>
                    </div>
                </div>

                <!-- Info panel -->
                <div class="p-7 flex flex-col gap-6">

                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Color Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">Name</span>
                                <span class="font-bold">${name}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">HEX</span>
                                <span class="font-mono font-bold">${hex}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">RGB</span>
                                <span class="font-mono">${rgbText}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">HSL</span>
                                <span class="font-mono">${hslText}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-slate-500 dark:text-slate-400 text-sm">Best Contrast</span>
                                <span class="flex items-center gap-2 font-bold">
                                    <span class="w-5 h-5 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm" style="background:${contrastColor};"></span>
                                    ${contrast}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Copy buttons -->
                    <div class="grid grid-cols-3 gap-2">
                        <button class="copy-btn px-3 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-xl text-xs font-bold transition-all" data-copy="${hex}">
                            Copy HEX
                        </button>
                        <button class="copy-btn px-3 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-xl text-xs font-bold transition-all" data-copy="rgb(${rgbText})">
                            Copy RGB
                        </button>
                        <button class="copy-btn px-3 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white rounded-xl text-xs font-bold transition-all" data-copy="hsl(${hslText})">
                            Copy HSL
                        </button>
                    </div>

                    <!-- View palette link -->
                    <a href="${colorPageUrl}"
                        class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-secondary to-primary text-white font-bold rounded-xl hover:shadow-lg hover:shadow-primary/25 hover:-translate-y-0.5 transition-all">
                        <i class="bi bi-box-arrow-up-right text-base" aria-hidden="true"></i>
                        View Shades & Palettes
                    </a>
                </div>
            </div>
        `;

        // Copy button logic
        card.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const value = btn.dataset.copy;
                const orig = btn.textContent;
                try {
                    await navigator.clipboard.writeText(value);
                    btn.textContent = '✓ Copied';
                    btn.classList.add('bg-green-500', 'text-white');
                    setTimeout(() => {
                        btn.textContent = orig;
                        btn.classList.remove('bg-green-500', 'text-white');
                    }, 1300);
                } catch (_) {
                    btn.textContent = 'Failed';
                    setTimeout(() => { btn.textContent = orig; }, 1300);
                }
            });
        });

        colorResultsGrid.appendChild(card);
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

})();
