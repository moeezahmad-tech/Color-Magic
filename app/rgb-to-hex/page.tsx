import React from 'react';
import { Metadata } from 'next';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';
import ColorConverterClient from '@/components/ui/ColorConverterClient';

export const metadata: Metadata = {
  title: 'RGB to Hex Converter',
  description: 'Convert RGB color values to hexadecimal codes instantly with real-time preview.',
};

export default function RgbToHexPage() {
  return (
    <div>
      <PageHeader 
        title="RGB to Hex Converter" 
        description="Convert RGB color values to hexadecimal codes instantly."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <ColorConverterClient initialMode="rgb-to-hex" />
    </div>
  );
}
