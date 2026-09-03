# ColorMagic Public REST API (V2 SQLite Edition)

Production-grade, ultra-fast, self-contained REST API for ColorMagic color datasets, hosted at `https://api.colormagic.techkreative.com/`.

Built with modern PHP OOP architecture, **SQLite in WAL (Write-Ahead Logging) mode**, HTTP ETags (304 Not Modified caching), and sub-millisecond query execution.

---

## Standalone & Isolated API Structure

The `api/` directory is **100% self-contained** and can be deployed directly to `api.colormagic` on your production server:

```
api.colormagic/ (or api/ directory)
├── .env.api.colormagic           <-- API Environment Configuration (Isolated)
├── .env.api.example              <-- Environment Template
├── .htaccess                     <-- Production URL Rewriting & Performance Headers
├── index.php                     <-- Root Entry & API Discovery
├── README.md                     <-- Documentation & Specs
├── data/                         <-- Local SQLite & JSON Datasets
│   ├── colormagic.sqlite         <-- SQLite Database (WAL mode, indexed)
│   ├── color-names.json          <-- Seed / Fallback Dataset (1,027 colors)
│   ├── gradients.json            <-- Seed / Fallback Dataset (329 gradients)
│   └── palettes.json             <-- Seed / Fallback Dataset (833 palettes)
├── cli/                          <-- CLI Maintenance Tools
│   ├── migrate.php               <-- Database Migrator & Seeder
│   └── test_endpoints.php        <-- Automated Verification Suite
├── v1/                           <-- V1 Backward-Compatible Handlers
│   ├── colors.php
│   ├── gradients.php
│   ├── palettes.php
│   └── index.php
└── v2/                           <-- V2 High-Performance REST Architecture
    ├── index.php                 <-- V2 Front Controller & Router
    └── src/
        ├── Config/Env.php        <-- Self-Contained Environment Loader
        ├── Database/Database.php <-- SQLite WAL Connection Pool
        ├── Controllers/          <-- REST Controllers
        │   ├── BaseController.php
        │   ├── ColorController.php
        │   ├── GradientController.php
        │   ├── PaletteController.php
        │   └── HealthController.php
        ├── Repositories/         <-- Indexed Data Repositories
        │   ├── ColorRepository.php
        │   ├── GradientRepository.php
        │   └── PaletteRepository.php
        └── Services/             <-- Performance & Migration Services
            ├── MigrationService.php
            └── ResponseHelper.php
```

---

## Performance Highlights

- **SQLite WAL Mode Engine**: Fast indexed single-record lookups (< 0.2 ms) and paginated queries (< 0.8 ms).
- **HTTP 304 Caching**: Automatically calculates `ETag` and responds with `304 Not Modified` when content hasn't changed.
- **Gzip Stream Compression**: Transparent payload compression via `ob_gzhandler`.
- **Environment Isolation**: Loaded directly from `api/.env.api.colormagic` (with automatic fallback to parent directory if placed in a sibling server setup).
- **Zero-Downtime Backward Compatibility**: All legacy V1 endpoints transparently run on the SQLite engine with automatic JSON fail-safe fallback.

---

## API Routes & Endpoints

### V2 Endpoints (Recommended)

| Method | Route | Description | Query Parameters / Path Params |
|---|---|---|---|
| `GET` | `/v2/health` | Health diagnostics, DB status, record counts | None |
| `GET` | `/v2/colors` | List colors with search & pagination | `q`, `page`, `limit`, `format=dict` |
| `GET` | `/v2/colors/{hex}` | Single color lookup by 6-char hex | `hex` (e.g. `/v2/colors/123524`) |
| `GET` | `/v2/colors/slug/{slug}` | Single color lookup by slug | `slug` (e.g. `/v2/colors/slug/phthalo-green`) |
| `GET` | `/v2/gradients` | List CSS gradients with filters | `q`, `style`, `type`, `page`, `limit` |
| `GET` | `/v2/gradients/{id}` | Single gradient lookup by ID | `id` (e.g. `/v2/gradients/gradient_1`) |
| `GET` | `/v2/palettes` | List curated color palettes | `q`, `style`, `page`, `limit` |
| `GET` | `/v2/palettes/{id}` | Single palette lookup by ID | `id` (e.g. `/v2/palettes/palette_1`) |
| `POST`| `/v2/palettes` | Submit community palette (Dashboard) | JSON body with `name`, `style`, `colors` |

### V1 Endpoints (Legacy Compatible)

- **Colors**: `GET /v1/colors` (supports `?hex=123524`, `?slug=phthalo-green`, `?q=blue`, `?page=1&limit=20`)
- **Gradients**: `GET /v1/gradients` (supports `?id=gradient_1`, `?style=Warm`, `?q=sunset`)
- **Palettes**: `GET /v1/palettes` (supports `?id=palette_1`, `?style=Eco`, `?q=forest`)
- **Root**: `GET /` or `GET /v1/health`

---

## Example Usage & Curl Commands

### 1. Colors

```bash
# Direct single color by Hex
curl -i https://api.colormagic.techkreative.com/v2/colors/123524

# Direct single color by Slug
curl -i https://api.colormagic.techkreative.com/v2/colors/slug/phthalo-green

# Search colors with pagination
curl -i "https://api.colormagic.techkreative.com/v2/colors?q=blue&page=1&limit=20"

# Fetch all colors as a keyed dictionary
curl -i "https://api.colormagic.techkreative.com/v2/colors?format=dict"
```

### 2. Gradients

```bash
# Get all gradients (paginated)
curl -i "https://api.colormagic.techkreative.com/v2/gradients?page=1&limit=20"

# Filter by style (e.g. Warm, Cool, Pastel, Dark, Neon)
curl -i "https://api.colormagic.techkreative.com/v2/gradients?style=Warm"

# Filter by gradient type (linear, radial)
curl -i "https://api.colormagic.techkreative.com/v2/gradients?type=radial"

# Get single gradient by ID
curl -i https://api.colormagic.techkreative.com/v2/gradients/gradient_1
```

### 3. Palettes

```bash
# Get curated palettes
curl -i "https://api.colormagic.techkreative.com/v2/palettes?page=1&limit=20"

# Filter by style (Eco, Pastel, Vintage, Neon, Monochrome, Earthy)
curl -i "https://api.colormagic.techkreative.com/v2/palettes?style=Eco"

# Search palettes
curl -i "https://api.colormagic.techkreative.com/v2/palettes?q=forest"

# Get single palette by ID
curl -i https://api.colormagic.techkreative.com/v2/palettes/palette_1

# Submit new community palette (User Dashboard)
curl -X POST https://api.colormagic.techkreative.com/v2/palettes \
  -H "Content-Type: application/json" \
  -d '{"name": "Nordic Dusk", "style": "Cool", "colors": ["#2E3440", "#3B4252", "#88C0D0", "#ECEFF4"]}'
```

---

## Database Management & CLI Commands

```bash
# Run migrations & seed data into api/data/colormagic.sqlite
php api/cli/migrate.php

# Force re-seed
php api/cli/migrate.php --force

# Inspect database stats
php api/cli/migrate.php --stats

# Run full automated verification suite
php api/cli/test_endpoints.php
```
