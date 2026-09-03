# ColorMagic Public REST API (V2 SQLite Edition)

Production-grade, ultra-fast, standalone REST API powering ColorMagic color intelligence, palette generation, and gradient rendering.

- **Base URL:** `https://colormagic-api.techkreative.com/`
- **Engine:** SQLite 3 with WAL (Write-Ahead Logging) + Zero-Dependency PHP OOP
- **Compatibility:** PHP 7.0 - PHP 8.4+
- **Latency:** `< 0.2 ms` (Single Lookups) | `< 0.8 ms` (Paginated Search)
- **Caching:** HTTP 304 ETag Negotiation + Public HTTP Cache-Control

---

## Table of Contents

1. [Architecture & Directory Structure](#architecture--directory-structure)
2. [Key Performance & Reliability Features](#key-performance--reliability-features)
3. [Quick Start & Root Discovery](#quick-start--root-discovery)
4. [Complete API Reference](#complete-api-reference)
   - [Health & Diagnostics](#1-health--diagnostics)
   - [Colors API](#2-colors-api)
   - [Gradients API](#3-gradients-api)
   - [Palettes API](#4-palettes-api)
   - [Legacy V1 Endpoints](#5-legacy-v1-compatibility-endpoints)
   - [Direct Static JSON Fallbacks](#6-direct-static-json-datasets)
5. [Standard Request & Response Envelope](#standard-request--response-envelope)
6. [HTTP Status Codes & Error Handling](#http-status-codes--error-handling)
7. [Client Integration Recipes](#client-integration-recipes)
   - [cURL](#curl-examples)
   - [JavaScript / TypeScript (Fetch API)](#javascript--typescript-fetch-api)
   - [Python (requests)](#python-requests)
8. [CLI Maintenance & Diagnostics](#cli-maintenance--diagnostics)
9. [Configuration & Environment Variables](#configuration--environment-variables)
10. [Web Server & Apache Configuration](#web-server--apache-configuration)

---

## Architecture & Directory Structure

The `api/` directory is **100% self-contained and isolated**. It can be served directly as an independent subdomain (e.g. `https://colormagic-api.techkreative.com/`) or accessed as `/api/` in a monolithic root setup.

```
api/
├── .env.api.colormagic           <-- API Environment Configuration (Isolated)
├── .env.api.example              <-- Environment Variable Template
├── .htaccess                     <-- Apache URL Rewriting, CORS, Gzip & Caching Headers
├── index.php                     <-- Root Entrypoint & Discovery Route (GET /)
├── README.md                     <-- Complete Technical Documentation & Reference
├── color-names.json              <-- Root Fallback Seed Dataset (1,027 colors)
├── gradients.json                <-- Root Fallback Seed Dataset (329 gradients)
├── palettes.json                 <-- Root Fallback Seed Dataset (833 palettes)
├── colors.php                    <-- V1 Backward-Compatible Color Route
├── gradients.php                 <-- V1 Backward-Compatible Gradient Route
├── palettes.php                  <-- V1 Backward-Compatible Palette Route
│
├── cli/                          <-- CLI Operations & Verification Tools
│   ├── migrate.php               <-- SQLite Schema Migrator & Seeder
│   └── test_endpoints.php        <-- Automated Verification & Latency Benchmark Suite
│
├── data/                         <-- Data Storage Directory
│   ├── colormagic.sqlite         <-- High-Performance SQLite Database (WAL mode, indexed)
│   ├── color-names.json          <-- Seed Dataset (1,027 colors)
│   ├── gradients.json            <-- Seed Dataset (329 gradients)
│   └── palettes.json             <-- Seed Dataset (833 palettes)
│
├── v1/                           <-- V1 Direct Route Wrappers
│   ├── index.php
│   ├── colors.php
│   ├── gradients.php
│   └── palettes.php
│
└── v2/                           <-- V2 High-Performance REST Architecture
    ├── .htaccess                 <-- V2 Front Controller Rewriter
    ├── index.php                 <-- V2 Router & Front Controller
    └── src/
        ├── Config/
        │   └── Env.php           <-- Standalone .env Loader with cascading resolution
        ├── Database/
        │   └── Database.php      <-- SQLite WAL Connection Pool & PRAGMA Optimizer
        ├── Controllers/
        │   ├── BaseController.php<-- Query parsing, JSON input reader
        │   ├── ColorController.php
        │   ├── GradientController.php
        │   ├── PaletteController.php
        │   └── HealthController.php
        ├── Repositories/
        │   ├── ColorRepository.php
        │   ├── GradientRepository.php
        │   └── PaletteRepository.php
        └── Services/
            ├── MigrationService.php
            └── ResponseHelper.php <-- Standard JSON Envelope, Latency Timer, ETags
```

---

## Key Performance & Reliability Features

| Feature | Technical Implementation |
|---|---|
| **SQLite WAL Mode Engine** | Runs with `PRAGMA journal_mode = WAL`, `synchronous = NORMAL`, `temp_store = MEMORY`, `cache_size = -64000` (64MB cache). Enables concurrent reads without locking writers. |
| **Indexed Lookups** | B-Tree indexes on `colors(hex)`, `colors(slug)`, `gradients(id)`, `gradients(style)`, `palettes(id)`, and `palettes(style)` yield `< 0.2ms` lookups. |
| **HTTP 304 ETag Negotiation** | Generates an MD5-based `ETag` on every successful payload. If `If-None-Match` header matches, responds with `304 Not Modified` (0 payload bytes transferred). |
| **Fail-Safe JSON Fallback** | If the SQLite database file is missing or inaccessible, repositories transparently fall back to reading JSON files without throwing 500 errors. |
| **Transparent Payload Gzip** | Gzip stream compression via Apache `mod_deflate` / PHP output buffers for JSON responses. |
| **Zero External Dependencies** | Built using pure PHP PDO and standard libraries without Composer requirements, enabling drop-in deployment to any PHP 7.0+ host. |

---

## Quick Start & Root Discovery

### Root Endpoint

Returns API metadata, discovery endpoints, and documentation links.

- **URL:** `GET /` or `GET /index.php`
- **Sample Request:**
  ```bash
  curl -i https://colormagic-api.techkreative.com/
  ```
- **Sample Response:**
  ```json
  {
    "status": "success",
    "name": "ColorMagic API",
    "active_version": "v2",
    "documentation": "https://colormagic-api.techkreative.com/README.md",
    "v2_endpoints": {
      "health": "https://colormagic-api.techkreative.com/v2/health",
      "colors": {
        "url": "https://colormagic-api.techkreative.com/v2/colors",
        "by_hex": "https://colormagic-api.techkreative.com/v2/colors/{hex}",
        "by_slug": "https://colormagic-api.techkreative.com/v2/colors/slug/{slug}",
        "query_params": ["q", "hex", "slug", "page", "limit", "format"]
      },
      "gradients": {
        "url": "https://colormagic-api.techkreative.com/v2/gradients",
        "by_id": "https://colormagic-api.techkreative.com/v2/gradients/{id}",
        "query_params": ["q", "style", "type", "id", "page", "limit"]
      },
      "palettes": {
        "url": "https://colormagic-api.techkreative.com/v2/palettes",
        "by_id": "https://colormagic-api.techkreative.com/v2/palettes/{id}",
        "submit": "POST https://colormagic-api.techkreative.com/v2/palettes",
        "query_params": ["q", "style", "id", "page", "limit"]
      }
    },
    "v1_endpoints": {
      "gradients": "https://colormagic-api.techkreative.com/v1/gradients",
      "palettes": "https://colormagic-api.techkreative.com/v1/palettes",
      "colors": "https://colormagic-api.techkreative.com/v1/colors",
      "direct_json": {
        "gradients": "https://colormagic-api.techkreative.com/gradients.json",
        "palettes": "https://colormagic-api.techkreative.com/palettes.json",
        "colors": "https://colormagic-api.techkreative.com/color-names.json"
      }
    },
    "timestamp": "2026-09-03T17:30:00+00:00"
  }
  ```

---

## Complete API Reference

### 1. Health & Diagnostics

Inspect database engine health, table statistics, journal mode, and active configuration.

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/v2/health` | Full system diagnostic and database metrics |
| `GET` | `/health` | Shorthand alias to `/v2/health` |

#### Response Example (`200 OK`):
```json
{
  "status": "success",
  "latency_ms": 0.35,
  "data": {
    "status": "healthy",
    "api_name": "ColorMagic REST API",
    "version": "v2",
    "php_version": "8.2.12",
    "environment": "production",
    "database": {
      "status": "connected",
      "driver": "sqlite",
      "journal_mode": "wal",
      "size_bytes": 1048576,
      "records": {
        "colors": 1027,
        "gradients": 329,
        "palettes": 833
      }
    },
    "endpoints": {
      "health": "https://colormagic-api.techkreative.com/v2/health",
      "colors": "https://colormagic-api.techkreative.com/v2/colors",
      "color_hex": "https://colormagic-api.techkreative.com/v2/colors/{hex}",
      "gradients": "https://colormagic-api.techkreative.com/v2/gradients",
      "grad_id": "https://colormagic-api.techkreative.com/v2/gradients/{id}",
      "palettes": "https://colormagic-api.techkreative.com/v2/palettes",
      "pal_id": "https://colormagic-api.techkreative.com/v2/palettes/{id}"
    },
    "timestamp": "2026-09-03T17:30:00+00:00"
  }
}
```

---

### 2. Colors API

Access the database of 1,027+ curated, named color entries with aliases and search indexing.

| Method | Route | Description |
|---|---|---|
| `GET` | `/v2/colors` | List colors with search & pagination |
| `GET` | `/v2/colors/{hex}` | Single color lookup by 3-to-8 char hex code |
| `GET` | `/v2/colors/slug/{slug}` | Single color lookup by URL slug |

#### Query Parameters for `GET /v2/colors`

| Parameter | Type | Default | Description |
|---|---|---|---|
| `q` | `string` | `""` | Search query matching name, hex code, or slug (e.g. `blue`, `emerald`, `123524`) |
| `page` | `integer` | `1` | Page number for pagination (minimum: `1`) |
| `limit` | `integer` | `50` | Records per page (minimum: `1`, maximum: `200`) |
| `format` | `string` | `"list"` | Set to `"dict"` to retrieve the entire dataset keyed by uppercase hex code |
| `hex` | `string` | `""` | Lookup single color via query parameter instead of path |
| `slug` | `string` | `""` | Lookup single color via query parameter instead of path |

#### Color Response Examples

##### Single Color Lookup (`GET /v2/colors/123524` or `GET /v2/colors/slug/phthalo-green`):
```json
{
  "status": "success",
  "latency_ms": 0.12,
  "data": {
    "hex": "#123524",
    "raw_hex": "123524",
    "name": "Phthalo green",
    "slug": "phthalo-green",
    "aliases": ["Deep Pine", "Dark Emerald"]
  }
}
```

##### Paginated Color Search (`GET /v2/colors?q=emerald&page=1&limit=2`):
```json
{
  "status": "success",
  "latency_ms": 0.42,
  "total": 6,
  "page": 1,
  "limit": 2,
  "data": [
    {
      "hex": "#50C878",
      "raw_hex": "50C878",
      "name": "Emerald",
      "slug": "emerald",
      "aliases": ["Paris green"]
    },
    {
      "hex": "#046307",
      "raw_hex": "046307",
      "name": "Emerald Green",
      "slug": "emerald-green",
      "aliases": []
    }
  ]
}
```

##### Dictionary Format (`GET /v2/colors?format=dict`):
```json
{
  "status": "success",
  "latency_ms": 0.85,
  "total": 1027,
  "format": "dictionary",
  "data": {
    "123524": {
      "hex": "#123524",
      "name": "Phthalo green",
      "slug": "phthalo-green",
      "aliases": []
    },
    "50C878": {
      "hex": "#50C878",
      "name": "Emerald",
      "slug": "emerald",
      "aliases": ["Paris green"]
    }
  }
}
```

---

### 3. Gradients API

Search, filter, and retrieve CSS linear and radial gradients with complete CSS output, angles, and color stops.

| Method | Route | Description |
|---|---|---|
| `GET` | `/v2/gradients` | List & filter CSS gradients |
| `GET` | `/v2/gradients/{id}` | Single gradient lookup by unique ID |

#### Query Parameters for `GET /v2/gradients`

| Parameter | Type | Default | Description |
|---|---|---|---|
| `q` | `string` | `""` | Search query matching gradient name, style, type, or hex color |
| `style` | `string` | `null` | Filter by aesthetic style: `Warm`, `Cool`, `Pastel`, `Dark`, `Neon`, `Monochrome`, `Vibrant`, `Subtle`, `Retro` |
| `type` | `string` | `null` | Filter by gradient type: `linear` or `radial` |
| `page` | `integer` | `1` | Page number for pagination |
| `limit` | `integer` | `50` | Records per page (max: `200`) |
| `id` | `string` | `""` | Retrieve single gradient via query parameter |

#### Gradient Response Examples

##### Single Gradient Lookup (`GET /v2/gradients/gradient_1`):
```json
{
  "status": "success",
  "latency_ms": 0.14,
  "data": {
    "id": "gradient_1",
    "name": "Sunset Blaze",
    "style": "Warm",
    "type": "linear",
    "colors": ["#FF512F", "#F09819"],
    "css": "linear-gradient(135deg, #FF512F 0%, #F09819 100%)",
    "angle": 135
  }
}
```

##### Filtered Gradient List (`GET /v2/gradients?style=Warm&type=linear&limit=2`):
```json
{
  "status": "success",
  "latency_ms": 0.51,
  "total": 45,
  "page": 1,
  "limit": 2,
  "data": [
    {
      "id": "gradient_1",
      "name": "Sunset Blaze",
      "style": "Warm",
      "type": "linear",
      "colors": ["#FF512F", "#F09819"],
      "css": "linear-gradient(135deg, #FF512F 0%, #F09819 100%)",
      "angle": 135
    },
    {
      "id": "gradient_2",
      "name": "Solar Flare",
      "style": "Warm",
      "type": "linear",
      "colors": ["#F12711", "#F5AF19"],
      "css": "linear-gradient(90deg, #F12711 0%, #F5AF19 100%)",
      "angle": 90
    }
  ]
}
```

---

### 4. Palettes API

Access curated color schemes and submit community palettes.

| Method | Route | Description |
|---|---|---|
| `GET` | `/v2/palettes` | List, search, and filter curated color palettes |
| `GET` | `/v2/palettes/{id}` | Single palette lookup by unique ID |
| `POST` | `/v2/palettes` | Submit new palette (community/dashboard) |

#### Query Parameters for `GET /v2/palettes`

| Parameter | Type | Default | Description |
|---|---|---|---|
| `q` | `string` | `""` | Search query matching palette name, style, or embedded color hex code |
| `style` | `string` | `null` | Filter by palette mood/style: `Eco`, `Pastel`, `Vintage`, `Neon`, `Monochrome`, `Earthy`, `Cyberpunk`, `Minimal` |
| `page` | `integer` | `1` | Page number for pagination |
| `limit` | `integer` | `50` | Records per page (max: `200`) |
| `id` | `string` | `""` | Retrieve single palette via query parameter |

#### Palette Response Examples

##### Single Palette Lookup (`GET /v2/palettes/palette_1`):
```json
{
  "status": "success",
  "latency_ms": 0.11,
  "data": {
    "id": "palette_1",
    "name": "Forest Breath",
    "style": "Eco",
    "colors": ["#2D5A27", "#4E8752", "#87B37A", "#D1E2C4", "#F4F7F2"]
  }
}
```

##### Submit Community Palette (`POST /v2/palettes`):

**Request Headers:**
- `Content-Type: application/json`

**Request Body Schema:**
```json
{
  "name": "Nordic Aurora",
  "style": "Cool",
  "colors": ["#2E3440", "#3B4252", "#88C0D0", "#81A1C1", "#ECEFF4"],
  "user_id": "usr_99a81b2"
}
```

**Response (`201 Created`):**
```json
{
  "status": "success",
  "latency_ms": 0.65,
  "message": "Palette submitted successfully for review",
  "data": {
    "id": "user_pal_66d74b88d3a12",
    "name": "Nordic Aurora",
    "style": "Cool",
    "status": "pending"
  }
}
```

---

### 5. Legacy V1 Compatibility Endpoints

All V1 routes remain 100% backward compatible and are internally accelerated by the SQLite engine with automatic JSON fail-safe fallback.

| Endpoint | Method | Supported Query Parameters | Output |
|---|---|---|---|
| `/v1/colors` | `GET` | `?hex=123524`, `?slug=phthalo-green`, `?q=blue`, `?page=1&limit=20` | Color records / dictionary |
| `/v1/gradients` | `GET` | `?id=gradient_1`, `?style=Warm`, `?type=linear`, `?q=sunset` | Gradient array / object |
| `/v1/palettes` | `GET` | `?id=palette_1`, `?style=Eco`, `?q=forest` | Palette array / object |
| `/v1/health` | `GET` | None | API Status discovery |

---

### 6. Direct Static JSON Datasets

For offline use, bundle embedding, or static asset caching, datasets are also accessible as raw JSON files:

- `https://colormagic-api.techkreative.com/color-names.json`
- `https://colormagic-api.techkreative.com/gradients.json`
- `https://colormagic-api.techkreative.com/palettes.json`

---

## Standard Request & Response Envelope

### Success Envelope Structure

All successful V2 responses use a unified schema:

```json
{
  "status": "success",
  "latency_ms": 0.18,
  "total": 1027,
  "page": 1,
  "limit": 50,
  "data": { ... }
}
```

- `status`: Always `"success"` for 2xx responses.
- `latency_ms`: Float indicating high-resolution server execution time in milliseconds.
- `total` / `page` / `limit`: Present on paginated list endpoints.
- `data`: Payload object or array.

### Error Envelope Structure

Errors return appropriate HTTP status codes and never return cached responses:

```json
{
  "status": "error",
  "message": "Color not found for hex code '#INVALID'",
  "latency_ms": 0.15,
  "details": {
    "available_routes": [
      "GET /v2/health",
      "GET /v2/colors",
      "GET /v2/colors/{hex}"
    ]
  }
}
```

---

## HTTP Status Codes & Error Handling

| Code | Meaning | Condition |
|---|---|---|
| `200 OK` | Request succeeded | Standard GET retrieval |
| `201 Created` | Resource created | Successful `POST /v2/palettes` |
| `304 Not Modified` | Cached content valid | `If-None-Match` matches server `ETag` |
| `400 Bad Request` | Invalid query syntax | Malformed input parameters |
| `404 Not Found` | Resource does not exist | Unknown hex code, ID, slug, or route |
| `422 Unprocessable Entity` | Validation failure | Missing required fields or < 2 colors on palette submission |
| `500 Internal Server Error` | Uncaught server exception | Database read fault without JSON fallback |

---

## Client Integration Recipes

### cURL Examples

#### Single Color by Hex:
```bash
curl -i https://colormagic-api.techkreative.com/v2/colors/123524
```

#### Conditional Request using ETag (HTTP 304):
```bash
curl -i https://colormagic-api.techkreative.com/v2/colors/123524 \
  -H 'If-None-Match: "3a8bb68c5b6bda481f33cc26a27e7d7b"'
```

#### Search Gradients by Style:
```bash
curl -i "https://colormagic-api.techkreative.com/v2/gradients?style=Warm&limit=10"
```

#### Submit a Community Palette:
```bash
curl -X POST https://colormagic-api.techkreative.com/v2/palettes \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Neon Twilight",
    "style": "Neon",
    "colors": ["#00F0FF", "#7000FF", "#FF007B", "#FFE600"]
  }'
```

---

### JavaScript / TypeScript (Fetch API)

```typescript
const BASE_URL = 'https://colormagic-api.techkreative.com';

// 1. Fetch Single Color with ETag Caching
async function fetchColorByHex(hex: string) {
  const cleanHex = hex.replace('#', '');
  const res = await fetch(`${BASE_URL}/v2/colors/${cleanHex}`);
  if (!res.ok) throw new Error(`Color not found (${res.status})`);
  const json = await res.json();
  return json.data; // { hex: "#123524", name: "Phthalo green", ... }
}

// 2. Search Gradients
async function searchGradients(query: string, style = 'Warm') {
  const params = new URLSearchParams({ q: query, style, limit: '20' });
  const res = await fetch(`${BASE_URL}/v2/gradients?${params}`);
  const json = await res.json();
  return json.data;
}

// 3. Submit Community Palette
async function submitPalette(name: string, style: string, colors: string[]) {
  const res = await fetch(`${BASE_URL}/v2/palettes`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, style, colors })
  });
  return await res.json();
}
```

---

### Python (requests)

```python
import requests

BASE_URL = "https://colormagic-api.techkreative.com"

# 1. Lookup single color
response = requests.get(f"{BASE_URL}/v2/colors/123524")
if response.status_code == 200:
    color = response.json()["data"]
    print(f"Color: {color['name']} ({color['hex']})")

# 2. Search palettes
params = {"q": "forest", "style": "Eco", "limit": 10}
response = requests.get(f"{BASE_URL}/v2/palettes", params=params)
data = response.json()
print(f"Found {data['total']} palettes:")
for palette in data["data"]:
    print(f"- {palette['name']}: {', '.join(palette['colors'])}")
```

---

## CLI Maintenance & Diagnostics

The `api/cli/` directory provides command-line utilities for maintaining the SQLite database, running migrations, and executing automated verification suites.

### Database Migration & Seeder (`cli/migrate.php`)

```bash
# Standard migration & seed (skips if tables already contain data)
php api/cli/migrate.php

# Force wipe and re-seed from JSON files
php api/cli/migrate.php --force

# Inspect database stats without altering data
php api/cli/migrate.php --stats
```

**Example Output:**
```
====================================================
  ColorMagic SQLite Database Migration & CLI Tool   
====================================================

Database Path: e:/Coding/color_magic/api/data/colormagic.sqlite
Loaded Env:    e:/Coding/color_magic/api/.env.api.colormagic
Environment:   production

---------------- Database Stats --------------------
  Total Colors:    1027
  Total Gradients: 329
  Total Palettes:  833
  Database Size:   1024 KB
  WAL Mode:        wal
----------------------------------------------------

Status: SUCCESS - SQLite ready for production traffic!
```

---

### Automated Verification & Latency Benchmark (`cli/test_endpoints.php`)

Runs end-to-end repository queries, tests indexes, checks WAL mode, verifies lookups, and calculates latency benchmarks.

```bash
php api/cli/test_endpoints.php
```

**Example Output:**
```
====================================================
   ColorMagic API V2 Automated Verification Suite   
====================================================

 [PASS] SQLite DB Connection Initialized (latency: 0.12ms)
 [PASS] SQLite Journal Mode is WAL (mode: wal)
 [PASS] Color Lookup by Hex (123524 -> Phthalo green) (0.08ms)
 [PASS] Color Lookup by Slug ('phthalo-green') (0.09ms)
 [PASS] Color Search ('blue') returns paginated items (total: 48)
 [PASS] Gradient Lookup by ID ('gradient_1' -> Sunset Blaze) (0.07ms)
 [PASS] Gradient Filter by Style ('Warm') (total: 45)
 [PASS] Palette Lookup by ID ('palette_1' -> Forest Breath) (0.07ms)
 [PASS] Palette Search ('Forest') (total: 8)
 [PASS] Palette Filter by Style ('Eco') (total: 62)
 [PASS] Community Palette Submission in DB (ID: user_pal_66d74b...)

---------------- Benchmark Summary -----------------
  Passed: 11 / 11 tests
  Average Query Latency: < 0.2 ms
----------------------------------------------------

All tests passed successfully! ColorMagic V2 API is 100% operational.
```

---

## Configuration & Environment Variables

The API loads configuration from `.env.api.colormagic` in the `api/` directory (or falls back to `.env.api.colormagic` / `.env` in the parent directory).

Copy `.env.api.example` to `.env.api.colormagic`:

```ini
# Application Setup
APP_NAME=ColorMagic-API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://colormagic-api.techkreative.com
API_VERSION=v2

# SQLite Database Path (relative to api/ directory or absolute path)
DB_DRIVER=sqlite
DB_PATH=data/colormagic.sqlite

# Cache & Performance
CACHE_ENABLED=true
CACHE_TTL=86400
GZIP_COMPRESSION=true

# Community Palettes Security & Rate Limiting (Future)
API_SECRET_KEY=your_secure_32_char_secret_key_here
RATE_LIMIT_ENABLED=false
RATE_LIMIT_MAX_REQUESTS=120
RATE_LIMIT_WINDOW=60
```

---

## Web Server & Apache Configuration

The `api/.htaccess` file configures CORS headers, Gzip compression, and URL rewrites for production hosting:

```apache
DirectoryIndex index.php
Options -Indexes +FollowSymLinks

# Enable CORS Headers for universal client access
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
</IfModule>

# Ensure proper MIME type for static JSON fallback files
AddType application/json .json

# Enable Gzip Compression for JSON responses
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE application/json
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On

    # Handle HTTP OPTIONS preflight requests immediately
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ - [R=200,L]

    # Serve physical files directly
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ - [L]

    # Shorthand route aliases
    RewriteRule ^health/?$ v2/index.php [L,QSA]
    RewriteRule ^colors?/([0-9A-Fa-f]{3,8})/?$ v2/index.php [L,QSA]

    # V1 Legacy Endpoint rewrites
    RewriteRule ^v1/gradients/?$ gradients.php [L,QSA]
    RewriteRule ^v1/palettes/?$ palettes.php [L,QSA]
    RewriteRule ^v1/colors/?$ colors.php [L,QSA]
    RewriteRule ^v1/health/?$ index.php [L,QSA]

    RewriteRule ^gradients/?$ gradients.php [L,QSA]
    RewriteRule ^palettes/?$ palettes.php [L,QSA]
    RewriteRule ^colors/?$ colors.php [L,QSA]
</IfModule>
```

---

## License

ColorMagic API is open-source software released under the [MIT License](file:///e:/Coding/color_magic/LICENSE).
