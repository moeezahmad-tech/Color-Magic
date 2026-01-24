# 🎨 Color Magic

**Professional Color Palette Generator & Explorer**

A beautiful, fully-featured web application for designers and developers to explore color palettes, find color names from hex codes, and generate professional color schemes using color theory.

![Color Magic](images/full-logo.png)

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
colorCodeFinder/
├── .github/workflows/   # CI/CD deployment
├── data/                # JSON data files
│   ├── color-names.json # 300+ color name mappings
│   └── colors.json      # Curated palette collection
├── images/              # Logo and assets
├── index.html           # Main application
├── manifest.json        # PWA manifest
├── robots.txt           # SEO
└── sitemap.xml          # SEO
```

## 🏃 Getting Started

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/colorCodeFinder.git
   ```

2. Open `index.html` in your browser - no build step required!

## 🚀 Deployment

This project uses GitHub Actions for automated deployment. Push to `main` branch to trigger deployment.

### Setup Secrets
Add these secrets in your GitHub repository settings:
- `FTP_SERVER` - Your FTP hostname
- `FTP_USERNAME` - Your FTP username
- `FTP_PASSWORD` - Your FTP password

## 📝 License

MIT License - feel free to use this project for your own purposes.

## 🤝 Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

---

Made with ❤️ by [TechKreative](https://techkreative.com)
