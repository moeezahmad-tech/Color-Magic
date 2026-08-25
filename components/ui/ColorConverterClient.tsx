'use client';

import React, { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { 
  normalizeHex, 
  hexToRgb, 
  hexToHsl, 
  rgbToHex, 
  rgbToHsl, 
  hslToHex, 
  hslToRgb 
} from '@/lib/color-math';
import { useToast } from '@/components/ui/ToastProvider';
import { ArrowLeftRight, Copy, ChevronDown, RefreshCw } from 'lucide-react';

export type ConversionMode = 
  | 'hex-to-rgb'
  | 'rgb-to-hex'
  | 'hex-to-hsl'
  | 'hsl-to-hex'
  | 'rgb-to-hsl'
  | 'hsl-to-rgb';

interface Props {
  initialMode: ConversionMode;
}

const MODES = [
  { id: 'hex-to-rgb', label: 'HEX to RGB' },
  { id: 'rgb-to-hex', label: 'RGB to HEX' },
  { id: 'hex-to-hsl', label: 'HEX to HSL' },
  { id: 'hsl-to-hex', label: 'HSL to HEX' },
  { id: 'rgb-to-hsl', label: 'RGB to HSL' },
  { id: 'hsl-to-rgb', label: 'HSL to RGB' },
];

export default function ColorConverterClient({ initialMode }: Props) {
  const router = useRouter();
  const { showToast } = useToast();
  
  // State for inputs
  const [hexInput, setHexInput] = useState('#E74C3C');
  const [rInput, setRInput] = useState(231);
  const [gInput, setGInput] = useState(76);
  const [bInput, setBInput] = useState(60);
  const [hInput, setHInput] = useState(6);
  const [sInput, setSInput] = useState(78);
  const [lInput, setLInput] = useState(57);
  
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);

  // Derived Values
  const activeHex = normalizeHex(hexInput);
  
  // Handlers for switching modes smoothly
  const handleModeSwitch = (modeId: string) => {
    setIsDropdownOpen(false);
    if (modeId === initialMode) return;
    router.push(`/${modeId}`);
  };

  const copyToClipboard = (text: string, label: string) => {
    navigator.clipboard.writeText(text);
    showToast(`Copied ${label} to clipboard!`);
  };

  // Render logic based on mode
  const renderHexInput = () => (
    <div className="flex-1 w-full">
      <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">HEX Code</label>
      <input
        type="text"
        value={hexInput}
        onChange={(e) => setHexInput(e.target.value)}
        placeholder="#E74C3C"
        className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-5 py-3.5 font-mono text-base text-slate-900 focus:outline-none transition-colors"
      />
    </div>
  );

  const renderRgbInput = () => (
    <div className="flex-1 w-full grid grid-cols-3 gap-3">
      <div>
        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">R</label>
        <input
          type="number" min="0" max="255" value={rInput}
          onChange={(e) => setRInput(Number(e.target.value))}
          className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-3 py-3.5 font-mono text-base text-slate-900 focus:outline-none text-center"
        />
      </div>
      <div>
        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">G</label>
        <input
          type="number" min="0" max="255" value={gInput}
          onChange={(e) => setGInput(Number(e.target.value))}
          className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-3 py-3.5 font-mono text-base text-slate-900 focus:outline-none text-center"
        />
      </div>
      <div>
        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">B</label>
        <input
          type="number" min="0" max="255" value={bInput}
          onChange={(e) => setBInput(Number(e.target.value))}
          className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-3 py-3.5 font-mono text-base text-slate-900 focus:outline-none text-center"
        />
      </div>
    </div>
  );

  const renderHslInput = () => (
    <div className="flex-1 w-full grid grid-cols-3 gap-3">
      <div>
        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">H</label>
        <input
          type="number" min="0" max="360" value={hInput}
          onChange={(e) => setHInput(Number(e.target.value))}
          className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-3 py-3.5 font-mono text-base text-slate-900 focus:outline-none text-center"
        />
      </div>
      <div>
        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">S%</label>
        <input
          type="number" min="0" max="100" value={sInput}
          onChange={(e) => setSInput(Number(e.target.value))}
          className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-3 py-3.5 font-mono text-base text-slate-900 focus:outline-none text-center"
        />
      </div>
      <div>
        <label className="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2 text-center">L%</label>
        <input
          type="number" min="0" max="100" value={lInput}
          onChange={(e) => setLInput(Number(e.target.value))}
          className="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-purple-500 rounded-xl px-3 py-3.5 font-mono text-base text-slate-900 focus:outline-none text-center"
        />
      </div>
    </div>
  );

  const getPreviewColor = () => {
    try {
      if (initialMode.startsWith('hex')) return activeHex;
      if (initialMode.startsWith('rgb')) return rgbToHex(rInput, gInput, bInput);
      if (initialMode.startsWith('hsl')) return hslToHex(hInput, sInput, lInput);
    } catch {
      return '#000000';
    }
    return '#000000';
  };

  const previewColor = getPreviewColor();

  const getConversionResult = () => {
    try {
      if (initialMode === 'hex-to-rgb') {
        const rgb = hexToRgb(activeHex);
        return { label: 'RGB Result', text: `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`, split: rgb };
      }
      if (initialMode === 'rgb-to-hex') {
        const hex = rgbToHex(rInput, gInput, bInput);
        return { label: 'HEX Result', text: hex, split: null };
      }
      if (initialMode === 'hex-to-hsl') {
        const hsl = hexToHsl(activeHex);
        return { label: 'HSL Result', text: `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`, split: hsl };
      }
      if (initialMode === 'hsl-to-hex') {
        const hex = hslToHex(hInput, sInput, lInput);
        return { label: 'HEX Result', text: hex, split: null };
      }
      if (initialMode === 'rgb-to-hsl') {
        const hsl = rgbToHsl(rInput, gInput, bInput);
        return { label: 'HSL Result', text: `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`, split: hsl };
      }
      if (initialMode === 'hsl-to-rgb') {
        const rgb = hslToRgb(hInput, sInput, lInput);
        return { label: 'RGB Result', text: `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`, split: rgb };
      }
    } catch {
      return { label: 'Error', text: 'Invalid Input', split: null };
    }
    return { label: 'Error', text: 'Invalid Input', split: null };
  };

  const result = getConversionResult();

  return (
    <div className="max-w-4xl mx-auto px-4 pt-8 pb-16 space-y-8">
      {/* Dropdown Selector */}
      <div className="flex justify-center relative z-50">
        <div className="relative inline-block text-left w-full sm:w-64">
          <button
            onClick={() => setIsDropdownOpen(!isDropdownOpen)}
            onBlur={() => setTimeout(() => setIsDropdownOpen(false), 200)}
            className="w-full flex items-center justify-between px-5 py-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-purple-300 transition-all font-bold text-slate-800"
          >
            <div className="flex items-center gap-2">
              <RefreshCw className="w-4 h-4 text-purple-500" />
              <span>{MODES.find(m => m.id === initialMode)?.label || 'Conversion'}</span>
            </div>
            <ChevronDown className={`w-4 h-4 text-slate-400 transition-transform ${isDropdownOpen ? 'rotate-180' : ''}`} />
          </button>

          {isDropdownOpen && (
            <div className="origin-top-right absolute right-0 mt-2 w-full rounded-2xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 z-50 border border-slate-100 overflow-hidden">
              <div className="py-1">
                {MODES.map((mode) => (
                  <button
                    key={mode.id}
                    onClick={() => handleModeSwitch(mode.id)}
                    className={`group flex items-center w-full px-5 py-3 text-sm font-semibold transition-colors ${
                      initialMode === mode.id ? 'bg-purple-50 text-purple-700' : 'text-slate-700 hover:bg-slate-50'
                    }`}
                  >
                    {mode.label}
                  </button>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>

      <div className="bg-white border border-slate-100 rounded-3xl p-6 sm:p-10 shadow-sm flex flex-col md:flex-row items-center gap-8 md:gap-12 relative overflow-hidden">
        {/* Background Decor */}
        <div className="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-10 -mt-10 pointer-events-none opacity-50" />
        
        {/* Input Column */}
        <div className="w-full md:w-1/2 flex flex-col items-start gap-4 z-10">
          <h2 className="text-xl font-extrabold text-slate-900 tracking-tight">Enter Source Color</h2>
          <div className="w-full">
            {initialMode.startsWith('hex') && renderHexInput()}
            {initialMode.startsWith('rgb') && renderRgbInput()}
            {initialMode.startsWith('hsl') && renderHslInput()}
          </div>
        </div>

        <div className="hidden md:flex items-center justify-center z-10">
          <div className="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
            <ArrowLeftRight className="w-5 h-5" />
          </div>
        </div>

        {/* Output Column */}
        <div className="w-full md:w-1/2 flex flex-col items-start gap-4 z-10">
           <h2 className="text-xl font-extrabold text-slate-900 tracking-tight">Conversion Result</h2>
           <div className="w-full bg-slate-50 border border-slate-200/80 rounded-2xl p-5">
             <div className="flex items-center justify-between mb-4">
               <span className="text-xs font-bold uppercase tracking-wider text-slate-400">{result.label}</span>
               <button 
                  onClick={() => copyToClipboard(result.text, result.label)}
                  className="text-xs font-bold text-purple-600 bg-purple-100/50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5"
               >
                 <Copy className="w-3.5 h-3.5" /> Copy
               </button>
             </div>
             
             <div className="flex items-center gap-4">
               <div 
                 className="w-16 h-16 sm:w-20 sm:h-20 rounded-xl shadow-2xs border border-slate-200 shrink-0" 
                 style={{ backgroundColor: previewColor }} 
               />
               <div className="text-lg sm:text-2xl font-mono font-black text-slate-900 break-all">
                 {result.text}
               </div>
             </div>
           </div>
        </div>
      </div>
    </div>
  );
}
