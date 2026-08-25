# Color Magic - REST API Specifications & Next.js API Routes

This document outlines all public API endpoints provided by **Color Magic**, including parameters, response schemas, CORS configuration, and Next.js Route Handlers replacement specs.

---

## Environment Variables & OAuth Configuration

Copy `.env.example` to `.env.local` and fill in the values. **Never commit real credentials.**

```env
# .env.local (gitignored)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
NEXTAUTH_SECRET=your_generated_secret
NEXTAUTH_URL=https://colormagic.techkreative.com
NEXT_PUBLIC_SITE_URL=https://colormagic.techkreative.com
NEXT_PUBLIC_GA_ID=G-XXXXXXXXXX
```

Generate `NEXTAUTH_SECRET` with: `openssl rand -base64 32`

### Google OAuth Configuration Settings
- **Authorized JavaScript Origins**:
  - `https://colormagic.techkreative.com`
  - `http://localhost:3000` (local dev)
- **Authorized Redirect URIs**:
  - `https://colormagic.techkreative.com/api/auth/callback/google`
  - `http://localhost:3000/api/auth/callback/google`

---

## Existing REST API Endpoints Overview

Base API Subdomain URL: `https://api.colormagic.techkreative.com/`

| Endpoint URL | Method | PHP Backend File | Data Source File | Description |
| :--- | :--- | :--- | :--- | :--- |
| `/v1/palettes` | GET | `api/palettes.php` | `api/palettes.json` | List/search/paginate curated color palettes. |
| `/v1/gradients` | GET | `api/gradients.php` | `api/gradients.json` | List/search/paginate hand-crafted CSS gradients. |
| `/v1/colors` | GET | `api/colors.php` | `api/color-names.json` | Search/lookup named colors by Hex or Slug. |

---

## Endpoint Details & Schemas

### 1. Palettes Endpoint (`/v1/palettes` / `api/palettes.php`)

#### Query Parameters:
- `id` (string, optional): Single palette lookup by ID (e.g. `palette_1`).
- `q` (string, optional): Case-insensitive search query (matches palette `name` or hex values in `colors`).
- `page` (integer, default: `1`): Page number for paginated results.
- `limit` (integer, optional, max: `100`): Items per page.

#### Example Request:
`GET https://api.colormagic.techkreative.com/v1/palettes?page=1&limit=2`

#### Example Response:
```json
{
  "status": "success",
  "page": 1,
  "limit": 2,
  "total": 150,
  "total_pages": 75,
  "data": [
    {
      "id": "palette_1",
      "slug": "copper-archive",
      "name": "Copper Archive",
      "colors": ["#2C3E50", "#E74C3C", "#ECF0F1", "#3498DB", "#2ECC71"],
      "tags": ["minimalist", "modern", "cool"]
    },
    {
      "id": "palette_2",
      "slug": "dust-road",
      "name": "Dust Road",
      "colors": ["#8E44AD", "#3498DB", "#1ABC9C", "#F1C40F", "#E67E22"],
      "tags": ["vintage", "warm"]
    }
  ]
}
```

---

### 2. Gradients Endpoint (`/v1/gradients` / `api/gradients.php`)

#### Query Parameters:
- `id` (string, optional): Lookup single gradient by ID (e.g. `pm_24`).
- `style` (string, optional): Filter by mood/category (e.g. `warm`, `cool`, `dark`, `neon`, `pastel`).
- `type` (string, optional): Filter by gradient type (`linear` or `radial`).
- `q` (string, optional): Search query matching `name`, `style`, or `type`.
- `page` (integer, default: `1`): Page index.
- `limit` (integer, optional): Page limit.

#### Example Response:
```json
{
  "status": "success",
  "data": {
    "id": "pm_24",
    "name": "Peachy Sunrise",
    "style": "warm",
    "type": "linear",
    "angle": "135deg",
    "colors": ["#FF7E5F", "#FEB47B"]
  }
}
```

---

### 3. Colors Endpoint (`/v1/colors` / `api/colors.php`)

#### Query Parameters:
- `hex` (string, optional): Hexadecimal color code without leading hash (e.g. `FF5733`).
- `slug` (string, optional): URL-friendly slug name (e.g. `midnight-blue`).
- `q` (string, optional): Case-insensitive search matching color `name` or `hex`.
- `page` & `limit`: Standard pagination parameters.

#### Example Response:
```json
{
  "status": "success",
  "data": {
    "hex": "#FF5733",
    "name": "Persimmon",
    "slug": "persimmon",
    "rgb": {"r": 255, "g": 87, "b": 51},
    "hsl": {"h": 11, "s": 100, "l": 60}
  }
}
```

---

## Next.js API Route Handlers Implementation Spec

When migrating to Next.js, API routes will reside in `src/app/api/v1/...` as TypeScript route handlers:

### 1. `src/app/api/v1/palettes/route.ts`
```typescript
import { NextRequest, NextResponse } from 'next/server';
import palettesData from '@/data/palettes.json';

export async function GET(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const q = searchParams.get('q')?.toLowerCase() || '';
  const id = searchParams.get('id');
  const page = parseInt(searchParams.get('page') || '1', 10);
  const limit = parseInt(searchParams.get('limit') || '0', 10);

  if (id) {
    const palette = palettesData.find((p) => p.id === id);
    if (!palette) {
      return NextResponse.json({ status: 'error', message: 'Palette not found' }, { status: 404 });
    }
    return NextResponse.json({ status: 'success', data: palette });
  }

  let filtered = palettesData;
  if (q) {
    filtered = filtered.filter(
      (item) =>
        item.name.toLowerCase().includes(q) ||
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
```

### 2. `src/app/api/v1/gradients/route.ts`
Handles gradient listings with type/style filters and JSON responses.

### 3. `src/app/api/v1/colors/route.ts`
Handles color name lookup and nearest color search.
