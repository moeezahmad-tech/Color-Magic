import React from 'react';
import { Metadata } from 'next';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';
import ColorConverterClient from '@/components/ui/ColorConverterClient';

export const metadata: Metadata = {
  title: 'HSL to Hex Converter',
  description: 'Convert HSL (Hue, Saturation, Lightness) color values to Hexadecimal codes instantly with real-time preview.',
};

export default function HslToHexPage() {
  return (
    <div>
      <PageHeader 
        title="HSL to Hex Converter" 
        description="Convert HSL color values to Hexadecimal codes instantly."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <ColorConverterClient initialMode="hsl-to-hex" />
    </div>
  );
}
