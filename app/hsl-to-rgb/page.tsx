import React from 'react';
import { Metadata } from 'next';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';
import ColorConverterClient from '@/components/ui/ColorConverterClient';

export const metadata: Metadata = {
  title: 'HSL to RGB Converter | Color Magic',
  description: 'Convert HSL color values to RGB instantly with real-time preview.',
};

export default function HslToRgbPage() {
  return (
    <div>
      <PageHeader 
        title="HSL to RGB Converter" 
        description="Convert HSL color values to RGB instantly."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <ColorConverterClient initialMode="hsl-to-rgb" />
    </div>
  );
}
