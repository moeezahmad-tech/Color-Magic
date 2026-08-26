'use client';

import React from 'react';
import { Gradient } from '@/types';
import { hexToRgb, rgbToHsl, getLuminance } from '@/lib/color-math';
import { useToast } from '@/components/ui/ToastProvider';
import { useFavoritesStore } from '@/store/useFavoritesStore';
import { Heart, Hash, RefreshCw, Copy, ExternalLink, ArrowLeft, Code } from 'lucide-react';
import Link from 'next/link';
import { GradientCard } from '@/components/ui/GradientCard';
import { PaletteCard } from '@/components/ui/PaletteCard';
import { ScrollableSection } from '@/components/ui/ScrollableSection';
import { Palette, ColorName } from '@/types';

interface Props {
  gradient: Gradient;
  relatedGradients: Gradient[];
  relatedPalettes: Palette[];
  relatedColors: ColorName[];
}

export default function GradientDetailClient({ gradient, relatedGradients, relatedPalettes, relatedColors }: Props) {
  const { showToast } = useToast();
  const { isGradientFavorited, toggleFavoriteGradient } = useFavoritesStore();

  if (!gradient) {
    return (
      <div className="max-w-4xl mx-auto py-24 text-center px-4">
        <h1 className="text-3xl font-bold text-slate-900 mb-4">Gradient Not Found</h1>
        <p className="text-slate-600 mb-6">The requested gradient could not be located.</p>
        <Link href="/gradients" className="px-6 py-3 rounded-full bg-purple-600 text-white font-bold text-sm inline-flex items-center gap-2">
          <ArrowLeft className="w-4 h-4" /> Back to Gradients
        </Link>
      </div>
    );
  }

  const isFav = isGradientFavorited(gradient.id);

  const angleNum =
    typeof gradient.angle === 'number'
      ? gradient.angle
      : parseInt(String(gradient.angle || '135').replace(/[^0-9]/g, ''), 10) || 135;

  const colorStops = gradient.colors.join(', ');
  const generatedCss =
    gradient.css ||
    (gradient.type === 'radial'
      ? `radial-gradient(circle, ${colorStops})`
      : `linear-gradient(${angleNum}deg, ${colorStops})`);

  const copyCss = () => {
    navigator.clipboard.writeText(`background: ${generatedCss};`);
    showToast('Copied CSS gradient!');
  };

  const copyText = (val: string, label: string) => {
    navigator.clipboard.writeText(val);
    showToast(`Copied ${label}: ${val}`);
  };

  const colorDetails = gradient.colors.map((hex) => {
    const rgb = hexToRgb(hex);
    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
    const lum = getLuminance(rgb.r, rgb.g, rgb.b);
    return { hex, rgb, hsl, brightness: Math.round(lum * 100) };
  });

  const [colorA, colorB] = gradient.colors;

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      {/* Breadcrumb */}
      <nav className="text-xs text-slate-400 mb-6 flex items-center gap-2">
        <Link href="/" className="hover:text-pink-500 transition-colors">Home</Link>
        <span>/</span>
        <Link href="/gradients" className="hover:text-pink-500 transition-colors">Gradients</Link>
        <span>/</span>
        <span className="text-slate-600 font-semibold">{gradient.name}</span>
      </nav>

      {/* Main Card */}
      <div className="bg-[#FFF5F7] border border-pink-100 rounded-3xl p-6 sm:p-10 mb-10 shadow-sm">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          {/* Live Gradient Preview */}
          <div
            className="lg:col-span-6 h-64 sm:h-80 w-full rounded-2xl overflow-hidden shadow-md border border-slate-100 cursor-pointer"
            style={{ background: generatedCss }}
            onClick={copyCss}
            title="Click to copy CSS"
          />

          {/* Info */}
          <div className="lg:col-span-6 space-y-6">
            <div>
              <h1 className="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{gradient.name}</h1>
              <p className="text-xs text-slate-500 mt-1 capitalize">{gradient.style} · {gradient.type} gradient</p>
            </div>

            <div className="grid grid-cols-3 gap-3 p-3 bg-white/80 rounded-xl border border-pink-100/60 text-center">
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">TYPE</span>
                <span className="text-sm font-bold text-slate-800 capitalize">{gradient.type}</span>
              </div>
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">COLORS</span>
                <span className="text-sm font-bold text-slate-800">{gradient.colors.length}</span>
              </div>
              <div>
                <span className="text-[10px] font-bold uppercase text-slate-400 block">ANGLE</span>
                <span className="text-sm font-bold text-slate-800">{gradient.type === 'radial' ? 'circle' : `${angleNum}°`}</span>
              </div>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <button onClick={copyCss} className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-purple-500 text-white font-bold text-xs shadow-md flex items-center gap-1.5">
                <Copy className="w-3.5 h-3.5" /> Copy CSS
              </button>
              <button
                onClick={() => toggleFavoriteGradient(gradient.id)}
                className={`px-4 py-2.5 rounded-xl border font-bold text-xs flex items-center gap-1.5 transition-colors ${isFav ? 'bg-pink-100 text-pink-600 border-pink-200' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'}`}
              >
                <Heart className={`w-3.5 h-3.5 ${isFav ? 'fill-pink-600' : ''}`} />
                Favorite
              </button>
            </div>

            <div className="grid grid-cols-2 gap-2 pt-1">
              {colorDetails.map((item, i) => (
                <div
                  key={`${item.hex}-${i}`}
                  onClick={() => copyText(item.hex, item.hex)}
                  className="bg-white border border-slate-100 rounded-xl p-2.5 flex items-center justify-between cursor-pointer group hover:border-pink-300 transition-all"
                >
                  <div className="flex items-center gap-2.5">
                    <div className="w-5 h-5 rounded-md border border-slate-200" style={{ backgroundColor: item.hex }} />
                    <span className="text-xs font-mono font-bold text-slate-800">{item.hex}</span>
                  </div>
                  <span className="text-[10px] font-mono text-slate-400">{item.rgb.r},{item.rgb.g},{item.rgb.b}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* CSS Code Block */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 mb-8 space-y-4 shadow-sm">
        <h2 className="text-xl font-extrabold text-slate-900">CSS Code</h2>
        <div className="bg-[#F0F9FF] border border-sky-100 rounded-2xl p-4 sm:p-5 flex items-center justify-between gap-4">
          <code className="text-xs sm:text-sm font-mono text-slate-800 break-all">background: {generatedCss};</code>
          <button onClick={copyCss} className="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors flex items-center gap-1.5 shrink-0">
            <Copy className="w-3.5 h-3.5" /> Copy
          </button>
        </div>
      </div>



      {/* Color Information */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-5 mb-12">
        <div>
          <h2 className="text-xl font-extrabold text-slate-900">Color Information</h2>
          <p className="text-xs text-slate-500 mt-0.5">RGB, HSL, and brightness values for each stop</p>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {colorDetails.map((item, i) => (
            <div key={`${item.hex}-info-${i}`} className="border border-slate-100 rounded-2xl overflow-hidden">
              <div className="h-28 w-full" style={{ backgroundColor: item.hex }} />
              <div className="p-4 bg-white space-y-2">
                <div className="text-sm font-mono font-bold text-slate-900">{item.hex}</div>
                <div className="space-y-1 text-xs text-slate-500 font-mono">
                  <div className="flex justify-between">
                    <span>RGB</span>
                    <span className="font-bold text-slate-800">{item.rgb.r}, {item.rgb.g}, {item.rgb.b}</span>
                  </div>
                  <div className="flex justify-between">
                    <span>HSL</span>
                    <span className="font-bold text-slate-800">{item.hsl.h}°, {item.hsl.s}%, {item.hsl.l}%</span>
                  </div>
                  <div className="flex justify-between">
                    <span>BRIGHTNESS</span>
                    <span className="font-bold text-slate-800">{item.brightness}%</span>
                  </div>
                </div>
                <div className="flex gap-2 pt-2 border-t border-slate-100">
                  <button onClick={() => copyText(item.hex, 'HEX')} className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 flex items-center justify-center gap-1">
                    <Copy className="w-3 h-3" /> HEX
                  </button>
                  <button onClick={() => copyText(`rgb(${item.rgb.r}, ${item.rgb.g}, ${item.rgb.b})`, 'RGB')} className="flex-1 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-bold hover:bg-slate-100 flex items-center justify-center gap-1">
                    <Copy className="w-3 h-3" /> RGB
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Related Gradients */}
      {relatedGradients.length > 0 && (
        <div className="space-y-6 mb-12">
          <div className="flex items-center justify-between">
            <h2 className="text-2xl font-extrabold text-slate-900">Related Gradients</h2>
            <Link href="/gradients" className="text-xs font-bold text-pink-600 hover:text-pink-700 transition-colors">
              Explore All Gradients →
            </Link>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {relatedGradients.map((rg) => (
              <GradientCard key={rg.id} gradient={rg} />
            ))}
          </div>
        </div>
      )}

      {/* Related Color Palettes */}
      {relatedPalettes && relatedPalettes.length > 0 && (
        <div className="space-y-6 mb-12">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-2xl font-extrabold text-slate-900">Related Color Palettes</h2>
              <p className="text-xs text-slate-500 mt-0.5">Matching {gradient.style.toLowerCase()} schemes for UI & design</p>
            </div>
            <Link href="/palettes" className="text-xs font-bold text-pink-600 hover:text-pink-700 transition-colors">
              View All Palettes →
            </Link>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {relatedPalettes.slice(0, 6).map((rp) => (
              <PaletteCard key={rp.id} palette={rp} />
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
