'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import {
  ChevronDown,
  Menu,
  X,
  Palette,
  Sparkles,
  Wand2,
  Image as ImageIcon,
  Pipette,
  Tag,
  Sliders,
  HelpCircle,
  Moon,
  Bookmark,
  User,
} from 'lucide-react';
import { GithubIcon, GoogleIcon } from '@/components/ui/Icons';

const NAV_SECTIONS = [
  {
    label: 'EXPLORE',
    items: [
      { href: '/palettes', icon: Palette, color: 'text-pink-500', label: 'Color Palettes', desc: 'Browse 150+ curated schemes' },
      { href: '/gradients', icon: Sparkles, color: 'text-amber-500', label: 'CSS Gradients', desc: '100+ copy-ready gradients' },
    ],
  },
  {
    label: 'CREATE',
    items: [
      { href: '/generate-palette', icon: Wand2, color: 'text-purple-500', label: 'Generate Palette', desc: 'Color theory generator' },
      { href: '/palette-from-image', icon: ImageIcon, color: 'text-fuchsia-500', label: 'Palette from Image', desc: 'Extract from any photo' },
      { href: '/find-color', icon: Pipette, color: 'text-emerald-500', label: 'Find Color', desc: 'Hex to name & formats' },
    ],
  },
  {
    label: 'CONVERTERS',
    items: [
      { href: '/hex-to-color-name', icon: Tag, color: 'text-violet-500', label: 'Hex to Color Name', desc: '1000+ named colors' },
      { href: '/hex-to-rgb', icon: Sliders, color: 'text-indigo-500', label: 'Hex to RGB', desc: 'Bidirectional converter' },
      { href: '/what-color-is', icon: HelpCircle, color: 'text-amber-500', label: 'What Color Is This?', desc: 'Identify any hex code' },
      { href: '/dark-color-finder', icon: Moon, color: 'text-blue-500', label: 'Dark Color Finder', desc: 'Deep & dark shades' },
      { href: '/brand-color-lookup', icon: Bookmark, color: 'text-teal-500', label: 'Brand Colors', desc: 'Google, Apple, Spotify...' },
    ],
  },
];

export const Navbar = () => {
  const pathname = usePathname();
  const [isToolsOpen, setIsToolsOpen] = useState(false);
  const [isMobileOpen, setIsMobileOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  // Close mobile menu on route change
  useEffect(() => {
    setIsMobileOpen(false);
    setIsToolsOpen(false);
  }, [pathname]);

  // Add shadow on scroll
  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  const isActive = (href: string) => pathname === href;

  return (
    <>
      <header
        className={`sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-slate-100 transition-shadow duration-200 ${
          scrolled ? 'shadow-sm' : ''
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">

            {/* ── Brand ──────────────────────────────────────────── */}
            <Link href="/" className="flex items-center gap-2.5 group shrink-0">
              <Image
                src="/logo.png"
                alt="Color Magic Logo"
                width={32}
                height={32}
                className="w-8 h-8 object-contain"
                priority
              />
              <span className="text-xl font-bold tracking-tight text-slate-900">
                Color{' '}
                <span className="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                  Magic
                </span>
              </span>
            </Link>

            {/* ── Desktop Nav ────────────────────────────────────── */}
            <nav className="hidden md:flex items-center gap-1">
              <Link
                href="/"
                className={`px-3 py-2 rounded-lg text-sm font-semibold transition-colors ${
                  isActive('/') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
                }`}
              >
                Home
              </Link>

              {/* Tools Dropdown */}
              <div className="relative">
                <button
                  onClick={() => setIsToolsOpen((v) => !v)}
                  onBlur={() => setTimeout(() => setIsToolsOpen(false), 200)}
                  className={`flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors ${
                    isToolsOpen
                      ? 'text-purple-600 bg-purple-50'
                      : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
                  }`}
                >
                  <span>Tools</span>
                  <ChevronDown
                    className={`w-3.5 h-3.5 transition-transform duration-200 ${
                      isToolsOpen ? 'rotate-180 text-purple-500' : ''
                    }`}
                  />
                </button>

                {isToolsOpen && (
                  <div className="absolute right-0 mt-2 w-[600px] bg-white border border-slate-100 rounded-2xl shadow-2xl p-4 z-50 flex gap-4">
                    {/* Column 1 */}
                    <div className="flex-1 space-y-4">
                      {NAV_SECTIONS.slice(0, 2).map((section) => (
                        <div key={section.label}>
                          <div className="px-3 py-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            {section.label}
                          </div>
                          {section.items.map(({ href, icon: Icon, color, label, desc }) => (
                            <Link
                              key={href}
                              href={href}
                              className={`flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors group ${
                                isActive(href) ? 'bg-purple-50' : 'hover:bg-slate-50'
                              }`}
                            >
                              <div className={`w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 group-hover:bg-white transition-colors`}>
                                <Icon className={`w-4 h-4 ${color}`} />
                              </div>
                              <div>
                                <div className={`text-sm font-semibold ${isActive(href) ? 'text-purple-700' : 'text-slate-800'}`}>
                                  {label}
                                </div>
                                <div className="text-[11px] text-slate-400">{desc}</div>
                              </div>
                            </Link>
                          ))}
                        </div>
                      ))}
                    </div>

                    {/* Column 2 */}
                    <div className="flex-1 space-y-4">
                      {NAV_SECTIONS.slice(2, 3).map((section) => (
                        <div key={section.label}>
                          <div className="px-3 py-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            {section.label}
                          </div>
                          {section.items.map(({ href, icon: Icon, color, label, desc }) => (
                            <Link
                              key={href}
                              href={href}
                              className={`flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors group ${
                                isActive(href) ? 'bg-purple-50' : 'hover:bg-slate-50'
                              }`}
                            >
                              <div className={`w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 group-hover:bg-white transition-colors`}>
                                <Icon className={`w-4 h-4 ${color}`} />
                              </div>
                              <div>
                                <div className={`text-sm font-semibold ${isActive(href) ? 'text-purple-700' : 'text-slate-800'}`}>
                                  {label}
                                </div>
                                <div className="text-[11px] text-slate-400">{desc}</div>
                              </div>
                            </Link>
                          ))}
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>

              {/* Profile pill */}
              <Link
                href="/profile"
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200/60 ml-2"
              >
                <User className="w-3.5 h-3.5" />
                Profile
              </Link>

              {/* Open Source pill */}
              <Link
                href="/open-source"
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200/60 ml-1"
              >
                <GithubIcon className="w-3.5 h-3.5" />
                Open Source
              </Link>

              {/* Login CTA */}
              <Link
                href="/login"
                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white text-xs font-bold shadow-sm hover:shadow-md transition-all ml-1"
              >
                <GoogleIcon className="w-3.5 h-3.5 bg-white rounded-full p-0.5" />
                Login
              </Link>
            </nav>

            {/* ── Mobile Hamburger ───────────────────────────────── */}
            <button
              onClick={() => setIsMobileOpen((v) => !v)}
              className="md:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors"
              aria-label={isMobileOpen ? 'Close menu' : 'Open menu'}
            >
              {isMobileOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>
        </div>
      </header>

      {/* ── Mobile Full-Screen Overlay ──────────────────────────────────── */}
      {isMobileOpen && (
        <div className="fixed inset-0 z-40 bg-white/98 backdrop-blur-lg flex flex-col md:hidden overflow-y-auto">
          {/* Mobile header */}
          <div className="flex items-center justify-between px-4 h-16 border-b border-slate-100 shrink-0">
            <Link href="/" onClick={() => setIsMobileOpen(false)} className="flex items-center gap-2.5">
              <Image src="/logo.png" alt="Color Magic" width={28} height={28} className="w-7 h-7 object-contain" />
              <span className="text-lg font-bold text-slate-900">
                Color <span className="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">Magic</span>
              </span>
            </Link>
            <button
              onClick={() => setIsMobileOpen(false)}
              className="p-2 rounded-xl bg-slate-100 text-slate-700"
              aria-label="Close menu"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Mobile nav items */}
          <nav className="flex-1 px-4 py-6 space-y-1">
            <Link
              href="/"
              onClick={() => setIsMobileOpen(false)}
              className={`flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-colors ${
                isActive('/') ? 'bg-purple-50 text-purple-700' : 'text-slate-700 hover:bg-slate-50'
              }`}
            >
              Home
            </Link>

            {NAV_SECTIONS.map((section) => (
              <div key={section.label}>
                <div className="px-4 pt-5 pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                  {section.label}
                </div>
                {section.items.map(({ href, icon: Icon, color, label }) => (
                  <Link
                    key={href}
                    href={href}
                    onClick={() => setIsMobileOpen(false)}
                    className={`flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-colors ${
                      isActive(href) ? 'bg-purple-50 text-purple-700' : 'text-slate-700 hover:bg-slate-50'
                    }`}
                  >
                    <Icon className={`w-4 h-4 shrink-0 ${color}`} />
                    {label}
                  </Link>
                ))}
              </div>
            ))}

            <div className="pt-4 border-t border-slate-100 space-y-1">
              <Link
                href="/profile"
                onClick={() => setIsMobileOpen(false)}
                className="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
              >
                <User className="w-4 h-4 text-blue-500" />
                Profile
              </Link>
              <Link
                href="/open-source"
                onClick={() => setIsMobileOpen(false)}
                className="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors"
              >
                <GithubIcon className="w-4 h-4 text-slate-700" />
                Open Source
              </Link>
            </div>
          </nav>

          {/* Mobile Login CTA */}
          <div className="px-4 py-6 border-t border-slate-100 shrink-0">
            <Link
              href="/login"
              onClick={() => setIsMobileOpen(false)}
              className="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold text-sm shadow-md"
            >
              <GoogleIcon className="w-4 h-4 bg-white rounded-full p-0.5" />
              Sign in with Google
            </Link>
          </div>
        </div>
      )}
    </>
  );
};
