import type { Metadata } from 'next';
import PaletteFromImageClient from './PaletteFromImageClient';

export const metadata: Metadata = {
  title: 'Extract Color Palette from Image',
  description:
    'Upload any image and instantly extract a beautiful color palette. Get hex, RGB, and HSL values for every dominant color — all processed in your browser.',
  keywords: ['palette from image', 'extract colors from photo', 'image color picker', 'dominant colors'],
  alternates: { canonical: '/palette-from-image' },
  openGraph: {
    title: 'Color Magic | Extract Palette from Image',
    description:
      'Upload an image and extract dominant colors instantly — hex, RGB, HSL values ready to copy.',
    url: '/palette-from-image',
  },
};

export default function PaletteFromImagePage() {
  return <PaletteFromImageClient />;
}
