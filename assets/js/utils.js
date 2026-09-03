/**
 * utils.js
 * Shared utility helpers — no ES module syntax so it works as a plain script.
 * Must be loaded before any script that uses these functions.
 */

window.ColorMagic = window.ColorMagic || {};

window.ColorMagic.getApiBase = function () {
    if (typeof window !== 'undefined' && window.COLORMAGIC_API_BASE && window.COLORMAGIC_API_BASE.indexOf('http') === 0) {
        return window.COLORMAGIC_API_BASE.replace(/\/+$/, '');
    }
    return 'https://colormagic-api.techkreative.com';
};

window.ColorMagic.apiBase = window.ColorMagic.getApiBase();

window.ColorMagic.getApiUrl = function (endpoint) {
    var base = window.ColorMagic.getApiBase();
    var clean = endpoint.replace(/^\//, '');
    // Add v=2.1 to bust stale CDN cache containing legacy duplicate CORS headers
    var sep = clean.indexOf('?') === -1 ? '?' : '&';
    if (clean.indexOf('v=') === -1) {
        clean = clean + sep + 'v=2.1';
    }
    return base + '/' + clean;
};

/**
 * High-Performance REST API Client for ColorMagic V2 & Static Fallbacks
 */
window.ColorMagic.api = {
    /**
     * Execute HTTP request with JSON envelope unwrapping and fallback logic
     */
    request: async function (endpoint, options) {
        options = options || {};
        var url = window.ColorMagic.getApiUrl(endpoint);
        var headers = Object.assign({
            'Accept': 'application/json'
        }, options.headers || {});

        if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
            options.body = JSON.stringify(options.body);
            headers['Content-Type'] = 'application/json';
        }

        options.headers = headers;

        var response = await fetch(url, options);
        if (!response.ok) {
            var errJson = null;
            try { errJson = await response.json(); } catch (_) {}
            var errMsg = (errJson && errJson.message) ? errJson.message : ('HTTP ' + response.status + ' Error');
            var err = new Error(errMsg);
            err.status = response.status;
            err.details = errJson;
            throw err;
        }
        var data = await response.json();
        // Unwrap standard V2 envelope { status: 'success', data: ... }
        if (data && typeof data === 'object' && data.status === 'success' && 'data' in data) {
            return data;
        }
        // Raw json fallback
        return { status: 'success', data: data };
    },

    /**
     * Health Diagnostics (GET /v2/health)
     */
    getHealth: async function () {
        var res = await this.request('v2/health');
        return res.data;
    },

    /**
     * Colors (GET /v2/colors)
     */
    getColors: async function (params) {
        params = params || {};
        var query = new URLSearchParams();
        if (params.q) query.set('q', params.q);
        if (params.page) query.set('page', params.page);
        if (params.limit) query.set('limit', params.limit);
        if (params.format) query.set('format', params.format);
        if (params.hex) query.set('hex', params.hex);
        if (params.slug) query.set('slug', params.slug);

        var queryString = query.toString();
        var endpoint = 'v2/colors' + (queryString ? '?' + queryString : '');
        try {
            var res = await this.request(endpoint);
            return res;
        } catch (e) {
            // Fallback to static JSON file
            var fallback = await this.request('color-names.json');
            return fallback;
        }
    },

    /**
     * Single Color by Hex (GET /v2/colors/{hex})
     */
    getColorByHex: async function (hex) {
        var clean = String(hex || '').replace(/^#/, '').trim().toUpperCase();
        if (!clean) return null;
        try {
            var res = await this.request('v2/colors/' + clean);
            return res.data;
        } catch (e) {
            // Fallback to static dictionary lookup
            var dictRes = await this.request('color-names.json');
            var dict = dictRes.data || {};
            if (dict[clean]) {
                var entry = dict[clean];
                var name = (typeof entry === 'object') ? (entry.name || clean) : String(entry);
                var slug = (typeof entry === 'object' && entry.slug) ? entry.slug : name.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                var aliases = (typeof entry === 'object' && entry.aliases) ? entry.aliases : [];
                return { hex: '#' + clean, raw_hex: clean, name: name, slug: slug, aliases: aliases };
            }
            return null;
        }
    },

    /**
     * Single Color by Slug (GET /v2/colors/slug/{slug})
     */
    getColorBySlug: async function (slug) {
        var clean = String(slug || '').trim().toLowerCase();
        if (!clean) return null;
        try {
            var res = await this.request('v2/colors/slug/' + encodeURIComponent(clean));
            return res.data;
        } catch (e) {
            var dictRes = await this.request('color-names.json');
            var dict = dictRes.data || {};
            for (var k in dict) {
                var entry = dict[k];
                var s = (typeof entry === 'object' && entry.slug) ? entry.slug : ((typeof entry === 'object' ? entry.name : entry) || '').toLowerCase().replace(/[^a-z0-9]+/g, '-');
                if (s === clean) {
                    var name = (typeof entry === 'object') ? (entry.name || k) : String(entry);
                    return { hex: '#' + k, raw_hex: k, name: name, slug: s, aliases: (typeof entry === 'object' && entry.aliases) ? entry.aliases : [] };
                }
            }
            return null;
        }
    },

    /**
     * Gradients (GET /v2/gradients)
     */
    getGradients: async function (params) {
        params = params || {};
        var query = new URLSearchParams();
        if (params.q) query.set('q', params.q);
        if (params.style && params.style !== 'all') query.set('style', params.style);
        if (params.type && params.type !== 'all') query.set('type', params.type);
        if (params.page) query.set('page', params.page);
        if (params.limit) query.set('limit', params.limit);
        if (params.id) query.set('id', params.id);

        var queryString = query.toString();
        var endpoint = 'v2/gradients' + (queryString ? '?' + queryString : '');
        try {
            var res = await this.request(endpoint);
            return res;
        } catch (e) {
            var fallback = await this.request('gradients.json');
            return fallback;
        }
    },

    /**
     * Single Gradient by ID (GET /v2/gradients/{id})
     */
    getGradientById: async function (id) {
        var clean = String(id || '').trim();
        if (!clean) return null;
        try {
            var res = await this.request('v2/gradients/' + encodeURIComponent(clean));
            return res.data;
        } catch (e) {
            var fallback = await this.request('gradients.json');
            var list = Array.isArray(fallback.data) ? fallback.data : [];
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === clean) return list[i];
            }
            return null;
        }
    },

    /**
     * Palettes (GET /v2/palettes)
     */
    getPalettes: async function (params) {
        params = params || {};
        var query = new URLSearchParams();
        if (params.q) query.set('q', params.q);
        if (params.style && params.style !== 'all') query.set('style', params.style);
        if (params.page) query.set('page', params.page);
        if (params.limit) query.set('limit', params.limit);
        if (params.id) query.set('id', params.id);

        var queryString = query.toString();
        var endpoint = 'v2/palettes' + (queryString ? '?' + queryString : '');
        try {
            var res = await this.request(endpoint);
            return res;
        } catch (e) {
            var fallback = await this.request('palettes.json');
            return fallback;
        }
    },

    /**
     * Single Palette by ID or Slug (GET /v2/palettes/{id})
     */
    getPaletteById: async function (id) {
        var clean = String(id || '').trim();
        if (!clean) return null;
        try {
            var res = await this.request('v2/palettes/' + encodeURIComponent(clean));
            return res.data;
        } catch (e) {
            // Fallback: search in static JSON by ID or slug
            var fallback = await this.request('palettes.json');
            var list = Array.isArray(fallback.data) ? fallback.data : [];
            // Try exact ID match first
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === clean) return list[i];
            }
            // Try slug match
            function slugify(name) {
                return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            }
            for (var j = 0; j < list.length; j++) {
                if (slugify(list[j].name || '') === clean) return list[j];
            }
            return null;
        }
    },

    /**
     * Submit Community Palette (POST /v2/palettes)
     */
    submitPalette: async function (paletteData) {
        var res = await this.request('v2/palettes', {
            method: 'POST',
            body: paletteData
        });
        return res;
    }
};

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

    return navigator.clipboard.writeText(text).then(function () {
        btn.innerHTML = '<i class="bi bi-check-circle-fill text-emerald-500 animate-bounce"></i> <span>' + (label || 'Copied!') + '</span>';
        btn.classList.add('ring-2', 'ring-emerald-500/50');
        setTimeout(function () {
            btn.innerHTML = origHTML;
            btn.classList.remove('ring-2', 'ring-emerald-500/50');
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

