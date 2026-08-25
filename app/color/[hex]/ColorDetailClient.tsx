'use client';

import React from 'react';
import {
  normalizeHex,
  hexToRgb,
  rgbToHsl,
  rgbToHsv,
  rgbToCmyk,
  rgbToLab,
  findClosestColorName,
  generateTintsAndShades,
} from '@/lib/color-math';
import { generateHarmonyColors } from '@/lib/theory';
import { useToast } from '@/components/ui/ToastProvider';
import { Copy, ArrowLeft, Hash, Paintbrush, Zap, Layers, RefreshCw } from 'lucide-react';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { GradientCard } from '@/components/ui/GradientCard';
import { ScrollableSection } from '@/components/ui/ScrollableSection';
import { ColorName, Palette, Gradient } from '@/types';
import Link from 'next/link';

interface Props {
  hex: string;
  colors: ColorName[];
  relatedColors: ColorName[];
  relatedPalettes: Palette[];
  relatedGradients: Gradient[];
}

export default function ColorDetailClient({ hex, colors, relatedColors, relatedPalettes, relatedGradients }: Props) {
  const { showToast } = useToast();

  const normalized = normalizeHex(hex);
  const rgb = hexToRgb(normalized);
  const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
  const hsv = rgbToHsv(rgb.r, rgb.g, rgb.b);
  const cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);
  const lab = rgbToLab(rgb.r, rgb.g, rgb.b);
  const closestName = findClosestColorName(normalized, colors);

  const complementary = generateHarmonyColors(normalized, 'complementary');
  const triadic = generateHarmonyColors(normalized, 'triadic');
  const analogous = generateHarmonyColors(normalized, 'analogous');
  const { tints, shades } = generateTintsAndShades(normalized, 6);

  const copyText = (val: string, label: string) => {
    navigator.clipboard.writeText(val);
    showToast(`Copied ${label}: ${val}`);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      {/* Breadcrumb */}
      <nav className="text-xs text-slate-400 mb-6 flex items-center gap-2">
        <Link href="/" className="hover:text-pink-500 transition-colors">Home</Link>
        <span>/</span>
        <Link href="/find-color" className="hover:text-pink-500 transition-colors">Find Color</Link>
        <span>/</span>
        <span className="text-slate-600 font-mono font-bold">{normalized}</span>
      </nav>

      {/* Hero Swatch & Header */}
      <div className="bg-[#FFF5F7] border border-pink-100 rounded-3xl p-6 sm:p-10 mb-10 shadow-sm">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
          <div
            className="h-56 w-full rounded-2xl shadow-xl border border-slate-200 flex items-end p-4 cursor-pointer"
            style={{ backgroundColor: normalized }}
            onClick={() => copyText(normalized, 'HEX')}
          >
            <span className="text-sm font-mono font-bold px-3 py-1.5 bg-white/90 text-slate-900 rounded-xl backdrop-blur-md shadow-sm">
              {normalized}
            </span>
          </div>

          <div className="md:col-span-2 space-y-4">
            <span className="text-xs font-bold uppercase tracking-wider text-pink-500 block">
              Color Analysis Report
            </span>
            <h1 className="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight">
              {closestName.name}
            </h1>
            <p className="text-slate-500 text-sm leading-relaxed">
              Hex: <span className="font-mono font-bold text-slate-800">{normalized}</span>
              {' '}• RGB: <span className="font-mono text-slate-800">{rgb.r}, {rgb.g}, {rgb.b}</span>
              {' '}• HSL: <span className="font-mono text-slate-800">{hsl.h}°, {hsl.s}%, {hsl.l}%</span>
            </p>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
              {[
                { label: 'HEX', value: normalized, copy: normalized },
                { label: 'RGB', value: `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`, copy: `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})` },
                { label: 'HSL', value: `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`, copy: `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)` },
                { label: 'HSV', value: `hsv(${hsv.h}, ${hsv.s}%, ${hsv.v}%)`, copy: `hsv(${hsv.h}, ${hsv.s}%, ${hsv.v}%)` },
              ].map(({ label, value, copy }) => (
                <div
                  key={label}
                  onClick={() => copyText(copy, label)}
                  className="bg-white border border-slate-100 rounded-xl p-3 cursor-pointer hover:border-pink-300 transition-all group"
                >
                  <span className="text-[10px] font-bold uppercase text-slate-400 block">{label}</span>
                  <span className="text-xs font-mono font-bold text-slate-900 group-hover:text-pink-600 transition-colors break-all">
                    {value}
                  </span>
                </div>
              ))}
            </div>

            <div className="flex flex-wrap gap-2 pt-1">
              <button
                onClick={() => copyText(normalized, 'HEX')}
                className="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 text-white text-xs font-bold shadow-md flex items-center gap-1.5"
              >
                <Copy className="w-3.5 h-3.5" /> Copy HEX
              </button>
              <button
                onClick={() => copyText(`rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`, 'RGB')}
                className="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:border-pink-300 transition-colors flex items-center gap-1.5"
              >
                <Copy className="w-3.5 h-3.5" /> Copy RGB
              </button>
              <button
                onClick={() => copyText(`hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`, 'HSL')}
                className="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:border-pink-300 transition-colors flex items-center gap-1.5"
              >
                <Copy className="w-3.5 h-3.5" /> Copy HSL
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Tints & Shades */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 mb-8 shadow-sm space-y-5">
        <h2 className="text-xl font-extrabold text-slate-900">Shades and Tints of {normalized}</h2>
        <p className="text-sm text-slate-500">
          A shade adds black to the hue; a tint adds white. Click any swatch to copy its hex.
        </p>
        <div>
          <p className="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Shades</p>
          <div className="flex gap-2 h-14">
            {shades.map((s, i) => (
              <div
                key={`shade-${i}`}
                onClick={() => copyText(s, 'Shade')}
                className="flex-1 rounded-xl cursor-pointer hover:scale-105 transition-transform border border-slate-200 relative group"
                style={{ backgroundColor: s }}
                title={s}
              >
                <span className="absolute bottom-1 left-1/2 -translate-x-1/2 text-[9px] font-mono bg-white/80 px-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                  {s}
                </span>
              </div>
            ))}
          </div>
        </div>
        <div>
          <p className="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Tints</p>
          <div className="flex gap-2 h-14">
            {tints.map((t, i) => (
              <div
                key={`tint-${i}`}
                onClick={() => copyText(t, 'Tint')}
                className="flex-1 rounded-xl cursor-pointer hover:scale-105 transition-transform border border-slate-200 relative group"
                style={{ backgroundColor: t }}
                title={t}
              >
                <span className="absolute bottom-1 left-1/2 -translate-x-1/2 text-[9px] font-mono bg-white/80 px-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                  {t}
                </span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Color Harmonies */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 mb-8 shadow-sm space-y-6">
        <h2 className="text-xl font-extrabold text-slate-900">Color Harmonies</h2>
        {[
          { label: 'Complementary', colors: complementary },
          { label: 'Triadic', colors: triadic },
          { label: 'Analogous', colors: analogous },
        ].map(({ label, colors }) => (
          <div key={label}>
            <p className="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{label}</p>
            <div className="flex gap-2 h-16">
              {colors.map((c, i) => (
                <div
                  key={`${label}-${i}`}
                  onClick={() => copyText(c, label)}
                  className="flex-1 rounded-xl cursor-pointer hover:scale-105 transition-transform border border-slate-200 flex items-end justify-center pb-2 group relative"
                  style={{ backgroundColor: c }}
                >
                  <span className="text-[9px] font-mono font-bold bg-white/85 text-slate-900 px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    {c}
                  </span>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* Full Conversion Table */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
        <h2 className="text-xl font-extrabold text-slate-900 mb-5">All Color Format Values</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
          {[
            { label: 'HEX', value: normalized },
            { label: 'RGB', value: `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})` },
            { label: 'HSL', value: `hsl(${hsl.h}deg, ${hsl.s}%, ${hsl.l}%)` },
            { label: 'HSV', value: `hsv(${hsv.h}, ${hsv.s}%, ${hsv.v}%)` },
            { label: 'CMYK', value: `cmyk(${cmyk.c}%, ${cmyk.m}%, ${cmyk.y}%, ${cmyk.k}%)` },
            { label: 'LAB', value: `lab(${lab.l.toFixed(1)}, ${lab.a.toFixed(1)}, ${lab.b.toFixed(1)})` },
          ].map(({ label, value }) => (
            <div
              key={label}
              onClick={() => copyText(value, label)}
              className="flex items-center justify-between bg-slate-50 border border-slate-100 hover:border-pink-300 rounded-xl px-4 py-3 cursor-pointer group transition-all"
            >
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">{label}</span>
                <span className="font-mono text-slate-800 text-sm font-bold">{value}</span>
              </div>
              <Copy className="w-3.5 h-3.5 text-slate-300 group-hover:text-pink-500 transition-colors" />
            </div>
          ))}
        </div>
      </div>

      <div className="space-y-16 mt-10">
        {/* Related Colors */}
        {relatedColors.length > 0 && (
          <ScrollableSection title="Related Colors">
            {relatedColors.map((c, i) => (
              <Link
                href={`/color/${c.hex.replace('#', '')}`}
                key={`${c.hex}-${i}`}
                className="w-32 md:w-40 shrink-0 snap-start group flex flex-col gap-2 p-2 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-all cursor-pointer"
              >
                <div 
                  className="w-full h-24 rounded-lg border border-slate-200 shadow-sm group-hover:scale-105 transition-transform"
                  style={{ backgroundColor: c.hex }} 
                />
                <div>
                  <span className="block text-sm font-bold text-slate-800 truncate">{c.name}</span>
                  <span className="block text-xs font-mono text-slate-500">{c.hex}</span>
                </div>
              </Link>
            ))}
          </ScrollableSection>
        )}

        {/* Related Palettes */}
        {relatedPalettes.length > 0 && (
          <ScrollableSection title="Related Palettes" viewMoreHref="/palettes" viewMoreText="View All Palettes">
            {relatedPalettes.map((rp) => (
              <div key={rp.id} className="w-72 md:w-80 shrink-0 snap-start">
                <PaletteCard palette={rp} />
              </div>
            ))}
          </ScrollableSection>
        )}

        {/* Related Gradients */}
        {relatedGradients.length > 0 && (
          <ScrollableSection title="Related Gradients" viewMoreHref="/gradients" viewMoreText="View All Gradients">
            {relatedGradients.map((rg) => (
              <div key={rg.id} className="w-72 md:w-80 shrink-0 snap-start">
                <GradientCard gradient={rg} />
              </div>
            ))}
          </ScrollableSection>
        )}
      </div>
    </div>
  );
}
