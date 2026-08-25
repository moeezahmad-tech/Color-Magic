import { MetadataRoute } from 'next';
import { fetchPalettes, fetchGradients, fetchColors } from '@/lib/api-client';

const baseUrl = process.env.NEXT_PUBLIC_SITE_URL ?? 'https://colormagic.techkreative.com';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const allPalettes = await fetchPalettes();
  const allGradients = await fetchGradients();
  const allColorNames = await fetchColors();

  // ─── Static pages ────────────────────────────────────────────────────────────
  const staticRoutes: MetadataRoute.Sitemap = [
    { url: baseUrl, lastModified: new Date(), changeFrequency: 'weekly', priority: 1.0 },
    { url: `${baseUrl}/palettes`, lastModified: new Date(), changeFrequency: 'weekly', priority: 0.9 },
    { url: `${baseUrl}/gradients`, lastModified: new Date(), changeFrequency: 'weekly', priority: 0.9 },
    { url: `${baseUrl}/find-color`, lastModified: new Date(), changeFrequency: 'weekly', priority: 0.9 },
    { url: `${baseUrl}/generate-palette`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/palette-from-image`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/hex-to-color-name`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/hex-to-rgb`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/what-color-is`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/dark-color-finder`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/brand-color-lookup`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.8 },
    { url: `${baseUrl}/open-source`, lastModified: new Date(), changeFrequency: 'monthly', priority: 0.5 },
  ];

  // ─── Palette detail pages (/palette/[slug]) ───────────────────────────────
  const paletteRoutes: MetadataRoute.Sitemap = allPalettes.map((p) => ({
    url: `${baseUrl}/palette/${p.slug}/`,
    lastModified: new Date(),
    changeFrequency: 'monthly' as const,
    priority: 0.7,
  }));

  // ─── Gradient detail pages (/gradient/[id]) ───────────────────────────────
  const gradientRoutes: MetadataRoute.Sitemap = allGradients.map((g) => ({
    url: `${baseUrl}/gradient/${g.id}/`,
    lastModified: new Date(),
    changeFrequency: 'monthly' as const,
    priority: 0.7,
  }));

  // ─── Named color detail pages (/color/[hex]) — the 3 lakh pages ──────────
  // Generate a URL for every named color by hex
  const colorRoutes: MetadataRoute.Sitemap = allColorNames
    .filter((c) => c.hex && /^#[0-9A-Fa-f]{6}$/.test(c.hex))
    .map((c) => ({
      url: c.slug
        ? `${baseUrl}/color/${c.slug}/`
        : `${baseUrl}/color/${c.hex.replace('#', '').toLowerCase()}/`,
      lastModified: new Date(),
      changeFrequency: 'yearly' as const,
      priority: 0.6,
    }));

  return [...staticRoutes, ...paletteRoutes, ...gradientRoutes, ...colorRoutes];
}
