import React from 'react';
import { Metadata } from 'next';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';
import ColorConverterClient from '@/components/ui/ColorConverterClient';

export const metadata: Metadata = {
  title: 'RGB to HSL Converter',
  description: 'Convert RGB color values to HSL instantly with real-time preview.',
};

export default function RgbToHslPage() {
  return (
    <div>
      <PageHeader 
        title="RGB to HSL Converter" 
        description="Convert RGB color values to HSL instantly."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <ColorConverterClient initialMode="rgb-to-hsl" />
    </div>
  );
}
