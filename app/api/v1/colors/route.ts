import { NextRequest, NextResponse } from 'next/server';
import { fetchColors } from '@/lib/api-client';
import { findClosestColorName, normalizeHex, hexToRgb, rgbToHsl } from '@/lib/color-math';

export async function GET(request: NextRequest) {
  const allColorNames = await fetchColors();
  const { searchParams } = new URL(request.url);
  const hex = searchParams.get('hex');
  const q = searchParams.get('q')?.toLowerCase() || '';

  if (hex) {
    const norm = normalizeHex(hex);
    const match = findClosestColorName(norm, allColorNames);
    const rgb = hexToRgb(norm);
    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

    return NextResponse.json({
      status: 'success',
      data: {
        hex: norm,
        name: match.name,
        slug: match.name.toLowerCase().replace(/\s+/g, '-'),
        rgb,
        hsl,
      },
    });
  }

  let filtered = allColorNames;
  if (q) {
    filtered = filtered.filter(
      (item) =>
        item.name.toLowerCase().includes(q) || item.hex.toLowerCase().includes(q)
    );
  }

  return NextResponse.json({ status: 'success', total: filtered.length, data: filtered.slice(0, 100) });
}
