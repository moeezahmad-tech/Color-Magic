'use client';

import React, { useState, useMemo, useEffect } from 'react';
import { ColorName } from '@/types';
import { useToast } from '@/components/ui/ToastProvider';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { hexToRgb, hexToHsl } from '@/lib/color-math';
import { PageHeader } from '@/components/ui/PageHeader';
import { 
  Search, 
  Shuffle, 
  ChevronDown, 
  Copy, 
  ExternalLink, 
  Heart, 
  Droplets,
  Sparkles
} from 'lucide-react';
import Link from 'next/link';

interface Props {
  colors: ColorName[];
}

function shuffleArray<T>(array: T[]): T[] {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
}

const COLOR_FAMILIES = [
  { id: 'all', label: 'All Colors' },
  { id: 'red', label: 'Reds & Corals' },
  { id: 'orange', label: 'Oranges & Ambers' },
  { id: 'yellow', label: 'Yellows & Golds' },
  { id: 'green', label: 'Greens & Mints' },
  { id: 'cyan', label: 'Cyans & Teals' },
  { id: 'blue', label: 'Blues & Indigos' },
  { id: 'purple', label: 'Purples & Violets' },
  { id: 'pink', label: 'Pinks & Roses' },
  { id: 'neutral', label: 'Grays & Neutrals' },
  { id: 'favorites', label: 'Saved Colors' },
];

function getColorFamily(h: number, s: number, l: number): string {
  if (s < 12 || l < 8 || l > 94) return 'neutral';
  if (h >= 345 || h < 15) return 'red';
  if (h >= 15 && h < 45) return 'orange';
  if (h >= 45 && h < 70) return 'yellow';
  if (h >= 70 && h < 165) return 'green';
  if (h >= 165 && h < 195) return 'cyan';
  if (h >= 195 && h < 265) return 'blue';
  if (h >= 265 && h < 315) return 'purple';
  return 'pink';
}

const ITEMS_PER_PAGE = 72;

export default function ColorsClient({ colors }: Props) {
  const [displayColors, setDisplayColors] = useState<ColorName[]>(colors);
  const [searchQuery, setSearchQuery] = useState('');
  const [activeFamily, setActiveFamily] = useState('all');
  const [isFamilyDropdownOpen, setIsFamilyDropdownOpen] = useState(false);
  const [visibleCount, setVisibleCount] = useState(ITEMS_PER_PAGE);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const { showToast } = useToast();
  const { favoriteColors, toggleFavoriteColor, isColorFavorited } = useFavoritesStore();

  const handleShuffle = () => {
    setDisplayColors((prev) => shuffleArray(prev));
    setVisibleCount(ITEMS_PER_PAGE);
  };

  const copyText = (val: string, label: string) => {
    navigator.clipboard.writeText(val);
    showToast(`Copied ${label}: ${val}`);
  };

  const handleToggleColorFav = (e: React.MouseEvent, hex: string) => {
    e.preventDefault();
    e.stopPropagation();
    const isNowFav = isColorFavorited(hex);
    toggleFavoriteColor(hex);
    showToast(isNowFav ? `Removed ${hex} from favorites` : `Saved ${hex} to favorites!`);
  };

  const enrichedColors = useMemo(() => {
    return displayColors.map((c) => {
      const rgb = hexToRgb(c.hex);
      const hsl = hexToHsl(c.hex);
      const family = getColorFamily(hsl.h, hsl.s, hsl.l);
      const cleanHex = c.hex.replace('#', '').toLowerCase();
      return {
        ...c,
        rgb,
        hsl,
        family,
        cleanHex,
      };
    });
  }, [displayColors]);

  const filteredColors = useMemo(() => {
    return enrichedColors.filter((item) => {
      if (activeFamily === 'favorites') {
        if (!favoriteColors.includes(item.hex.toUpperCase())) return false;
      } else if (activeFamily !== 'all' && item.family !== activeFamily) {
        return false;
      }

      if (searchQuery.trim()) {
        const q = searchQuery.toLowerCase().trim();
        const matchesName = item.name.toLowerCase().includes(q);
        const matchesHex = item.hex.toLowerCase().includes(q);
        return matchesName || matchesHex;
      }

      return true;
    });
  }, [enrichedColors, activeFamily, searchQuery, favoriteColors]);

  const currentVisibleColors = useMemo(() => {
    return filteredColors.slice(0, visibleCount);
  }, [filteredColors, visibleCount]);

  return (
    <div>
      <PageHeader 
        title="Explore Named Colors" 
        description="Browse thousands of curated named colors — search by name, hex code, or color family."
        icon={<Droplets className="w-6 h-6" />}
      />
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">
        <div className="space-y-6">
          {/* Controls Bar */}
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
          {/* Search */}
          <div className="relative flex-1 w-full sm:w-auto">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Search by color name or hex code (e.g. Amber, #f59e0b)..."
              value={searchQuery}
              onChange={(e) => {
                setSearchQuery(e.target.value);
                setVisibleCount(ITEMS_PER_PAGE);
              }}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-all placeholder:text-slate-400"
            />
          </div>

          <div className="flex items-center gap-2 w-full sm:w-auto">
            {/* Shuffle Button */}
            <button
              onClick={handleShuffle}
              title="Shuffle Colors"
              className="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-sky-50 hover:text-sky-600 hover:border-sky-200 transition-all shrink-0 cursor-pointer"
            >
              <Shuffle className="w-4 h-4 text-sky-500" />
              <span>Shuffle</span>
            </button>

            {/* Family Dropdown */}
            <div className="relative flex-1 sm:flex-initial">
              <button
                onClick={() => setIsFamilyDropdownOpen(!isFamilyDropdownOpen)}
                onBlur={() => setTimeout(() => setIsFamilyDropdownOpen(false), 200)}
                className="w-full sm:w-auto flex items-center justify-between gap-2 px-5 py-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition-colors"
              >
                <span>Family ({COLOR_FAMILIES.find((f) => f.id === activeFamily)?.label || activeFamily})</span>
                <ChevronDown className={`w-4 h-4 text-slate-500 transition-transform ${isFamilyDropdownOpen ? 'rotate-180' : ''}`} />
              </button>

              {isFamilyDropdownOpen && (
                <div className="origin-top-right absolute right-0 mt-2 w-56 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50 max-h-80 overflow-y-auto">
                  {COLOR_FAMILIES.map((family) => (
                    <button
                      key={family.id}
                      onClick={() => {
                        setActiveFamily(family.id);
                        setIsFamilyDropdownOpen(false);
                        setVisibleCount(ITEMS_PER_PAGE);
                      }}
                      className={`w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 transition-colors flex items-center justify-between ${
                        activeFamily === family.id ? 'text-sky-600 font-bold bg-sky-50/50' : 'text-slate-700'
                      }`}
                    >
                      <span>{family.label}</span>
                      {family.id === 'favorites' && mounted && (
                        <span className="text-[10px] bg-pink-100 text-pink-700 px-1.5 py-0.5 rounded-full">
                          {favoriteColors.length}
                        </span>
                      )}
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Color Count Banner */}
        <div className="flex items-center justify-between text-xs text-slate-500 font-medium px-1">
          <span>
            Showing <strong className="text-slate-800">{currentVisibleColors.length}</strong> of{' '}
            <strong className="text-slate-800">{filteredColors.length}</strong> named colors
          </span>
          {activeFamily !== 'all' && (
            <button
              onClick={() => {
                setActiveFamily('all');
                setSearchQuery('');
                setVisibleCount(ITEMS_PER_PAGE);
              }}
              className="text-sky-600 hover:underline font-semibold"
            >
              Reset filters
            </button>
          )}
        </div>

        {/* Grid */}
        {filteredColors.length === 0 ? (
          <div className="text-center py-20 bg-white border border-slate-100 rounded-3xl shadow-sm">
            <Sparkles className="w-12 h-12 text-slate-400 mx-auto mb-3" />
            <h3 className="text-lg font-bold text-slate-800">No Colors Found</h3>
            <p className="text-slate-500 text-xs mt-1 max-w-sm mx-auto">
              {activeFamily === 'favorites'
                ? "You haven't saved any colors yet. Click the heart icon on any color to add it here!"
                : 'Try adjusting your search query or choosing another color family.'}
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            {currentVisibleColors.map((item) => {
              const isColorFav = mounted && isColorFavorited(item.hex);
              return (
                <div
                  key={item.hex}
                  className="group bg-white rounded-2xl border border-slate-100 p-3.5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
                >
                  <div>
                    {/* Color Preview Swatch */}
                    <Link
                      href={`/color/${item.cleanHex}`}
                      className="block h-32 w-full rounded-xl overflow-hidden border border-slate-200/80 shadow-inner relative group/color-swatch mb-3"
                      title={`View ${item.name} (${item.hex})`}
                    >
                      <div
                        className="w-full h-full group-hover/color-swatch:scale-105 transition-transform duration-300"
                        style={{ backgroundColor: item.hex }}
                      />
                      <span className="absolute bottom-2 right-2 text-[10px] font-bold bg-white/90 text-slate-800 px-2 py-0.5 rounded backdrop-blur-xs opacity-0 group-hover/color-swatch:opacity-100 transition-opacity flex items-center gap-1">
                        Inspect <ExternalLink className="w-2.5 h-2.5" />
                      </span>
                    </Link>

                    {/* Name & Hex */}
                    <div className="flex items-start justify-between gap-2">
                      <div className="min-w-0">
                        <Link
                          href={`/color/${item.cleanHex}`}
                          className="text-base font-bold text-slate-900 hover:text-sky-600 block truncate transition-colors"
                        >
                          {item.name}
                        </Link>
                        <span className="text-xs font-mono text-slate-500 block mt-0.5">
                          {item.hex}
                        </span>
                      </div>
                      <button
                        onClick={(e) => handleToggleColorFav(e, item.hex)}
                        className="p-1.5 rounded-lg text-slate-300 hover:text-pink-500 hover:bg-pink-50 transition-colors shrink-0"
                        title={isColorFav ? 'Remove from favorites' : 'Save to favorites'}
                      >
                        <Heart className={`w-4 h-4 ${isColorFav ? 'fill-pink-500 text-pink-500' : ''}`} />
                      </button>
                    </div>

                    <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mt-2">
                      <span>rgb({item.rgb.r}, {item.rgb.g}, {item.rgb.b})</span>
                      <span>{item.hsl.h}°, {item.hsl.s}%</span>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-2 pt-3 mt-3 border-t border-slate-100">
                    <button
                      onClick={() => copyText(item.hex, 'HEX')}
                      className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center gap-1 transition-colors"
                    >
                      <Copy className="w-3 h-3" /> HEX
                    </button>
                    <button
                      onClick={() => copyText(`rgb(${item.rgb.r}, ${item.rgb.g}, ${item.rgb.b})`, 'RGB')}
                      className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center gap-1 transition-colors"
                    >
                      <Copy className="w-3 h-3" /> RGB
                    </button>
                    <Link
                      href={`/color/${item.cleanHex}`}
                      className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 hover:text-slate-900 flex items-center justify-center gap-1 transition-colors text-center"
                      title={`View ${item.name} details`}
                    >
                      <ExternalLink className="w-3 h-3 text-slate-400" /> Details
                    </Link>
                  </div>
                </div>
              );
            })}
          </div>
        )}

        {/* Load More Button */}
        {visibleCount < filteredColors.length && (
          <div className="text-center pt-8">
            <button
              onClick={() => setVisibleCount((prev) => prev + ITEMS_PER_PAGE)}
              className="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2 cursor-pointer"
            >
              <span>Load More Colors</span>
              <span className="text-xs text-slate-400 font-mono">
                (+{Math.min(ITEMS_PER_PAGE, filteredColors.length - visibleCount)})
              </span>
            </button>
          </div>
        )}
      </div>
    </div>
    </div>
  );
}
