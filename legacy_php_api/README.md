# ColorMagic Public API

Public REST API for ColorMagic color datasets, hosted at `https://api.colormagic.techkreative.com/`.

All responses are returned as JSON with CORS enabled (`Access-Control-Allow-Origin: *`).

## Base Endpoints

| URL | Description |
|-----|-------------|
| `GET /` | API info & health |
| `GET /v1/health` | API health check |
| `GET /v1/gradients` | List / search CSS gradients |
| `GET /v1/palettes` | List / search color palettes |
| `GET /v1/colors` | List / search named colors |
| `GET /gradients.json` | Raw gradients JSON |
| `GET /palettes.json` | Raw palettes JSON |
| `GET /color-names.json` | Raw color names JSON |

---

## Gradients `GET /v1/gradients`

### Query Parameters

| Param | Type | Description |
|-------|------|-------------|
| `id` | string | Single gradient by ID (e.g. `gradient_1`) |
| `style` | string | Filter by style: `warm`, `cool`, `dark`, `neon`, `pastel`, `purple`, `nature`, `pink`, `earth`, `mono` |
| `type` | string | Filter by type: `linear` or `radial` |
| `q` | string | Search by name, style, or type |
| `page` | integer | Page number (default: `1`) |
| `limit` | integer | Items per page (max: `100`) |

### Example Requests

```
GET https://api.colormagic.techkreative.com/v1/gradients
GET https://api.colormagic.techkreative.com/v1/gradients?style=warm
GET https://api.colormagic.techkreative.com/v1/gradients?q=sunset
GET https://api.colormagic.techkreative.com/v1/gradients?id=gradient_1
GET https://api.colormagic.techkreative.com/v1/gradients?page=1&limit=20
```

### Example Response

```json
{
  "status": "success",
  "total": 1,
  "data": {
    "id": "pm_24",
    "name": "Peachy Sunrise",
    "style": "warm",
    "type": "linear",
    "angle": 135,
    "colors": ["#FF7E5F", "#FEB47B"],
    "css": "linear-gradient(135deg, #FF7E5F, #FEB47B)"
  }
}
```

---

## Palettes `GET /v1/palettes`

### Query Parameters

| Param | Type | Description |
|-------|------|-------------|
| `id` | string | Single palette by ID (e.g. `palette_1`) |
| `q` | string | Search by name or hex color in palette |
| `page` | integer | Page number (default: `1`) |
| `limit` | integer | Items per page (max: `100`) |

### Example Requests

```
GET https://api.colormagic.techkreative.com/v1/palettes
GET https://api.colormagic.techkreative.com/v1/palettes?q=ocean
GET https://api.colormagic.techkreative.com/v1/palettes?id=palette_1
GET https://api.colormagic.techkreative.com/v1/palettes?page=1&limit=20
```

### Example Response

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
    }
  ]
}
```

---

## Colors `GET /v1/colors`

### Query Parameters

| Param | Type | Description |
|-------|------|-------------|
| `hex` | string | Lookup by hex code (without `#`, e.g. `FF5733`) |
| `slug` | string | Lookup by URL slug (e.g. `midnight-blue`) |
| `q` | string | Search by name or hex |
| `page` | integer | Page number (default: `1`) |
| `limit` | integer | Items per page (max: `100`) |

### Example Requests

```
GET https://api.colormagic.techkreative.com/v1/colors
GET https://api.colormagic.techkreative.com/v1/colors?hex=EC4899
GET https://api.colormagic.techkreative.com/v1/colors?slug=midnight-blue
GET https://api.colormagic.techkreative.com/v1/colors?q=blue
GET https://api.colormagic.techkreative.com/v1/colors?page=1&limit=20
```

### Example Response

```json
{
  "status": "success",
  "data": {
    "hex": "#FF5733",
    "name": "Persimmon",
    "slug": "persimmon",
    "rgb": { "r": 255, "g": 87, "b": 51 },
    "hsl": { "h": 11, "s": 100, "l": 60 }
  }
}
```

---

## CORS

All endpoints respond with:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, OPTIONS
```

No API key is required. This API is free and open source.
