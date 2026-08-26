import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

interface FavoritesState {
  favoritePalettes: string[]; // Palette IDs or slugs
  favoriteGradients: string[]; // Gradient IDs
  favoriteColors: string[]; // Color Hex codes
  toggleFavoritePalette: (id: string) => void;
  toggleFavoriteGradient: (id: string) => void;
  toggleFavoriteColor: (hex: string) => void;
  isPaletteFavorited: (id: string) => boolean;
  isGradientFavorited: (id: string) => boolean;
  isColorFavorited: (hex: string) => boolean;
}

export const useFavoritesStore = create<FavoritesState>()(
  persist(
    (set, get) => ({
      favoritePalettes: [],
      favoriteGradients: [],
      favoriteColors: [],

      toggleFavoritePalette: (id: string) => {
        const { favoritePalettes } = get();
        const exists = favoritePalettes.includes(id);
        const updated = exists
          ? favoritePalettes.filter((item) => item !== id)
          : [...favoritePalettes, id];
        set({ favoritePalettes: updated });
      },

      toggleFavoriteGradient: (id: string) => {
        const { favoriteGradients } = get();
        const exists = favoriteGradients.includes(id);
        const updated = exists
          ? favoriteGradients.filter((item) => item !== id)
          : [...favoriteGradients, id];
        set({ favoriteGradients: updated });
      },

      toggleFavoriteColor: (hex: string) => {
        const normalized = hex.toUpperCase();
        const { favoriteColors } = get();
        const exists = favoriteColors.includes(normalized);
        const updated = exists
          ? favoriteColors.filter((item) => item !== normalized)
          : [...favoriteColors, normalized];
        set({ favoriteColors: updated });
      },

      isPaletteFavorited: (id: string) => get().favoritePalettes.includes(id),
      isGradientFavorited: (id: string) => get().favoriteGradients.includes(id),
      isColorFavorited: (hex: string) => get().favoriteColors.includes(hex.toUpperCase()),
    }),
    {
      name: 'colormagic_favorites_storage',
      storage: createJSONStorage(() => localStorage),
    }
  )
);
