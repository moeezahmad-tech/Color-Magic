'use client';

import React, { useState, useEffect } from 'react';
import { Palette, Gradient } from '@/types';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { GradientCard } from '@/components/ui/GradientCard';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { Heart, Sparkles, Palette as PaletteIcon } from 'lucide-react';
import Link from 'next/link';

interface Props {
  palettes: Palette[];
  gradients: Gradient[];
}

export default function ProfileClient({ palettes, gradients }: Props) {
  const [activeTab, setActiveTab] = useState<'palettes' | 'gradients'>('palettes');
  const [mounted, setMounted] = useState(false);
  const { favoritePalettes, favoriteGradients } = useFavoritesStore();

  useEffect(() => {
    setMounted(true);
  }, []);

  const favPaletteItems = palettes.filter((p) => favoritePalettes.includes(p.id || p.slug));
  const favGradientItems = gradients.filter((g) => favoriteGradients.includes(g.id));

  const handleDeleteAccount = () => {
    if (confirm('Are you sure you want to delete your account? This action purges all stored preferences.')) {
      window.location.href = '/api/user/delete';
    }
  };

  if (!mounted) return null;

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="flex justify-between items-end mb-8">
        <div>
          <h1 className="text-4xl font-black text-slate-900 tracking-tight">Your Profile</h1>
          <p className="text-slate-600 mt-1 text-base">Manage your saved palettes and gradients.</p>
        </div>
        <button 
          onClick={handleDeleteAccount}
          className="text-xs text-red-500 hover:text-red-700 font-bold px-4 py-2 border border-red-200 hover:border-red-500 rounded-lg transition-colors"
        >
          Delete Data
        </button>
      </div>

      {/* Tabs */}
      <div className="flex items-center gap-2 mb-8">
        <button
          onClick={() => setActiveTab('palettes')}
          className={`px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 ${
            activeTab === 'palettes'
              ? 'bg-pink-500 text-white shadow-lg shadow-pink-500/30'
              : 'bg-slate-100 border border-slate-200 text-slate-500 hover:text-slate-800'
          }`}
        >
          <PaletteIcon className="w-4 h-4" />
          <span>Saved Palettes ({favPaletteItems.length})</span>
        </button>

        <button
          onClick={() => setActiveTab('gradients')}
          className={`px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 ${
            activeTab === 'gradients'
              ? 'bg-pink-500 text-white shadow-lg shadow-pink-500/30'
              : 'bg-slate-100 border border-slate-200 text-slate-500 hover:text-slate-800'
          }`}
        >
          <Sparkles className="w-4 h-4" />
          <span>Saved Gradients ({favGradientItems.length})</span>
        </button>
      </div>

      {/* Tab Content */}
      {activeTab === 'palettes' ? (
        favPaletteItems.length === 0 ? (
          <div className="text-center py-20 bg-pink-50/50 border border-pink-100 rounded-3xl">
            <Heart className="w-12 h-12 text-pink-300 mx-auto mb-3" />
            <h3 className="text-xl font-bold text-slate-800">No Saved Palettes Yet</h3>
            <p className="text-slate-500 text-sm mt-1 mb-6">
              Click the heart icon on any palette card to add it to your favorites.
            </p>
            <Link
              href="/palettes"
              className="px-6 py-2.5 rounded-xl bg-pink-500 text-white font-bold text-sm inline-block"
            >
              Explore Palettes
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {favPaletteItems.map((p) => (
              <PaletteCard key={p.id} palette={p} />
            ))}
          </div>
        )
      ) : favGradientItems.length === 0 ? (
        <div className="text-center py-20 bg-pink-50/50 border border-pink-100 rounded-3xl">
          <Sparkles className="w-12 h-12 text-purple-300 mx-auto mb-3" />
          <h3 className="text-xl font-bold text-slate-800">No Saved Gradients Yet</h3>
          <p className="text-slate-500 text-sm mt-1 mb-6">
            Click the heart icon on any gradient card to save it here.
          </p>
          <Link
            href="/gradients"
            className="px-6 py-2.5 rounded-xl bg-pink-500 text-white font-bold text-sm inline-block"
          >
            Explore Gradients
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {favGradientItems.map((g) => (
            <GradientCard key={g.id} gradient={g} />
          ))}
        </div>
      )}
    </div>
  );
}
