import type { Metadata } from 'next';
import PalettesClient from './PalettesClient';

export const metadata: Metadata = {
  title: 'Explore Color Palettes',
  description:
    'Explore thousands of professional color palettes with Color Magic — the open-source color tool for designers. Filter by Pastel, Vintage, Neon, Minimalist, Earthy, and more.',
  keywords: ['color palette explorer', 'color schemes', 'designer tools', 'color palettes'],
  alternates: { canonical: '/palettes' },
  openGraph: {
    title: 'Color Magic | Explore Color Palettes',
    description: 'Explore thousands of professional color palettes for designers and developers.',
    url: '/palettes',
  },
};

import { fetchPalettes } from '@/lib/api-client';

export default async function PalettesPage() {
  const palettes = await fetchPalettes();
  return <PalettesClient palettes={palettes} />;
}
