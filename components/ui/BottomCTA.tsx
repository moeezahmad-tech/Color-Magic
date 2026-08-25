'use client';

import React from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';

const CTAS = {
  default: {
    title: "Start Exploring Colors Today",
    desc: "Free, open-source, and always improving. No account needed — just instant color tools for designers and developers.",
    primary: { label: "Explore Palettes", href: "/palettes" },
    secondary: { label: "Find a Color", href: "/find-color" }
  },
  palettes: {
    title: "Need Custom Colors?",
    desc: "Generate your own unique color palettes using our AI-powered tool or extract them from your favorite images.",
    primary: { label: "Generate Palette", href: "/generate-palette" },
    secondary: { label: "Palette from Image", href: "/palette-from-image" }
  },
  converters: {
    title: "Explore More Tools",
    desc: "We offer a suite of color tools. Discover names for your hex codes or convert between formats instantly.",
    primary: { label: "Hex to Color Name", href: "/hex-to-color-name" },
    secondary: { label: "CSS Gradients", href: "/gradients" }
  },
  gradients: {
    title: "Looking for Solid Colors?",
    desc: "Check out our curated collections of beautiful color palettes for your next project.",
    primary: { label: "Explore Palettes", href: "/palettes" },
    secondary: { label: "Find a Color", href: "/find-color" }
  }
};

export function BottomCTA() {
  const pathname = usePathname() || '';

  let cta = CTAS.default;
  if (pathname.includes('/palettes')) {
    cta = CTAS.palettes;
  } else if (pathname.includes('/find-color') || pathname.includes('/hex-to')) {
    cta = CTAS.converters;
  } else if (pathname.includes('/gradients')) {
    cta = CTAS.gradients;
  } else if (pathname.includes('/generate-palette') || pathname.includes('/palette-from-image')) {
    cta = CTAS.default;
  }

  return (
    <section className="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 mt-12">
      <div className="bg-gradient-to-r from-purple-600 to-pink-500 rounded-3xl p-12 text-center text-white relative overflow-hidden">
        <div 
          aria-hidden 
          className="absolute inset-0 opacity-10" 
          style={{ backgroundImage: 'radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 50%, white 1px, transparent 1px)', backgroundSize: '60px 60px' }} 
        />
        <div className="relative">
          <h2 className="text-3xl sm:text-4xl font-black mb-4 tracking-tight">
            {cta.title}
          </h2>
          <p className="text-white/80 text-base mb-8 max-w-xl mx-auto">
            {cta.desc}
          </p>
          <div className="flex flex-wrap justify-center gap-4">
            <Link
              href={cta.primary.href}
              className="px-8 py-3.5 rounded-2xl bg-white text-purple-700 font-bold text-sm hover:bg-purple-50 transition-colors shadow-md"
            >
              {cta.primary.label}
            </Link>
            <Link
              href={cta.secondary.href}
              className="px-8 py-3.5 rounded-2xl bg-white/10 border border-white/30 text-white font-bold text-sm hover:bg-white/20 transition-colors"
            >
              {cta.secondary.label}
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}
