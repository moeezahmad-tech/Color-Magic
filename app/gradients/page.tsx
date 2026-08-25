import type { Metadata } from 'next';
import GradientsClient from './GradientsClient';

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

import { fetchGradients } from '@/lib/api-client';

export default async function GradientsPage() {
  const gradients = await fetchGradients();
  return <GradientsClient gradients={gradients} />;
}
