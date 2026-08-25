'use client';

import React, { useState } from 'react';
import { ColorName } from '@/types';
import { findClosestColorName, normalizeHex, hexToRgb, rgbToHsl } from '@/lib/color-math';
import { useToast } from '@/components/ui/ToastProvider';
import { HelpCircle } from 'lucide-react';

interface Props {
  colors: ColorName[];
}

export default function WhatColorIsClient({ colors }: Props) {
  const { showToast } = useToast();
  const [query, setQuery] = useState('2ECC71');

  const normalized = normalizeHex(query);
  const rgb = hexToRgb(normalized);
  const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
  const match = findClosestColorName(normalized, colors);

  const getFamily = (h: number, s: number, l: number) => {
    if (l < 15) return 'Dark / Black Family';
    if (l > 88) return 'Light / White Family';
    if (s < 12) return 'Achromatic / Grey Family';
    if (h >= 345 || h < 15) return 'Red Family';
    if (h >= 15 && h < 45) return 'Orange Family';
    if (h >= 45 && h < 70) return 'Yellow Family';
    if (h >= 70 && h < 165) return 'Green Family';
    if (h >= 165 && h < 260) return 'Blue Family';
    if (h >= 260 && h < 315) return 'Purple / Violet Family';
    return 'Pink / Magenta Family';
  };

  const family = getFamily(hsl.h, hsl.s, hsl.l);

  return (
    <div className="max-w-7xl mx-auto px-4 pt-8 pb-16 space-y-8">


      <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        <div className="flex gap-4">
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Type hex code (e.g. 2ECC71)..."
            className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-5 py-3.5 font-mono text-base text-slate-900 focus:outline-none"
          />
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-[#FFF5F7] border border-pink-100 rounded-2xl items-center">
          <div
            className="h-40 w-full rounded-2xl border border-slate-200 shadow-sm"
            style={{ backgroundColor: normalized }}
          />
          <div className="space-y-3">
            <div>
              <span className="text-xs font-bold text-slate-400 uppercase tracking-widest block">Closest Match</span>
              <h2 className="text-2xl font-black text-slate-900">{match.name}</h2>
            </div>
            <div>
              <span className="text-xs font-bold text-slate-400 uppercase tracking-widest block">Color Family</span>
              <div className="text-lg font-bold text-pink-600">{family}</div>
            </div>
            <div>
              <span className="text-xs font-bold text-slate-400 uppercase tracking-widest block">HSL Hue Angle</span>
              <div className="text-sm font-mono text-slate-700">{hsl.h}° (Sat: {hsl.s}%, Light: {hsl.l}%)</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
