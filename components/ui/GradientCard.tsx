'use client';

import React from 'react';
import Link from 'next/link';
import { Gradient } from '@/types';
import { useToast } from './ToastProvider';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { Heart, Copy, ExternalLink } from 'lucide-react';

interface GradientCardProps {
  gradient: Gradient;
}

export const GradientCard: React.FC<GradientCardProps> = ({ gradient }) => {
  const { showToast } = useToast();
  const { isGradientFavorited, toggleFavoriteGradient } = useFavoritesStore();
  const [mounted, setMounted] = React.useState(false);
  React.useEffect(() => {
    setMounted(true);
  }, []);

  const isFav = mounted ? isGradientFavorited(gradient.id) : false;

  const colorStops = gradient.colors.join(', ');

  const angleNum =
    typeof gradient.angle === 'number'
      ? gradient.angle
      : parseInt(String(gradient.angle || '135').replace(/[^0-9]/g, ''), 10) || 135;

  const gradientCss =
    gradient.css ||
    (gradient.type === 'radial'
      ? `radial-gradient(circle, ${colorStops})`
      : `linear-gradient(${angleNum}deg, ${colorStops})`);

  const copyCss = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    const cssRule = `background: ${gradientCss};`;
    navigator.clipboard.writeText(cssRule);
    showToast(`Copied CSS gradient to clipboard!`);
  };

  const handleFavorite = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    toggleFavoriteGradient(gradient.id);
    showToast(isFav ? 'Removed from favorites' : 'Added to favorites!');
  };

  return (
    <div className="bg-white border border-slate-100 hover:border-slate-200 rounded-3xl p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
      {/* Top Gradient Visual Box */}
        <div
          className="relative h-44 w-full rounded-2xl overflow-hidden shadow-inner mb-4 flex items-end justify-end p-3 transition-transform group-hover:scale-[1.01] bg-gray-100"
          style={{ background: gradientCss || '#f0f0f0' }}
        >
        <button
          onClick={copyCss}
          className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/90 text-slate-800 text-xs font-bold shadow-md hover:bg-white transition-all backdrop-blur-sm"
        >
          <Copy className="w-3.5 h-3.5 text-slate-700" />
          <span>Copy CSS</span>
        </button>
      </div>

      {/* Info Header */}
      <div className="space-y-2">
        <div className="flex items-center justify-between gap-2">
          <Link
            href={`/gradient/${gradient.id}`}
            className="text-base font-bold text-slate-900 hover:text-purple-600 truncate transition-colors"
          >
            {gradient.name}
          </Link>

          <div className="flex items-center gap-1.5 shrink-0">
            {/* Radial vs Linear Pill Tag */}
            <span className="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-200/60">
              {gradient.type === 'radial' ? 'RADIAL' : `← LINEAR`}
            </span>

            <Link
              href={`/gradient/${gradient.id}`}
              className="p-1 rounded-md text-slate-400 hover:text-slate-700 transition-colors"
              title="Inspect Gradient"
            >
              <ExternalLink className="w-3.5 h-3.5" />
            </Link>

            <button
              onClick={handleFavorite}
              className={`p-1 rounded-md transition-colors ${
                isFav ? 'text-pink-500' : 'text-slate-400 hover:text-pink-500'
              }`}
              title={isFav ? 'Remove Favorite' : 'Save Favorite'}
            >
              <Heart className={`w-3.5 h-3.5 ${isFav ? 'fill-pink-500' : ''}`} />
            </button>
          </div>
        </div>

        {/* Subtitle Details e.g. "Dark • 2 colors • 45°" */}
        <p className="text-xs text-slate-500 capitalize font-medium">
          {gradient.style} • {gradient.colors.length} colors • {gradient.type === 'radial' ? 'circle' : `${angleNum}°`}
        </p>

        {/* Bottom Split Color Stop Bars */}
        <div className="h-3 w-full rounded-lg overflow-hidden flex gap-0.5 pt-1">
          {gradient.colors.map((c, i) => (
            <div
              key={`${c}-${i}`}
              className="flex-1 h-full rounded-xs"
              style={{ backgroundColor: c }}
            />
          ))}
        </div>
      </div>
    </div>
  );
};
