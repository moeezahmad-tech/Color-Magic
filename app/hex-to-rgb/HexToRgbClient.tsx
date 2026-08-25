'use client';

import React, { useState } from 'react';
import { hexToRgb, normalizeHex } from '@/lib/color-math';
import { useToast } from '@/components/ui/ToastProvider';
import { Copy, Sliders } from 'lucide-react';

export default function HexToRgbClient() {
  const { showToast } = useToast();
  const [hexInput, setHexInput] = useState('#E74C3C');

  const normalized = normalizeHex(hexInput);
  const rgb = hexToRgb(normalized);
  const rgbString = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;

  const copyRgb = () => {
    navigator.clipboard.writeText(rgbString);
    showToast(`Copied ${rgbString} to clipboard!`);
  };

  return (
    <div className="max-w-4xl mx-auto px-4 pt-8 pb-16 space-y-8">


      <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        <div className="flex flex-col sm:flex-row gap-3">
          <input
            type="text"
            value={hexInput}
            onChange={(e) => setHexInput(e.target.value)}
            placeholder="#E74C3C"
            className="flex-1 bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-5 py-3.5 font-mono text-base text-slate-900 focus:outline-none"
          />
          <button
            onClick={copyRgb}
            className="px-6 py-3.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold text-sm shadow-md hover:from-purple-700 hover:to-pink-600 transition-colors flex items-center justify-center gap-2"
          >
            <Copy className="w-4 h-4" /> Copy RGB
          </button>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 p-6 bg-slate-50 border border-slate-100 rounded-2xl items-center">
          <div
            className="h-20 sm:h-full w-full rounded-xl border border-slate-200 shadow-2xs"
            style={{ backgroundColor: normalized }}
          />
          <div className="text-center sm:text-left">
            <span className="text-xs text-slate-400 font-bold uppercase">Red (R)</span>
            <div className="text-2xl font-mono font-bold text-slate-900">{rgb.r}</div>
          </div>
          <div className="text-center sm:text-left">
            <span className="text-xs text-slate-400 font-bold uppercase">Green (G)</span>
            <div className="text-2xl font-mono font-bold text-slate-900">{rgb.g}</div>
          </div>
          <div className="text-center sm:text-left">
            <span className="text-xs text-slate-400 font-bold uppercase">Blue (B)</span>
            <div className="text-2xl font-mono font-bold text-slate-900">{rgb.b}</div>
          </div>
        </div>
      </div>
    </div>
  );
}
