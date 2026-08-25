import { NextRequest, NextResponse } from 'next/server';
import { fetchGradients } from '@/lib/api-client';

export async function GET(request: Request) {
  const allGradients = await fetchGradients();
  const { searchParams } = new URL(request.url);
  const q = searchParams.get('q')?.toLowerCase() || '';
  const id = searchParams.get('id');
  const style = searchParams.get('style')?.toLowerCase();
  const type = searchParams.get('type')?.toLowerCase();
  const page = parseInt(searchParams.get('page') || '1', 10);
  const limit = parseInt(searchParams.get('limit') || '0', 10);

  if (id) {
    const gradient = allGradients.find((g) => g.id === id);
    if (!gradient) {
      return NextResponse.json({ status: 'error', message: 'Gradient not found' }, { status: 404 });
    }
    return NextResponse.json({ status: 'success', data: gradient });
  }

  let filtered = allGradients;

  if (style) {
    filtered = filtered.filter((g) => g.style.toLowerCase() === style);
  }

  if (type) {
    filtered = filtered.filter((g) => g.type === type);
  }

  if (q) {
    filtered = filtered.filter(
      (item) =>
        item.name.toLowerCase().includes(q) ||
        item.style.toLowerCase().includes(q) ||
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
