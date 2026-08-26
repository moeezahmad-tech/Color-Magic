'use client';

import React, { useState } from 'react';
import { Palette, ColorName, Gradient } from '@/types';
import { hexToRgb, hexToHsl, getLuminance, getContrastRatio, findClosestColorName } from '@/lib/color-math';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { useToast } from '@/components/ui/ToastProvider';
import { Heart, Hash, Layers, ChevronRight, Check, Copy, Code, ArrowLeft, ExternalLink } from 'lucide-react';
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

function getWcagBadge(ratio: number) {
  if (ratio >= 7.0) return { label: 'AAA Pass', bg: 'bg-emerald-500/20 text-emerald-950 border-emerald-500/30' };
  if (ratio >= 4.5) return { label: 'AA Pass', bg: 'bg-emerald-500/20 text-emerald-950 border-emerald-500/30' };
  if (ratio >= 3.0) return { label: 'AA Large', bg: 'bg-amber-500/20 text-amber-950 border-amber-500/30' };
  return { label: 'Low Contrast', bg: 'bg-red-500/20 text-red-950 border-red-500/30' };
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

  // Calculate top 6 colorful, accessible contrast combinations from palette
  const contrastPairs = React.useMemo(() => {
    const colors = palette.colors;
    const pairs: { bg: string; text: string; ratio: number; score: number; isColorful: boolean }[] = [];
    const seenPairs = new Set<string>();

    for (let i = 0; i < colors.length; i++) {
      for (let j = 0; j < colors.length; j++) {
        if (i === j) continue;
        const c1 = colors[i];
        const c2 = colors[j];
        const pairKey = `${c1}->${c2}`;
        if (seenPairs.has(pairKey)) continue;
        seenPairs.add(pairKey);

        const rgb1 = hexToRgb(c1);
        const rgb2 = hexToRgb(c2);
        const lum1 = getLuminance(rgb1.r, rgb1.g, rgb1.b);
        const lum2 = getLuminance(rgb2.r, rgb2.g, rgb2.b);
        const ratio = getContrastRatio(lum1, lum2);

        // Filter for usable contrast ratios
        if (ratio < 3.0) continue;

        const hsl1 = hexToHsl(c1);
        const hsl2 = hexToHsl(c2);
        const isColorful = hsl1.s > 20 || hsl2.s > 20;
        const totalSaturation = hsl1.s + hsl2.s;

        // Score formula: heavily rewards pairs that have rich color vibrancy while maintaining high contrast
        const vibrancyBonus = isColorful ? totalSaturation * 0.15 : -8;
        const score = ratio * 1.5 + vibrancyBonus;

        pairs.push({ bg: c1, text: c2, ratio, score, isColorful });
      }
    }

    // Sort by calculated score (vibrant high contrast first)
    pairs.sort((a, b) => b.score - a.score);

    // Pick top 6 distinct combinations (or top 4/2 if fewer colors)
    let selected = pairs.slice(0, 6);

    if (selected.length === 0) {
      const c1 = colors[0];
      const c2 = colors[colors.length - 1] || colors[0];
      const lum1 = getLuminance(hexToRgb(c1).r, hexToRgb(c1).g, hexToRgb(c1).b);
      const lum2 = getLuminance(hexToRgb(c2).r, hexToRgb(c2).g, hexToRgb(c2).b);
      const ratio = getContrastRatio(lum1, lum2);
      selected = [
        { bg: c1, text: c2, ratio, score: 0, isColorful: true },
        { bg: c2, text: c1, ratio, score: 0, isColorful: true },
      ];
    }

    const roleLabels = [
      'Vibrant Surface',
      'Dark Mode UI',
      'Accent Banner',
      'Interactive Hero',
      'Secondary Card',
      'Badge & Highlight',
    ];

    return selected.map((item, index) => ({
      id: `pair-${item.bg}-${item.text}-${index}`,
      bg: item.bg,
      text: item.text,
      ratio: item.ratio,
      label: roleLabels[index % roleLabels.length],
      wcag: getWcagBadge(item.ratio),
    }));
  }, [palette.colors]);

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
          <div className="lg:col-span-6 h-64 sm:h-80 w-full rounded-2xl overflow-hidden flex shadow-md border border-slate-200/80 bg-slate-100">
            {palette.colors.map((color, i) => (
              <div
                key={`${color}-${i}`}
                className="swatch-band min-w-0 h-full relative cursor-pointer group border-r border-black/5 last:border-r-0"
                style={{ backgroundColor: color }}
                onClick={() => copyText(color, color)}
                title={`Click to copy ${color}`}
              >
                <span className="text-[10px] font-mono font-bold bg-white/90 text-slate-900 px-1.5 py-0.5 rounded shadow-sm backdrop-blur-sm pointer-events-none absolute bottom-3 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
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
            <div className="grid grid-cols-2 gap-2 pt-1 max-h-80 overflow-y-auto pr-1">
              {colorDetails.map((item) => {
                const cleanHex = item.hex.replace('#', '').toLowerCase();
                return (
                  <div
                    key={item.hex}
                    className="bg-white border border-slate-100 rounded-xl p-2.5 flex items-center justify-between group hover:border-pink-300 transition-all shadow-2xs"
                  >
                    <Link
                      href={`/color/${cleanHex}`}
                      className="flex items-center gap-2.5 min-w-0 hover:opacity-85 transition-opacity"
                      title={`View details for ${item.name} (${item.hex})`}
                    >
                      <div 
                        className="w-6 h-6 rounded-md border border-slate-300 shadow-2xs shrink-0" 
                        style={{ backgroundColor: item.hex }} 
                      />
                      <div className="min-w-0">
                        <span className="text-xs font-mono font-bold text-slate-800 hover:text-purple-600 block truncate transition-colors">{item.hex}</span>
                        <span className="text-[10px] text-slate-400 block truncate">{item.name}</span>
                      </div>
                    </Link>
                    <div className="flex items-center gap-1 shrink-0">
                      <button
                        onClick={() => copyText(item.hex, item.hex)}
                        className="p-1 rounded text-slate-300 hover:text-pink-500 hover:bg-pink-50 transition-colors"
                        title={`Copy ${item.hex}`}
                      >
                        <Copy className="w-3 h-3" />
                      </button>
                      <Link
                        href={`/color/${cleanHex}`}
                        className="p-1 rounded text-slate-300 hover:text-purple-600 hover:bg-purple-50 transition-colors"
                        title={`View ${item.hex} details`}
                      >
                        <ExternalLink className="w-3 h-3" />
                      </Link>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>

      {/* Recommended Colorful Contrast Previews */}
      <div className="space-y-4 mb-12">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div>
            <h2 className="text-lg font-black text-slate-900 tracking-tight">
              Recommended Contrast Pairings
            </h2>
            <p className="text-xs text-slate-500 mt-0.5">
              High-contrast, colorful combinations from this palette evaluated for WCAG legibility.
            </p>
          </div>
          <span className="self-start sm:self-auto text-xs font-bold px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
            {contrastPairs.length} Accessible Pairs
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {contrastPairs.map((pair, index) => (
            <div
              key={pair.id}
              className="rounded-3xl p-6 flex flex-col justify-between min-h-[175px] shadow-sm border border-black/5 hover:shadow-md transition-all group relative overflow-hidden"
              style={{ backgroundColor: pair.bg, color: pair.text }}
            >
              <div className="flex items-center justify-between gap-2">
                <span className="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-black/10 backdrop-blur-xs">
                  {pair.label}
                </span>
                <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-white/20 backdrop-blur-xs border border-white/20">
                  {pair.ratio}:1 • {pair.wcag.label}
                </span>
              </div>

              <div className="my-3">
                <div className="text-3xl font-black tracking-tight">Aa</div>
                <p className="text-xs font-medium mt-1 opacity-90 leading-relaxed line-clamp-1">
                  Sample interface typography & headline contrast.
                </p>
              </div>

              <div className="flex items-center justify-between text-[11px] font-mono opacity-90 pt-2.5 border-t border-current/15">
                <span className="truncate">bg: <strong className="font-bold">{pair.bg}</strong></span>
                <span className="truncate">text: <strong className="font-bold">{pair.text}</strong></span>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Palette Story */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 space-y-3 mb-10">
        <h2 className="text-xl font-extrabold text-slate-900">About the {palette.name} Palette</h2>
        <p className="text-sm text-slate-600 leading-relaxed">
          The <strong>{palette.name}</strong> palette is a {palette.style.toLowerCase()} color scheme featuring {palette.colors.length} carefully curated colors from {palette.colors[0]} to {palette.colors[palette.colors.length - 1]}. These colors work harmoniously together for UI design, branding, illustration, and web development projects.
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
