'use client';

import React, { useState, useMemo } from 'react';
import { ColorName } from '@/types';
import {
  normalizeHex,
  hexToRgb,
  rgbToHsl,
  findClosestColorName,
} from '@/lib/color-math';
import { useToast } from '@/components/ui/ToastProvider';
import { Search, Pipette, Sliders, Hash, Compass, Copy } from 'lucide-react';

interface Props {
  colors: ColorName[];
}

export default function FindColorClient({ colors }: Props) {
  const { showToast } = useToast();
  const [hexInput, setHexInput] = useState('EC4899');
  const [activeQuery, setActiveQuery] = useState('EC4899');

  const normalized = useMemo(() => {
    return normalizeHex(activeQuery);
  }, [activeQuery]);

  const rgb = useMemo(() => hexToRgb(normalized), [normalized]);
  const hsl = useMemo(() => rgbToHsl(rgb.r, rgb.g, rgb.b), [rgb]);
  const closestName = useMemo(
    () => findClosestColorName(normalized, colors),
    [normalized]
  );

  const sampleColors = ['#FF5733', '#EC4899', '#3B82F6', '#10B981', '#191970', '#003800'];

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (hexInput.trim()) {
      setActiveQuery(hexInput.trim());
    }
  };

  const copyText = (val: string, label: string) => {
    navigator.clipboard.writeText(val);
    showToast(`Copied ${label}: ${val}`);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 sm:pt-10 sm:pb-20 space-y-14">
      {/* Hero Card Container */}
      <div className="bg-[#FFF5F7] border border-pink-100/80 rounded-3xl p-10 flex flex-col items-center justify-center shadow-sm">
        {/* Right Swatch Box */}
        <div className="flex flex-col items-center shrink-0">
          <div
            className="w-40 h-40 rounded-2xl border border-slate-200 shadow-md transition-all"
            style={{ backgroundColor: normalized }}
          />
          <span className="text-xs font-mono font-bold text-slate-700 mt-3">
            {normalized}
          </span>
        </div>
      </div>

      {/* Search Input Bar */}
      <form
        onSubmit={handleSubmit}
        className="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center gap-4"
      >
        <div className="relative flex-1 w-full flex items-center bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-3.5">
          <span className="font-mono font-bold text-slate-400 mr-2">#</span>
          <input
            type="text"
            value={hexInput}
            onChange={(e) => setHexInput(e.target.value)}
            placeholder="enter any hex code"
            className="w-full bg-transparent font-mono text-sm text-slate-900 placeholder-slate-400 focus:outline-none"
          />
        </div>
        <button
          type="submit"
          className="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 shrink-0"
        >
          <Search className="w-4 h-4" /> Find Info
        </button>
      </form>

      {/* TRY THESE COLORS Section */}
      <div className="space-y-4">
        <span className="text-xs font-bold text-slate-400 uppercase tracking-widest block">
          TRY THESE COLORS
        </span>
        <div className="flex flex-wrap gap-3">
          {sampleColors.map((color) => (
            <button
              key={color}
              onClick={() => {
                setHexInput(color.replace('#', ''));
                setActiveQuery(color.replace('#', ''));
              }}
              className="flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 hover:border-pink-300 text-xs font-mono font-bold text-slate-800 shadow-2xs transition-all"
            >
              <div
                className="w-3.5 h-3.5 rounded-full border border-slate-300"
                style={{ backgroundColor: color }}
              />
              <span>{color}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Matched Name Result Banner */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 sm:p-10 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
          <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">Matched Color Name</span>
          <h2 className="text-3xl sm:text-4xl font-black text-slate-900 mt-2">{closestName.name}</h2>
        </div>
        <button
          onClick={() => copyText(closestName.name, 'Color Name')}
          className="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition-colors shrink-0"
        >
          <Copy className="w-3.5 h-3.5" /> Copy Name
        </button>
      </div>

      {/* 3 Highlight Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {/* HEX Card */}
        <div
          onClick={() => copyText(normalized, 'HEX')}
          className="bg-white border border-slate-100 hover:border-pink-200 rounded-2xl p-7 shadow-sm flex items-start gap-4 cursor-pointer group transition-all"
        >
          <div className="w-11 h-11 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center shrink-0">
            <Hash className="w-5 h-5" />
          </div>
          <div>
            <h3 className="text-base font-bold text-slate-900 group-hover:text-pink-600 transition-colors">
              HEX Code
            </h3>
            <p className="text-xs text-slate-500 mt-1.5">
              6-digit hex representation
            </p>
            <div className="mt-4 text-lg font-mono font-bold text-slate-900">
              {normalized}
            </div>
          </div>
        </div>

        {/* RGB Card */}
        <div
          onClick={() => copyText(`rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`, 'RGB')}
          className="bg-white border border-slate-100 hover:border-purple-200 rounded-2xl p-7 shadow-sm flex items-start gap-4 cursor-pointer group transition-all"
        >
          <div className="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
            <Sliders className="w-5 h-5" />
          </div>
          <div>
            <h3 className="text-base font-bold text-slate-900 group-hover:text-purple-600 transition-colors">
              RGB Values
            </h3>
            <p className="text-xs text-slate-500 mt-1.5">
              Red, Green, Blue channels (0–255)
            </p>
            <div className="mt-4 text-lg font-mono font-bold text-slate-900">
              RGB({rgb.r}, {rgb.g}, {rgb.b})
            </div>
          </div>
        </div>

        {/* HSL Card */}
        <div
          onClick={() => copyText(`hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`, 'HSL')}
          className="bg-white border border-slate-100 hover:border-emerald-200 rounded-2xl p-7 shadow-sm flex items-start gap-4 cursor-pointer group transition-all"
        >
          <div className="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <Compass className="w-5 h-5" />
          </div>
          <div>
            <h3 className="text-base font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">
              HSL Values
            </h3>
            <p className="text-xs text-slate-500 mt-1.5">
              Hue, Saturation, Lightness
            </p>
            <div className="mt-4 text-lg font-mono font-bold text-slate-900">
              HSL({hsl.h}°, {hsl.s}%, {hsl.l}%)
            </div>
          </div>
        </div>
      </div>

      {/* FAQ Section */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 sm:p-10 space-y-8">
        <h2 className="text-xl font-extrabold text-slate-900">Frequently Asked Questions</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="bg-slate-50/70 border border-slate-100 rounded-2xl p-7 space-y-3">
            <h3 className="text-sm font-bold text-slate-900">What color is this hex code?</h3>
            <p className="text-xs text-slate-600 leading-relaxed">
              Enter any 6-digit hex code in the search box above and we&apos;ll instantly tell you its closest color name, plus RGB and HSL values. Works for all 16.7 million possible hex colors.
            </p>
          </div>

          <div className="bg-slate-50/70 border border-slate-100 rounded-2xl p-7 space-y-3">
            <h3 className="text-sm font-bold text-slate-900">How do I convert hex to RGB?</h3>
            <p className="text-xs text-slate-600 leading-relaxed">
              Hex codes are just RGB values written in hexadecimal. Each pair of characters represents Red, Green, or Blue (0-255). Our tool does this conversion automatically.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
