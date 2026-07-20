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

    function toggleFavorite(paletteId) {
        const favorites = getFavorites();
        const index = favorites.indexOf(paletteId);
        if (index > -1) {
            favorites.splice(index, 1);
        } else {
            favorites.push(paletteId);
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

    return { getFavorites, saveFavorites, toggleFavorite, isFavorite, updateFavoriteButton };
})();
