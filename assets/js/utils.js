/**
 * utils.js
 * Shared utility helpers — no ES module syntax so it works as a plain script.
 * Must be loaded before any script that uses these functions.
 */

window.ColorMagic = window.ColorMagic || {};

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
 * Shared Data Fetcher / Provider.
 * Performs direct HTTP fetch to https://api.colormagic.techkreative.com/
 * @param {string} key - 'gradients' | 'palettes' | 'color-names'
 * @returns {Promise<any>}
 */
window.ColorMagic.fetchData = function (key) {
    var pathMap = {
        'gradients': 'gradients.json',
        'palettes': 'palettes.json',
        'color-names': 'color-names.json'
    };
    var endpoint = pathMap[key] || (key + '.json');
    var targetUrl = 'https://api.colormagic.techkreative.com/' + endpoint;

    return fetch(targetUrl)
        .then(function (res) {
            console.log('[fetchData] Response for', key, ':', res);
            if (!res.ok) {
                console.error('[fetchData] Error status:', res.status, res.statusText);
                throw new Error('Failed to load ' + key + ' (Status: ' + res.status + ')');
            }
            return res.json();
        })
        .then(function (resData) {
            console.log('[fetchData] Parsed JSON for', key, ':', resData);
            var data = (resData && resData.data !== undefined) ? resData.data : resData;
            return data;
        })
        .catch(function (error) {
            console.error('[fetchData] Fetch failed for', key, ':', error);
            throw error;
        });
};

