import type { Metadata } from 'next';
import ColorsClient from './ColorsClient';
import { fetchColors } from '@/lib/api-client';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
  title: 'Explore Named Colors & Color Codes',
  description:
    'Browse thousands of curated named colors with hex codes, RGB, HSL values, tints, shades, and accessible pairings on Color Magic — the open-source color tool for designers.',
  keywords: ['color explorer', 'named colors', 'color names', 'hex color codes', 'color picker', 'designer colors'],
  alternates: { canonical: '/colors' },
  openGraph: {
    title: 'Color Magic | Explore Named Colors & Color Codes',
    description: 'Browse thousands of curated named colors with hex codes, RGB, and HSL values.',
    url: '/colors',
  },
};

export default async function ColorsPage() {
  const colors = await fetchColors();
  const shuffledColors = [...colors].sort(() => 0.5 - Math.random());

  return <ColorsClient colors={shuffledColors} />;
}
