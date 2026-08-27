import React from 'react';
import { Metadata } from 'next';
import { PageHeader } from '@/components/ui/PageHeader';
import { ArrowLeftRight } from 'lucide-react';
import ColorConverterClient from '@/components/ui/ColorConverterClient';

export const metadata: Metadata = {
  title: 'Hex to RGB Converter',
  description: 'Convert hex color codes to RGB values instantly with real-time preview.',
};

export default function HexToRgbPage() {
  return (
    <div>
      <PageHeader 
        title="Hex to RGB Converter" 
        description="Convert hex color codes to RGB values instantly."
        icon={<ArrowLeftRight className="w-6 h-6" />}
      />
      <ColorConverterClient initialMode="hex-to-rgb" />
    </div>
  );
}
