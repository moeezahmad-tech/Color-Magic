# Color Magic — Social Media Publishing Dashboard (`Dashboard_App`)

A modern, fast, standalone Node.js & Express application for 1-click and batch social media publishing (Pinterest via UniPost & Instagram via Meta Graph API) for Color Magic's palettes, CSS gradients, and named colors.

---

## 🚀 Quick Start

### 1. Install Dependencies
```bash
cd Dashboard_App
npm install
```

### 2. Configure Environment (`.env`)
The `.env` file has been automatically placed inside this directory with your UniPost key and Pinterest configuration:
```env
PORT=3000
UNIPOST_KEY=up_live_...
UNIPOST_PINTEREST_ACCOUNT_ID=e8a6759b-e0d3-4118-8c59-e8e5bf990171
PINTEREST_BOARD_PALETTES=1092193415823453337
PINTEREST_BOARD_GRADIENTS=1092193415823453436
PINTEREST_BOARD_COLORS=1092193415823453337

# For Instagram Content Publishing:
INSTAGRAM_ACCOUNT_ID=
INSTAGRAM_ACCESS_TOKEN=
```

### 3. Launch the Server
```bash
npm start
```
Then visit:
👉 **[http://localhost:3000](http://localhost:3000)**

---

## 🎨 Features & Architecture

- **Independent from Main PHP App**: The main PHP application remains clean, lightweight, and focused on users and SEO, with zero dashboard or admin overhead.
- **Direct Dataset Sync**: Directly reads and updates `../data/palettes.json`, `../data/gradients.json`, and `../data/color-names.json` when pins and Instagram posts are published.
- **RESTful Endpoints**:
  - `GET /api/stats`: Overview counts & posting progress
  - `GET /api/palettes`: Filterable, searchable, paginated palettes
  - `GET /api/gradients`: Linear, radial, mesh gradient explorer
  - `GET /api/colors`: 1,027 curated named colors
  - `POST /api/post/pinterest`: 1-click posting to Pinterest via UniPost
  - `POST /api/post/instagram`: 1-click posting to Instagram via Graph API
  - `POST /api/batch/pinterest`: Auto-post runner for unposted items
- **Sleek UI**: Tailwind CSS, Bootstrap Icons, dark/light mode toggle, instant toast notifications.

