import type { Metadata } from 'next';
import HexToColorNameClient from './HexToColorNameClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { Tag } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Hex to Color Name Converter — Find What Color is Your Hex Code',
  description:
    'Free hex to color name converter. Enter any hex code to instantly find its human-readable color name. Browse 1000+ named colors with hex, RGB, and HSL values.',
  keywords: [
    'hex to color name',
    'color name converter',
    'hex color lookup',
    'what color is this hex code',
    'named colors',
    'color identifier',
  ],
  alternates: { canonical: '/hex-to-color-name' },
  openGraph: {
    title: 'Hex to Color Name Converter — Find What Color is Your Hex Code | Color Magic',
    description:
      'Free hex to color name converter. Enter any hex code to instantly find its human-readable color name from 1000+ named colors.',
    url: '/hex-to-color-name',
  },
};

import { fetchColors } from '@/lib/api-client';

export default async function HexToColorNamePage() {
  const colors = await fetchColors();
  return (
    <div>
      <PageHeader 
        title="Hex to Color Name Converter"
        description="Instantly find the human-readable color name for any hex code from 1000+ named colors."
        icon={<Tag className="w-6 h-6" />}
      />
      <HexToColorNameClient colors={colors} />
    </div>
  );
}
