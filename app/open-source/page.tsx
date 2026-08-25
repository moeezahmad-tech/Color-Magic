import type { Metadata } from 'next';
import React from 'react';
import { Heart, Code2, ShieldCheck, Award, GitFork, GitBranch, GitPullRequest, Search, Palette, Wand2, Image, Sparkles, Layers, Terminal, Users, GitMerge, Wrench } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Open Source | Color Magic - Professional Color Palette Generator',
  description:
    'Color Magic is an open-source color palette generator and explorer. Browse the code, contribute, and help make color selection easier for designers worldwide.',
  keywords: ['open source', 'color palette', 'GitHub', 'color tool', 'design tool', 'contribute'],
  alternates: { canonical: '/open-source' },
  openGraph: {
    title: 'Open Source | Color Magic',
    description: 'Color Magic is an open-source project. Explore the code and contribute.',
    url: '/open-source',
  },
};
import { GithubIcon } from '@/components/ui/Icons';

export default function OpenSourcePage() {
  return (
    <div className="max-w-4xl mx-auto px-4 py-16 space-y-12">
      {/* Header */}
      <div className="text-center space-y-3">
        <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-pink-50 text-pink-500 rounded-full border border-pink-100 text-xs font-bold">
          <GithubIcon className="w-3.5 h-3.5 text-pink-500" /> Open Source & Community
        </span>
        <h1 className="text-4xl sm:text-5xl font-black tracking-tight bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
          Color Magic
        </h1>
        <p className="text-lg font-semibold text-pink-500">Professional Color Palette Generator &amp; Explorer</p>
        <p className="text-slate-500 text-xs font-bold uppercase tracking-wider">Founded by TechKreative</p>
      </div>

      {/* About the Project */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 space-y-3 shadow-xs">
        <h2 className="text-2xl font-extrabold text-slate-900">About the Project</h2>
        <p className="text-slate-600 text-sm leading-relaxed">
          Color Magic is a beautiful, fully-featured web application designed for designers and developers to explore color
          palettes, find color names from hex codes, and generate professional color schemes using color theory principles.
        </p>
        <p className="text-slate-600 text-sm leading-relaxed">
          Built with modern web technologies, this project demonstrates how powerful web applications can be created while
          maintaining excellent performance and user experience. The current version is rewritten in Next.js, TypeScript,
          and Tailwind CSS — open source under the MIT License.
        </p>
        <a
          href="https://github.com/moeezahmad-tech/Color-Magic"
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center gap-2 mt-2 px-5 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm font-bold rounded-xl transition-all hover:shadow-md"
        >
          <GithubIcon className="w-4 h-4" />
          View on GitHub
        </a>
      </div>

      {/* Grid Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div className="bg-white border border-slate-100 rounded-3xl p-6 text-center shadow-xs">
          <Code2 className="w-8 h-8 text-pink-500 mx-auto mb-2" />
          <div className="text-3xl font-black text-slate-900">100%</div>
          <div className="text-xs text-slate-500 font-bold uppercase mt-1">
            TypeScript &amp; Client Canvas Math
          </div>
        </div>

        <div className="bg-white border border-slate-100 rounded-3xl p-6 text-center shadow-xs">
          <ShieldCheck className="w-8 h-8 text-emerald-500 mx-auto mb-2" />
          <div className="text-3xl font-black text-slate-900">MIT</div>
          <div className="text-xs text-slate-500 font-bold uppercase mt-1">
            Free &amp; Open Source License
          </div>
        </div>

        <div className="bg-white border border-slate-100 rounded-3xl p-6 text-center shadow-xs">
          <Award className="w-8 h-8 text-purple-500 mx-auto mb-2" />
          <div className="text-3xl font-black text-slate-900">250+</div>
          <div className="text-xs text-slate-500 font-bold uppercase mt-1">
            Curated Swatches &amp; Gradients
          </div>
        </div>
      </div>

      {/* Features */}
      <div className="space-y-4">
        <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
          <Sparkles className="w-6 h-6 text-pink-500" /> Features
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {/* Find Color */}
          <div className="bg-white border border-slate-100 rounded-3xl p-6 space-y-2 shadow-xs">
            <div className="flex items-center gap-2 text-pink-500 font-bold">
              <Search className="w-5 h-5" /> Find Color
            </div>
            <ul className="text-sm text-slate-600 space-y-1 list-disc list-inside">
              <li>Enter any hex code to discover color information</li>
              <li>Get color names from 300+ named colors</li>
              <li>View RGB, HSL values, luminance, and contrast</li>
              <li>Copy hex and RGB values with one click</li>
            </ul>
          </div>

          {/* Explore Palettes */}
          <div className="bg-white border border-slate-100 rounded-3xl p-6 space-y-2 shadow-xs">
            <div className="flex items-center gap-2 text-purple-500 font-bold">
              <Palette className="w-5 h-5" /> Explore Palettes
            </div>
            <ul className="text-sm text-slate-600 space-y-1 list-disc list-inside">
              <li>Browse 150+ professionally curated palettes</li>
              <li>Filter by style: Pastel, Vintage, Neon, Minimalist</li>
              <li>Search palettes by name, theme, or hex code</li>
              <li>Copy entire palettes to clipboard</li>
            </ul>
          </div>

          {/* Generate Palette */}
          <div className="bg-white border border-slate-100 rounded-3xl p-6 space-y-2 shadow-xs">
            <div className="flex items-center gap-2 text-emerald-500 font-bold">
              <Wand2 className="w-5 h-5" /> Generate Palette
            </div>
            <ul className="text-sm text-slate-600 space-y-1 list-disc list-inside">
              <li>Create 5-color palettes from any base color</li>
              <li>Choose from color theory schemes</li>
              <li>Apply variations: Classic, Soft &amp; Muted, Deep &amp; Bold</li>
              <li>All calculations done locally — no API required</li>
            </ul>
          </div>

          {/* Palette from Image */}
          <div className="bg-white border border-slate-100 rounded-3xl p-6 space-y-2 shadow-xs">
            <div className="flex items-center gap-2 text-orange-500 font-bold">
              <Image className="w-5 h-5" /> Palette from Image
            </div>
            <ul className="text-sm text-slate-600 space-y-1 list-disc list-inside">
              <li>Extract dominant colors from any image</li>
              <li>K-means clustering for accurate results</li>
              <li>Works entirely in the browser — no uploads</li>
              <li>Copy extracted swatches instantly</li>
            </ul>
          </div>
        </div>
      </div>

      {/* Tech Stack */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 space-y-4 shadow-xs">
        <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
          <Wrench className="w-6 h-6 text-slate-500" /> Tech Stack
        </h2>
        <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
          {[
            { label: 'Next.js', desc: 'App Router framework' },
            { label: 'TypeScript', desc: 'Type-safe codebase' },
            { label: 'Tailwind CSS', desc: 'Utility-first styling' },
            { label: 'Lucide Icons', desc: 'Icon library' },
            { label: 'next-auth', desc: 'Authentication' },
            { label: 'Canvas API', desc: 'Client-side color math' },
          ].map(({ label, desc }) => (
            <div key={label} className="bg-slate-50 rounded-2xl px-4 py-3">
              <div className="text-sm font-extrabold text-slate-900">{label}</div>
              <div className="text-xs text-slate-500 mt-0.5">{desc}</div>
            </div>
          ))}
        </div>
      </div>

      {/* Getting Started */}
      <div className="bg-gradient-to-r from-purple-500 to-pink-500 rounded-3xl p-8 space-y-4">
        <h2 className="text-2xl font-extrabold text-white flex items-center gap-2">
          <Terminal className="w-6 h-6 text-white/90" /> Getting Started
        </h2>
        <p className="text-white/80 text-sm">Clone the repository and start exploring the code:</p>
        <pre className="bg-white/10 rounded-2xl px-5 py-4 text-sm text-white overflow-x-auto">
          <code>{`git clone https://github.com/moeezahmad-tech/Color-Magic.git\ncd Color-Magic\nnpm install\nnpm run dev`}</code>
        </pre>
        <p className="text-white/70 text-xs">Requires Node.js 18+. Open http://localhost:3000 in your browser.</p>
      </div>

      {/* Contributors */}
      <div className="space-y-4">
        <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
          <Users className="w-6 h-6 text-purple-500" /> Top Contributors
        </h2>
        <p className="text-slate-500 text-sm">Meet the amazing people who have contributed to Color Magic</p>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {/* Lead contributor */}
          <div className="bg-[#FFF5F7] border border-pink-100 rounded-3xl p-6 flex items-center gap-4 shadow-xs">
            <div className="w-14 h-14 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center shrink-0 shadow-md">
              <Heart className="w-7 h-7 text-white fill-white" />
            </div>
            <div>
              <span className="text-xs font-bold text-pink-500 uppercase tracking-wider block">
                Founder &amp; Lead Developer
              </span>
              <h3 className="text-lg font-black text-slate-900">Moeez Ahmad</h3>
              <p className="text-slate-500 text-xs">@moeezahmad-tech</p>
              <p className="text-slate-600 text-xs mt-1 leading-relaxed">
                Full-stack software architect specializing in modern web ecosystems, high-performance UI systems, and designer tools.
              </p>
            </div>
          </div>

          {/* Be a contributor CTA */}
          <a
            href="https://github.com/moeezahmad-tech/Color-Magic/graphs/contributors"
            target="_blank"
            rel="noopener noreferrer"
            className="bg-white border-2 border-dashed border-slate-200 hover:border-pink-300 rounded-3xl p-6 flex flex-col items-center justify-center gap-2 text-center transition-colors group"
          >
            <div className="w-14 h-14 rounded-full bg-slate-100 group-hover:bg-pink-50 flex items-center justify-center shrink-0 transition-colors">
              <GithubIcon className="w-7 h-7 text-slate-400 group-hover:text-pink-400 transition-colors" />
            </div>
            <span className="text-sm font-bold text-slate-700 group-hover:text-pink-500 transition-colors">Your Name Here</span>
            <span className="text-xs text-slate-400">Be a contributor!</span>
          </a>
        </div>
        <a
          href="https://github.com/moeezahmad-tech/Color-Magic/graphs/contributors"
          target="_blank"
          rel="noopener noreferrer"
          className="inline-flex items-center gap-2 text-xs text-slate-500 hover:text-pink-500 font-semibold transition-colors"
        >
          <GithubIcon className="w-3.5 h-3.5" /> View all contributors on GitHub
        </a>
      </div>

      {/* Contributing */}
      <div className="bg-white border border-slate-100 rounded-3xl p-8 space-y-5 shadow-xs">
        <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
          <GitMerge className="w-6 h-6 text-emerald-500" /> Contributing
        </h2>
        <p className="text-slate-600 text-sm">
          Contributions are welcome! Here&apos;s how you can help make Color Magic even better:
        </p>
        <ol className="space-y-3">
          {[
            { step: '1', title: 'Fork the Repository', desc: 'Create your own fork of the project' },
            { step: '2', title: 'Create a Feature Branch', desc: 'Work on your changes in a dedicated branch' },
            { step: '3', title: 'Make Your Changes', desc: 'Add features, fix bugs, or improve documentation' },
            { step: '4', title: 'Submit a Pull Request', desc: 'Open a PR with a clear description of your changes' },
          ].map(({ step, title, desc }) => (
            <li key={step} className="flex items-start gap-4">
              <span className="w-8 h-8 rounded-full bg-pink-100 text-pink-600 font-black text-sm flex items-center justify-center shrink-0">
                {step}
              </span>
              <div>
                <div className="text-sm font-bold text-slate-900">{title}</div>
                <div className="text-xs text-slate-500 mt-0.5">{desc}</div>
              </div>
            </li>
          ))}
        </ol>
        <p className="text-slate-500 text-xs">
          Feel free to open issues for bug reports, feature requests, or questions about the project.
        </p>
      </div>

      {/* Try Our Tools */}
      <div className="space-y-4">
        <h2 className="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
          <Layers className="w-6 h-6 text-pink-500" /> Try Our Tools
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {[
            { href: '/find-color', icon: <Search className="w-5 h-5" />, color: 'text-pink-500', label: 'Find Color', desc: 'Enter any hex code and get its name, RGB, HSL and more' },
            { href: '/hex-to-color-name', icon: <Code2 className="w-5 h-5" />, color: 'text-purple-500', label: 'Hex to Color Name', desc: 'Convert any hex code to a human-readable color name' },
            { href: '/palettes', icon: <Palette className="w-5 h-5" />, color: 'text-emerald-500', label: 'Explore Palettes', desc: 'Browse 150+ curated color palettes' },
            { href: '/generate-palette', icon: <Wand2 className="w-5 h-5" />, color: 'text-orange-500', label: 'Generate Palette', desc: 'Create color schemes using color theory' },
          ].map(({ href, icon, color, label, desc }) => (
            <a
              key={href}
              href={href}
              className="bg-white border border-slate-100 hover:border-pink-200 rounded-3xl p-5 flex items-center gap-4 shadow-xs transition-colors group"
            >
              <span className={`${color} group-hover:scale-110 transition-transform`}>{icon}</span>
              <div>
                <div className="text-sm font-bold text-slate-900">{label}</div>
                <div className="text-xs text-slate-500 mt-0.5">{desc}</div>
              </div>
            </a>
          ))}
        </div>
      </div>

      {/* License terms */}
      <article className="bg-white border border-slate-100 rounded-3xl p-8 text-slate-600 space-y-3">
        <h3 className="text-xl font-extrabold text-slate-900">MIT License Overview</h3>
        <p className="text-sm leading-relaxed">
          Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
          documentation files, to deal in the Software without restriction, including without limitation the rights to use,
          copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software.
        </p>
      </article>
    </div>
  );
}
