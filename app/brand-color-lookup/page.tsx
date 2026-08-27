import type { Metadata } from 'next';
import BrandColorLookupClient from './BrandColorLookupClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { Briefcase } from 'lucide-react';

export const metadata: Metadata = {
  title: 'Brand Color Lookup',
  description:
    'Find exact hex codes and RGB values for popular brand colors. Google, Apple, Spotify, Netflix, Meta, and 25+ more brand color palettes.',
  keywords: [
    'brand colors',
    'brand color codes',
    'company hex codes',
    'Google color',
    'Apple color',
    'Spotify color',
    'brand identity colors',
  ],
  alternates: { canonical: '/brand-color-lookup' },
  openGraph: {
    title: 'Brand Color Lookup | Color Magic',
    description: 'Find exact hex codes and RGB values for popular brand colors.',
    url: '/brand-color-lookup',
  },
};

export default function BrandColorLookupPage() {
  return (
    <div>
      <PageHeader 
        title="Brand Color Lookup"
        description="Find exact hex codes and RGB values for popular brand colors like Google, Apple, and Spotify."
        icon={<Briefcase className="w-6 h-6" />}
      />
      <BrandColorLookupClient />
    </div>
  );
}
