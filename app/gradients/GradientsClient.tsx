'use client';

import React, { useState, useMemo } from 'react';
import { Gradient } from '@/types';
import { GradientCard } from '@/components/ui/GradientCard';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { Search, ChevronDown, Sparkles, Shuffle } from 'lucide-react';

interface Props {
  gradients: Gradient[];
}

function shuffleArray<T>(array: T[]): T[] {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
}

const ITEMS_PER_PAGE = 72;

export default function GradientsClient({ gradients }: Props) {
  const [displayGradients, setDisplayGradients] = useState<Gradient[]>(gradients);
  const [activeCategory, setActiveCategory] = useState('all');
  const [gradientType, setGradientType] = useState<'all' | 'linear' | 'radial'>('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [isFilterDropdownOpen, setIsFilterDropdownOpen] = useState(false);
  const [visibleCount, setVisibleCount] = useState(ITEMS_PER_PAGE);

  const { favoriteGradients } = useFavoritesStore();

  const handleShuffle = () => {
    setDisplayGradients((prev) => shuffleArray(prev));
    setVisibleCount(ITEMS_PER_PAGE);
  };

  const filterCategories = [
    { id: 'all', label: 'All Styles' },
    { id: 'warm', label: 'Warm' },
    { id: 'cool', label: 'Cool' },
    { id: 'purple', label: 'Purple' },
    { id: 'nature', label: 'Nature' },
    { id: 'pink', label: 'Pink' },
    { id: 'dark', label: 'Dark' },
    { id: 'pastel', label: 'Pastel' },
    { id: 'neon', label: 'Neon' },
    { id: 'favorites', label: `Favorites (${favoriteGradients.length})` },
  ];

  const filteredGradients = useMemo(() => {
    return displayGradients.filter((gradient) => {
      // Type Filter
      if (gradientType !== 'all' && gradient.type !== gradientType) {
        return false;
      }

      // Category / Style Filter
      if (activeCategory === 'favorites') {
        return favoriteGradients.includes(gradient.id);
      }
      if (activeCategory !== 'all' && gradient.style.toLowerCase() !== activeCategory.toLowerCase()) {
        return false;
      }

      // Search Query
      if (searchQuery.trim()) {
        const q = searchQuery.toLowerCase();
        const matchesName = gradient.name.toLowerCase().includes(q);
        const matchesColor = gradient.colors.some((c) => c.toLowerCase().includes(q));
        const matchesStyle = gradient.style.toLowerCase().includes(q);
        return matchesName || matchesColor || matchesStyle;
      }

      return true;
    });
  }, [displayGradients, activeCategory, gradientType, searchQuery, favoriteGradients]);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div className="space-y-6">
        {/* Controls Bar */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
          {/* Search */}
          <div className="relative flex-1 w-full sm:w-auto">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Search gradients by name, style, or hex code..."
              value={searchQuery}
              onChange={(e) => {
                setSearchQuery(e.target.value);
                setVisibleCount(ITEMS_PER_PAGE);
              }}
              className="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-slate-400"
            />
          </div>

          <div className="flex items-center gap-2 w-full sm:w-auto">
            {/* Shuffle Button */}
            <button
              onClick={handleShuffle}
              title="Shuffle Gradients"
              className="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all shrink-0 cursor-pointer"
            >
              <Shuffle className="w-4 h-4 text-amber-500" />
              <span>Shuffle</span>
            </button>

            {/* Filter Dropdown */}
            <div className="relative flex-1 sm:flex-initial">
              <button
                onClick={() => setIsFilterDropdownOpen(!isFilterDropdownOpen)}
                onBlur={() => setTimeout(() => setIsFilterDropdownOpen(false), 200)}
                className="w-full sm:w-auto flex items-center justify-between gap-2 px-5 py-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition-colors"
              >
                <span>Filters ({activeCategory})</span>
                <ChevronDown className={`w-4 h-4 text-slate-500 transition-transform ${isFilterDropdownOpen ? 'rotate-180' : ''}`} />
              </button>

              {isFilterDropdownOpen && (
                <div className="origin-top-right absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50">
                  {filterCategories.map((cat) => (
                    <button
                      key={cat.id}
                      onClick={() => {
                        setActiveCategory(cat.id);
                        setIsFilterDropdownOpen(false);
                        setVisibleCount(ITEMS_PER_PAGE);
                      }}
                      className={`w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 transition-colors ${
                        activeCategory === cat.id ? 'text-purple-600 font-bold bg-purple-50/50' : 'text-slate-700'
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

        {/* Grid */}
        {filteredGradients.length === 0 ? (
          <div className="text-center py-20 bg-slate-50 border border-slate-100 rounded-2xl">
            <Sparkles className="w-12 h-12 text-slate-400 mx-auto mb-3" />
            <h3 className="text-lg font-bold text-slate-800">No Gradients Found</h3>
            <p className="text-slate-500 text-xs mt-1">
              Try adjusting category or search filters.
            </p>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredGradients.slice(0, visibleCount).map((gradient) => (
                <GradientCard key={gradient.id} gradient={gradient} />
              ))}
            </div>

            {/* Load More Button */}
            {visibleCount < filteredGradients.length && (
              <div className="text-center pt-8">
                <button
                  onClick={() => setVisibleCount((prev) => prev + ITEMS_PER_PAGE)}
                  className="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all inline-flex items-center gap-2 cursor-pointer"
                >
                  <span>Load More Gradients</span>
                  <span className="text-xs text-slate-400 font-mono">
                    (+{Math.min(ITEMS_PER_PAGE, filteredGradients.length - visibleCount)})
                  </span>
                </button>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}
