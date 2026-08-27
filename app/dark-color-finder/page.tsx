import type { Metadata } from 'next';
import DarkColorFinderClient from './DarkColorFinderClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { Moon } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Dark Color Name Finder',
  description:
    'Find names for dark and deep color shades. Browse 50+ dark colors organized by hue family — dark reds, blues, greens, purples, and more.',
  keywords: ['dark color names', 'dark shade finder', 'deep color names', 'dark hex colors', 'midnight blue'],
  alternates: { canonical: '/dark-color-finder' },
  openGraph: {
    title: 'Dark Color Name Finder | Color Magic',
    description: 'Find names for dark and deep color shades. Browse 50+ dark colors organized by hue family.',
    url: '/dark-color-finder',
  },
};

import { fetchPalettes } from '@/lib/api-client';

export default async function DarkColorFinderPage() {
  const palettes = await fetchPalettes();
  return (
    <div>
      <PageHeader 
        title="Dark Color Name Finder"
        description="Discover names for deep, dark shades and midnight tones organized by hue family."
        icon={<Moon className="w-6 h-6" />}
      />
      <DarkColorFinderClient palettes={palettes} />
    </div>
  );
}
