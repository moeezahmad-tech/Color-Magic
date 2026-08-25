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
  const { isPaletteFavorited, toggleFavoritePalette } = useFavoritesStore();
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

  return (
    <div className="bg-white border border-slate-100 hover:border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
      {/* Palette Visual Swatches Band (Matching Screenshot 4) */}
      <div className="relative h-44 w-full rounded-2xl overflow-hidden flex mb-4 shadow-inner">
        {palette.colors.map((color, index) => (
          <div
            key={`${color}-${index}`}
            className="swatch-band relative flex items-end justify-center pb-2 cursor-pointer group/swatch"
            style={{ backgroundColor: color }}
            onClick={(e) => copyHex(e, color)}
            title={`Click to copy ${color}`}
          >
            <span className="opacity-0 group-hover/swatch:opacity-100 text-[10px] font-mono font-bold bg-white/85 text-slate-900 px-1.5 py-0.5 rounded absolute bottom-2 transition-opacity">
              {color}
            </span>
          </div>
        ))}
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
