'use client';

import React from 'react';
import Link from 'next/link';
import { Palette as PaletteType } from '@/types';
import { useToast } from './ToastProvider';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { Heart, ExternalLink, Copy } from 'lucide-react';

interface PaletteCardProps {
  palette: PaletteType;
}

export const PaletteCard: React.FC<PaletteCardProps> = ({ palette }) => {
  const { showToast } = useToast();
  const { isPaletteFavorited, toggleFavoritePalette, isColorFavorited, toggleFavoriteColor } = useFavoritesStore();
  const [mounted, setMounted] = React.useState(false);
  React.useEffect(() => {
    setMounted(true);
  }, []);

  const isFav = mounted ? isPaletteFavorited(palette.id || palette.slug) : false;

  const copyHex = (e: React.MouseEvent, hex: string) => {
    e.preventDefault();
    e.stopPropagation();
    navigator.clipboard.writeText(hex);
    showToast(`Copied ${hex} to clipboard!`);
  };

  const copyPaletteArray = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    const arrayStr = JSON.stringify(palette.colors);
    navigator.clipboard.writeText(arrayStr);
    showToast(`Copied palette array!`);
  };

  const handleFavorite = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    toggleFavoritePalette(palette.id || palette.slug);
    showToast(isFav ? 'Removed from favorites' : 'Added to favorites!');
  };

  const handleToggleColorFav = (e: React.MouseEvent, hex: string) => {
    e.preventDefault();
    e.stopPropagation();
    const isNowFav = isColorFavorited(hex);
    toggleFavoriteColor(hex);
    showToast(isNowFav ? `Removed ${hex} from favorites` : `Saved ${hex} to favorites!`);
  };

  return (
    <div className="bg-white border border-slate-100 hover:border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
      {/* Palette Visual Swatches Band */}
      <div className="relative h-44 w-full rounded-2xl overflow-hidden mb-4 shadow-inner flex">
        {palette.colors.map((color, index) => {
          const cleanHex = color.replace('#', '').toLowerCase();
          const isColorFav = mounted ? isColorFavorited(color) : false;

          return (
            <div
              key={`${color}-${index}`}
              className="swatch-band min-w-0 h-full relative flex flex-col justify-end items-center pb-2.5 cursor-pointer group/swatch border-r border-black/5 last:border-r-0 overflow-hidden"
              style={{ backgroundColor: color }}
              onClick={(e) => copyHex(e, color)}
              title={`Click to copy ${color}`}
            >
              {/* Top-Left action icons in column format (top-to-bottom) sliding down on hover */}
              <div 
                className="absolute top-2 left-2 z-20 flex flex-col items-start gap-1.5 transform -translate-y-8 opacity-0 group-hover/swatch:translate-y-0 group-hover/swatch:opacity-100 transition-all duration-300 ease-out"
                onClick={(e) => e.stopPropagation()}
              >
                <button
                  onClick={(e) => copyHex(e, color)}
                  className="w-6 h-6 rounded-full bg-white/90 hover:bg-white text-slate-700 shadow-md backdrop-blur-sm flex items-center justify-center transition-transform hover:scale-110 cursor-pointer"
                  title={`Copy ${color}`}
                >
                  <Copy className="w-3 h-3" />
                </button>
                <Link
                  href={`/color/${cleanHex}`}
                  onClick={(e) => e.stopPropagation()}
                  className="w-6 h-6 rounded-full bg-white/90 hover:bg-white text-slate-700 shadow-md backdrop-blur-sm flex items-center justify-center transition-transform hover:scale-110 cursor-pointer"
                  title={`Open ${color} details`}
                >
                  <ExternalLink className="w-3 h-3" />
                </Link>
                <button
                  onClick={(e) => handleToggleColorFav(e, color)}
                  className="w-6 h-6 rounded-full bg-white/90 hover:bg-white text-slate-700 shadow-md backdrop-blur-sm flex items-center justify-center transition-transform hover:scale-110 cursor-pointer"
                  title={isColorFav ? `Remove ${color} from favorites` : `Favorite ${color}`}
                >
                  <Heart className={`w-3 h-3 ${isColorFav ? 'fill-pink-500 text-pink-500' : ''}`} />
                </button>
              </div>

              {/* Bottom Hex Tag */}
              <span className="opacity-0 group-hover/swatch:opacity-100 text-[10px] font-mono font-bold bg-white/90 text-slate-900 px-1.5 py-0.5 rounded shadow-sm backdrop-blur-sm pointer-events-none transition-opacity whitespace-nowrap z-10">
                {color}
              </span>
            </div>
          );
        })}
      </div>

      {/* Footer Info & Actions */}
      <div className="flex items-center justify-between gap-2 pt-1">
        <div className="flex flex-col min-w-0">
          <Link
            href={`/palette/${palette.slug}`}
            className="text-base font-bold text-slate-900 hover:text-purple-600 truncate transition-colors"
          >
            {palette.name}
          </Link>
          <span className="text-xs text-slate-400 mt-0.5">
            By Color Studio • <span className="capitalize">{palette.style || 'Modern'}</span>
          </span>
        </div>

        <div className="flex items-center gap-1 shrink-0">
          <Link
            href={`/palette/${palette.slug}`}
            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-colors"
            title="Inspect Palette"
          >
            <ExternalLink className="w-4 h-4" />
          </Link>
          <button
            onClick={handleFavorite}
            className={`p-1.5 rounded-lg transition-colors ${
              isFav ? 'text-pink-500' : 'text-slate-400 hover:text-pink-500 hover:bg-slate-50'
            }`}
            title={isFav ? 'Remove Favorite' : 'Save Favorite'}
          >
            <Heart className={`w-4 h-4 ${isFav ? 'fill-pink-500' : ''}`} />
          </button>
          <button
            onClick={copyPaletteArray}
            className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-colors"
            title="Copy Palette Hex Array"
          >
            <Copy className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  );
};
