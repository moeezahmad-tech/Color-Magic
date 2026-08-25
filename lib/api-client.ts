import { Palette, Gradient, ColorName } from '@/types';
import localPalettes from '@/data/palettes.json';
import localGradients from '@/data/gradients.json';
import localColors from '@/data/color-names.json';

const API_BASE = 'https://api.colormagic.techkreative.com';

function formatLocalPalettes(): Palette[] {
  return (localPalettes as any[]).map((p: any) => {
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
}

function formatLocalGradients(): Gradient[] {
  return (localGradients as any[]).map((g: any) => ({
    id: g.id,
    name: g.name,
    style: g.style || 'Modern',
    type: g.type || 'linear',
    colors: g.colors || [],
    css: g.css,
  }));
}

function formatLocalColors(): ColorName[] {
  return Object.values(localColors as Record<string, any>).map((item: any) => ({
    hex: item.hex?.startsWith('#') ? item.hex : `#${item.hex || '000000'}`,
    name: item.name || 'Unknown',
    slug: item.slug || '',
    aliases: item.aliases || [],
  }));
}

/**
 * Fetches and formats the list of all curated palettes with zero-downtime local fallback.
 */
export async function fetchPalettes(): Promise<Palette[]> {
  try {
    const res = await fetch(`${API_BASE}/palettes`, { 
      next: { revalidate: 3600, tags: ['palettes'] },
      headers: { 'Content-Type': 'application/json' },
      signal: AbortSignal.timeout(4000),
    });
    
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    
    const items = (json.data || []).map((p: any) => {
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

    if (items.length > 0) return items;
    return formatLocalPalettes();
  } catch (error) {
    console.warn('[api-client] Remote API unreachable or timed out, serving resilient local palettes fallback.');
    return formatLocalPalettes();
  }
}

/**
 * Fetches and formats the list of all gradients with zero-downtime local fallback.
 */
export async function fetchGradients(): Promise<Gradient[]> {
  try {
    const res = await fetch(`${API_BASE}/gradients`, { 
      next: { revalidate: 3600, tags: ['gradients'] },
      headers: { 'Content-Type': 'application/json' },
      signal: AbortSignal.timeout(4000),
    });

    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    const items = json.data || [];
    if (items.length > 0) return items;
    return formatLocalGradients();
  } catch (error) {
    console.warn('[api-client] Remote API unreachable or timed out, serving resilient local gradients fallback.');
    return formatLocalGradients();
  }
}

/**
 * Fetches and formats the list of all named colors with zero-downtime local fallback.
 */
export async function fetchColors(): Promise<ColorName[]> {
  try {
    const res = await fetch(`${API_BASE}/colors`, { 
      next: { revalidate: 3600, tags: ['colors'] },
      headers: { 'Content-Type': 'application/json' },
      signal: AbortSignal.timeout(4000),
    });

    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    const json = await res.json();
    
    const items = Object.values(json.data || {}).map((item: any) => ({
      hex: item.hex?.startsWith('#') ? item.hex : `#${item.hex || '000000'}`,
      name: item.name || 'Unknown',
      slug: item.slug || '',
      aliases: item.aliases || [],
    }));

    if (items.length > 0) return items;
    return formatLocalColors();
  } catch (error) {
    console.warn('[api-client] Remote API unreachable or timed out, serving resilient local colors fallback.');
    return formatLocalColors();
  }
}