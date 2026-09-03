/**
 * utils.js
 * Shared utility helpers — no ES module syntax so it works as a plain script.
 * Must be loaded before any script that uses these functions.
 */

window.ColorMagic = window.ColorMagic || {};

window.ColorMagic.getApiBase = function () {
    if (typeof window !== 'undefined' && window.location) {
        var host = window.location.hostname;
        var isLocal = host === 'localhost' || host === '127.0.0.1' || host.indexOf('.test') !== -1;
        if (!isLocal) {
            // Production frontend MUST talk to dedicated API server
            return (window.COLORMAGIC_API_BASE && window.COLORMAGIC_API_BASE.indexOf('http') === 0)
                ? window.COLORMAGIC_API_BASE.replace(/\/+$/, '')
                : 'https://colormagic-api.techkreative.com';
        }
    }
    if (window.COLORMAGIC_API_BASE) {
        return window.COLORMAGIC_API_BASE.replace(/\/+$/, '');
    }
    if (window.CM_API_URL) {
        return window.CM_API_URL.replace(/\/+$/, '');
    }
    if (typeof window !== 'undefined' && window.location && window.location.pathname.indexOf('/ColorMagic') === 0) {
        return '/ColorMagic/api';
    }
    return '/api';
};

window.ColorMagic.apiBase = window.ColorMagic.getApiBase();

window.ColorMagic.getApiUrl = function (endpoint) {
    var base = window.ColorMagic.getApiBase();
    var clean = endpoint.replace(/^\//, '');
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

        try {
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
        } catch (error) {
            // If remote API failed with CORS or network error, attempt local relative fallback on local servers
            var isLocalFallback = url.indexOf('http') !== 0;
            if (!isLocalFallback && !options._retriedLocal && typeof window !== 'undefined' && window.location) {
                var host = window.location.hostname;
                var isLocal = host === 'localhost' || host === '127.0.0.1' || host.indexOf('.test') !== -1;
                if (isLocal) {
                    options._retriedLocal = true;
                    var localBase = (window.location.pathname.indexOf('/ColorMagic') === 0) ? '/ColorMagic/api' : '/api';
                    var clean = endpoint.replace(/^\//, '');
                    var localUrl = localBase + '/' + clean;
                    try {
                        var localRes = await fetch(localUrl, options);
                        if (localRes.ok) {
                            var localData = await localRes.json();
                            if (localData && typeof localData === 'object' && localData.status === 'success' && 'data' in localData) {
                                return localData;
                            }
                            return { status: 'success', data: localData };
                        }
                    } catch (_) {}
                }
            }
            throw error;
        }
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
