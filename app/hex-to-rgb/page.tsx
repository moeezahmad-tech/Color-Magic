import type { Metadata } from 'next';
import HexToRgbClient from './HexToRgbClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Hex to RGB Converter — Free Online Color Code Conversion',
  description:
    'Free hex to RGB converter. Instantly convert hex color codes to RGB values and vice versa. Live color preview, conversion formula, and example conversions table.',
  keywords: ['hex to RGB', 'RGB converter', 'hex to RGB converter', 'color code conversion', 'RGB to hex'],
  alternates: { canonical: '/hex-to-rgb' },
  openGraph: {
    title: 'Hex to RGB Converter — Free Online Color Code Conversion | Color Magic',
    description:
      'Free hex to RGB converter. Instantly convert hex color codes to RGB values and vice versa with live color preview.',
    url: '/hex-to-rgb',
  },
};

export default function HexToRgbPage() {
  return (
    <div>
      <PageHeader 
        title="Hex to RGB Converter"
        description="Instantly convert hex color codes to RGB values and vice versa with live preview."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <HexToRgbClient />
    </div>
  );
}
