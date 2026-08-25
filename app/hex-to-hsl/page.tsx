import React from 'react';
import { Metadata } from 'next';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';
import ColorConverterClient from '@/components/ui/ColorConverterClient';

export const metadata: Metadata = {
  title: 'Hex to HSL Converter | Color Magic',
  description: 'Convert Hex color codes to HSL (Hue, Saturation, Lightness) instantly with real-time preview.',
};

export default function HexToHslPage() {
  return (
    <div>
      <PageHeader 
        title="Hex to HSL Converter" 
        description="Convert Hex color codes to HSL instantly."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <ColorConverterClient initialMode="hex-to-hsl" />
    </div>
  );
}
