<div align="center">
  
  # ✨ Color Magic

  <p align="center">
    <strong>The Ultimate Open-Source Color Toolkit & Design Intelligence Platform</strong>
  </p>

  <p align="center">
    <a href="https://nextjs.org/"><img src="https://img.shields.io/badge/Next.js-15-black?style=flat-square&logo=next.js" alt="Next.js" /></a>
    <a href="https://react.dev/"><img src="https://img.shields.io/badge/React-19-blue?style=flat-square&logo=react" alt="React" /></a>
    <a href="https://tailwindcss.com/"><img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" /></a>
    <a href="https://www.typescriptlang.org/"><img src="https://img.shields.io/badge/TypeScript-007ACC?style=flat-square&logo=typescript&logoColor=white" alt="TypeScript" /></a>
    <a href="https://github.com/pmndrs/zustand"><img src="https://img.shields.io/badge/State-Zustand-orange?style=flat-square" alt="Zustand" /></a>
    <img src="https://img.shields.io/github/license/moeezahmad-tech/Color-Magic?style=flat-square&color=pink" alt="License" />
  </p>
  
  <p align="center">
    <a href="https://github.com/moeezahmad-tech/Color-Magic/issues">Report Bug</a>
    ·
    <a href="https://github.com/moeezahmad-tech/Color-Magic/issues">Request Feature</a>
    ·
    <a href="https://github.com/moeezahmad-tech/Color-Magic/pulls">Contribute</a>
  </p>
</div>

<br />

---

## 🎨 Overview

**Color Magic** is a lightning-fast, privacy-first, open-source color workspace built for designers, developers, and creatives. It brings together every color tool you need—from extracting palettes from images to generating accessible WCAG color harmonies, CSS gradients, and shade variations—all within a unified, modern interface.

- ⚡ **Zero Paywalls & Zero Sign-ups**: Immediate access to every single tool.
- 🔒 **100% Privacy-First**: In-browser client-side image processing; your files never touch a server.
- 🛡️ **High Availability**: Built-in ISR caching and zero-downtime offline fallbacks.
- 🚀 **Performant & Responsive**: Powered by Next.js 15 App Router, React 19, and Tailwind CSS.

<br />

## 🌟 Catalog Hubs & Exploration

Color Magic provides dedicated, high-performance catalog hubs for exploratory design work:

### 1. 🏷️ Named Colors Explorer (`/colors`)
- **1,000+ Curated Colors**: Explore an extensive catalog of named colors with real-time search by name or hex code.
- **Color Family Filters**: Filter by color families (*Reds, Oranges, Yellows, Greens, Cyans, Blues, Purples, Pinks, Neutrals*).
- **Progressive Batch Loading**: Seamlessly browse large datasets with **72-item batch loading** ("Load More Colors").
- **Client-Side Shuffle**: Explore fresh palettes dynamically without redundant network trips.
- **Hex, RGB & HSL Codes**: One-click copying for all primary color models.

### 2. 🎨 Curated Color Palettes (`/palettes`)
- **Style Categories**: Filter across themes including *Pastel, Vintage, Neon, Minimalist, Earthy, Eco*, and your *Saved Favorites*.
- **Progressive Loading (+72 items)**: Infinite-like exploration with smooth pagination.
- **Deep Palette Inspector (`/palette/[slug]`)**: View contrast scores, harmonic variations, and copy exact hex values instantly.

### 3. 🌈 CSS Gradient Studio (`/gradients`)
- **Linear & Radial Gradients**: Curated collection of modern CSS gradients ready for web projects.
- **Style Filters**: Discover gradients by aesthetic (*Warm, Cool, Purple, Nature, Pink, Dark, Pastel, Neon*).
- **One-Click CSS Export**: Copy production-ready CSS snippets formatted for direct use in stylesheets.

<br />

## 🛠️ Complete Suite of Color Utilities

| Tool & Icon | Description & Capabilities | Route |
| :--- | :--- | :---: |
| 🔍 **Hex Color Finder** | Analyzes any hex code to provide precise color names, RGB/HSL values, and WCAG accessibility contrast ratios. | [`/find-color`](/find-color) |
| 🏷️ **Hex to Color Name** | Instantly identifies human-readable names for obscure hex codes using an integrated database of 1000+ colors. | [`/hex-to-color-name`](/hex-to-color-name) |
| 🎨 **What Color Is This?** | Interactive playground to inspect, identify, and dissect unknown color codes on the fly. | [`/what-color-is`](/what-color-is) |
| 🖼️ **Image Palette Extractor** | Extracts dominant color palettes from user-uploaded images using client-side K-Means++ clustering. | [`/palette-from-image`](/palette-from-image) |
| ✨ **Palette Generator** | Algorithmic color harmony generator based on color theory (Analogous, Complementary, Triadic, Tetradic). | [`/generate-palette`](/generate-palette) |
| 🌙 **Dark Color Finder** | Curated discovery tool tuned for low-luminance, high-contrast dark mode UI themes. | [`/dark-color-finder`](/dark-color-finder) |
| 💼 **Brand Color Lookup** | Official hex codes and primary palettes from major tech, social, and global brands. | [`/brand-color-lookup`](/brand-color-lookup) |
| 🔄 **Hex ⇄ RGB Converter** | Precision bi-directional conversion between Hex and RGB formats with live previews. | [`/hex-to-rgb`](/hex-to-rgb) · [`/rgb-to-hex`](/rgb-to-hex) |
| 🔄 **Hex ⇄ HSL Converter** | Precision bi-directional conversion between Hex and HSL color spaces. | [`/hex-to-hsl`](/hex-to-hsl) · [`/hsl-to-hex`](/hsl-to-hex) |
| 🔄 **RGB ⇄ HSL Converter** | Direct conversion between RGB channel values and HSL cylindrical coordinates. | [`/rgb-to-hsl`](/rgb-to-hsl) · [`/hsl-to-rgb`](/hsl-to-rgb) |
| 👤 **User Profile & Favorites** | Centralized dashboard managing your bookmarked colors, palettes, and gradients stored locally. | [`/profile`](/profile) |

<br />

## ⚡ High-Availability & Performance Architecture

```
┌────────────────────────────────────────────────────────┐
│                   Next.js 15 App                       │
└──────────────────────────┬─────────────────────────────┘
                           │
            ┌──────────────▼──────────────┐
            │   ISR Shield (revalidate)   │
            │   60s / 3600s Edge Cache    │
            └──────────────┬──────────────┘
                           │
             ┌─────────────┴─────────────┐
             ▼                           ▼
    ┌─────────────────┐         ┌─────────────────┐
    │  Remote API     │ Timeout │  Local Fallback │
    │  (Backend)      ├────────►│  Datasets       │
    └─────────────────┘ (>4000ms)└─────────────────┘
                                        │
                                        ▼
                               Zero Downtime Guarantee
```

- **ISR Shield & Edge Caching**: Next.js Data Cache tags (`revalidate: 3600`) ensure rapid delivery and protect upstream APIs from overload.
- **Automated Cache Purge Workflows**: GitHub Actions workflow integrations to revalidate caches on deployment.
- **Zero-Downtime Local Fallback (`lib/api-client.ts`)**: Built-in 4-second timeout guards and resilient offline fallback datasets (`/data/*.json`) ensure pages render smoothly even if remote APIs are unavailable.
- **Zero-Dependency Math Engine (`lib/color-math.ts`)**: Lightweight, pure TypeScript color conversion and WCAG contrast calculation functions for maximum speed.
- **Client-Side Persistence (`store/useFavoritesStore.ts`)**: User favorites and bookmarks are persisted seamlessly in `localStorage` via Zustand with hydration safety.

<br />

## 💻 Tech Stack

| Layer | Technology | Purpose |
| :--- | :--- | :--- |
| **Framework** | **Next.js 15 (App Router)** | Server & Client Components, Route Handlers, ISR. |
| **UI Library** | **React 19** | Concurrent rendering, modern hooks, and state management. |
| **Styling** | **Tailwind CSS** | Responsive utility styling, custom typography, design system. |
| **State** | **Zustand** | Global state management with `persist` middleware for local storage. |
| **Icons** | **Lucide React** | Consistent, lightweight SVG icon suite. |
| **Language** | **TypeScript** | Strict end-to-end type safety. |
| **Deployment** | **Vercel** | Edge network deployment, automated CI/CD. |

<br />

## 📁 Project Structure

```text
color_magic/
├── app/                        # Next.js App Router routes & layouts
│   ├── (auth)/                 # Authentication & login routes
│   ├── brand-color-lookup/     # Brand color explorer
│   ├── color/[hex]/            # Color detail & shade generator
│   ├── colors/                 # 1000+ Named colors directory
│   ├── dark-color-finder/      # Dark UI color schemes
│   ├── find-color/             # Hex analyzer tool
│   ├── generate-palette/       # Algorithmic palette creator
│   ├── gradients/              # CSS gradient catalog
│   ├── hex-to-color-name/      # Color name identifier
│   ├── hex-to-rgb/             # Format converters (Hex, RGB, HSL)
│   ├── palette/[slug]/         # Palette detail inspector
│   ├── palette-from-image/     # In-browser image color extractor
│   ├── palettes/               # Curated color palettes catalog
│   ├── profile/                # User profile & saved favorites
│   ├── what-color-is/          # Color identifier playground
│   ├── layout.tsx              # Global layout with Navbar & Footer
│   ├── sitemap.ts              # Dynamic SEO sitemap generator
│   └── robots.ts               # Robots.txt configuration
├── components/                 # Reusable UI components
│   ├── layout/                 # Navbar, Footer, MobileNav
│   └── ui/                     # PageHeader, PaletteCard, GradientCard, Toast
├── data/                       # Offline fallback datasets (colors, palettes, gradients)
├── lib/                        # Utilities (color-math, api-client, analytics)
├── store/                      # Zustand store (favorites, UI state)
└── types/                      # TypeScript definitions
```

<br />

## 🛠️ Getting Started (Local Development)

### Prerequisites
- **Node.js** (v18.17.0 or higher recommended)
- **npm**, **yarn**, or **pnpm**

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/moeezahmad-tech/Color-Magic.git
   cd Color-Magic
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Start the local development server**:
   ```bash
   npm run dev
   ```

4. **Open in browser**:
   Navigate to [http://localhost:3000](http://localhost:3000) to see the application live.

### Build & Production Validation

```bash
# Type check and build production bundle
npm run build

# Start production server locally
npm run start
```

<br />

## 🤝 Contributing

Contributions are warmly welcomed! If you'd like to fix a bug, add a new color tool, or refine the documentation:

1. **Fork** the repository.
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Commit your changes**: `git commit -m "feat: add amazing color tool"`
4. **Push to your branch**: `git push origin feature/amazing-feature`
5. **Open a Pull Request** against the `main` branch.

<br />

## 📄 License

This project is open-source and available under the **MIT License**. You are free to use, modify, and distribute it for personal and commercial projects.

---

<div align="center">
  <p>Crafted with 🖤 for the Open Source Creative Community.</p>
</div>