import type { Metadata } from 'next';
import FindColorClient from './FindColorClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { Search } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Color Name Finder from Hex Code',
  description:
    'Free hex to color name finder. Enter any hex code to instantly discover its color name, RGB, HSL values, contrast ratio, and accessibility info. Lookup 1000+ color names.',
  keywords: ['hex color finder', 'color name from hex', 'RGB HSL converter', 'color info tool'],
  alternates: { canonical: '/find-color' },
  openGraph: {
    title: 'Color Name Finder from Hex Code | Color Magic',
    description:
      'Free hex to color name finder. Enter any hex code to get color name, RGB, HSL and contrast info instantly.',
    url: '/find-color',
  },
};

import { fetchColors } from '@/lib/api-client';

export default async function FindColorPage() {
  const colors = await fetchColors();
  return (
    <div>
      <PageHeader 
        title="Find Color Name from Hex Code"
        description="Convert any hex code to its nearest human-readable color name instantly."
        icon={<Search className="w-6 h-6" />}
      />
      <FindColorClient colors={colors} />
    </div>
  );
}
