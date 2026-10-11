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
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return (r * 299 + g * 587 + b * 114) / 1000 > 155;
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

