import type { Metadata } from 'next';
import GradientsClient from './GradientsClient';
import { PageHeader } from '@/components/ui/PageHeader';
import { Layers } from 'lucide-react';
import { fetchGradients } from '@/lib/api-client';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
  title: 'Explore CSS Gradients',
  description:
    'Browse hundreds of beautiful CSS gradients — linear, radial, and mesh. Copy ready-to-use CSS for your projects.',
  keywords: ['css gradients', 'linear gradient', 'radial gradient', 'design tool'],
  alternates: { canonical: '/gradients' },
  openGraph: {
    title: 'Color Magic | Explore CSS Gradients',
    description: 'Hundreds of beautiful CSS gradients — linear and radial — ready to copy.',
    url: '/gradients',
  },
};

export default async function GradientsPage() {
  const gradients = await fetchGradients();
  const shuffledGradients = [...gradients].sort(() => 0.5 - Math.random());
  return (
    <div>
      <PageHeader 
        title="Explore CSS Gradients"
        description="Browse hundreds of beautiful CSS gradients — linear, radial, and mesh. Ready to copy."
        icon={<Layers className="w-6 h-6" />}
      />
      <GradientsClient gradients={shuffledGradients} />
    </div>
  );
}


