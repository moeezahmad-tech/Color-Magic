'use client';

import React, { useState } from 'react';
import { useToast } from '@/components/ui/ToastProvider';
import { Search, Globe, Bookmark } from 'lucide-react';

interface BrandColor {
  brand: string;
  category: string;
  colors: { name: string; hex: string }[];
}

export default function BrandColorLookupClient() {
  const { showToast } = useToast();
  const [query, setQuery] = useState('');

  const brands: BrandColor[] = [
    {
      brand: 'Google',
      category: 'Tech Giant',
      colors: [
        { name: 'Google Blue', hex: '#4285F4' },
        { name: 'Google Red', hex: '#EA4335' },
        { name: 'Google Yellow', hex: '#FBBC05' },
        { name: 'Google Green', hex: '#34A853' },
      ],
    },
    {
      brand: 'Spotify',
      category: 'Audio Streaming',
      colors: [
        { name: 'Spotify Green', hex: '#1DB954' },
        { name: 'Spotify Black', hex: '#191414' },
        { name: 'Spotify White', hex: '#FFFFFF' },
      ],
    },
    {
      brand: 'Apple',
      category: 'Consumer Hardware',
      colors: [
        { name: 'Space Grey', hex: '#555555' },
        { name: 'Silver', hex: '#E2E2E2' },
        { name: 'Midnight', hex: '#1D252C' },
        { name: 'Starlight', hex: '#F0E5D8' },
      ],
    },
    {
      brand: 'Stripe',
      category: 'Fintech',
      colors: [
        { name: 'Stripe Blurple', hex: '#635BFF' },
        { name: 'Stripe Slate', hex: '#0A2540' },
        { name: 'Stripe Turquoise', hex: '#00D4B2' },
      ],
    },
    {
      brand: 'Vercel',
      category: 'Developer Cloud',
      colors: [
        { name: 'Vercel Black', hex: '#000000' },
        { name: 'Vercel White', hex: '#FFFFFF' },
        { name: 'Vercel Blue', hex: '#0070F3' },
      ],
    },
    {
      brand: 'Netflix',
      category: 'Media Streaming',
      colors: [
        { name: 'Netflix Red', hex: '#E50914' },
        { name: 'Netflix Black', hex: '#141414' },
      ],
    },
    {
      brand: 'Figma',
      category: 'Design Tools',
      colors: [
        { name: 'Figma Red', hex: '#F24E1E' },
        { name: 'Figma Orange', hex: '#FF7262' },
        { name: 'Figma Purple', hex: '#A259FF' },
        { name: 'Figma Blue', hex: '#1ABCFE' },
        { name: 'Figma Green', hex: '#0ACF83' },
      ],
    },
    {
      brand: 'GitHub',
      category: 'Developer Platform',
      colors: [
        { name: 'GitHub Dark', hex: '#0D1117' },
        { name: 'GitHub Accent', hex: '#238636' },
        { name: 'GitHub Purple', hex: '#8957E5' },
      ],
    },
  ];

  const filtered = brands.filter(
    (b) =>
      b.brand.toLowerCase().includes(query.toLowerCase()) ||
      b.category.toLowerCase().includes(query.toLowerCase())
  );

  const copyHex = (hex: string, label: string) => {
    navigator.clipboard.writeText(hex);
    showToast(`Copied ${label}: ${hex}`);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 space-y-8">


      <div className="max-w-xl mx-auto">
        <div className="relative">
          <Search className="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Search brand name (e.g. Spotify, Figma, Stripe)..."
            className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none"
          />
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filtered.map((item) => (
          <div key={item.brand} className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-2xl font-extrabold text-slate-900">{item.brand}</h2>
                <span className="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-50 text-slate-500 border border-slate-200/60">
                  {item.category}
                </span>
              </div>

              <div className="space-y-3">
                {item.colors.map((c) => (
                  <div
                    key={c.name}
                    onClick={() => copyHex(c.hex, c.name)}
                    className="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-purple-200 cursor-pointer group transition-all"
                  >
                    <div className="flex items-center gap-3">
                      <div
                        className="w-8 h-8 rounded-lg border border-slate-200 shadow-2xs"
                        style={{ backgroundColor: c.hex }}
                      />
                      <span className="text-sm font-semibold text-slate-800 group-hover:text-purple-600">
                        {c.name}
                      </span>
                    </div>
                    <span className="text-xs font-mono font-bold text-pink-500">
                      {c.hex}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
