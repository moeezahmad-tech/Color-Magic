import React from 'next';
import { fetchPalettes, fetchColors, fetchGradients } from '@/lib/api-client';
import { Metadata } from 'next';
import PaletteDetailClient from './PaletteDetailClient';

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://colormagic.techkreative.com';

interface Props {
  params: Promise<{
    slug: string;
  }>;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const resolvedParams = await params;
  const allPalettes = await fetchPalettes();
  const palette = allPalettes.find((p) => p.slug === resolvedParams.slug);

  if (!palette) {
    return {
      title: 'Palette Not Found | Color Magic',
      robots: { index: false, follow: false }
    };
  }

  const colorList = palette.colors.slice(0, 5).join(', ');
  const title = `${palette.name} Color Palette — ${palette.style} Color Scheme`;
  const description = `Explore the ${palette.name} color palette featuring ${colorList}. Copy-ready HEX, RGB, CSS Variables and Tailwind config. ${palette.style} color scheme for designers.`;
  const canonicalUrl = `${siteUrl}/palette/${palette.slug}/`;

  return {
    title,
    description,
    keywords: [
      `${palette.name} color palette`,
      `${palette.style} color scheme`,
      `${palette.name} hex codes`,
      'color palette',
      'design colors',
      palette.style,
    ],
    alternates: { canonical: canonicalUrl },
    openGraph: {
      title: `${title} | Color Magic`,
      description,
      url: canonicalUrl,
      images: [{ url: `${siteUrl}/og-preview.png`, width: 1200, height: 630, alt: `${palette.name} color palette` }],
    },
    twitter: {
      card: 'summary_large_image',
      title: `${title} | Color Magic`,
      description,
    },
  };
}

export default async function PaletteDetailPage({ params }: Props) {
  const { slug } = await params;
  const allPalettes = await fetchPalettes();
  const allColorNames = await fetchColors();
  const allGradients = await fetchGradients();
  const palette = allPalettes.find((p) => p.slug === slug);

  if (!palette) {
    return <div>Palette not found</div>;
  }

  const relatedPalettes = allPalettes
    .filter((p) => p.style === palette.style && p.id !== palette.id)
    .slice(0, 6);

  if (relatedPalettes.length < 6) {
    const additional = allPalettes
      .filter((p) => p.id !== palette.id && !relatedPalettes.find((rp) => rp.id === p.id))
      .slice(0, 6 - relatedPalettes.length);
    relatedPalettes.push(...additional);
  }

  const relatedGradients = allGradients
    .filter((g) => g.style === palette.style)
    .slice(0, 6);

  if (relatedGradients.length < 6) {
    const additional = allGradients
      .filter((g) => !relatedGradients.find((rg) => rg.id === g.id))
      .slice(0, 6 - relatedGradients.length);
    relatedGradients.push(...additional);
  }

  const relatedColors = [...allColorNames].sort(() => 0.5 - Math.random()).slice(0, 12);

  return <PaletteDetailClient palette={palette} colors={allColorNames} relatedPalettes={relatedPalettes} relatedGradients={relatedGradients} relatedColors={relatedColors} />;
}
