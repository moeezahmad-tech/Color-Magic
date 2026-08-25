'use client';

import React from 'react';
import Link from 'next/link';
import {
  Palette,
  Sparkles,
  Pipette,
  Search,
  Tag,
  Monitor,
  Image as ImageIcon,
  HelpCircle,
  Moon,
  Briefcase,
  Wand2,
  Layers,
  ArrowRight,
  ChevronDown,
  Code2,
  Star,
  Heart,
  UploadCloud,
  ArrowLeftRight,
} from 'lucide-react';

export default function HomePage() {
  const toolCards = [
    { title: 'Hex Color Finder', desc: 'Enter any hex code and get its name, RGB, HSL and contrast info', href: '/find-color', icon: <Search className="w-5 h-5 text-purple-500" /> },
    { title: 'Hex to Color Name', desc: 'Convert any hex code to a human-readable color name instantly', href: '/hex-to-color-name', icon: <Tag className="w-5 h-5 text-pink-500" /> },
    { title: 'Hex to RGB Converter', desc: 'Fast, accurate hex to RGB and RGB to hex color conversion', href: '/hex-to-rgb', icon: <ArrowLeftRight className="w-5 h-5 text-blue-500" /> },
    { title: 'Palette from Image', desc: 'Upload any photo and extract its dominant colors instantly', href: '/palette-from-image', icon: <ImageIcon className="w-5 h-5 text-emerald-500" /> },
    { title: 'What Color Is This Code?', desc: 'Identify any hex or RGB color code with our interactive tool', href: '/what-color-is', icon: <Pipette className="w-5 h-5 text-orange-500" /> },
    { title: 'Dark Color Name Finder', desc: 'Discover names for deep, dark shades and midnight tones', href: '/dark-color-finder', icon: <Moon className="w-5 h-5 text-indigo-500" /> },
    { title: 'Brand Color Lookup', desc: 'Find hex codes and palettes from popular brand identities', href: '/brand-color-lookup', icon: <Briefcase className="w-5 h-5 text-slate-700" /> },
    { title: 'Generate Palette', desc: 'Create harmonious color schemes using color theory rules', href: '/generate-palette', icon: <Wand2 className="w-5 h-5 text-yellow-500" /> },
    { title: 'CSS Gradients', desc: 'Browse 100+ hand-crafted linear and radial CSS gradients', href: '/gradients', icon: <Layers className="w-5 h-5 text-teal-500" /> },
  ];

  const faqs = [
    {
      q: 'What is a hex color code?',
      a: 'A hex color code is a 6-character combination of numbers and letters (0-9, A-F) preceded by a # symbol. It represents a specific color using the RGB model. For example, #FF5733 is a vibrant red-orange, where FF is the red channel, 57 is green, and 33 is blue.',
    },
    {
      q: 'How do I find a color name from a hex code?',
      a: 'Use our Color Finder tool — simply enter any hex code and instantly get its closest color name, along with RGB and HSL values. Our database includes over 1,000 named colors with precise Lab-space matching.',
    },
    {
      q: 'What is a color palette generator?',
      a: 'A color palette generator creates harmonious color combinations using color theory rules like complementary, analogous, triadic, and tetradic relationships. Try our Palette Generator to create professional schemes from any seed color.',
    },
    {
      q: 'How do I convert hex to RGB?',
      a: 'Each pair of hex digits converts to a decimal value (0–255) for one RGB channel. For example, #3B82F6 becomes RGB(59, 130, 246). Use our Hex to RGB Converter for instant, accurate conversions both ways.',
    },
  ];

  const baseScrollCards = [
    { name: 'Neon Sunset', colors: ['#6366f1', '#8b5cf6', '#d946ef', '#ec4899'] },
    { name: 'Pastel Dream', colors: ['#fca5a5', '#fef08a', '#a7f3d0', '#93c5fd'] },
    { name: 'Forest Hills', colors: ['#10b981', '#059669', '#047857', '#064e3b'] },
    { name: 'Autumn Fire', colors: ['#f97316', '#ea580c', '#c2410c', '#7c2d12'] },
    { name: 'Rose Crimson', colors: ['#f43f5e', '#e11d48', '#be123c', '#9f1239'] },
  ];
  const scrollCards = [...baseScrollCards, ...baseScrollCards];

  const baseScrollCards2 = [
    { name: 'Slate Mono', colors: ['#1e293b', '#334155', '#475569', '#64748b'] },
    { name: 'Deep Ocean', colors: ['#06b6d4', '#0891b2', '#0e7490', '#155e75'] },
    { name: 'Cool Grey', colors: ['#e2e8f0', '#cbd5e1', '#94a3b8', '#64748b'] },
    { name: 'Gold Rush', colors: ['#fbbf24', '#f59e0b', '#d97706', '#b45309'] },
    { name: 'Lavender', colors: ['#a78bfa', '#8b5cf6', '#7c3aed', '#6d28d9'] },
  ];
  const scrollCards2 = [...baseScrollCards2, ...baseScrollCards2];

  return (
    <div className="bg-white overflow-x-hidden">

      {/* ═══════════════════════════════════════════════════════════════════
          HERO SECTION — fills exactly 100vh minus the 64px navbar
      ═══════════════════════════════════════════════════════════════════ */}
      <section
        className="relative flex items-center"
        style={{ minHeight: 'calc(100vh - 64px)' }}
      >
        {/* Subtle gradient background blobs — same as OLD PROJECT */}
        <div
          aria-hidden
          className="pointer-events-none absolute top-0 left-0 w-[520px] h-[520px] rounded-full opacity-[0.07]"
          style={{ background: 'radial-gradient(circle, #7c3aed, transparent 70%)', transform: 'translate(-40%, -40%)' }}
        />
        <div
          aria-hidden
          className="pointer-events-none absolute bottom-0 right-0 w-[440px] h-[440px] rounded-full opacity-[0.06]"
          style={{ background: 'radial-gradient(circle, #ec4899, transparent 70%)', transform: 'translate(40%, 40%)' }}
        />

        <div className="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-0">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            {/* ── Left: Hero Text ───────────────────────────────────────── */}
            <div className="lg:col-span-7 space-y-7">


              <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-900 leading-[1.08]">
                Find{' '}
                <span className="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                  Color Names
                </span>{' '}
                &{' '}
                <span className="relative">
                  Generate
                </span>{' '}
                Professional Palettes
              </h1>

              <p className="text-lg text-slate-500 leading-relaxed max-w-2xl font-normal">
                Instantly identify any color&apos;s name from its hex code. Convert hex to RGB and HSL, generate harmonious palettes with color theory, and explore thousands of curated color schemes — all free and open-source.
              </p>

              {/* CTA Buttons */}
              <div className="flex flex-wrap items-center gap-4 pt-1">
                <Link
                  href="/palettes"
                  className="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-bold text-sm shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/30 transition-all hover:-translate-y-0.5"
                >
                  Explore Palettes
                  <ArrowRight className="w-4 h-4" />
                </Link>
                <Link
                  href="/generate-palette"
                  className="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-white border-2 border-slate-200 hover:border-purple-300 text-slate-800 font-bold text-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
                >
                  <Sparkles className="w-4 h-4 text-purple-500" />
                  Generate Now
                </Link>
                <Link
                  href="/find-color"
                  className="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-white border-2 border-slate-200 hover:border-pink-300 text-slate-800 font-bold text-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
                >
                  <Pipette className="w-4 h-4 text-pink-500" />
                  Find Color
                </Link>
              </div>


            </div>

            {/* ── Right: Animated Palette Scroll ────────────────────────── */}
            <div
              className="lg:col-span-5 hidden lg:flex items-center justify-center relative overflow-hidden animation-container w-full"
              style={{ height: 'min(480px, calc(100vh - 200px))' }}
            >
              {/* Column 1 — scrolls up */}
              <div className="w-1/2 px-2 flex flex-col gap-3 animate-scroll-up">
                {scrollCards.map((card, i) => (
                  <div
                    key={`col1-${i}`}
                    className="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-2 shrink-0 hover:shadow-md transition-shadow"
                  >
                    <div className="flex h-11 rounded-xl overflow-hidden">
                      {card.colors.map((c) => (
                        <div key={c} className="flex-1" style={{ backgroundColor: c }} />
                      ))}
                    </div>
                    <div className="text-[10px] font-bold text-slate-400 text-center uppercase tracking-widest">
                      {card.name}
                    </div>
                  </div>
                ))}
              </div>

              {/* Column 2 — scrolls down */}
              <div className="w-1/2 px-2 flex flex-col gap-3 animate-scroll-down">
                {scrollCards2.map((card, i) => (
                  <div
                    key={`col2-${i}`}
                    className="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-2 shrink-0 hover:shadow-md transition-shadow"
                  >
                    <div className="flex h-11 rounded-xl overflow-hidden">
                      {card.colors.map((c) => (
                        <div key={c} className="flex-1" style={{ backgroundColor: c }} />
                      ))}
                    </div>
                    <div className="text-[10px] font-bold text-slate-400 text-center uppercase tracking-widest">
                      {card.name}
                    </div>
                  </div>
                ))}
              </div>
            </div>

          </div>
        </div>


      </section>

      {/* ═══════════════════════════════════════════════════════════════════
          9 FEATURE CARDS GRID
      ═══════════════════════════════════════════════════════════════════ */}
      <section className="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          
          {/* 1. Explore Palettes */}
          <Link href="/palettes" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-pink-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-pink-100 text-pink-500 flex items-center justify-center mb-6">
                <Palette className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-pink-600 transition-colors tracking-tight">
                Explore Palettes
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Discover thousands of curated, trending color palettes. Filter by style, save your favorites, and copy hex codes with a single click.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex gap-1 h-3 rounded-full overflow-hidden">
              {['#7C3AED', '#C084FC', '#F472B6', '#FBBF24', '#34D399'].map((c) => (
                <div key={c} className="flex-1" style={{ backgroundColor: c }} />
              ))}
            </div>
          </Link>

          {/* 2. Generate Palette */}
          <Link href="/generate-palette" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-purple-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mb-6">
                <Sparkles className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-purple-600 transition-colors tracking-tight">
                Generate Palette
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Design unique color schemes based on color theory. Generate monochromatic, analogous, complementary, and triadic harmonies instantly.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex justify-center items-center">
              {['#8B5CF6', '#d8b4fe', '#f9a8d4', '#f472b6'].map((c, i) => (
                <div
                  key={c}
                  className="w-3.5 h-3.5 rounded-full shadow-sm"
                  style={{ backgroundColor: c, marginLeft: i === 0 ? 0 : '8px' }}
                />
              ))}
            </div>
          </Link>

          {/* 3. Find Color */}
          <Link href="/find-color" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-emerald-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-6">
                <Pipette className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-emerald-600 transition-colors tracking-tight">
                Find Color
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Convert Hex codes to human-readable names. Access exact values for RGB and HSL formats, review contrast levels, and download shade profiles.
              </p>
            </div>
            <div className="relative z-10 mt-8 bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 flex items-center justify-between text-[10px] sm:text-xs font-mono text-slate-400">
              <span className="font-semibold text-slate-500">#EC4899</span>
              <ArrowRight className="w-3.5 h-3.5 text-slate-400" />
              <span className="font-semibold text-slate-500">RGB(236, 72, 153)</span>
            </div>
          </Link>

          {/* 4. Gradients */}
          <Link href="/gradients" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-amber-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center mb-6">
                <Layers className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-amber-500 transition-colors tracking-tight">
                Gradients
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Explore beautiful linear & radial CSS gradients. Filter by style, preview live, and copy ready-to-use CSS with one click.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex gap-2 h-3.5 w-full">
              <div className="flex-1 rounded-sm bg-gradient-to-r from-purple-600 to-pink-500" />
              <div className="flex-1 rounded-sm bg-gradient-to-r from-red-500 to-pink-500" />
              <div className="flex-1 rounded-sm bg-gradient-to-r from-emerald-500 to-teal-400" />
            </div>
          </Link>

          {/* 5. Open Source */}
          <Link href="https://github.com/moeezahmad-tech/Color-Magic" target="_blank" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-slate-50/80 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center mb-6">
                <Code2 className="w-5 h-5" /> 
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-slate-700 transition-colors tracking-tight">
                Open Source
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Explore the code behind Color Magic. Built entirely with React, Tailwind CSS, and Next.js. Fork, modify, and contribute on GitHub.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex items-center gap-1.5 text-[11px] font-bold text-slate-500">
              <Star className="w-4 h-4 text-yellow-400 fill-yellow-400" />
              <span>Star on GitHub</span>
            </div>
          </Link>

          {/* 6. Favorites */}
          <Link href="/profile" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-red-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-6">
                <Heart className="w-5 h-5 fill-red-500" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-red-500 transition-colors tracking-tight">
                Favorites
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                All your saved colors, palettes and gradients in one place. Access your personal collection anytime and pick up right where you left off.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex items-center gap-1.5 text-[11px] font-bold text-slate-500">
              <Heart className="w-4 h-4 text-red-400 fill-red-400" />
              <span>Your collection</span>
            </div>
          </Link>

          {/* 7. Palette from Image */}
          <Link href="/palette-from-image" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-fuchsia-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-fuchsia-100 text-fuchsia-500 flex items-center justify-center mb-6">
                <ImageIcon className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-fuchsia-500 transition-colors tracking-tight">
                Palette from Image
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Upload any photo and extract its dominant color palette instantly. All processing happens in your browser — your images never leave your device.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex items-center gap-1.5 text-[11px] font-bold text-slate-500">
              <UploadCloud className="w-4 h-4 text-purple-400" />
              <span>Upload & extract</span>
            </div>
          </Link>

          {/* 8. Hex to RGB */}
          <Link href="/hex-to-rgb" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-cyan-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-cyan-100 text-cyan-500 flex items-center justify-center mb-6">
                <ArrowLeftRight className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-cyan-500 transition-colors tracking-tight">
                Hex to RGB
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Convert hex color codes to RGB values and vice versa in real-time. Live color preview, conversion formula, and example table included.
              </p>
            </div>
            <div className="relative z-10 mt-8 bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 flex items-center justify-between text-[10px] sm:text-xs font-mono text-slate-400">
              <span className="font-semibold text-slate-500">#3B82F6</span>
              <ArrowLeftRight className="w-3.5 h-3.5 text-slate-400" />
              <span className="font-semibold text-slate-500">59, 130, 246</span>
            </div>
          </Link>

          {/* 9. Dark Color Finder */}
          <Link href="/dark-color-finder" className="group relative bg-white border border-slate-100 hover:border-purple-200 rounded-3xl p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between overflow-hidden min-h-[320px]">
            <div className="absolute top-0 right-0 w-64 h-64 bg-indigo-50/50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110 pointer-events-none" />
            <div className="relative z-10">
              <div className="w-10 h-10 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center mb-6">
                <Moon className="w-5 h-5" />
              </div>
              <h3 className="text-2xl font-black text-slate-900 mb-3 group-hover:text-indigo-500 transition-colors tracking-tight">
                Dark Color Finder
              </h3>
              <p className="text-sm text-slate-500 leading-relaxed">
                Discover names for deep, dark shades and midnight tones. Perfect for dark themes, backgrounds, and low-light design work.
              </p>
            </div>
            <div className="relative z-10 mt-8 flex gap-1 h-3.5 rounded-sm overflow-hidden w-full">
              {['#020617', '#1e1b4b', '#312e81', '#4c1d95', '#701a75'].map((c) => (
                <div key={c} className="flex-1" style={{ backgroundColor: c }} />
              ))}
            </div>
          </Link>

        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════════
          POPULAR TOOLS GRID
      ═══════════════════════════════════════════════════════════════════ */}
      <section className="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div className="text-center mb-12">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Popular Color Tools
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {toolCards.map((tool) => (
            <Link
              key={tool.title}
              href={tool.href}
              className="bg-white border border-slate-100 hover:border-slate-200 rounded-2xl p-6 transition-all duration-200 hover:shadow-sm group flex flex-col gap-2"
            >
              <div className="flex items-center gap-2 mb-1">
                <span className="flex items-center justify-center">{tool.icon}</span>
                <h3 className="text-base font-bold text-slate-900 group-hover:text-slate-700 transition-colors">
                  {tool.title}
                </h3>
              </div>
              <p className="text-sm text-slate-500 leading-relaxed">
                {tool.desc}
              </p>
            </Link>
          ))}
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════════
          FAQ SECTION
      ═══════════════════════════════════════════════════════════════════ */}

      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div className="text-center mb-12">
          <p className="text-xs font-bold uppercase tracking-widest text-pink-500 mb-3">Help</p>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Frequently Asked Questions
          </h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-5xl mx-auto">
          {faqs.map((faq) => (
            <div
              key={faq.q}
              className="bg-white border border-slate-100 rounded-3xl p-7 space-y-3 hover:border-purple-100 hover:shadow-sm transition-all"
            >
              <h3 className="text-sm font-bold text-slate-900">{faq.q}</h3>
              <p className="text-sm text-slate-500 leading-relaxed">{faq.a}</p>
            </div>
          ))}
        </div>
      </section>

      {/* ═══════════════════════════════════════════════════════════════════
          CTA BOTTOM BANNER
      ═══════════════════════════════════════════════════════════════════ */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div className="bg-gradient-to-r from-purple-600 to-pink-500 rounded-3xl p-12 text-center text-white relative overflow-hidden">
          <div aria-hidden className="absolute inset-0 opacity-10" style={{ backgroundImage: 'radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 50%, white 1px, transparent 1px)', backgroundSize: '60px 60px' }} />
          <div className="relative">
            <h2 className="text-3xl sm:text-4xl font-black mb-4 tracking-tight">
              Start Exploring Colors Today
            </h2>
            <p className="text-white/80 text-base mb-8 max-w-xl mx-auto">
              Free, open-source, and always improving. No account needed — just instant color tools for designers and developers.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link
                href="/palettes"
                className="px-8 py-3.5 rounded-2xl bg-white text-purple-700 font-bold text-sm hover:bg-purple-50 transition-colors shadow-md"
              >
                Explore Palettes
              </Link>
              <Link
                href="/find-color"
                className="px-8 py-3.5 rounded-2xl bg-white/10 border border-white/30 text-white font-bold text-sm hover:bg-white/20 transition-colors"
              >
                Find a Color
              </Link>
            </div>
          </div>
        </div>
      </section>

    </div>
  );
}
