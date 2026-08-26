'use client';

import React, { useState, useEffect, useRef } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { useSession, signOut } from 'next-auth/react';
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
  Droplet,
  LogOut,
  Heart,
  ShieldCheck,
} from 'lucide-react';
import { GithubIcon, GoogleIcon } from '@/components/ui/Icons';

const NAV_SECTIONS = [
  {
    label: 'EXPLORE',
    items: [
      { href: '/palettes', icon: Palette, color: 'text-pink-500', label: 'Color Palettes', desc: 'Browse 150+ curated schemes' },
      { href: '/gradients', icon: Sparkles, color: 'text-amber-500', label: 'CSS Gradients', desc: '100+ copy-ready gradients' },
      { href: '/colors', icon: Droplet, color: 'text-sky-500', label: 'Explore Colors', desc: '1,000+ curated named colors' },
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
  const { data: session, status } = useSession();
  const [isToolsOpen, setIsToolsOpen] = useState(false);
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
  const [isMobileOpen, setIsMobileOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const userMenuRef = useRef<HTMLDivElement>(null);

  // Close menus on route change
  useEffect(() => {
    setIsMobileOpen(false);
    setIsToolsOpen(false);
    setIsUserMenuOpen(false);
  }, [pathname]);

  // Click outside to close user dropdown
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (userMenuRef.current && !userMenuRef.current.contains(event.target as Node)) {
        setIsUserMenuOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

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

            {/* ── Desktop Navigation ──────────────────────────────── */}
            <nav className="hidden md:flex items-center gap-1">
              <Link
                href="/palettes"
                className={`px-3 py-1.5 rounded-full text-xs font-bold transition-all ${
                  isActive('/palettes')
                    ? 'bg-pink-50 text-pink-700'
                    : 'text-slate-700 hover:bg-slate-100'
                }`}
              >
                Palettes
              </Link>
              <Link
                href="/gradients"
                className={`px-3 py-1.5 rounded-full text-xs font-bold transition-all ${
                  isActive('/gradients')
                    ? 'bg-purple-50 text-purple-700'
                    : 'text-slate-700 hover:bg-slate-100'
                }`}
              >
                Gradients
              </Link>
              <Link
                href="/colors"
                className={`px-3 py-1.5 rounded-full text-xs font-bold transition-all ${
                  isActive('/colors')
                    ? 'bg-sky-50 text-sky-700'
                    : 'text-slate-700 hover:bg-slate-100'
                }`}
              >
                Colors
              </Link>

              {/* Tools Mega-Dropdown */}
              <div
                className="relative"
                onMouseEnter={() => setIsToolsOpen(true)}
                onMouseLeave={() => setIsToolsOpen(false)}
              >
                <button
                  onClick={() => setIsToolsOpen(!isToolsOpen)}
                  className={`flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all ${
                    isToolsOpen ? 'bg-slate-100 text-slate-900' : 'text-slate-700 hover:bg-slate-100'
                  }`}
                >
                  <span>Tools</span>
                  <ChevronDown className={`w-3.5 h-3.5 transition-transform duration-200 ${isToolsOpen ? 'rotate-180' : ''}`} />
                </button>

                {isToolsOpen && (
                  <div className="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[600px] bg-white border border-slate-100 rounded-2xl shadow-2xl p-4 z-50 flex gap-4 animate-in fade-in-0 zoom-in-95 duration-150">
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

              {/* Open Source pill */}
              <Link
                href="/open-source"
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200/60 ml-2"
              >
                <GithubIcon className="w-3.5 h-3.5" />
                Open Source
              </Link>

              {/* ── User Auth State ─────────────────────────────────── */}
              {status === 'authenticated' && session?.user ? (
                <div className="relative ml-2" ref={userMenuRef}>
                  <button
                    onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
                    className="flex items-center gap-2 p-1 pl-2 pr-2.5 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all cursor-pointer group"
                    title={session.user.name || 'User Account'}
                  >
                    <div className="relative w-7 h-7 rounded-full overflow-hidden border border-purple-300 shadow-xs shrink-0">
                      {session.user.image ? (
                        <Image
                          src={session.user.image}
                          alt={session.user.name || 'User DP'}
                          width={28}
                          height={28}
                          className="w-full h-full object-cover"
                          unoptimized
                        />
                      ) : (
                        <div className="w-full h-full bg-gradient-to-tr from-purple-600 to-pink-500 flex items-center justify-center text-white text-xs font-black">
                          {session.user.name?.charAt(0) || 'U'}
                        </div>
                      )}
                      <span className="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-500 ring-1 ring-white" />
                    </div>

                    <span className="text-xs font-bold text-slate-800 max-w-[100px] truncate hidden lg:inline-block">
                      {session.user.name?.split(' ')[0] || 'Profile'}
                    </span>
                    <ChevronDown className={`w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform ${isUserMenuOpen ? 'rotate-180' : ''}`} />
                  </button>

                  {/* User Dropdown Menu */}
                  {isUserMenuOpen && (
                    <div className="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 animate-in fade-in-0 zoom-in-95 duration-150">
                      {/* User Info Header */}
                      <div className="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full overflow-hidden border border-purple-200 shrink-0">
                          {session.user.image ? (
                            <Image
                              src={session.user.image}
                              alt={session.user.name || 'User'}
                              width={40}
                              height={40}
                              className="w-full h-full object-cover"
                              unoptimized
                            />
                          ) : (
                            <div className="w-full h-full bg-gradient-to-tr from-purple-600 to-pink-500 flex items-center justify-center text-white text-sm font-black">
                              {session.user.name?.charAt(0) || 'U'}
                            </div>
                          )}
                        </div>
                        <div className="min-w-0">
                          <div className="text-xs font-bold text-slate-900 truncate">
                            {session.user.name || 'Google User'}
                          </div>
                          <div className="text-[11px] text-slate-400 truncate">
                            {session.user.email}
                          </div>
                        </div>
                      </div>

                      {/* Menu Links */}
                      <div className="py-1">
                        <Link
                          href="/profile"
                          onClick={() => setIsUserMenuOpen(false)}
                          className="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-purple-50 hover:text-purple-700 transition-colors"
                        >
                          <Heart className="w-4 h-4 text-pink-500" />
                          <span>Saved Favorites & Library</span>
                        </Link>
                        <Link
                          href="/profile#palettes"
                          onClick={() => setIsUserMenuOpen(false)}
                          className="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors pl-10"
                        >
                          <span>My Color Palettes</span>
                        </Link>
                        <Link
                          href="/profile#gradients"
                          onClick={() => setIsUserMenuOpen(false)}
                          className="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors pl-10"
                        >
                          <span>My CSS Gradients</span>
                        </Link>
                      </div>

                      {/* Sign Out Action */}
                      <div className="pt-1 border-t border-slate-100">
                        <button
                          onClick={() => {
                            setIsUserMenuOpen(false);
                            signOut({ callbackUrl: '/' });
                          }}
                          className="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors text-left cursor-pointer"
                        >
                          <LogOut className="w-4 h-4 text-red-500" />
                          <span>Sign Out</span>
                        </button>
                      </div>
                    </div>
                  )}
                </div>
              ) : (
                <div className="flex items-center gap-2 ml-2">
                  <Link
                    href="/profile"
                    className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200/60"
                  >
                    <User className="w-3.5 h-3.5" />
                    Profile
                  </Link>
                  <Link
                    href="/login"
                    className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white text-xs font-bold shadow-sm hover:shadow-md transition-all cursor-pointer"
                  >
                    <GoogleIcon className="w-3.5 h-3.5 bg-white rounded-full p-0.5" />
                    Sign In
                  </Link>
                </div>
              )}
            </nav>

            {/* ── Mobile Hamburger ───────────────────────────────── */}
            <button
              onClick={() => setIsMobileOpen((v) => !v)}
              className="md:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer"
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
              className="p-2 rounded-xl bg-slate-100 text-slate-700 cursor-pointer"
              aria-label="Close menu"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Mobile User Profile Header (if logged in) */}
          {status === 'authenticated' && session?.user && (
            <div className="mx-4 mt-4 p-4 rounded-2xl bg-gradient-to-r from-purple-50 via-pink-50 to-white border border-purple-100 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-full overflow-hidden border border-purple-300 shrink-0">
                  {session.user.image ? (
                    <Image
                      src={session.user.image}
                      alt={session.user.name || 'User'}
                      width={40}
                      height={40}
                      className="w-full h-full object-cover"
                      unoptimized
                    />
                  ) : (
                    <div className="w-full h-full bg-purple-600 flex items-center justify-center text-white text-xs font-bold">
                      {session.user.name?.charAt(0) || 'U'}
                    </div>
                  )}
                </div>
                <div className="min-w-0">
                  <div className="text-xs font-bold text-slate-900 truncate">{session.user.name}</div>
                  <div className="text-[10px] text-slate-500 truncate">{session.user.email}</div>
                </div>
              </div>
              <span className="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full flex items-center gap-1">
                <ShieldCheck className="w-3 h-3" /> Active
              </span>
            </div>
          )}

          {/* Mobile nav items */}
          <nav className="flex-1 px-4 py-4 space-y-1">
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
                <div className="px-4 pt-4 pb-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
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
                Saved Favorites & Profile
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

          {/* Mobile Login / Logout CTA */}
          <div className="px-4 py-6 border-t border-slate-100 shrink-0">
            {status === 'authenticated' ? (
              <button
                onClick={() => {
                  setIsMobileOpen(false);
                  signOut({ callbackUrl: '/' });
                }}
                className="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-red-50 hover:bg-red-100 text-red-700 font-bold text-sm border border-red-200 transition-colors cursor-pointer"
              >
                <LogOut className="w-4 h-4" />
                Sign Out
              </button>
            ) : (
              <Link
                href="/login"
                onClick={() => setIsMobileOpen(false)}
                className="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold text-sm shadow-md cursor-pointer"
              >
                <GoogleIcon className="w-4 h-4 bg-white rounded-full p-0.5" />
                Sign In with Google
              </Link>
            )}
          </div>
        </div>
      )}
    </>
  );
};
