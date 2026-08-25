import { Palette, Gradient, ColorName } from '@/types';

const API_BASE = 'https://api.colormagic.techkreative.com';

/**
 * Fetches and formats the list of all curated palettes.
 */
export async function fetchPalettes(): Promise<Palette[]> {
  try {
    const res = await fetch(`${API_BASE}/palettes`, { next: { revalidate: 3600 } });
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    
    return (json.data || []).map((p: any) => {
      const slug = p.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') || p.id;
      const styleTag = p.style ? p.style.toLowerCase() : 'modern';
      return {
        id: p.id,
        slug,
        name: p.name,
        style: p.style || 'Modern',
        tags: [styleTag, 'curated', 'popular'],
        colors: p.colors,
      };
    });
  } catch (error) {
    console.error('Error fetching palettes from API:', error);
    return [];
  }
}

/**
 * Fetches and formats the list of all gradients.
 */
export async function fetchGradients(): Promise<Gradient[]> {
  try {
    const res = await fetch(`${API_BASE}/gradients`, { next: { revalidate: 3600 } });
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    return json.data || [];
  } catch (error) {
    console.error('Error fetching gradients from API:', error);
    return [];
  }
}

/**
 * Fetches and formats the list of all named colors.
 */
export async function fetchColors(): Promise<ColorName[]> {
  try {
    const res = await fetch(`${API_BASE}/colors`, { next: { revalidate: 3600 } });
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    
    // The API returns an object where keys are hex codes, so we extract the values.
    return Object.values(json.data || {}).map((item: any) => ({
      hex: item.hex.startsWith('#') ? item.hex : `#${item.hex}`,
      name: item.name,
      slug: item.slug,
      aliases: item.aliases || [],
    }));
  } catch (error) {
    console.error('Error fetching colors from API:', error);
    return [];
  }
}
