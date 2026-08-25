import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { GithubIcon } from '@/components/ui/Icons';
import { BottomCTA } from '@/components/ui/BottomCTA';

const footerLinks = {
  Explore: [
    { href: '/palettes', label: 'Color Palettes' },
    { href: '/gradients', label: 'CSS Gradients' },
    { href: '/generate-palette', label: 'Generate Palette' },
    { href: '/palette-from-image', label: 'Palette from Image' },
  ],
  Converters: [
    { href: '/find-color', label: 'Find Color' },
    { href: '/hex-to-color-name', label: 'Hex to Color Name' },
    { href: '/hex-to-rgb', label: 'Hex to RGB' },
    { href: '/what-color-is', label: 'What Color Is This?' },
    { href: '/dark-color-finder', label: 'Dark Color Finder' },
    { href: '/brand-color-lookup', label: 'Brand Colors' },
  ],
  Resources: [
    { href: '/open-source', label: 'Open Source' },
    { href: '/profile', label: 'Saved Favorites' },
    { href: '/login', label: 'Sign In' },
  ],
};

export const Footer = () => {
  return (
    <>
      <BottomCTA />
      <footer className="w-full bg-slate-50 border-t border-slate-100 text-slate-600 mt-auto">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
          {/* Top: Brand + Links grid */}
        <div className="grid grid-cols-1 md:grid-cols-5 gap-10 mb-14">
          {/* Brand Column */}
          <div className="md:col-span-2 space-y-5">
            <Link href="/" className="inline-flex items-center gap-2.5">
              <Image
                src="/logo.png"
                alt="Color Magic Logo"
                width={32}
                height={32}
                className="w-8 h-8 object-contain"
              />
              <span className="text-xl font-bold tracking-tight text-slate-900">
                Color{' '}
                <span className="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                  Magic
                </span>
              </span>
            </Link>
            <p className="text-sm text-slate-500 leading-relaxed max-w-xs">
              Free, open-source color tools for designers and developers. Find color names, generate palettes, and explore CSS gradients.
            </p>
            <div className="flex items-center gap-3">
              <a
                href="https://github.com/moeezahmad-tech/Color-Magic"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors"
              >
                <GithubIcon className="w-3.5 h-3.5" />
                GitHub
              </a>

            </div>
          </div>

          {/* Link Columns */}
          {Object.entries(footerLinks).map(([title, links]) => (
            <div key={title}>
              <h4 className="text-xs font-black text-slate-900 uppercase tracking-widest mb-5">
                {title}
              </h4>
              <ul className="space-y-3">
                {links.map(({ href, label }) => (
                  <li key={href}>
                    <Link
                      href={href}
                      className="text-sm text-slate-500 hover:text-purple-600 transition-colors font-medium"
                    >
                      {label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Bottom bar */}
        <div className="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
          <p>
            © {new Date().getFullYear()} Color Magic. Powered by{' '}
            <a
              href="https://techkreative.com"
              target="_blank"
              rel="noopener noreferrer"
              className="font-bold text-pink-500 hover:underline"
            >
              TechKreative
            </a>
            . MIT License.
          </p>
          <div className="flex items-center gap-5">
            <Link href="/palettes" className="hover:text-slate-700 transition-colors font-medium">Palettes</Link>
            <Link href="/gradients" className="hover:text-slate-700 transition-colors font-medium">Gradients</Link>
            <Link href="/open-source" className="hover:text-slate-700 transition-colors font-medium">Open Source</Link>
          </div>
        </div>
      </div>
    </footer>
    </>
  );
};
