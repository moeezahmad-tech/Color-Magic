import type { Metadata } from 'next';
import PaletteFromImageClient from './PaletteFromImageClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { Image as ImageIcon, ShieldCheck } from 'lucide-react';

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
  return (
    <div>
      <PageHeader 
        title="Extract Color Palette from Image"
        description="Upload any image and instantly extract a beautiful color palette."
        icon={<ImageIcon className="w-6 h-6" />}
        action={
          <span className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 text-xs font-bold shadow-2xs">
            <ShieldCheck className="w-4 h-4" /> 100% Client-Side Privacy
          </span>
        }
      />
      <PaletteFromImageClient />
    </div>
  );
}
