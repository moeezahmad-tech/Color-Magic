'use client';

import React, { useState, useEffect, useMemo } from 'react';
import { Palette } from '@/types';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { Search, ChevronDown, Palette as PaletteIcon, Shuffle } from 'lucide-react';
import { PageHeader } from '@/components/ui/PageHeader';

interface Props {
  palettes: Palette[];
}

function shuffleArray<T>(array: T[]): T[] {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
}

export default function PalettesClient({ palettes }: Props) {
  const [displayPalettes, setDisplayPalettes] = useState<Palette[]>(palettes);
  const [searchQuery, setSearchQuery] = useState('');
  const [activeFilter, setActiveFilter] = useState('all');
  const [isFilterDropdownOpen, setIsFilterDropdownOpen] = useState(false);

  const { favoritePalettes } = useFavoritesStore();

  const handleShuffle = () => {
    setDisplayPalettes((prev) => shuffleArray(prev));
  };

  const filterCategories = [
    { id: 'all', label: 'All Styles' },
    { id: 'pastel', label: 'Pastel' },
    { id: 'vintage', label: 'Vintage' },
    { id: 'neon', label: 'Neon' },
    { id: 'minimalist', label: 'Minimalist' },
    { id: 'earthy', label: 'Earthy' },
    { id: 'eco', label: 'Eco' },
    { id: 'favorites', label: `Favorites (${favoritePalettes.length})` },
  ];

  const filteredPalettes = useMemo(() => {
    let items = displayPalettes;

    if (activeFilter === 'favorites') {
      items = items.filter((p) => favoritePalettes.includes(p.id || p.slug));
    } else if (activeFilter !== 'all') {
      items = items.filter((p) =>
        p.style.toLowerCase() === activeFilter.toLowerCase() ||
        p.tags.some((t) => t.toLowerCase() === activeFilter.toLowerCase())
      );
    }

    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim();
      items = items.filter(
        (p) =>
          p.name.toLowerCase().includes(q) ||
          p.style.toLowerCase().includes(q) ||
          p.colors.some((c) => c.toLowerCase().includes(q))
      );
    }

    return items;
  }, [activeFilter, searchQuery, favoritePalettes, displayPalettes]);

  return (
    <div>
      <PageHeader 
        title="Explore Color Palettes" 
        description="Browse thousands of professional color schemes — search by hex, theme, or style."
        icon={<PaletteIcon className="w-6 h-6" />}
      />
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-12">

      {/* Main Container */}
      <div className="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
        {/* Search Bar & Filter Dropdown Trigger */}
        <div className="flex flex-col sm:flex-row items-center gap-3 mb-8">
          <div className="relative flex-1 w-full">
            <Search className="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search hex, theme or color name..."
              className="w-full bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-purple-500 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors"
            />
          </div>

          <div className="flex items-center gap-2 w-full sm:w-auto">
            {/* Shuffle Button */}
            <button
              onClick={handleShuffle}
              title="Shuffle Palettes"
              className="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-purple-50 hover:text-purple-600 hover:border-purple-200 transition-all shrink-0"
            >
              <Shuffle className="w-4 h-4 text-purple-500" />
              <span>Shuffle</span>
            </button>

            {/* Filter Dropdown */}
            <div className="relative flex-1 sm:flex-initial">
              <button
                onClick={() => setIsFilterDropdownOpen(!isFilterDropdownOpen)}
                onBlur={() => setTimeout(() => setIsFilterDropdownOpen(false), 200)}
                className="w-full sm:w-auto flex items-center justify-between gap-2 px-5 py-3 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition-colors"
              >
                <span>Filters ({activeFilter})</span>
                <ChevronDown className={`w-4 h-4 text-slate-500 transition-transform ${isFilterDropdownOpen ? 'rotate-180' : ''}`} />
              </button>

              {isFilterDropdownOpen && (
                <div className="origin-top-right absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50">
                  {filterCategories.map((cat) => (
                    <button
                      key={cat.id}
                      onClick={() => {
                        setActiveFilter(cat.id);
                        setIsFilterDropdownOpen(false);
                      }}
                      className={`w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 transition-colors ${
                        activeFilter === cat.id ? 'text-purple-600 font-bold bg-purple-50/50' : 'text-slate-700'
                      }`}
                    >
                      {cat.label}
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* 3-Column Grid */}
        {filteredPalettes.length === 0 ? (
          <div className="text-center py-20 bg-slate-50 border border-slate-100 rounded-2xl">
            <PaletteIcon className="w-12 h-12 text-slate-400 mx-auto mb-3" />
            <h3 className="text-lg font-bold text-slate-800">No Palettes Found</h3>
            <p className="text-slate-500 text-xs mt-1">
              Try adjusting your search query or filter settings.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredPalettes.map((palette) => (
              <PaletteCard key={palette.id} palette={palette} />
            ))}
          </div>
        )}
      </div>
    </div>
    </div>
  );
}


