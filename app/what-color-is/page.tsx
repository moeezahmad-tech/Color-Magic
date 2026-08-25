import type { Metadata } from 'next';
import WhatColorIsClient from './WhatColorIsClient';

export const metadata: Metadata = {
  title: 'What Color Is This Code? — Online Hex Color Identifier',
  description:
    'Paste any hex code and we\'ll tell you exactly what color it is. Free online hex color identifier with 20 common examples organized by category.',
  keywords: ['what color is this', 'hex color identifier', 'identify color from code', 'color lookup tool'],
  alternates: { canonical: '/what-color-is' },
  openGraph: {
    title: 'What Color Is This Code? — Online Hex Color Identifier | Color Magic',
    description: 'Paste any hex code and we\'ll tell you exactly what color it is.',
    url: '/what-color-is',
  },
};

import { fetchColors } from '@/lib/api-client';

export default async function WhatColorIsPage() {
  const colors = await fetchColors();
  return <WhatColorIsClient colors={colors} />;
}
