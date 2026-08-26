import type { Metadata } from 'next';
import ProfileClient from './ProfileClient';
import { fetchPalettes, fetchGradients, fetchColors } from '@/lib/api-client';

export const metadata: Metadata = {
  title: 'Your Profile',
  description: 'Your Color Magic profile and saved color palettes, gradients, and colors.',
  alternates: { canonical: '/profile' },
  robots: { index: false, follow: false },
};

export default async function ProfilePage() {
  const [palettes, gradients, colors] = await Promise.all([
    fetchPalettes(),
    fetchGradients(),
    fetchColors(),
  ]);

  return <ProfileClient palettes={palettes} gradients={gradients} colors={colors} />;
}
