'use client';

import React, { useState, useEffect, useMemo } from 'react';
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

export default function GradientsClient({ gradients }: Props) {
  const [displayGradients, setDisplayGradients] = useState<Gradient[]>(gradients);
  const [activeCategory, setActiveCategory] = useState('all');
  const [gradientType, setGradientType] = useState<'all' | 'linear' | 'radial'>('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [isFilterDropdownOpen, setIsFilterDropdownOpen] = useState(false);

  const { favoriteGradients } = useFavoritesStore();

  // Shuffle gradients dynamically on client mount so each visitor gets a fresh UI order
  useEffect(() => {
    setDisplayGradients(shuffleArray(gradients));
  }, [gradients]);

  const handleShuffle = () => {
    setDisplayGradients((prev) => shuffleArray(prev));
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
    let items = displayGradients;

    if (activeCategory === 'favorites') {
      items = items.filter((g) => favoriteGradients.includes(g.id));
    } else if (activeCategory !== 'all') {
      items = items.filter(
        (g) => g.style.toLowerCase() === activeCategory.toLowerCase()
      );
    }

    if (gradientType !== 'all') {
      items = items.filter((g) => g.type === gradientType);
    }

    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim();
      items = items.filter(
        (g) =>
          g.name.toLowerCase().includes(q) ||
          g.style.toLowerCase().includes(q) ||
          g.colors.some((c) => c.toLowerCase().includes(q))
      );
    }

    return items;
  }, [activeCategory, gradientType, searchQuery, favoriteGradients, displayGradients]);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12">
      {/* Utilities */}
      <div className="flex justify-end mb-4">
        <span className="text-xs font-mono text-slate-400 font-semibold shrink-0">
          {filteredGradients.length} gradients
        </span>
      </div>

      {/* Main White Card Container */}
      <div className="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
        {/* Search Bar & Filters Dropdown */}
        <div className="flex flex-col sm:flex-row items-center gap-3 mb-8">
          <div className="relative flex-1 w-full">
            <Search className="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search gradient name, style, or type..."
              className="w-full bg-slate-50 border border-slate-200/80 focus:bg-white focus:border-purple-500 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors"
            />
          </div>

          <div className="flex items-center gap-2 w-full sm:w-auto">
            {/* Shuffle Button */}
            <button
              onClick={handleShuffle}
              title="Shuffle Gradients"
              className="flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all shrink-0"
            >
              <Shuffle className="w-4 h-4 text-amber-500" />
              <span>Shuffle</span>
            </button>

            {/* Filter Dropdown */}
            <div className="relative flex-1 sm:flex-initial">
              <button
                onClick={() => setIsFilterDropdownOpen(!isFilterDropdownOpen)}
                onBlur={() => setTimeout(() => setIsFilterDropdownOpen(false), 200)}
                className="w-full sm:w-auto flex items-center justify-between gap-2 px-5 py-3 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition-colors"
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
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredGradients.map((gradient) => (
              <GradientCard key={gradient.id} gradient={gradient} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}


