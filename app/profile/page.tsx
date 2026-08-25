import type { Metadata } from 'next';
import ProfileClient from './ProfileClient';

export const metadata: Metadata = {
  title: 'Your Profile',
  description: 'Your Color Magic profile and saved color palettes and gradients.',
  alternates: { canonical: '/profile' },
  robots: { index: false, follow: false },
};

import { fetchPalettes, fetchGradients } from '@/lib/api-client';

export default async function ProfilePage() {
  const palettes = await fetchPalettes();
  const gradients = await fetchGradients();
  return <ProfileClient palettes={palettes} gradients={gradients} />;
}
