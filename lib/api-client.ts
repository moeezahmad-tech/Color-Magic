import { Palette, Gradient, ColorName } from '@/types';

const API_BASE = 'https://api.colormagic.techkreative.com';

/**
 * Fetches and formats the list of all curated palettes.
 */
export async function fetchPalettes(): Promise<Palette[]> {
  try {
    const res = await fetch(`${API_BASE}/palettes`, { 
      next: { revalidate: 3600 },
      headers: { 'Content-Type': 'application/json' }
    });
    
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    
    return (json.data || []).map((p: any) => {
      const slug = p.name ? p.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') : String(p.id);
      const styleTag = p.style ? p.style.toLowerCase() : 'modern';
      return {
        id: p.id,
        slug,
        name: p.name || 'Untitled Palette',
        style: p.style || 'Modern',
        tags: [styleTag, 'curated', 'popular'],
        colors: p.colors || [],
      };
    });
  } catch (error) {
    console.warn('Warning: Failed to fetch palettes at build time, returning fallback array:', error);
    return [];
  }
}

/**
 * Fetches and formats the list of all gradients.
 */
export async function fetchGradients(): Promise<Gradient[]> {
  try {
    const res = await fetch(`${API_BASE}/gradients`, { 
      next: { revalidate: 3600 },
      headers: { 'Content-Type': 'application/json' }
    });

    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    return json.data || [];
  } catch (error) {
    console.warn('Warning: Failed to fetch gradients at build time, returning fallback array:', error);
    return [];
  }
}

/**
 * Fetches and formats the list of all named colors.
 */
export async function fetchColors(): Promise<ColorName[]> {
  try {
    const res = await fetch(`${API_BASE}/colors`, { 
      next: { revalidate: 3600 },
      headers: { 'Content-Type': 'application/json' }
    });

    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    
    return Object.values(json.data || {}).map((item: any) => ({
      hex: item.hex?.startsWith('#') ? item.hex : `#${item.hex || '000000'}`,
      name: item.name || 'Unknown',
      slug: item.slug || '',
      aliases: item.aliases || [],
    }));
  } catch (error) {
    console.warn('Warning: Failed to fetch colors at build time, returning fallback array:', error);
    return [];
  }
}