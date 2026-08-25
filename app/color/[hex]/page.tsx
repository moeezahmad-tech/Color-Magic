import type { Metadata } from 'next';
import { fetchColors, fetchPalettes, fetchGradients } from '@/lib/api-client';
import { normalizeHex, hexToRgb, rgbToHsl, findClosestColorName } from '@/lib/color-math';
import ColorDetailClient from './ColorDetailClient';

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://colormagic.techkreative.com';

interface Props {
  params: Promise<{ hex: string }>;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { hex: rawHex } = await params;
  const allColorNames = await fetchColors();
  const normalized = normalizeHex(rawHex);
  const rgb = hexToRgb(normalized);
  const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
  const closest = findClosestColorName(normalized, allColorNames);
  const hexWithHash = normalized;

  const title = `${closest.name} ${hexWithHash} Color Details, RGB, HSL and Related Palettes`;
  const description = `Explore ${closest.name} (${hexWithHash}) with RGB(${rgb.r}, ${rgb.g}, ${rgb.b}) and HSL(${hsl.h}, ${hsl.s}%, ${hsl.l}%). Discover related color palettes, tints, shades, harmonies, and CSS values — all free on Color Magic.`;
  const canonicalUrl = `${siteUrl}/color/${rawHex.toLowerCase()}/`;
  const ogImage = `${siteUrl}/colors/${normalized.replace('#', '').toLowerCase()}.webp`;

  return {
    title,
    description,
    keywords: [
      `${closest.name} color`,
      `${hexWithHash} hex code`,
      `${hexWithHash} color name`,
      `${closest.name} RGB`,
      `${closest.name} HSL`,
      'hex color details',
      'color palette',
    ],
    alternates: { canonical: canonicalUrl },
    openGraph: {
      title: `${title} | Color Magic`,
      description,
      url: canonicalUrl,
      images: [{ url: ogImage, width: 600, height: 600, alt: `${closest.name} color swatch` }],
    },
    twitter: {
      card: 'summary_large_image',
      title: `${title} | Color Magic`,
      description,
      images: [ogImage],
    },
  };
}

export default async function ColorDetailPage({ params }: Props) {
  const { hex: rawHex } = await params;
  const colors = await fetchColors();
  const allPalettes = await fetchPalettes();
  const allGradients = await fetchGradients();

  const hexUpper = '#' + rawHex.toUpperCase().replace('#', '');
  
  const relatedPalettes = allPalettes
    .filter((p) => p.colors.some((c) => c.toUpperCase() === hexUpper))
    .slice(0, 6);
  if (relatedPalettes.length < 6) {
    const additional = [...allPalettes].filter(p => !relatedPalettes.find(rp => rp.id === p.id)).sort(() => 0.5 - Math.random()).slice(0, 6 - relatedPalettes.length);
    relatedPalettes.push(...additional);
  }

  const relatedGradients = allGradients
    .filter((g) => g.colors.some((c) => c.toUpperCase() === hexUpper))
    .slice(0, 6);
  if (relatedGradients.length < 6) {
    const additional = [...allGradients].filter(g => !relatedGradients.find(rg => rg.id === g.id)).sort(() => 0.5 - Math.random()).slice(0, 6 - relatedGradients.length);
    relatedGradients.push(...additional);
  }

  const relatedColors = [...colors].sort(() => 0.5 - Math.random()).slice(0, 12);

  return <ColorDetailClient hex={rawHex} colors={colors} relatedColors={relatedColors} relatedPalettes={relatedPalettes} relatedGradients={relatedGradients} />;
}
