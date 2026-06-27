# 🎨 Color Magic

**Professional Color Palette Generator & Explorer**

A beautiful, fully-featured web application for designers and developers to explore color palettes, find color names from hex codes, and generate professional color schemes using color theory.

![Color Magic](images/logo.png)

## ✨ Features

### 🔍 Find Color
- Enter any hex code to discover color information
- Get color names from a curated database of 300+ named colors
- View RGB, HSL values, luminance, and contrast information
- Copy hex and RGB values with one click

### 🎨 Explore Palettes
- Browse 150+ professionally curated color palettes
- Filter by style: Pastel, Vintage, Neon, Minimalist, Earthy, Eco
- Search palettes by name, theme, or hex code
- Copy entire palettes to clipboard

### ⚡ Generate Palette
- Create 5-color palettes from any base color
- Choose from color theory schemes: Mono, Contrast, Triade, Tetrade, Analogic
- Apply variations: Classic, Soft & Muted, Deep & Bold
- All calculations done locally - no API required

## 🚀 Live Demo

Visit: [colormagic.techkreative.com](https://colormagic.techkreative.com)

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Styling**: Tailwind CSS
- **Icons**: Material Symbols
- **Fonts**: Space Grotesk (Google Fonts)
- **Deployment**: GitHub Actions + FTP

## 📁 Project Structure

```
ColorMagic/
├── .htaccess
├── index.html                    ← Pages at root (SEO-friendly, clean URLs)
├── palette.html
├── find-color.html
├── generate-palette.html
├── open-source.html
├── color.php                     ← Dynamic PHP handlers
├── colorImage.php
├── paletteImage.php
├── sitemap.xml                   ← Sitemap index (points to sub-sitemaps)
├── robots.txt
├── manifest.json
├── BingSiteAuth.xml              ← Search engine verification (can't move)
├── yandex_13ee0143663b95c8.html
├── assets/                       ← All static assets consolidated
│   ├── css/
│   │   ├── main.css
│   │   └── style.css
│   ├── js/
│   │   ├── app.js
│   │   ├── explore-palettes.js
│   │   ├── palette-page.js
│   │   ├── sidebar.js
│   │   ├── tailwind-config.js
│   │   └── ...
│   └── images/
│       ├── logo.png
│       └── TopContributers/
├── data/                         ← JSON data files
│   ├── colors.json
│   └── color-names.json
└── sitemaps/                     ← Sub-sitemaps organized together
    ├── pages.xml
    ├── palettes.xml
    ├── colors.xml
    └── images.xml

```

## 🏃 Getting Started

```bash
   git clone https://github.com/moeezahmad-tech/Color-Magic.git
```

## 🤝 Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

---

Made with ❤️ by [TechKreative](https://techkreative.com)
