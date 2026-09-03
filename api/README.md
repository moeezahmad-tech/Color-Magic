# ColorMagic Public REST API (V2 SQLite Edition)

Production-grade, ultra-fast, self-contained REST API powering ColorMagic color intelligence, palette discovery, CSS gradient rendering, and community submissions.

- **Base URL (Production):** `https://colormagic-api.techkreative.com/`
- **Engine:** SQLite 3 with WAL (Write-Ahead Logging) + Zero-Dependency PHP OOP
- **Compatibility:** PHP 7.0 - PHP 8.4+
- **Latency:** `< 0.2 ms` (Single Lookups) | `< 0.8 ms` (Paginated Search)
- **Caching:** HTTP 304 ETag Negotiation + Public HTTP Cache-Control
- **Fail-Safe:** Automatic Dual-Layer Fallback to JSON Datasets

---

## Table of Contents

1. [System Architecture & Directory Structure](#system-architecture--directory-structure)
2. [Key Performance & Reliability Features](#key-performance--reliability-features)
3. [Database Schema & Storage Architecture](#database-schema--storage-architecture)
4. [Quick Start & Root Discovery](#quick-start--root-discovery)
5. [Complete API Route & Endpoint Reference](#complete-api-route--endpoint-reference)
   - [1. Health & System Diagnostics](#1-health--system-diagnostics)
   - [2. Colors API](#2-colors-api)
   - [3. Gradients API](#3-gradients-api)
   - [4. Palettes API](#4-palettes-api)
   - [5. Community Palette Submission](#5-community-palette-submission)
   - [6. Legacy V1 Backward Compatibility](#6-legacy-v1-backward-compatibility)
   - [7. Direct Static JSON Datasets](#7-direct-static-json-datasets)
6. [Standard Request & Response Envelope](#standard-request--response-envelope)
7. [HTTP Status Codes & Error Handling](#http-status-codes--error-handling)
8. [Multi-Language Client Integration Recipes](#multi-language-client-integration-recipes)
   - [cURL](#1-curl)
   - [JavaScript / TypeScript (Fetch API)](#2-javascript--typescript-fetch-api)
   - [Node.js (Axios)](#3-nodejs-axios)
   - [Python (requests)](#4-python-requests)
   - [PHP (Native cURL / Client)](#5-php-native-curl)
   - [Go (net/http)](#6-go-nethttp)
9. [CLI Maintenance & Automation Tools](#cli-maintenance--automation-tools)
   - [Database Migrator & Seeder (`cli/migrate.php`)](#database-migrator--seeder-climigratephp)
   - [Automated Verification & Benchmark Suite (`cli/test_endpoints.php`)](#automated-verification--benchmark-suite-clitest_endpointsphp)
10. [Configuration & Environment Variables](#configuration--environment-variables)
11. [Web Server Deployment Guide](#web-server-deployment-guide)
    - [Apache (.htaccess)](#apache-htaccess-included)
    - [Nginx Configuration](#nginx-server-block)
12. [Security & Production Hardening](#security--production-hardening)
13. [Troubleshooting & FAQ](#troubleshooting--faq)
14. [License](#license)

---

## System Architecture & Directory Structure

The `api/` directory is **100% self-contained and isolated**. It can be deployed directly to an independent subdomain (e.g. `https://colormagic-api.techkreative.com/`), hosted as a subfolder in a monolithic deployment, or run in containerized environments.

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
├── cli/                          <-- CLI Maintenance & Verification Tools
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
        │   ├── BaseController.php<-- Query parsing, JSON input reader, request context
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
| **SQLite WAL Mode Engine** | Configured with `PRAGMA journal_mode = WAL;`, `synchronous = NORMAL;`, `temp_store = MEMORY;`, `cache_size = -64000;` (64MB memory cache), `mmap_size = 268435456;` (256MB memory mapping), and `busy_timeout = 5000;`. Enables non-blocking concurrent reads during writes. |
| **B-Tree Database Indexing** | Dedicated indexes on `colors(slug)`, `colors(name)`, `gradients(style)`, `gradients(type)`, `palettes(style)`, and `user_palettes(status)` guarantee sub-millisecond execution. |
| **HTTP 304 ETag Negotiation** | Computes MD5 checksums for every successful payload. Returns `304 Not Modified` with zero response body when client sends a matching `If-None-Match` header. |
| **Dual-Layer JSON Fail-Safe** | If SQLite PDO drivers are uninstalled or the database file is temporarily inaccessible, repositories automatically switch to in-memory JSON file streams without service disruption. |
| **Transparent Gzip Compression** | Payloads are compressed on-the-fly via Apache `mod_deflate` and PHP output buffers, reducing JSON payload transfer size by up to 85%. |
| **Zero External Dependencies** | Built using pure native PHP (PDO, SPL, JSON) with no Composer dependencies required. Compatible with any shared hosting, VPS, or serverless PHP runtime. |

---

## Database Schema & Storage Architecture

The database is structured as an indexed SQLite schema with dedicated tables for core color entities:

```sql
-- 1. Colors Table
CREATE TABLE IF NOT EXISTS colors (
    hex TEXT PRIMARY KEY NOT NULL,
    name TEXT NOT NULL,
    slug TEXT NOT NULL,
    aliases TEXT NOT NULL DEFAULT '[]',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_colors_slug ON colors(slug);
CREATE INDEX IF NOT EXISTS idx_colors_name ON colors(name);

-- 2. Gradients Table
CREATE TABLE IF NOT EXISTS gradients (
    id TEXT PRIMARY KEY NOT NULL,
    name TEXT NOT NULL,
    style TEXT NOT NULL,
    type TEXT NOT NULL,
    colors TEXT NOT NULL,
    css TEXT NOT NULL,
    angle INTEGER DEFAULT NULL,
    shape TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_gradients_style ON gradients(style);
CREATE INDEX IF NOT EXISTS idx_gradients_type ON gradients(type);

-- 3. Curated Palettes Table
CREATE TABLE IF NOT EXISTS palettes (
    id TEXT PRIMARY KEY NOT NULL,
    name TEXT NOT NULL,
    style TEXT NOT NULL,
    colors TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_palettes_style ON palettes(style);

-- 4. User Submitted Palettes Table
CREATE TABLE IF NOT EXISTS user_palettes (
    id TEXT PRIMARY KEY NOT NULL,
    user_id TEXT DEFAULT NULL,
    name TEXT NOT NULL,
    style TEXT NOT NULL DEFAULT 'Custom',
    colors TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_user_palettes_status ON user_palettes(status);
```

---

## Quick Start & Root Discovery

### Root Discovery Endpoint (`GET /`)

The root endpoint provides machine-readable API discovery, endpoint URL maps, query parameter specifications, and documentation links.

- **URL:** `GET https://colormagic-api.techkreative.com/`
- **Method:** `GET`
- **Headers:** `Accept: application/json`

#### Response (`200 OK`):
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
  "timestamp": "2026-09-03T17:35:00+00:00"
}
```

---

## Complete API Route & Endpoint Reference

### 1. Health & System Diagnostics

Inspect database engine health, table statistics, journal mode, and active configuration.

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/v2/health` | Full system diagnostic, database metrics & endpoint map |
| `GET` | `/health` | Shorthand alias to `/v2/health` |

#### Response (`200 OK`):
```json
{
  "status": "success",
  "latency_ms": 0.28,
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
    "timestamp": "2026-09-03T17:35:00+00:00"
  }
}
```

---

### 2. Colors API

Query the dataset of **1,027+ named colors** with search, pagination, alias resolution, and dictionary exports.

| Method | Route | Description |
|---|---|---|
| `GET` | `/v2/colors` | List, search, paginate colors or fetch full dictionary |
| `GET` | `/v2/colors/{hex}` | Single color lookup by 3-8 char hex code (e.g. `123524`, `FF5733`) |
| `GET` | `/v2/colors/slug/{slug}` | Single color lookup by URL slug (e.g. `phthalo-green`, `midnight-blue`) |

#### Query Parameters for `GET /v2/colors`

| Parameter | Type | Default | Description |
|---|---|---|---|
| `q` | `string` | `""` | Search query matching name, hex code, or slug (e.g. `blue`, `emerald`, `123524`) |
| `page` | `integer` | `1` | Page number for pagination (min: `1`) |
| `limit` | `integer` | `50` | Records per page (min: `1`, max: `200`) |
| `format` | `string` | `"list"` | Set to `"dict"` to retrieve the entire dataset keyed by hex code |
| `hex` | `string` | `""` | Lookup single color via query param (`?hex=123524`) |
| `slug` | `string` | `""` | Lookup single color via query param (`?slug=phthalo-green`) |

#### Color Response Examples

##### Single Color Lookup (`GET /v2/colors/123524`):
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
  "latency_ms": 0.38,
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
  "latency_ms": 0.79,
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

Query **329+ hand-crafted CSS gradients** (linear and radial) with full CSS output, angles, color stops, and style categorization.

| Method | Route | Description |
|---|---|---|
| `GET` | `/v2/gradients` | List, filter, search, and paginate gradients |
| `GET` | `/v2/gradients/{id}` | Single gradient lookup by unique ID (e.g. `gradient_1`) |

#### Query Parameters for `GET /v2/gradients`

| Parameter | Type | Default | Description |
|---|---|---|---|
| `q` | `string` | `""` | Search gradient name, style, type, or hex color |
| `style` | `string` | `null` | Filter by aesthetic style: `Warm`, `Cool`, `Purple`, `Nature`, `Pink`, `Dark`, `Pastel`, `Neon`, `Earth`, `Mono` |
| `type` | `string` | `null` | Filter by gradient type: `linear` or `radial` |
| `page` | `integer` | `1` | Page number for pagination |
| `limit` | `integer` | `50` | Records per page (max: `200`) |
| `id` | `string` | `""` | Retrieve single gradient via query parameter (`?id=gradient_1`) |

#### Gradient Response Examples

##### Single Gradient Lookup (`GET /v2/gradients/gradient_1`):
```json
{
  "status": "success",
  "latency_ms": 0.11,
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
  "latency_ms": 0.44,
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

Explore **833+ curated color palettes** categorized by themes (Eco, Pastel, Vintage, Neon, Monochrome, Earthy, Cyberpunk, Minimal).

| Method | Route | Description |
|---|---|---|
| `GET` | `/v2/palettes` | List, search, and filter curated color palettes |
| `GET` | `/v2/palettes/{id}` | Single palette lookup by ID (e.g. `palette_1`) |

#### Query Parameters for `GET /v2/palettes`

| Parameter | Type | Default | Description |
|---|---|---|---|
| `q` | `string` | `""` | Search palette name, style, or embedded color hex code |
| `style` | `string` | `null` | Filter by palette style: `Eco`, `Pastel`, `Vintage`, `Neon`, `Monochrome`, `Earthy`, `Cyberpunk`, `Minimal` |
| `page` | `integer` | `1` | Page number for pagination |
| `limit` | `integer` | `50` | Records per page (max: `200`) |
| `id` | `string` | `""` | Retrieve single palette via query parameter (`?id=palette_1`) |

#### Palette Response Examples

##### Single Palette Lookup (`GET /v2/palettes/palette_1`):
```json
{
  "status": "success",
  "latency_ms": 0.10,
  "data": {
    "id": "palette_1",
    "name": "Forest Breath",
    "style": "Eco",
    "colors": ["#2D5A27", "#4E8752", "#87B37A", "#D1E2C4", "#F4F7F2"]
  }
}
```

##### Filtered Palette Search (`GET /v2/palettes?style=Eco&limit=1`):
```json
{
  "status": "success",
  "latency_ms": 0.39,
  "total": 62,
  "page": 1,
  "limit": 1,
  "data": [
    {
      "id": "palette_1",
      "name": "Forest Breath",
      "style": "Eco",
      "colors": ["#2D5A27", "#4E8752", "#87B37A", "#D1E2C4", "#F4F7F2"]
    }
  ]
}
```

---

### 5. Community Palette Submission

Submit user-generated or community palettes to the database for moderation and storage.

- **URL:** `POST https://colormagic-api.techkreative.com/v2/palettes`
- **Headers:** `Content-Type: application/json`

#### Request Body Schema:

```json
{
  "name": "Nordic Aurora",
  "style": "Cool",
  "colors": ["#2E3440", "#3B4252", "#88C0D0", "#81A1C1", "#ECEFF4"],
  "user_id": "usr_99a81b2"
}
```

| Field | Type | Required | Description |
|---|---|---|---|
| `name` | `string` | **Yes** | Palette display name (e.g. `"Nordic Aurora"`) |
| `colors` | `array<string>` | **Yes** | Array of at least 2 valid hex color codes (e.g. `["#2E3440", "#88C0D0"]`) |
| `style` | `string` | No | Category tag (default: `"Custom"`) |
| `user_id` | `string` | No | Identifier of the submitting user / session |

#### Response (`201 Created`):
```json
{
  "status": "success",
  "latency_ms": 0.58,
  "message": "Palette submitted successfully for review",
  "data": {
    "id": "user_pal_66d74b88d3a12.98124501",
    "name": "Nordic Aurora",
    "style": "Cool",
    "status": "pending"
  }
}
```

#### Validation Error Response (`422 Unprocessable Entity`):
```json
{
  "status": "error",
  "message": "A palette must contain at least 2 valid hex color codes",
  "latency_ms": 0.09
}
```

---

### 6. Legacy V1 Backward Compatibility

All V1 endpoints remain 100% active, serving the same response schemas as legacy versions while benefiting from the SQLite acceleration engine.

| Legacy Endpoint | Method | Supported Parameters | Output |
|---|---|---|---|
| `/v1/colors` | `GET` | `?hex=123524`, `?slug=phthalo-green`, `?q=blue`, `?page=1&limit=20` | Color records / keyed dictionary |
| `/v1/gradients` | `GET` | `?id=gradient_1`, `?style=Warm`, `?type=linear`, `?q=sunset` | Gradient array / object |
| `/v1/palettes` | `GET` | `?id=palette_1`, `?style=Eco`, `?q=forest` | Palette array / object |
| `/v1/health` | `GET` | None | API root discovery |

---

### 7. Direct Static JSON Datasets

For embedded offline bundles, build-step prerendering, or raw file downloads, datasets can be accessed directly:

- `https://colormagic-api.techkreative.com/color-names.json` (1,027 colors)
- `https://colormagic-api.techkreative.com/gradients.json` (329 gradients)
- `https://colormagic-api.techkreative.com/palettes.json` (833 palettes)

---

## Standard Request & Response Envelope

### Success Envelope

All successful responses (2xx) follow a consistent wrapper:

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

- `status`: Always `"success"`.
- `latency_ms`: Execution latency in milliseconds (high-resolution timer).
- `total` / `page` / `limit`: Included when returning paginated datasets.
- `data`: Response payload (Object or Array).

### Error Envelope

Errors return appropriate HTTP status codes and never return cached responses:

```json
{
  "status": "error",
  "message": "Color not found for hex code '#ZZZZZZ'",
  "latency_ms": 0.12,
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

| Code | Status | Trigger Condition |
|---|---|---|
| `200 OK` | Success | Standard GET query or retrieval succeeded |
| `201 Created` | Created | Palette created via `POST /v2/palettes` |
| `304 Not Modified` | Not Modified | Client `If-None-Match` header matches response `ETag` |
| `400 Bad Request` | Client Error | Malformed query parameters or invalid syntax |
| `404 Not Found` | Not Found | Route, color hex, slug, gradient ID, or palette ID not found |
| `422 Unprocessable Entity` | Validation Error | Missing required fields or `< 2` valid hex colors on palette submission |
| `500 Internal Server Error` | Server Fault | Unhandled exception without JSON fallback |

---

## Multi-Language Client Integration Recipes

### 1. cURL

```bash
# 1. Lookup single color with ETag support
curl -i https://colormagic-api.techkreative.com/v2/colors/123524

# 2. Conditional request (returns 304 Not Modified if unchanged)
curl -i https://colormagic-api.techkreative.com/v2/colors/123524 \
  -H 'If-None-Match: "3a8bb68c5b6bda481f33cc26a27e7d7b"'

# 3. Filter gradients by style
curl -i "https://colormagic-api.techkreative.com/v2/gradients?style=Warm&limit=10"

# 4. Submit a new palette
curl -X POST https://colormagic-api.techkreative.com/v2/palettes \
  -H "Content-Type: application/json" \
  -d '{"name": "Cyberpunk Neon", "style": "Neon", "colors": ["#00F0FF", "#7000FF", "#FF007B"]}'
```

---

### 2. JavaScript / TypeScript (Fetch API)

```typescript
const BASE_URL = 'https://colormagic-api.techkreative.com';

// Lookup color by Hex
async function getColor(hex: string) {
  const clean = hex.replace('#', '');
  const res = await fetch(`${BASE_URL}/v2/colors/${clean}`);
  if (!res.ok) throw new Error(`HTTP error: ${res.status}`);
  const { data } = await res.json();
  return data;
}

// Search Gradients
async function searchGradients(query: string, style = 'Warm') {
  const params = new URLSearchParams({ q: query, style, limit: '20' });
  const res = await fetch(`${BASE_URL}/v2/gradients?${params}`);
  const { data, total } = await res.json();
  return { gradients: data, total };
}

// Submit Community Palette
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

### 3. Node.js (Axios)

```javascript
const axios = require('axios');

const api = axios.create({
  baseURL: 'https://colormagic-api.techkreative.com',
  timeout: 5000,
});

async function run() {
  // 1. Fetch color
  const colorRes = await api.get('/v2/colors/50C878');
  console.log('Color Name:', colorRes.data.data.name);

  // 2. Fetch palettes
  const palettesRes = await api.get('/v2/palettes', {
    params: { style: 'Eco', limit: 5 }
  });
  console.log('Palettes Count:', palettesRes.data.total);
}

run().catch(console.error);
```

---

### 4. Python (requests)

```python
import requests

BASE_URL = "https://colormagic-api.techkreative.com"

# 1. Lookup single color
res = requests.get(f"{BASE_URL}/v2/colors/123524")
if res.status_code == 200:
    color = res.json()["data"]
    print(f"Color: {color['name']} ({color['hex']})")

# 2. Search Palettes
res = requests.get(f"{BASE_URL}/v2/palettes", params={"q": "forest", "style": "Eco", "limit": 5})
data = res.json()
print(f"Found {data['total']} palettes:")
for p in data["data"]:
    print(f"- {p['name']}: {p['colors']}")

# 3. Submit Palette
submission = {
    "name": "Nordic Dusk",
    "style": "Cool",
    "colors": ["#2E3440", "#3B4252", "#88C0D0", "#ECEFF4"]
}
post_res = requests.post(f"{BASE_URL}/v2/palettes", json=submission)
print("Submission Result:", post_res.json())
```

---

### 5. PHP (Native cURL)

```php
<?php

$baseUrl = 'https://colormagic-api.techkreative.com';

// 1. Single Color Lookup
$ch = curl_init("{$baseUrl}/v2/colors/123524");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

echo "Color: " . $response['data']['name'] . "\n";

// 2. Submit Palette
$payload = json_encode([
    'name'   => 'Sunset Glow',
    'style'  => 'Warm',
    'colors' => ['#FF4E50', '#F9D423', '#EDE574']
]);

$ch = curl_init("{$baseUrl}/v2/palettes");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$result = json_decode(curl_exec($ch), true);
curl_close($ch);

print_r($result);
```

---

### 6. Go (net/http)

```go
package main

import (
	"encoding/json"
	"fmt"
	"net/http"
)

type ColorResponse struct {
	Status string `json:"status"`
	Data   struct {
		Hex  string `json:"hex"`
		Name string `json:"name"`
		Slug string `json:"slug"`
	} `json:"data"`
}

func main() {
	resp, err := http.Get("https://colormagic-api.techkreative.com/v2/colors/123524")
	if err != nil {
		panic(err)
	}
	defer resp.Body.Close()

	var result ColorResponse
	json.NewDecoder(resp.Body).Decode(&result)

	fmt.Printf("Color: %s (%s)\n", result.Data.Name, result.Data.Hex)
}
```

---

## CLI Maintenance & Automation Tools

The `api/cli/` directory provides command-line utilities for maintaining the SQLite database, running migrations, and executing automated verification suites.

### Database Migrator & Seeder (`cli/migrate.php`)

```bash
# Standard migration & seed (skips seeding if tables already contain data)
php api/cli/migrate.php

# Force wipe and re-seed from JSON files
php api/cli/migrate.php --force

# Inspect database stats without altering data
php api/cli/migrate.php --stats
```

#### Output Example:
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

### Automated Verification & Benchmark Suite (`cli/test_endpoints.php`)

Runs assertions across the entire API repository stack, benchmarks latency, and verifies fallback layers.

```bash
php api/cli/test_endpoints.php
```

#### Output Example:
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

Configuration is loaded from `.env.api.colormagic` in the `api/` directory (or falls back to `.env.api.colormagic` / `.env` in the parent directory).

```ini
# Application Configuration
APP_NAME=ColorMagic-API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://colormagic-api.techkreative.com
API_VERSION=v2

# SQLite Database Storage (relative to api/ directory or absolute path)
DB_DRIVER=sqlite
DB_PATH=data/colormagic.sqlite

# Cache & Performance Tuning
CACHE_ENABLED=true
CACHE_TTL=86400
GZIP_COMPRESSION=true

# Security & Future Rate Limiting
API_SECRET_KEY=change_me_to_a_random_32_character_secret_key
RATE_LIMIT_ENABLED=false
RATE_LIMIT_MAX_REQUESTS=120
RATE_LIMIT_WINDOW=60
```

---

## Web Server Deployment Guide

### Apache (.htaccess Included)

The included `api/.htaccess` file configures CORS, Gzip, and URL rewriting:

```apache
DirectoryIndex index.php
Options -Indexes +FollowSymLinks

# Enable CORS Headers
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
</IfModule>

# MIME Type for Static JSON
AddType application/json .json

# Enable Gzip Compression for Payloads
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE application/json
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On

    # OPTIONS Preflight Handling
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ - [R=200,L]

    # Serve Existing Files
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ - [L]

    # Shorthand Route Aliases
    RewriteRule ^health/?$ v2/index.php [L,QSA]
    RewriteRule ^colors?/([0-9A-Fa-f]{3,8})/?$ v2/index.php [L,QSA]

    # V1 Legacy Endpoints
    RewriteRule ^v1/gradients/?$ gradients.php [L,QSA]
    RewriteRule ^v1/palettes/?$ palettes.php [L,QSA]
    RewriteRule ^v1/colors/?$ colors.php [L,QSA]
    RewriteRule ^v1/health/?$ index.php [L,QSA]

    RewriteRule ^gradients/?$ gradients.php [L,QSA]
    RewriteRule ^palettes/?$ palettes.php [L,QSA]
    RewriteRule ^colors/?$ colors.php [L,QSA]
</IfModule>
```

### Nginx Server Block

If deploying on an Nginx server, use the following server configuration:

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name colormagic-api.techkreative.com;
    root /var/www/colormagic/api;
    index index.php;

    # Gzip Compression
    gzip on;
    gzip_types application/json text/plain text/css;

    # Global CORS Headers
    add_header Access-Control-Allow-Origin "*" always;
    add_header Access-Control-Allow-Methods "GET, POST, OPTIONS" always;
    add_header Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With" always;

    # Prevent direct download of SQLite database
    location ~* \.sqlite$ {
        deny all;
        return 404;
    }

    # Route V2 API requests
    location /v2/ {
        try_files $uri $uri/ /v2/index.php?$query_string;
    }

    # Root and V1 Route handling
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

## Security & Production Hardening

1. **SQL Injection Protection:** All database repository lookups use PDO parameterized prepared statements (`:hex`, `:slug`, `:id`, `:style`).
2. **Database Direct Download Prevention:** `.htaccess` and Nginx rules prevent web access to `.sqlite` or `.sqlite-wal` files.
3. **Input Sanitization:** Hex codes, slugs, and ID parameters are validated with strict regular expressions (`^[0-9A-Fa-f]{3,8}$`).
4. **ETag & No-Cache Error Responses:** Error responses explicitly set `Cache-Control: no-store, no-cache` to avoid cache poisoning of transient errors.

---

## Troubleshooting & FAQ

#### Q: What happens if `pdo_sqlite` is not installed in the PHP environment?
The repositories detect the missing extension and transparently switch to reading the local JSON seed files (`color-names.json`, `gradients.json`, `palettes.json`). API responses remain fully functional.

#### Q: How do I re-seed or rebuild the database after adding new colors?
Run the CLI migrator with the force flag:
```bash
php api/cli/migrate.php --force
```

#### Q: Why do single-record lookups return 304 Not Modified?
The API automatically sends `ETag` headers. When a browser or client includes `If-None-Match`, the server returns `304 Not Modified` with zero body transfer for maximum bandwidth savings.

---

## License

ColorMagic API is open-source software licensed under the [MIT License](file:///e:/Coding/color_magic/LICENSE).
