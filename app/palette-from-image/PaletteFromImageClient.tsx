'use client';

import React, { useState, useRef, useEffect, useCallback } from 'react';
import { extractColorsFromCanvas } from '@/lib/kmeans';
import { useToast } from '@/components/ui/ToastProvider';
import {
  Image as ImageIcon,
  UploadCloud,
  Copy,
  Sliders,
  ShieldCheck,
} from 'lucide-react';

export default function PaletteFromImageClient() {
  const { showToast } = useToast();
  const [imageSrc, setImageSrc] = useState<string | null>(null);
  const [colorCount, setColorCount] = useState<number>(6);
  const [extractedColors, setExtractedColors] = useState<string[]>([]);
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  const sampleImages = [
    {
      name: 'Sunset Coast',
      url: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80',
    },
    {
      name: 'Neon Architecture',
      url: 'https://images.unsplash.com/photo-1514565131-fce0801e5785?auto=format&fit=crop&w=600&q=80',
    },
    {
      name: 'Earthy Forest',
      url: 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=600&q=80',
    },
    {
      name: 'Vibrant Art',
      url: 'https://images.unsplash.com/photo-1541701494587-cb58502866ab?auto=format&fit=crop&w=600&q=80',
    },
  ];

  const processImage = useCallback(
    (src: string) => {
      const img = new Image();
      img.crossOrigin = 'anonymous';
      img.src = src;
      img.onload = () => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const maxDim = 400;
        let width = img.width;
        let height = img.height;

        if (width > height) {
          if (width > maxDim) {
            height = Math.round((height * maxDim) / width);
            width = maxDim;
          }
        } else {
          if (height > maxDim) {
            width = Math.round((width * maxDim) / height);
            height = maxDim;
          }
        }

        canvas.width = width;
        canvas.height = height;
        ctx.drawImage(img, 0, 0, width, height);

        const colors = extractColorsFromCanvas(ctx, width, height, colorCount);
        setExtractedColors(colors);
      };
    },
    [colorCount]
  );

  useEffect(() => {
    if (!imageSrc) {
      setImageSrc(sampleImages[0].url);
      processImage(sampleImages[0].url);
    } else {
      processImage(imageSrc);
    }
  }, [colorCount, imageSrc, processImage]);

  const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => {
        const result = event.target?.result as string;
        setImageSrc(result);
        processImage(result);
      };
      reader.readAsDataURL(file);
    }
  };

  const copyHex = (hex: string) => {
    navigator.clipboard.writeText(hex);
    showToast(`Copied ${hex} to clipboard!`);
  };

  const copyAll = () => {
    const jsonStr = JSON.stringify(extractedColors);
    navigator.clipboard.writeText(jsonStr);
    showToast(`Copied palette array!`);
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12">
      <canvas ref={canvasRef} className="hidden" />



      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        {/* Left Column: Image Dropzone & Preview */}
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div className="relative w-full h-80 sm:h-96 rounded-2xl overflow-hidden bg-slate-50 border border-slate-200 flex items-center justify-center mb-6">
              {imageSrc ? (
                // eslint-disable-next-line @next/next/no-img-element
                <img
                  src={imageSrc}
                  alt="Extracted source"
                  className="w-full h-full object-cover"
                />
              ) : (
                <div className="text-slate-400 text-center">No image selected</div>
              )}
            </div>

            <div className="space-y-4">
              <label className="flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-slate-50 border-2 border-dashed border-purple-200 hover:border-purple-500 text-slate-700 hover:text-purple-600 cursor-pointer transition-all">
                <UploadCloud className="w-6 h-6 text-purple-500" />
                <span className="font-bold text-sm">Upload Custom Image (PNG, JPG, WebP)</span>
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleFileUpload}
                  className="hidden"
                />
              </label>

              <div>
                <span className="text-xs font-bold uppercase text-slate-400 block mb-3">
                  Or select a sample photo:
                </span>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  {sampleImages.map((sample) => (
                    <button
                      key={sample.name}
                      onClick={() => {
                        setImageSrc(sample.url);
                        processImage(sample.url);
                      }}
                      className="group relative h-20 rounded-xl overflow-hidden border border-slate-200 hover:border-purple-500 transition-all text-left"
                    >
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={sample.url}
                        alt={sample.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform"
                      />
                      <div className="absolute inset-0 bg-white/20 group-hover:bg-white/10 flex items-end p-2 transition-colors">
                        <span className="text-[11px] font-bold text-slate-900 truncate drop-shadow-sm">
                          {sample.name}
                        </span>
                      </div>
                    </button>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Right Column: Dynamic Slider & Extracted Palette */}
        <div className="space-y-6">
          <div className="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <h2 className="text-xl font-extrabold text-slate-900 flex items-center gap-2">
              <Sliders className="w-5 h-5 text-purple-600" />
              <span>Extraction Settings</span>
            </h2>

            <div>
              <div className="flex justify-between items-center mb-2">
                <label className="text-xs font-bold uppercase tracking-wider text-slate-400">
                  Extracted Swatches Count
                </label>
                <span className="text-xs font-mono font-bold text-purple-600">
                  {colorCount} Colors
                </span>
              </div>
              <input
                type="range"
                min="3"
                max="12"
                value={colorCount}
                onChange={(e) => setColorCount(parseInt(e.target.value, 10))}
                className="w-full accent-purple-600 bg-slate-200 rounded-lg cursor-pointer"
              />
            </div>
          </div>

          <div className="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-xl font-extrabold text-slate-900">Dominant Colors</h2>
              <button
                onClick={copyAll}
                className="px-3.5 py-1.5 rounded-lg bg-purple-600 text-white text-xs font-bold hover:bg-purple-700 transition-colors flex items-center gap-1.5"
              >
                <Copy className="w-3.5 h-3.5" /> Copy Array
              </button>
            </div>

            <div className="grid grid-cols-1 gap-3">
              {extractedColors.map((color, index) => (
                <div
                  key={`${color}-${index}`}
                  onClick={() => copyHex(color)}
                  className="bg-slate-50 border border-slate-100 hover:border-purple-300 rounded-2xl p-3 flex items-center justify-between cursor-pointer group transition-all"
                >
                  <div className="flex items-center gap-3">
                    <div
                      className="w-10 h-10 rounded-xl shadow-2xs border border-slate-200"
                      style={{ backgroundColor: color }}
                    />
                    <span className="text-sm font-mono font-bold text-slate-900 group-hover:text-purple-600">
                      {color}
                    </span>
                  </div>
                  <span className="text-xs font-bold text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity">
                    Copy
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
