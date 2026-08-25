'use client';

import React, { useState } from 'react';
import { ColorName } from '@/types';
import { findClosestColorName, normalizeHex } from '@/lib/color-math';
import { useToast } from '@/components/ui/ToastProvider';
import { Copy, Tag } from 'lucide-react';

interface Props {
  colors: ColorName[];
}

export default function HexToColorNameClient({ colors }: Props) {
  const { showToast } = useToast();
  const [hexInput, setHexInput] = useState('#3498DB');

  const normalized = normalizeHex(hexInput);
  const result = findClosestColorName(normalized, colors);

  const copyResult = () => {
    navigator.clipboard.writeText(result.name);
    showToast(`Copied name "${result.name}" to clipboard!`);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 pt-8 pb-16 space-y-8">


      <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        <div className="flex flex-col sm:flex-row gap-3">
          <input
            type="text"
            value={hexInput}
            onChange={(e) => setHexInput(e.target.value)}
            placeholder="#3498DB"
            className="flex-1 bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-5 py-3.5 font-mono text-base text-slate-900 focus:outline-none"
          />
          <button
            onClick={copyResult}
            className="px-6 py-3.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold text-sm shadow-md hover:from-purple-700 hover:to-pink-600 transition-colors flex items-center justify-center gap-2"
          >
            <Copy className="w-4 h-4" /> Copy Name
          </button>
        </div>

        <div className="flex items-center gap-6 p-6 bg-[#FFF5F7] border border-pink-100 rounded-2xl">
          <div
            className="w-20 h-20 rounded-2xl shadow-sm border border-slate-200 shrink-0"
            style={{ backgroundColor: normalized }}
          />
          <div>
            <span className="text-xs font-bold text-slate-400 uppercase tracking-widest block">Matched Color Name</span>
            <h2 className="text-3xl font-black text-slate-900 mt-1">{result.name}</h2>
            <p className="text-xs text-slate-500 mt-1 font-mono">
              Target Hex: {normalized} • Match Hex: {result.hex}
            </p>
          </div>
        </div>
      </div>

      <article className="bg-white border border-slate-100 rounded-3xl p-8 text-slate-600 space-y-3">
        <h3 className="text-xl font-extrabold text-slate-900">How HEX to Color Name Matching Works</h3>
        <p className="text-sm leading-relaxed">
          Hexadecimal color codes define exact proportions of Red, Green, and Blue light. To convert a numerical hex code into a human-understandable color name, Color Magic converts both target colors and 300+ standardized color definitions into the CIELAB (Lab) color space.
        </p>
        <p className="text-sm leading-relaxed">
          Using the Delta E 76 Euclidean distance formula in Lab space, we locate the perceptually closest color name in human vision.
        </p>
      </article>
    </div>
  );
}
