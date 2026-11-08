/**
 * utils.js
 * Shared utility helpers — no ES module syntax so it works as a plain script.
 * Must be loaded before any script that uses these functions.
 */

window.ColorMagic = window.ColorMagic || {};

window.ColorMagic.getApiBase = function () {
    return '/api';
};

window.ColorMagic.apiBase = window.ColorMagic.getApiBase();

window.ColorMagic.getApiUrl = function (endpoint) {
    var clean = (endpoint || '').replace(/^\//, '').replace(/^data\//, '').replace(/^api\//, '');
    return '/api/' + clean;
};

/**
 * Ultra-Fast In-Memory Data Store & Client for ColorMagic
 */
(function () {
    var _cache = {
        colors: null,
        palettes: null,
        gradients: null
    };

    async function loadJson(file) {
        if (_cache[file]) {
            return _cache[file];
        }
        var cacheKey = window.CM_CACHE_KEY || Date.now();
        var url = '/api/' + file + '.json?t=' + cacheKey;
        var res = await fetch(url);
        if (!res.ok) {
            throw new Error('Failed to load ' + url);
        }
        var data = await res.json();
        _cache[file] = data;
        return data;
    }

    function slugify(name) {
        return (name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }

    window.ColorMagic.api = {
        /**
         * Generic request helper
         */
        request: async function (endpoint) {
            var clean = (endpoint || '').replace(/^\//, '').replace(/^data\//, '').replace(/^api\//, '');
            if (clean.indexOf('color-names') !== -1) {
                var c = await loadJson('color-names');
                return { status: 'success', data: c };
            }
            if (clean.indexOf('palettes') !== -1) {
                var p = await loadJson('palettes');
                return { status: 'success', data: p };
            }
            if (clean.indexOf('gradients') !== -1) {
                var g = await loadJson('gradients');
                return { status: 'success', data: g };
            }
            var res = await fetch('/api/' + clean);
            var data = await res.json();
            return { status: 'success', data: data };
        },

        getHealth: async function () {
            return { status: 'healthy', version: '2.0.0', embedded: true };
        },

        getColors: async function (params) {
            params = params || {};
            var dict = await loadJson('color-names');
            if (params.format === 'dict') {
                return { status: 'success', data: dict };
            }

            var list = [];
            for (var hex in dict) {
                var entry = dict[hex];
                var name = typeof entry === 'object' ? (entry.name || hex) : String(entry);
                var slug = typeof entry === 'object' && entry.slug ? entry.slug : slugify(name);
                var aliases = typeof entry === 'object' && entry.aliases ? entry.aliases : [];

                if (params.q) {
                    var q = params.q.toLowerCase();
                    if (hex.toLowerCase().indexOf(q) === -1 && name.toLowerCase().indexOf(q) === -1) {
                        continue;
                    }
                }
                list.push({ hex: '#' + hex, raw_hex: hex, name: name, slug: slug, aliases: aliases });
            }

            var limit = parseInt(params.limit, 10) || list.length;
            var page = parseInt(params.page, 10) || 1;
            var start = (page - 1) * limit;
            var paged = list.slice(start, start + limit);

            return {
                status: 'success',
                data: paged,
                meta: { total: list.length, page: page, limit: limit }
            };
        },

        getColorByHex: async function (hex) {
            var clean = String(hex || '').replace(/^#/, '').trim().toUpperCase();
            if (!clean) return null;
            var dict = await loadJson('color-names');
            if (dict[clean]) {
                var entry = dict[clean];
                var name = typeof entry === 'object' ? (entry.name || clean) : String(entry);
                var slug = typeof entry === 'object' && entry.slug ? entry.slug : slugify(name);
                var aliases = typeof entry === 'object' && entry.aliases ? entry.aliases : [];
                return { hex: '#' + clean, raw_hex: clean, name: name, slug: slug, aliases: aliases };
            }
            return null;
        },

        getColorBySlug: async function (slug) {
            var clean = String(slug || '').trim().toLowerCase();
            if (!clean) return null;
            var dict = await loadJson('color-names');
            for (var k in dict) {
                var entry = dict[k];
                var s = typeof entry === 'object' && entry.slug ? entry.slug : slugify(typeof entry === 'object' ? entry.name : entry);
                if (s === clean) {
                    var name = typeof entry === 'object' ? (entry.name || k) : String(entry);
                    return { hex: '#' + k, raw_hex: k, name: name, slug: s, aliases: (typeof entry === 'object' && entry.aliases) ? entry.aliases : [] };
                }
            }
            return null;
        },

        getGradients: async function (params) {
            params = params || {};
            var list = await loadJson('gradients');
            list = Array.isArray(list) ? list : [];

            var filtered = list;
            if (params.q) {
                var q = params.q.toLowerCase();
                filtered = filtered.filter(function (g) {
                    return (g.name && g.name.toLowerCase().indexOf(q) !== -1) ||
                           (g.colors && g.colors.some(function (c) { return c.toLowerCase().indexOf(q) !== -1; }));
                });
            }
            if (params.style && params.style !== 'all') {
                var st = params.style.toLowerCase();
                filtered = filtered.filter(function (g) {
                    return g.style && g.style.toLowerCase() === st;
                });
            }
            if (params.type && params.type !== 'all') {
                var tp = params.type.toLowerCase();
                filtered = filtered.filter(function (g) {
                    return g.type && g.type.toLowerCase() === tp;
                });
            }

            var limit = parseInt(params.limit, 10) || filtered.length;
            var page = parseInt(params.page, 10) || 1;
            var start = (page - 1) * limit;
            var paged = filtered.slice(start, start + limit);

            return {
                status: 'success',
                data: paged,
                meta: { total: filtered.length, page: page, limit: limit }
            };
        },

        getGradientById: async function (id) {
            var clean = String(id || '').trim();
            if (!clean) return null;
            var list = await loadJson('gradients');
            list = Array.isArray(list) ? list : [];
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === clean) return list[i];
            }
            return null;
        },

        getPalettes: async function (params) {
            params = params || {};
            var list = await loadJson('palettes');
            list = Array.isArray(list) ? list : [];

            var filtered = list;
            if (params.q) {
                var q = params.q.toLowerCase();
                filtered = filtered.filter(function (p) {
                    return (p.name && p.name.toLowerCase().indexOf(q) !== -1) ||
                           (p.colors && p.colors.some(function (c) { return c.toLowerCase().indexOf(q) !== -1; }));
                });
            }
            if (params.style && params.style !== 'all') {
                var st = params.style.toLowerCase();
                filtered = filtered.filter(function (p) {
                    return p.style && p.style.toLowerCase() === st;
                });
            }

            var limit = parseInt(params.limit, 10) || filtered.length;
            var page = parseInt(params.page, 10) || 1;
            var start = (page - 1) * limit;
            var paged = filtered.slice(start, start + limit);

            return {
                status: 'success',
                data: paged,
                meta: { total: filtered.length, page: page, limit: limit }
            };
        },

        getPaletteById: async function (id) {
            var clean = String(id || '').trim();
            if (!clean) return null;
            var list = await loadJson('palettes');
            list = Array.isArray(list) ? list : [];
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === clean) return list[i];
            }
            for (var j = 0; j < list.length; j++) {
                if (slugify(list[j].name || '') === clean) return list[j];
            }
            return null;
        },

        submitPalette: async function (paletteData) {
            return { status: 'success', data: paletteData };
        }
    };
})();

/**
 * Returns true if a hex color is perceptually light.
 * @param {string} hex - e.g. "#EC4899"
 */
window.ColorMagic.isLightColor = function (hex) {
    if (!hex) return true;
    hex = String(hex).replace('#', '').trim();
    if (hex.length === 3) hex = hex.split('').map(function (c) { return c + c; }).join('');
    if (hex.length < 6) return true;
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    return (r * 299 + g * 587 + b * 114) / 1000 > 155;
};

/**
 * Returns the semantic color family name for any hex code.
 * E.g. 'Red', 'Blue', 'Green', 'Tan', 'Teal', 'Black', 'Silver', 'Gray', 'Golden', etc.
 * @param {string} hex
 * @returns {string}
 */
window.ColorMagic.getColorFamily = function (hex) {
    if (!hex) return 'Color';
    hex = String(hex).replace('#', '').trim();
    if (!/^[0-9a-fA-F]{3,6}$/.test(hex)) return hex ? ('#' + hex.toUpperCase()) : 'Color';
    if (hex.length === 3) hex = hex.split('').map(function (c) { return c + c; }).join('');

    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);

    var rn = r / 255, gn = g / 255, bn = b / 255;
    var max = Math.max(rn, gn, bn), min = Math.min(rn, gn, bn);
    var delta = max - min;
    var l = (max + min) / 2;
    var s = delta === 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
    var h = 0;
    if (delta !== 0) {
        if (max === rn) {
            h = ((gn - bn) / delta) % 6;
        } else if (max === gn) {
            h = (bn - rn) / delta + 2;
        } else {
            h = (rn - gn) / delta + 4;
        }
        h = Math.round(h * 60);
        if (h < 0) h += 360;
    }
    var L = Math.round(l * 100);
    var S = Math.round(s * 100);

    // 1. Black (extremely dark)
    if (L <= 13 || (r <= 28 && g <= 28 && b <= 28)) return 'Black';

    // 2. White (extremely light and neutral)
    if (L >= 96 && S <= 25) return 'White';

    // 3. Achromatic Grays & Silvers
    if (S <= 13 || (Math.max(r, g, b) - Math.min(r, g, b) <= 15)) {
        return L >= 65 ? 'Silver' : 'Gray';
    }

    // 4. Tan / Beige / Khaki (warm, earthy, desaturated tones)
    if ((h >= 25 && h <= 55 && S >= 12 && S <= 55 && L >= 55) ||
        (h >= 25 && h <= 65 && L >= 82 && S <= 60)) {
        return 'Tan';
    }

    // 5. Golden / Gold (rich warm yellows with amber warmth)
    if (h >= 42 && h <= 56 && S >= 40 && L >= 30 && L <= 82 && b < 165) {
        return 'Golden';
    }

    // 6. Brown (dark warm tones)
    if (h >= 15 && h <= 45 && S >= 15 && L <= 38) {
        return 'Brown';
    }

    // 7. Hue spectrum classifications
    if (h >= 345 || h < 15) {
        return L >= 70 ? 'Pink' : 'Red';
    }
    if (h >= 15 && h < 45) {
        return 'Orange';
    }
    if (h >= 45 && h < 70) {
        return 'Yellow';
    }
    if (h >= 70 && h < 165) {
        if (h >= 70 && h < 85 && S >= 50) return 'Lime';
        return 'Green';
    }
    if (h >= 165 && h < 195) {
        return 'Teal';
    }
    if (h >= 195 && h < 215) {
        return L >= 55 ? 'Cyan' : 'Blue';
    }
    if (h >= 215 && h < 260) {
        return 'Blue';
    }
    if (h >= 260 && h < 320) {
        return 'Purple';
    }
    if (h >= 320 && h < 345) {
        return L >= 55 ? 'Pink' : 'Magenta';
    }
    return 'Blue';
};

/**
 * Returns the semantic color family name with 'Shade' appended.
 * E.g. 'Red Shade', 'Blue Shade', 'Green Shade', 'Tan Shade', 'Teal Shade',
 * 'Black Shade', 'Silver Shade', 'Gray Shade', 'Golden Shade', etc.
 * @param {string} hex
 * @returns {string}
 */
window.ColorMagic.getColorFamilyShade = function (hex) {
    var family = window.ColorMagic.getColorFamily(hex);
    if (!family) return 'Color Shade';
    return family + ' Shade';
};

/**
 * Returns a URL slug for a palette.
 * Appends numeric ID for duplicate-named palettes.
 * @param {{ name: string, id: string, _isDuplicate?: boolean }} palette
 */
window.ColorMagic.getPaletteSlug = function (palette) {
    let slug = palette.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    if (palette._isDuplicate) {
        slug = slug + '-' + palette.id.replace('palette_', '');
    }
    return slug;
};

/**
 * Marks duplicate-named palettes in an array with _isDuplicate = true.
 * Mutates the objects in place.
 * @param {Array} palettes
 */
window.ColorMagic.markDuplicateSlugs = function (palettes) {
    const nameCount = {};
    palettes.forEach(function (p) {
        const slug = p.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        nameCount[slug] = (nameCount[slug] || 0) + 1;
    });
    palettes.forEach(function (p) {
        const slug = p.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        if (nameCount[slug] > 1) p._isDuplicate = true;
    });
};

/**
 * Copies text to clipboard. Animates a button with success/fail/reset states.
 * @param {string} text
 * @param {HTMLElement|null} button
 * @param {string} successHTML
 * @param {string} failHTML
 * @param {string} resetHTML
 */
window.ColorMagic.copyToClipboard = function (text, button, successHTML, failHTML, resetHTML) {
    return navigator.clipboard.writeText(text).then(function () {
        if (button) {
            button.innerHTML = successHTML;
            setTimeout(function () { button.innerHTML = resetHTML; }, 1200);
        }
    }).catch(function () {
        if (button) {
            button.innerHTML = failHTML;
            setTimeout(function () { button.innerHTML = resetHTML; }, 1200);
        }
    });
};

/**
 * Animated visual feedback for Copy buttons
 * @param {HTMLElement} btn
 * @param {string} text
 * @param {string} [label]
 */
window.ColorMagic.animateCopy = function (btn, text, label) {
    if (!btn) return Promise.resolve();
    var origHTML = btn.dataset.origHtml || btn.innerHTML;
    btn.dataset.origHtml = origHTML;

    // Pop scale animation
    btn.classList.add('scale-95', 'transition-all', 'duration-150');
    setTimeout(function () { btn.classList.remove('scale-95'); }, 150);

    var isPrimary = btn.classList.contains('bg-primary') || btn.classList.contains('btn-primary') || btn.classList.contains('text-white');

    var copyPromise;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        copyPromise = navigator.clipboard.writeText(text).catch(function () {
            return fallbackCopy(text);
        });
    } else {
        copyPromise = fallbackCopy(text);
    }

    function fallbackCopy(str) {
        try {
            var ta = document.createElement('textarea');
            ta.value = str;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            return Promise.resolve();
        } catch (e) {
            return Promise.reject(e);
        }
    }

    return copyPromise.then(function () {
        if (isPrimary) {
            btn.innerHTML = '<i class="bi bi-check2 text-lg text-white"></i> <span class="text-white">' + (label || 'Copied!') + '</span>';
            btn.classList.add('ring-2', 'ring-white/40');
        } else {
            btn.innerHTML = '<i class="bi bi-check2 text-emerald-600 dark:text-emerald-400 text-base"></i> <span>' + (label || 'Copied!') + '</span>';
            btn.classList.add('ring-2', 'ring-emerald-500/30');
        }
        setTimeout(function () {
            btn.innerHTML = origHTML;
            btn.classList.remove('ring-2', 'ring-emerald-500/50', 'ring-emerald-500/30', 'ring-white/40');
        }, 1600);
    }).catch(function () {
        btn.innerHTML = '<i class="bi bi-x-circle text-red-500"></i> <span>Failed</span>';
        setTimeout(function () {
            btn.innerHTML = origHTML;
        }, 1600);
    });
};

/**
 * Animated visual feedback for Favorite buttons
 * @param {HTMLElement} btn
 * @param {boolean} isFav
 */
window.ColorMagic.animateFavorite = function (btn, isFav) {
    if (!btn) return;
    var icon = btn.querySelector('i');
    var span = btn.querySelector('span');

    // Pop spring animation
    btn.classList.add('scale-95', 'transition-all', 'duration-150');
    setTimeout(function () { btn.classList.remove('scale-95'); }, 150);

    if (icon) {
        icon.classList.add('transition-transform', 'duration-300', 'scale-125');
        setTimeout(function () { icon.classList.remove('scale-125'); }, 300);
    }

    if (isFav) {
        if (icon) icon.className = 'bi bi-heart-fill text-red-500 transition-transform duration-300';
        if (span) span.textContent = 'Added to Favorites!';
        btn.classList.add('ring-2', 'ring-red-500/40', 'text-red-500');
        setTimeout(function () {
            if (span) span.textContent = 'Favorited';
            btn.classList.remove('ring-2', 'ring-red-500/40');
        }, 1500);
    } else {
        if (icon) icon.className = 'bi bi-heart transition-transform duration-300';
        if (span) span.textContent = 'Removed';
        btn.classList.remove('text-red-500');
        setTimeout(function () {
            if (span) span.textContent = 'Favorite';
        }, 1200);
    }
};

/**
 * Animated visual feedback for Download PNG buttons
 * @param {HTMLElement} btn
 * @param {Function} downloadFn
 */
window.ColorMagic.animateDownload = function (btn, downloadFn) {
    if (!btn) return;
    var origHTML = btn.dataset.origHtml || btn.innerHTML;
    btn.dataset.origHtml = origHTML;
    btn.disabled = true;

    // Show spinner & Downloading state
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-primary"></i> <span>Downloading...</span>';
    btn.classList.add('scale-95', 'transition-all', 'duration-150');
    setTimeout(function () { btn.classList.remove('scale-95'); }, 150);

    setTimeout(function () {
        try {
            if (typeof downloadFn === 'function') downloadFn();
        } catch (e) {
            console.error('Download error:', e);
        }

        // Show Downloaded checkmark
        btn.innerHTML = '<i class="bi bi-check2-circle text-emerald-500 text-lg"></i> <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Downloaded!</span>';
        btn.classList.add('ring-2', 'ring-emerald-500/50');

        setTimeout(function () {
            btn.innerHTML = origHTML;
            btn.classList.remove('ring-2', 'ring-emerald-500/50');
            btn.disabled = false;
        }, 1800);
    }, 450);
};

