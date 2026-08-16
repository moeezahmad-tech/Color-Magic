# ColorMagic Public API

Public REST API for ColorMagic color datasets, hosted at `https://api.colormagic.techkreative.com/`.

All responses are returned as JSON with CORS enabled (`Access-Control-Allow-Origin: *`).

## Base Endpoints

- **API Info & Health**: `GET /` or `GET /v1/health`
- **Gradients**: `GET /v1/gradients` or `GET /gradients.json`
- **Palettes**: `GET /v1/palettes` or `GET /palettes.json`
- **Colors**: `GET /v1/colors` or `GET /color-names.json`

## Example Usage

### Get Gradients
- All Gradients: `GET https://api.colormagic.techkreative.com/v1/gradients`
- Filter by style: `GET https://api.colormagic.techkreative.com/v1/gradients?style=Warm`
- Filter by search query: `GET https://api.colormagic.techkreative.com/v1/gradients?q=sunset`
- Single item by ID: `GET https://api.colormagic.techkreative.com/v1/gradients?id=gradient_1`
- Paginated: `GET https://api.colormagic.techkreative.com/v1/gradients?page=1&limit=20`

### Get Palettes
- All Palettes: `GET https://api.colormagic.techkreative.com/v1/palettes`
- Search by query: `GET https://api.colormagic.techkreative.com/v1/palettes?q=ocean`
- Single item by ID: `GET https://api.colormagic.techkreative.com/v1/palettes?id=palette_1`
- Paginated: `GET https://api.colormagic.techkreative.com/v1/palettes?page=1&limit=20`

### Get Colors
- Color dataset: `GET https://api.colormagic.techkreative.com/v1/colors`
- Single color by hex: `GET https://api.colormagic.techkreative.com/v1/colors?hex=EC4899`
- Single color by slug: `GET https://api.colormagic.techkreative.com/v1/colors?slug=lapis-lazuli`
- Search by query: `GET https://api.colormagic.techkreative.com/v1/colors?q=blue`
- Paginated: `GET https://api.colormagic.techkreative.com/v1/colors?page=1&limit=20`
