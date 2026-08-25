'use client';

import React, { useState } from 'react';
import { Palette, ColorName, Gradient } from '@/types';
import { hexToRgb, getLuminance, findClosestColorName } from '@/lib/color-math';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { useToast } from '@/components/ui/ToastProvider';
import { Heart, Hash, Layers, ChevronRight, Check, Copy, Code, ArrowLeft } from 'lucide-react';
import Link from 'next/link';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { GradientCard } from '@/components/ui/GradientCard';
import { ScrollableSection } from '@/components/ui/ScrollableSection';


interface Props {
  palette: Palette;
  colors: ColorName[];
  relatedPalettes: Palette[];
  relatedGradients: Gradient[];
  relatedColors: ColorName[];
}

export default function PaletteDetailClient({ palette, colors, relatedPalettes, relatedGradients, relatedColors }: Props) {
  const { showToast } = useToast();
  const { isPaletteFavorited, toggleFavoritePalette } = useFavoritesStore();

  const [activeExport, setActiveExport] = useState<string | null>(null);

  if (!palette) {
    return (
      <div className="max-w-4xl mx-auto py-24 text-center px-4">
        <h1 className="text-3xl font-bold text-slate-900 mb-4">Palette Not Found</h1>
        <p className="text-slate-600 mb-6">The requested palette could not be matched.</p>
        <Link href="/palettes" className="px-6 py-3 rounded-full bg-purple-600 text-white font-bold text-sm inline-flex items-center gap-2">
          <ArrowLeft className="w-4 h-4" /> Back to Explore
        </Link>
      </div>
    );
  }

  const isFav = isPaletteFavorited(palette.id || palette.slug);

  const colorDetails = palette.colors.map((hex) => {
    const rgb = hexToRgb(hex);
    const lum = getLuminance(rgb.r, rgb.g, rgb.b);
    const closest = findClosestColorName(hex, colors);
    return { hex, rgb, lum, name: closest.name };
  });

  const copyText = (text: string, label: string) => {
    navigator.clipboard.writeText(text);
    showToast(`Copied ${label} to clipboard!`);
  };

  const copyAllColors = () => {
    navigator.clipboard.writeText(palette.colors.join(', '));
    showToast(`Copied all ${palette.colors.length} hex codes!`);
  };

  const copyCssVars = () => {
    const vars = palette.colors.map((c, i) => `  --color-${i + 1}: ${c};`).join('\n');
    navigator.clipboard.writeText(`:root {\n${vars}\n}`);
    showToast('Copied CSS Variables!');
  };

  const colorA = palette.colors[0];
  const colorB = palette.colors[palette.colors.length - 1];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      {/* Breadcrumb */}
      <nav className="text-xs text-slate-400 mb-6 flex items-center gap-2">
        <Link href="/" className="hover:text-pink-500 transition-colors">Home</Link>
        <span>/</span>
        <Link href="/palettes" className="hover:text-pink-500 transition-colors">Palettes</Link>
        <span>/</span>
        <span className="text-slate-600 font-semibold">{palette.name}</span>
      </nav>

      {/* Main Card */}
      <div className="bg-[#FFF5F7] border border-pink-100 rounded-3xl p-6 sm:p-10 mb-10 shadow-sm">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Swatch Visual */}
          <div className="lg:col-span-6 h-64 sm:h-80 w-full rounded-2xl overflow-hidden flex shadow-md border border-slate-100">
            {palette.colors.map((color, i) => (
              <div
                key={`${color}-${i}`}
                className="flex-1 h-full flex flex-col justify-end p-3 cursor-pointer group relative"
                style={{ backgroundColor: color }}
                onClick={() => copyText(color, color)}
              >
                <span className="text-[10px] font-mono font-bold bg-white/85 text-slate-900 px-1.5 py-0.5 rounded backdrop-blur-sm self-center opacity-0 group-hover:opacity-100 transition-opacity">
                  {color}
                </span>
              </div>
            ))}
          </div>

          {/* Info */}
          <div className="lg:col-span-6 space-y-6">
            <div>
              <h1 className="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{palette.name}</h1>
              <p className="text-xs text-slate-400 mt-1 capitalize">{palette.style} • {palette.colors.length} colors</p>
            </div>

            <div className="grid grid-cols-3 gap-3 p-3 bg-white/70 rounded-xl border border-pink-100/60 text-center">
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">TYPE</span>
                <span className="text-sm font-bold text-slate-800">{palette.style}</span>
              </div>
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">COLORS</span>
                <span className="text-sm font-bold text-slate-800">{palette.colors.length}</span>
              </div>
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">FORMAT</span>
                <span className="text-sm font-bold text-slate-800">HEX</span>
              </div>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button onClick={copyAllColors} className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-purple-500 text-white font-bold text-xs shadow-md flex items-center gap-1.5">
                <Copy className="w-3.5 h-3.5" /> Copy All Colors
              </button>
              <button onClick={copyCssVars} className="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                <Code className="w-3.5 h-3.5" /> Copy CSS Variables
              </button>
              <button
                onClick={() => toggleFavoritePalette(palette.id || palette.slug)}
                className={`px-4 py-2.5 rounded-xl border font-bold text-xs flex items-center gap-1.5 transition-colors ${isFav ? 'bg-pink-100 text-pink-600 border-pink-200' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'}`}
              >
                <Heart className={`w-3.5 h-3.5 ${isFav ? 'fill-pink-600' : ''}`} />
                Favorite
              </button>
            </div>

            {/* Individual swatch list */}
            <div className="grid grid-cols-2 gap-2 pt-1">
              {colorDetails.map((item) => (
                <div
                  key={item.hex}
                  onClick={() => copyText(item.hex, item.hex)}
                  className="bg-white border border-slate-100 rounded-xl p-2.5 flex items-center justify-between cursor-pointer group hover:border-pink-300 transition-all"
                >
                  <div className="flex items-center gap-2.5">
                    <div className="w-6 h-6 rounded-md border border-slate-200" style={{ backgroundColor: item.hex }} />
                    <div>
                      <span className="text-xs font-mono font-bold text-slate-800 block">{item.hex}</span>
                      <span className="text-[10px] text-slate-400">{item.name}</span>
                    </div>
                  </div>
                  <Copy className="w-3 h-3 text-slate-300 group-hover:text-pink-500 transition-colors" />
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Contrast Previews */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div className="rounded-3xl p-8 flex flex-col justify-between h-44 shadow-sm" style={{ backgroundColor: colorA, color: colorB }}>
          <span className="text-[10px] font-bold uppercase tracking-widest opacity-70">CONTRAST PREVIEW A</span>
          <div className="text-4xl font-extrabold">Aa</div>
          <span className="text-xs font-mono opacity-80">bg: {colorA} • text: {colorB}</span>
        </div>
        <div className="rounded-3xl p-8 flex flex-col justify-between h-44 shadow-sm border border-slate-100" style={{ backgroundColor: colorB, color: colorA }}>
          <span className="text-[10px] font-bold uppercase tracking-widest opacity-70">CONTRAST PREVIEW B</span>
          <div className="text-4xl font-extrabold">Aa</div>
          <span className="text-xs font-mono opacity-80">bg: {colorB} • text: {colorA}</span>
        </div>
      </div>

      {/* Palette Story */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 space-y-3 mb-10">
        <h2 className="text-xl font-extrabold text-slate-900">About the {palette.name} Palette</h2>
        <p className="text-sm text-slate-600 leading-relaxed">
          The <strong>{palette.name}</strong> palette is a {palette.style.toLowerCase()} color scheme featuring {palette.colors.length} carefully curated colors from {colorA} to {colorB}. These colors work harmoniously together for UI design, branding, illustration, and web development projects.
        </p>
        <p className="text-sm text-slate-600 leading-relaxed">
          Copy the hex codes above or use the CSS variables export for seamless integration into any design system.
        </p>
      </div>

      <div className="space-y-16 mt-10">
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
      </div>
    </div>
  );
}
