'use client';

import React, { useState, useEffect, useCallback } from 'react';
import { generateHarmonyColors, HarmonyType, VariationType } from '@/lib/theory';
import { SwatchTheory } from '@/types';
import { useToast } from '@/components/ui/ToastProvider';
import {
  Wand2,
  Lock,
  Unlock,
  Copy,
  RefreshCw,
} from 'lucide-react';
import { PageHeader } from '@/components/ui/PageHeader';

export default function GeneratePaletteClient() {
  const { showToast } = useToast();

  const [seedHex, setSeedHex] = useState<string>('#EC4899');
  const [harmony, setHarmony] = useState<HarmonyType>('complementary');
  const [variation, setVariation] = useState<VariationType>('classic');

  const [swatches, setSwatches] = useState<SwatchTheory[]>([
    { hex: '#EC4899', locked: false },
    { hex: '#7C3AED', locked: false },
    { hex: '#3B82F6', locked: false },
    { hex: '#10B981', locked: false },
    { hex: '#F59E0B', locked: false },
  ]);

  const generateNewSwatches = useCallback(() => {
    const generated = generateHarmonyColors(seedHex, harmony, variation);
    setSwatches((prev) =>
      prev.map((swatch, index) => {
        if (swatch.locked) return swatch;
        return { hex: generated[index] || swatch.hex, locked: false };
      })
    );
  }, [seedHex, harmony, variation]);

  useEffect(() => {
    generateNewSwatches();
  }, [seedHex, harmony, variation, generateNewSwatches]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.code === 'Space' && e.target === document.body) {
        e.preventDefault();
        generateNewSwatches();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [generateNewSwatches]);

  const toggleLock = (index: number) => {
    setSwatches((prev) =>
      prev.map((item, i) => (i === index ? { ...item, locked: !item.locked } : item))
    );
  };

  const copyHex = (hex: string) => {
    navigator.clipboard.writeText(hex);
    showToast(`Copied ${hex} to clipboard!`);
  };

  return (
    <div>
      <PageHeader
        title="Color Theory Palette Generator"
        description={
          <>
            Algorithmic color harmony generator. Press <span className="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded font-mono text-xs font-bold">Spacebar</span> to generate unlocked colors!
          </>
        }
        icon={<Wand2 className="w-6 h-6" />}
        action={
          <button
            onClick={generateNewSwatches}
            className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold text-sm shadow-md hover:from-purple-600 hover:to-pink-600 transition-opacity flex items-center gap-2"
          >
            <RefreshCw className="w-4 h-4" />
            <span>Regenerate (Space)</span>
          </button>
        }
      />
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-10">

      {/* Controls Bar */}
      <div className="bg-white border border-slate-100 rounded-3xl p-6 mb-10 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-6">
        {/* Seed Picker */}
        <div>
          <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">
            Base Seed Color
          </label>
          <div className="flex items-center gap-3">
            <input
              type="color"
              value={seedHex}
              onChange={(e) => setSeedHex(e.target.value.toUpperCase())}
              className="w-12 h-10 rounded-xl bg-transparent border border-slate-200 cursor-pointer"
            />
            <input
              type="text"
              value={seedHex}
              onChange={(e) => setSeedHex(e.target.value.toUpperCase())}
              className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-4 py-2 font-mono text-sm text-slate-900 focus:outline-none"
            />
          </div>
        </div>

        {/* Harmony Dropdown */}
        <div>
          <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">
            Color Harmony Theory
          </label>
          <select
            value={harmony}
            onChange={(e) => setHarmony(e.target.value as HarmonyType)}
            className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-4 py-2.5 text-sm text-slate-800 font-semibold focus:outline-none cursor-pointer"
          >
            <option value="monochromatic">Monochromatic</option>
            <option value="complementary">Complementary / Contrast</option>
            <option value="triadic">Triadic Harmony</option>
            <option value="tetradic">Tetradic Harmony</option>
            <option value="analogous">Analogous Harmony</option>
          </select>
        </div>

        {/* Variation Buttons */}
        <div>
          <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">
            Style Variation
          </label>
          <div className="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 border border-slate-200/80 rounded-xl text-xs font-bold">
            <button
              onClick={() => setVariation('classic')}
              className={`py-1.5 rounded-lg transition-colors ${
                variation === 'classic' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Classic
            </button>
            <button
              onClick={() => setVariation('soft')}
              className={`py-1.5 rounded-lg transition-colors ${
                variation === 'soft' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Soft
            </button>
            <button
              onClick={() => setVariation('bold')}
              className={`py-1.5 rounded-lg transition-colors ${
                variation === 'bold' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              Bold
            </button>
          </div>
        </div>
      </div>

      {/* Interactive 5-Column Swatch Bands */}
      <div className="h-[450px] w-full rounded-3xl overflow-hidden shadow-md border border-slate-100 grid grid-cols-1 sm:grid-cols-5">
        {swatches.map((item, index) => (
          <div
            key={`${item.hex}-${index}`}
            className="h-full relative flex flex-col justify-between p-6 transition-all group"
            style={{ backgroundColor: item.hex }}
          >
            {/* Top Controls */}
            <div className="flex justify-between items-center opacity-90 group-hover:opacity-100 transition-opacity">
              <button
                onClick={() => toggleLock(index)}
                className={`p-2.5 rounded-xl backdrop-blur-md transition-all ${
                  item.locked
                    ? 'bg-pink-500 text-white shadow-md'
                    : 'bg-white/30 text-white hover:bg-white/50'
                }`}
                title={item.locked ? 'Unlock swatch' : 'Lock swatch during generation'}
              >
                {item.locked ? <Lock className="w-4 h-4" /> : <Unlock className="w-4 h-4" />}
              </button>

              <button
                onClick={() => copyHex(item.hex)}
                className="p-2.5 rounded-xl bg-white/30 text-white backdrop-blur-md hover:bg-white/50 transition-all"
                title="Copy Hex"
              >
                <Copy className="w-4 h-4" />
              </button>
            </div>

            {/* Bottom Hex Label */}
            <div className="flex flex-col items-center">
              <button
                onClick={() => copyHex(item.hex)}
                className="text-lg font-mono font-bold px-3 py-1.5 bg-white/85 text-slate-900 rounded-xl backdrop-blur-md hover:scale-105 transition-transform"
              >
                {item.hex}
              </button>
              <span className="text-[11px] font-semibold text-white/90 uppercase tracking-widest mt-1">
                {item.locked ? 'Locked' : `Swatch ${index + 1}`}
              </span>
            </div>
          </div>
        ))}
      </div>
    </div>
    </div>
  );
}
