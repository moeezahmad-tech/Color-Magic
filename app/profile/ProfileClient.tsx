'use client';

import React, { useState, useEffect } from 'react';
import { Palette, Gradient, ColorName } from '@/types';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { GradientCard } from '@/components/ui/GradientCard';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { useToast } from '@/components/ui/ToastProvider';
import { findClosestColorName, hexToRgb } from '@/lib/color-math';
import { Heart, Sparkles, Palette as PaletteIcon, Droplet, Copy, ExternalLink, ArrowRight, Trash2 } from 'lucide-react';
import Link from 'next/link';

interface Props {
  palettes: Palette[];
  gradients: Gradient[];
  colors: ColorName[];
}

export default function ProfileClient({ palettes, gradients, colors }: Props) {
  const [mounted, setMounted] = useState(false);
  const { showToast } = useToast();
  const { 
    favoritePalettes, 
    favoriteGradients, 
    favoriteColors, 
    toggleFavoriteColor 
  } = useFavoritesStore();

  useEffect(() => {
    setMounted(true);
  }, []);

  const favPaletteItems = palettes.filter((p) => favoritePalettes.includes(p.id || p.slug));
  const favGradientItems = gradients.filter((g) => favoriteGradients.includes(g.id));

  const copyText = (val: string, label: string) => {
    navigator.clipboard.writeText(val);
    showToast(`Copied ${label}: ${val}`);
  };

  const handleRemoveColor = (e: React.MouseEvent, hex: string) => {
    e.preventDefault();
    e.stopPropagation();
    toggleFavoriteColor(hex);
    showToast(`Removed ${hex} from favorites`);
  };

  const handleDeleteAccount = () => {
    if (confirm('Are you sure you want to clear your saved favorites?')) {
      localStorage.removeItem('colormagic_favorites_storage');
      window.location.reload();
    }
  };

  if (!mounted) return null;

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      {/* Profile Header */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10 pb-8 border-b border-slate-100">
        <div>
          <h1 className="text-4xl font-black text-slate-900 tracking-tight">Your Profile & Library</h1>
          <p className="text-slate-600 mt-1 text-sm sm:text-base">
            All your saved color palettes, gradients, and custom colors stored locally.
          </p>
          
          {/* Quick Stat Summary Pills */}
          <div className="flex flex-wrap items-center gap-2.5 mt-4">
            <a 
              href="#palettes"
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-pink-50 text-pink-700 border border-pink-100/80 text-xs font-bold hover:bg-pink-100 transition-colors"
            >
              <PaletteIcon className="w-3.5 h-3.5" />
              <span>{favPaletteItems.length} Palettes</span>
            </a>
            <a 
              href="#gradients"
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100/80 text-xs font-bold hover:bg-purple-100 transition-colors"
            >
              <Sparkles className="w-3.5 h-3.5" />
              <span>{favGradientItems.length} Gradients</span>
            </a>
            <a 
              href="#colors"
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-sky-50 text-sky-700 border border-sky-100/80 text-xs font-bold hover:bg-sky-100 transition-colors"
            >
              <Droplet className="w-3.5 h-3.5" />
              <span>{favoriteColors.length} Colors</span>
            </a>
          </div>
        </div>

        <button 
          onClick={handleDeleteAccount}
          className="text-xs text-slate-400 hover:text-red-600 font-bold px-3.5 py-2 border border-slate-200 hover:border-red-300 rounded-xl transition-colors flex items-center gap-1.5 shrink-0"
          title="Clear all saved favorites"
        >
          <Trash2 className="w-3.5 h-3.5" />
          <span>Clear All Saved</span>
        </button>
      </div>

      <div className="space-y-16">
        {/* Section 1: Saved Palettes */}
        <section id="palettes" className="scroll-mt-24 space-y-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-8 h-8 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center">
                <PaletteIcon className="w-4 h-4" />
              </div>
              <div>
                <h2 className="text-2xl font-black text-slate-900 tracking-tight">
                  Saved Palettes
                </h2>
                <p className="text-xs text-slate-500">{favPaletteItems.length} saved color schemes</p>
              </div>
            </div>
            <Link
              href="/palettes"
              className="text-xs font-bold text-pink-600 hover:text-pink-700 transition-colors flex items-center gap-1"
            >
              Explore Palettes <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          {favPaletteItems.length === 0 ? (
            <div className="text-center py-14 px-4 bg-pink-50/40 border border-pink-100 rounded-3xl">
              <Heart className="w-10 h-10 text-pink-300 mx-auto mb-2.5" />
              <h3 className="text-lg font-bold text-slate-800">No Saved Palettes Yet</h3>
              <p className="text-slate-500 text-xs mt-1 mb-5 max-w-sm mx-auto">
                Click the heart icon on any palette to save it to your collection.
              </p>
              <Link
                href="/palettes"
                className="px-5 py-2 rounded-xl bg-pink-500 hover:bg-pink-600 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors"
              >
                Browse Palettes
              </Link>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {favPaletteItems.map((p) => (
                <PaletteCard key={p.id} palette={p} />
              ))}
            </div>
          )}
        </section>

        {/* Section 2: Saved Gradients */}
        <section id="gradients" className="scroll-mt-24 space-y-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                <Sparkles className="w-4 h-4" />
              </div>
              <div>
                <h2 className="text-2xl font-black text-slate-900 tracking-tight">
                  Saved Gradients
                </h2>
                <p className="text-xs text-slate-500">{favGradientItems.length} saved CSS gradients</p>
              </div>
            </div>
            <Link
              href="/gradients"
              className="text-xs font-bold text-purple-600 hover:text-purple-700 transition-colors flex items-center gap-1"
            >
              Explore Gradients <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          {favGradientItems.length === 0 ? (
            <div className="text-center py-14 px-4 bg-purple-50/40 border border-purple-100 rounded-3xl">
              <Sparkles className="w-10 h-10 text-purple-300 mx-auto mb-2.5" />
              <h3 className="text-lg font-bold text-slate-800">No Saved Gradients Yet</h3>
              <p className="text-slate-500 text-xs mt-1 mb-5 max-w-sm mx-auto">
                Click the heart icon on any gradient to bookmark ready-to-use CSS.
              </p>
              <Link
                href="/gradients"
                className="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors"
              >
                Browse Gradients
              </Link>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {favGradientItems.map((g) => (
                <GradientCard key={g.id} gradient={g} />
              ))}
            </div>
          )}
        </section>

        {/* Section 3: Saved Colors */}
        <section id="colors" className="scroll-mt-24 space-y-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                <Droplet className="w-4 h-4" />
              </div>
              <div>
                <h2 className="text-2xl font-black text-slate-900 tracking-tight">
                  Saved Colors
                </h2>
                <p className="text-xs text-slate-500">{favoriteColors.length} saved standalone colors</p>
              </div>
            </div>
            <Link
              href="/find-color"
              className="text-xs font-bold text-sky-600 hover:text-sky-700 transition-colors flex items-center gap-1"
            >
              Explore Colors <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          {favoriteColors.length === 0 ? (
            <div className="text-center py-14 px-4 bg-sky-50/40 border border-sky-100 rounded-3xl">
              <Droplet className="w-10 h-10 text-sky-300 mx-auto mb-2.5" />
              <h3 className="text-lg font-bold text-slate-800">No Saved Colors Yet</h3>
              <p className="text-slate-500 text-xs mt-1 mb-5 max-w-sm mx-auto">
                Save individual colors using the heart icon on any palette swatch or color page.
              </p>
              <Link
                href="/palettes"
                className="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors"
              >
                Discover Colors
              </Link>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
              {favoriteColors.map((hex) => {
                const cleanHex = hex.replace('#', '').toLowerCase();
                const matchedName = findClosestColorName(hex, colors).name;
                const rgb = hexToRgb(hex);

                return (
                  <div 
                    key={hex}
                    className="bg-white border border-slate-100 hover:border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group"
                  >
                    <div>
                      {/* Color Preview Swatch */}
                      <Link
                        href={`/color/${cleanHex}`}
                        className="block h-32 w-full rounded-xl overflow-hidden border border-slate-200/80 shadow-inner relative group/color-swatch mb-3"
                        title={`View ${matchedName} (${hex})`}
                      >
                        <div
                          className="w-full h-full group-hover/color-swatch:scale-105 transition-transform duration-300"
                          style={{ backgroundColor: hex }}
                        />
                        <span className="absolute bottom-2 right-2 text-[10px] font-bold bg-white/90 text-slate-800 px-2 py-0.5 rounded backdrop-blur-xs opacity-0 group-hover/color-swatch:opacity-100 transition-opacity flex items-center gap-1">
                          Inspect <ExternalLink className="w-2.5 h-2.5" />
                        </span>
                      </Link>

                      {/* Name & Hex */}
                      <div className="flex items-start justify-between gap-2">
                        <div className="min-w-0">
                          <Link
                            href={`/color/${cleanHex}`}
                            className="text-base font-bold text-slate-900 hover:text-sky-600 block truncate transition-colors"
                          >
                            {matchedName}
                          </Link>
                          <span className="text-xs font-mono text-slate-500 block mt-0.5">
                            {hex}
                          </span>
                        </div>
                        <button
                          onClick={(e) => handleRemoveColor(e, hex)}
                          className="p-1.5 rounded-lg text-pink-500 hover:bg-pink-50 transition-colors shrink-0"
                          title="Remove from favorites"
                        >
                          <Heart className="w-4 h-4 fill-pink-500" />
                        </button>
                      </div>

                      <div className="text-[11px] font-mono text-slate-400 mt-2">
                        rgb({rgb.r}, {rgb.g}, {rgb.b})
                      </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-2 pt-3 mt-3 border-t border-slate-100">
                      <button
                        onClick={() => copyText(hex, 'HEX')}
                        className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center gap-1 transition-colors"
                      >
                        <Copy className="w-3 h-3" /> HEX
                      </button>
                      <button
                        onClick={() => copyText(`rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`, 'RGB')}
                        className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center gap-1 transition-colors"
                      >
                        <Copy className="w-3 h-3" /> RGB
                      </button>
                      <Link
                        href={`/color/${cleanHex}`}
                        className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center gap-1 transition-colors text-center"
                        title={`View ${matchedName} details`}
                      >
                        <ExternalLink className="w-3 h-3 text-slate-400" /> Details
                      </Link>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </section>
      </div>
    </div>
  );
}
