import type { Metadata } from 'next';
import BrandColorLookupClient from './BrandColorLookupClient';

export const metadata: Metadata = {
  title: 'Brand Color Lookup — Find Hex Codes for Popular Brand Colors',
  description:
    'Find exact hex codes and RGB values for popular brand colors. Google, Apple, Spotify, Netflix, Meta, and 25+ more brand color palettes.',
  keywords: [
    'brand colors',
    'brand color codes',
    'company hex codes',
    'Google color',
    'Apple color',
    'Spotify color',
    'brand identity colors',
  ],
  alternates: { canonical: '/brand-color-lookup' },
  openGraph: {
    title: 'Brand Color Lookup — Find Hex Codes for Popular Brand Colors | Color Magic',
    description:
      'Find exact hex codes and RGB values for popular brand colors. Google, Apple, Spotify, Netflix, and 25+ more.',
    url: '/brand-color-lookup',
  },
};

export default function BrandColorLookupPage() {
  return <BrandColorLookupClient />;
}
