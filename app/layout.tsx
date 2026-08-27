import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';
import { Navbar } from '@/components/layout/Navbar';
import { Footer } from '@/components/layout/Footer';
import { ToastProvider } from '@/components/ui/ToastProvider';
import { VercelAnalytics } from '@/components/analytics/VercelAnalytics';
import { GoogleAnalytics } from '@next/third-parties/google';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-inter',
  display: 'swap',
});

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://colormagic.techkreative.com';

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl),
  title: {
    default: 'Color Name Finder & Palette Generator | Hex to Color Name — Color Magic',
    template: '%s | Color Magic',
  },
  description:
    'Free color name finder — convert any hex code to its color name, RGB, and HSL values. Generate professional color palettes and explore 150+ curated schemes.',
  keywords: [
    'color name finder',
    'hex to color name',
    'color palette generator',
    'hex color lookup',
    'RGB converter',
    'color code identifier',
    'Color Palettes',
    'CSS Gradients',
    'Color Theory Generator',
    'Image Color Extractor',
    'HEX to RGB',
    'Color Converter',
  ],
  authors: [{ name: 'Color Magic' }],
  creator: 'TechKreative',
  robots: { index: true, follow: true },
  openGraph: {
    type: 'website',
    siteName: 'Color Magic',
    title: 'Color Name Finder & Palette Generator | Hex to Color Name — Color Magic',
    description:
      'Free color name finder — convert any hex code to its color name, RGB, and HSL values. Generate professional palettes.',
    url: siteUrl,
    images: [
      {
        url: `${siteUrl}/og-preview.png`,
        width: 1200,
        height: 630,
        alt: 'Color Magic — Color Name Finder & Palette Generator',
      },
    ],
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Color Name Finder & Palette Generator | Color Magic',
    description:
      'Free color name finder — convert any hex code to its color name, RGB, and HSL values.',
    images: [`${siteUrl}/og-preview.png`],
  },
  icons: {
    icon: [
      { url: '/logo.png', type: 'image/png' },
    ],
    apple: '/logo.png',
    shortcut: '/logo.png',
  },
  manifest: '/manifest.json',
  verification: {
    yandex: '797f9c11121b55d1',
  },
};

import { AuthProvider } from '@/components/providers/AuthProvider';

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className={`${inter.variable} antialiased`}>
      <body
        suppressHydrationWarning
        className="bg-white text-slate-900 min-h-screen flex flex-col selection:bg-pink-500 selection:text-white"
      >
        <AuthProvider>
          <ToastProvider>
            <Navbar />
            <main className="flex-1 w-full">{children}</main>
            <Footer />
          </ToastProvider>
        </AuthProvider>
      </body>
      <GoogleAnalytics gaId={process.env.NEXT_PUBLIC_GA_ID || 'G-537L4MR968'} />
      <VercelAnalytics />
    </html>
  );
}
