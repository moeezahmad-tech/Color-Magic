import { NextRequest, NextResponse } from 'next/server';
import { fetchPalettes } from '@/lib/api-client';

export async function GET(request: NextRequest) {
  const allPalettes = await fetchPalettes();
  const { searchParams } = new URL(request.url);
  const q = searchParams.get('q')?.toLowerCase() || '';
  const id = searchParams.get('id');
  const page = parseInt(searchParams.get('page') || '1', 10);
  const limit = parseInt(searchParams.get('limit') || '0', 10);

  if (id) {
    const palette = allPalettes.find((p: any) => p.id === id || p.slug === id);
    if (!palette) {
      return NextResponse.json({ status: 'error', message: 'Palette not found' }, { status: 404 });
    }
    return NextResponse.json({ status: 'success', data: palette });
  }

  let filtered = allPalettes;
  if (q) {
    filtered = filtered.filter(
      (item) =>
        item.name.toLowerCase().includes(q) ||
        item.tags.some((t) => t.toLowerCase().includes(q)) ||
        item.colors.some((c) => c.toLowerCase().includes(q))
    );
  }

  if (limit > 0) {
    const total = filtered.length;
    const offset = (page - 1) * limit;
    const paged = filtered.slice(offset, offset + limit);

    return NextResponse.json({
      status: 'success',
      page,
      limit,
      total,
      total_pages: Math.ceil(total / limit),
      data: paged,
    });
  }

  return NextResponse.json({ status: 'success', total: filtered.length, data: filtered });
}
