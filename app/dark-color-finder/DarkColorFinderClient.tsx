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
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 space-y-8">


      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {darkPalettes.slice(0, 12).map((palette) => (
          <PaletteCard key={palette.id} palette={palette} />
        ))}
      </div>
    </div>
  );
}
