# Color Magic

**Professional Color Palette Generator & Explorer**

A beautiful, fully-featured open-source web application for designers and developers to explore color palettes, discover CSS gradients, find color names from hex codes, and generate professional color schemes using color theory.

![Color Magic](assets/images/logo.png)

## Live Demo

Visit: [colormagic.techkreative.com](https://colormagic.techkreative.com)

## Features

### Explore Palettes (`/palettes`)
- Browse 150+ professionally curated color palettes
- Filter by style: Pastel, Vintage, Neon, Minimalist, Earthy, Eco
- Search palettes by name, theme, or hex code
- Copy entire palettes or individual swatches to clipboard
- Save favorites locally with heart toggle

### CSS Gradients (`/gradients`)
- Browse 100 hand-crafted CSS gradients (linear & radial)
- 10 style categories: Warm, Cool, Purple, Nature, Pink, Dark, Pastel, Neon, Earth, Mono
- Filter by type (Linear / Radial) and style
- Live gradient preview cards with copy-ready CSS output
- Copy individual hex codes from color swatches

### Find Color (`/find-color`)
- Enter any hex code to discover color information
- Get color names from a curated database of 300+ named colors with alias support
- View RGB, HSL values, luminance, and contrast information
- Copy hex and RGB values with one click

### Generate Palette (`/generate-palette`)
- Create 5-color palettes from any base color
- Choose from color theory schemes: Mono, Contrast, Triade, Tetrade, Analogic
- Apply variations: Classic, Soft & Muted, Deep & Bold
- All calculations done locally — no API required

### Palette from Image (`/palette-from-image`)
- Upload any photo to extract its dominant color palette
- K-means++ clustering algorithm runs entirely in the browser — images never leave your device
- Adjustable color count (3–12) with a real-time slider
- Copy individual hex codes, open any color detail page, or copy the full palette at once
- Full RGB and HSL values for every extracted color

### Saved Palettes (`/palettes?filter=favorites`)
- Favorites are stored in the browser via `localStorage`
- Access from the home page card or the mobile sidebar

### Color Detail Pages
- **Hex route** (`/color/{hex}`) — e.g. `/color/FF5733` — shows full color info
- **Slug route** (`/color/{name}`) — e.g. `/color/midnight-blue` — named color lookup

### Palette Detail Page (`/palette/{slug}`)
- Full palette view with contrast checks, brightness chart, and export formats
- Export as HEX array, JSON, SCSS variables, or Tailwind config

## Page Routes

All pages use **clean, extensionless URLs** enforced by `.htaccess`.

| URL | PHP File | Description |
|-----|----------|-------------|
| `/` | `index.php` | Home page — hero section + 6-card feature grid |
| `/palettes` | `palettes.php` | Explore & search all palettes |
| `/palettes?filter=favorites` | `palettes.php` | Saved favorites view |
| `/gradients` | `gradients.php` | Browse CSS gradients |
| `/find-color` | `find-color.php` | Hex-to-name color lookup |
| `/generate-palette` | `generate-palette.php` | Color theory palette generator |
| `/palette-from-image` | `palette-from-image.php` | Extract palette from uploaded image |
| `/favorites` | `favorites.php` | Saved colors, palettes & gradients |
| `/hex-to-color-name` | `hex-to-color-name.php` | Hex-to-color-name lookup tool |
| `/hex-to-rgb` | `hex-to-rgb.php` | Hex-to-RGB converter |
| `/what-color-is` | `what-color-is.php` | Color identification tool |
| `/dark-color-finder` | `dark-color-finder.php` | Find dark shades of a color |
| `/brand-color-lookup` | `brand-color-lookup.php` | Brand color reference |
| `/color/{hex}` | `color.php?hex=` | Dynamic color detail (hex) |
| `/color/{slug}` | `color.php?slug=` | Dynamic color detail (name) |
| `/palette/{slug}` | `palette.php?slug=` | Palette detail page |
| `/colors/{hex}.webp` | `colorImage.php?hex=` | Color preview image (OG/SEO) |
| `/open-source` | `open-source.php` | GitHub & contributor info |

## Clean URL Routing (`.htaccess`)

The `.htaccess` file handles all URL rewriting with the following rule chain:

1. **Strip `index.php` / `index.html`** — `/index.php` redirects to `/`
2. **Strip extensions** — `/palettes.php` or `/page.html` redirects to `/palettes` or `/page`
3. **Custom dynamic routes** — `/color/FF5733` → `color.php?hex=FF5733`, `/palette/sunset` → `palette.php?slug=sunset`
4. **Generic extensionless resolution** — `/gradients` internally resolves to `gradients.php` if the file exists

All redirects use `THE_REQUEST` guards to prevent redirect loops from internal rewrites. Local and production hosts are handled with separate rules (local prepends `/ColorMagic/`).

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, JavaScript (ES5-compatible IIFE modules) |
| **Backend** | PHP (page rendering, JSON serving, dynamic routes) |
| **Styling** | Tailwind CSS (CDN) + custom `main.css` |
| **Icons** | Bootstrap Icons 1.11 |
| **Fonts** | Inter (Google Fonts — Variable, 14–32pt, 100–900 weight) |
| **Deployment** | GitHub Actions → FTP via `main.yml` workflow |
| **SEO** | XML sitemap index + sub-sitemaps, `robots.txt`, OG meta tags |
| **PWA** | `manifest.json` with standalone display mode |

## Project Structure

```
ColorMagic/
├── .github/
│   └── workflows/
│       ├── main.yml                    ← CI/CD: deploy to FTP
│       └── test.yml                    ← CI: automated tests
│
├── .htaccess                           ← URL rewriting & clean routes
├── robots.txt                          ← Search engine crawl rules
├── sitemap.xml                         ← Sitemap index (points to sub-sitemaps)
├── manifest.json                       ← PWA manifest
├── BingSiteAuth.xml                    ← Bing verification
├── yandex_13ee0143663b95c8.html       ← Yandex verification
├── LICENSE                             ← MIT License
│
├── index.php                           ← Home page
├── palettes.php                        ← Explore palettes page
├── gradients.php                       ← CSS gradients browser
├── find-color.php                      ← Hex-to-name color finder
├── generate-palette.php                ← Color theory palette generator
├── palette-from-image.php                ← Image-to-palette extractor
├── favorites.php                         ← Saved favorites page
├── open-source.php                       ← GitHub & contributors
│
├── color.php                           ← Dynamic color detail (hex & slug)
├── palette.php                         ← Palette detail page (slug)
├── colorImage.php                      ← Color preview image generator (OG/SEO)
├── paletteImage.php                    ← Palette preview image (OG/SEO)
│
├── components/                         ← Shared PHP partials
│   ├── navbar.php                      ← Sticky header + Tools dropdown
│   └── footer.php                      ← Site footer
│
├── assets/
│   ├── css/
│   │   ├── main.css                    ← Global styles, dark-mode, glow effects
│   │   └── style.css                   ← Legacy / additional styles
│   ├── js/
│   │   ├── app.js                      ← Global app init
│   │   ├── tailwind-config.js          ← Tailwind theme & color tokens
│   │   ├── utils.js                    ← Shared helpers (hex/RGB conversion, etc.)
│   │   ├── explore-palettes.js         ← Palettes page controller
│   │   ├── gradients-page.js           ← Gradients page controller
│   │   ├── find-color-page.js          ← Find Color page logic
│   │   ├── generate-palette.js         ← Color theory math engine
│   │   ├── generate-palette-page.js    ← Generate Palette page controller
│   │   ├── palette-from-image.js         ← Image palette extraction engine
│   │   ├── favorites-page.js            ← Favorites page controller
│   │   ├── palette-page.js             ← Palette detail page controller
│   │   ├── components/
│   │   │   └── palette-card.js         ← Reusable palette card component
│   │   └── services/
│   │       └── favorites.js            ← localStorage favorites service
│   ├── images/
│   │   ├── logo.png
│   │   └── TopContributers/
│   │       └── MoeezAhmad.webp
│   └── Inter/                          ← Self-hosted Inter font files
│       ├── Inter-VariableFont_opsz,wght.ttf
│       ├── Inter-Italic-VariableFont_opsz,wght.ttf
│       └── static/                     ← Individual weight .ttf files
│
├── data/                               ← Static JSON data files
│   ├── palettes.json                   ← 150+ curated palettes
│   ├── gradients.json                  ← 100 CSS gradients (linear & radial)
│   └── color-names.json                ← 300+ named colors keyed by hex
│
└── sitemaps/                           ← Sub-sitemaps for SEO
    ├── pages.xml                       ← Static page URLs
    ├── palettes.xml                    ← All palette detail URLs
    └── colors.xml                      ← All color detail URLs
```

## Data Formats

### `data/palettes.json`

Array of palette objects:

```json
[
  {
    "id": "palette_1",
    "style": "Eco",
    "name": "Forest Breath",
    "colors": ["#1B5E20", "#2E7D32", "#66BB6A", "#C8E6C9"]
  }
]
```

### `data/gradients.json`

Array of gradient objects. Linear gradients include an `angle`, radial gradients include a `shape`:

```json
[
  {
    "id": "gradient_1",
    "name": "Sunset Blaze",
    "style": "Warm",
    "type": "linear",
    "colors": ["#FF512F", "#DD2476"],
    "css": "linear-gradient(135deg, #FF512F, #DD2476)",
    "angle": 135
  },
  {
    "id": "gradient_4",
    "name": "Tropical Dusk",
    "style": "Warm",
    "type": "radial",
    "colors": ["#FF5F6D", "#FFC371"],
    "css": "radial-gradient(circle, #FF5F6D, #FFC371)",
    "shape": "circle"
  }
]
```

To regenerate `gradients.json`, run the Python script:

```bash
python generate_gradients.py
```

### `data/color-names.json`

Object keyed by 6-digit lowercase hex (no `#`). Each entry has a name and optional aliases:

```json
{
  "ff5733": {
    "hex": "ff5733",
    "name": "Cinnabar",
    "aliases": ["orange-red"]
  }
}
```

## Getting Started

### Prerequisites

- A local PHP server (e.g. `php -S localhost:8000`, XAMPP, or Laravel Valet)
- Clone the repository into a `ColorMagic` subfolder for local `.htaccess` paths to resolve correctly

### Run Locally

```bash
git clone https://github.com/moeezahmad-tech/Color-Magic.git ColorMagic
cd ColorMagic
php -S localhost:8000
```

Then visit `http://localhost:8000/ColorMagic/`

> The `.htaccess` detects local hosts and prepends `/ColorMagic/` to all redirect targets. On production it strips the prefix automatically.

## SEO & Search Engines

- **Sitemap index**: `/sitemap.xml` points to sub-sitemaps for pages, colors, and palettes
- **robots.txt**: Allows all crawlers, references sitemap
- **OG meta tags**: Every page includes Open Graph tags for social sharing
- **Color images**: `/colors/{hex}.webp` serves generated preview images for social cards
- **Bing**: `BingSiteAuth.xml` for verification
- **Yandex**: `yandex_13ee0143663b95c8.html` for verification

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

---

A Product of  [TechKreative](https://techkreative.com)
