'use client';

import React from 'react';
import { Palette } from '@/types';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { Moon } from 'lucide-react';

interface Props {
  palettes: Palette[];
}

export default function DarkColorFinderClient({ palettes }: Props) {
  const darkPalettes = palettes.filter((p) =>
    p.tags.some((t) =>
      ['dark', 'minimalist', 'cool', 'modern', 'vintage'].includes(t.toLowerCase())
    )
  );

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-8">
      <div className="text-center space-y-3">
        <h1 className="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight">
          Dark Color Name Finder
        </h1>
        <p className="text-slate-600 text-base max-w-2xl mx-auto">
          Discover names for deep, dark shades, low-luminance themes, and midnight tones.
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {darkPalettes.slice(0, 12).map((palette) => (
          <PaletteCard key={palette.id} palette={palette} />
        ))}
      </div>
    </div>
  );
}
