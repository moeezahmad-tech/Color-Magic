import React from 'react';
import { fetchGradients, fetchPalettes, fetchColors } from '@/lib/api-client';
import { Metadata } from 'next';
import GradientDetailClient from './GradientDetailClient';

export const revalidate = 2592000;

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://colormagic.techkreative.com';

interface Props {
  params: Promise<{
    id: string;
  }>;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const resolvedParams = await params;
  const allGradients = await fetchGradients();
  const gradient = allGradients.find((g) => g.id === resolvedParams.id);

  if (!gradient) {
    return {
      title: 'Gradient Not Found | Color Magic',
      robots: { index: false, follow: false }
    };
  }

  const colorList = gradient.colors.join(', ');
  const title = `${gradient.name} CSS Gradient — ${gradient.style} ${gradient.type} gradient`;
  const description = `${gradient.name} is a ${gradient.style} ${gradient.type} CSS gradient using colors ${colorList}. Copy the ready-to-use CSS code instantly — free on Color Magic.`;
  const canonicalUrl = `${siteUrl}/gradient/${gradient.id}/`;

  return {
    title,
    description,
    keywords: [
      `${gradient.name} gradient`,
      `${gradient.style} CSS gradient`,
      `${gradient.type} gradient`,
      'CSS gradient generator',
      'background gradient',
      colorList,
    ],
    alternates: { canonical: canonicalUrl },
    openGraph: {
      title: `${title} | Color Magic`,
      description,
      url: canonicalUrl,
      images: [{ url: `${siteUrl}/og-preview.png`, width: 1200, height: 630, alt: `${gradient.name} CSS gradient` }],
    },
    twitter: {
      card: 'summary_large_image',
      title: `${title} | Color Magic`,
      description,
    },
  };
}

export default async function GradientDetailPage({ params }: Props) {
  const { id } = await params;
  const allGradients = await fetchGradients();
  const gradient = allGradients.find((g) => g.id === id);

  if (!gradient) {
    return <div>Gradient not found</div>;
  }

  const allPalettes = await fetchPalettes();
  const allColors = await fetchColors();

  const relatedGradients = allGradients
    .filter((g) => g.style === gradient.style && g.id !== gradient.id)
    .slice(0, 6);

  if (relatedGradients.length < 6) {
    const additional = allGradients
      .filter((g) => g.id !== gradient.id && !relatedGradients.find((rg) => rg.id === g.id))
      .slice(0, 6 - relatedGradients.length);
    relatedGradients.push(...additional);
  }

  const relatedPalettes = allPalettes
    .filter((p) => p.style === gradient.style)
    .slice(0, 6);

  if (relatedPalettes.length < 6) {
    const additional = allPalettes
      .filter((p) => !relatedPalettes.find((rp) => rp.id === p.id))
      .slice(0, 6 - relatedPalettes.length);
    relatedPalettes.push(...additional);
  }

  const relatedColors = [...allColors].sort(() => 0.5 - Math.random()).slice(0, 12);

  return <GradientDetailClient gradient={gradient} relatedGradients={relatedGradients} relatedPalettes={relatedPalettes} relatedColors={relatedColors} />;
}
