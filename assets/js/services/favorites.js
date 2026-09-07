/**
 * services/favorites.js
 * LocalStorage-backed favorites service.
 * Plain script — attaches to window.ColorMagic.Favorites.
 * Must be loaded after utils.js.
 */

window.ColorMagic = window.ColorMagic || {};

window.ColorMagic.Favorites = (function () {
    const STORAGE_KEY = 'colorMagicFavorites';

    function getFavorites() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (_) {
            return [];
        }
    }

    function saveFavorites(favorites) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites));
    }

    // Background DB sync helper
    function syncToDatabase(type, id, action) {
        try {
            const hasUser = localStorage.getItem('cm_user') || sessionStorage.getItem('cm_user');
            if (!hasUser) return;
            const authBase = (window.CM_AUTH_BASE || '/auth');
            fetch(authBase + '/favorites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: action || 'toggle', type: type, id: id })
            }).catch(function () {});
        } catch (_) {}
    }

    function toggleFavorite(paletteId) {
        const favorites = getFavorites();
        const index = favorites.indexOf(paletteId);
        if (index > -1) {
            favorites.splice(index, 1);
            syncToDatabase('palette', paletteId, 'remove');
        } else {
            favorites.push(paletteId);
            syncToDatabase('palette', paletteId, 'add');
        }
        saveFavorites(favorites);
    }

    function isFavorite(paletteId) {
        return getFavorites().includes(paletteId);
    }

    function updateFavoriteButton(button, paletteId) {
        const icon = button.querySelector('i');
        if (!icon) return;
        if (isFavorite(paletteId)) {
            button.classList.remove('text-slate-400');
            button.classList.add('text-red-500');
            icon.className = 'bi bi-heart-fill text-lg';
            button.setAttribute('aria-pressed', 'true');
        } else {
            button.classList.remove('text-red-500');
            button.classList.add('text-slate-400');
            icon.className = 'bi bi-heart text-lg';
            button.setAttribute('aria-pressed', 'false');
        }
    }

    return { getFavorites, saveFavorites, toggleFavorite, isFavorite, updateFavoriteButton, syncToDatabase };
})();

// ─── Color Favorites ──────────────────────────────────────────────────────────
window.ColorMagic.ColorFavorites = (function () {
    const STORAGE_KEY = 'colorMagicColorFavorites';

    function getFavorites() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (_) {
            return [];
        }
    }

    function saveFavorites(favs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(favs));
    }

    function toggleFavorite(hex) {
        // Normalize: lowercase, no #
        const key = hex.replace('#', '').toLowerCase();
        const favs = getFavorites();
        const idx  = favs.indexOf(key);
        if (idx > -1) {
            favs.splice(idx, 1);
            window.ColorMagic.Favorites.syncToDatabase('color', key, 'remove');
        } else {
            favs.push(key);
            window.ColorMagic.Favorites.syncToDatabase('color', key, 'add');
        }
        saveFavorites(favs);
        return idx === -1; // true = was added
    }

    function isFavorite(hex) {
        const key = hex.replace('#', '').toLowerCase();
        return getFavorites().includes(key);
    }

    return { getFavorites, saveFavorites, toggleFavorite, isFavorite };
})();

// ─── Gradient Favorites ───────────────────────────────────────────────────────
window.ColorMagic.GradientFavorites = (function () {
    const STORAGE_KEY = 'colorMagicGradientFavorites';

    function getFavorites() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (_) {
            return [];
        }
    }

    function saveFavorites(favs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(favs));
    }

    function toggleFavorite(gradientId) {
        const favs = getFavorites();
        const idx  = favs.indexOf(gradientId);
        if (idx > -1) {
            favs.splice(idx, 1);
            window.ColorMagic.Favorites.syncToDatabase('gradient', gradientId, 'remove');
        } else {
            favs.push(gradientId);
            window.ColorMagic.Favorites.syncToDatabase('gradient', gradientId, 'add');
        }
        saveFavorites(favs);
        return idx === -1; // true = was added
    }

    function isFavorite(gradientId) {
        return getFavorites().includes(gradientId);
    }

    return { getFavorites, saveFavorites, toggleFavorite, isFavorite };
})();

// ─── Bi-directional DB Sync for Google Logged-In Users ───────────────────────
window.ColorMagic.syncFavoritesWithServer = function () {
    try {
        const hasUser = localStorage.getItem('cm_user') || sessionStorage.getItem('cm_user');
        if (!hasUser) return Promise.resolve();
        const authBase = (window.CM_AUTH_BASE || '/auth');
        const localPalettes = window.ColorMagic.Favorites.getFavorites();
        const localColors   = window.ColorMagic.ColorFavorites.getFavorites();
        const localGrads    = window.ColorMagic.GradientFavorites.getFavorites();

        return fetch(authBase + '/favorites.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'sync',
                palettes: localPalettes,
                colors: localColors,
                gradients: localGrads
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res && res.success && res.data) {
                if (Array.isArray(res.data.palettes)) window.ColorMagic.Favorites.saveFavorites(res.data.palettes);
                if (Array.isArray(res.data.colors)) window.ColorMagic.ColorFavorites.saveFavorites(res.data.colors);
                if (Array.isArray(res.data.gradients)) window.ColorMagic.GradientFavorites.saveFavorites(res.data.gradients);
            }
        })
        .catch(function () {});
    } catch (_) {
        return Promise.resolve();
    }
};

// Automatically sync when script loads if user is authenticated
if (typeof window !== 'undefined') {
    window.addEventListener('DOMContentLoaded', function () {
        if (window.ColorMagic && window.ColorMagic.syncFavoritesWithServer) {
            window.ColorMagic.syncFavoritesWithServer();
        }
    });
}
