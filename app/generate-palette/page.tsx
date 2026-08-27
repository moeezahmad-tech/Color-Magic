import type { Metadata } from 'next';
import GeneratePaletteClient from './GeneratePaletteClient';

export const metadata: Metadata = {
  title: 'Color Theory Palette Generator',
  description:
    'Generate professional palettes from one or more colors using color theory harmony rules — Monochromatic, Complementary, Triadic, Tetradic and Analogous.',
  keywords: ['color palette generator', 'color theory', 'hex code generator', 'color harmony'],
  alternates: { canonical: '/generate-palette' },
  openGraph: {
    title: 'Color Theory Palette Generator | Color Magic',
    description: 'Create professional 5-color palettes using color theory harmony rules.',
    url: '/generate-palette',
  },
};

export default function GeneratePalettePage() {
  return <GeneratePaletteClient />;
}
