# Color Magic - Concept & Next.js Architecture Blueprint

## Executive Overview
**Color Magic** is a modern, high-performance web platform designed for designers, frontend developers, and digital creators. It provides tools for discovering curated color palettes, CSS gradients, color theory generation, image color extraction, brand color reference, and dynamic color format conversion.

This document serves as the foundational architectural blueprint for migrating the existing PHP/Vanilla JS application into a full-stack **Next.js (App Router)** application.

---

## Core Product Pillars & Features

1. **Color Palette Exploration & Discovery (`/palettes`, `/palette/[slug]`)**
   - 150+ curated multi-color swatches with style filters (Pastel, Neon, Vintage, Minimalist, Earthy, Eco, etc.).
   - Palette details: Contrast compliance (WCAG AAA/AA), brightness distribution graphs, and export formats (HEX, JSON, SCSS, Tailwind CSS, SVG).

2. **CSS Gradient Gallery (`/gradients`, `/gradient/[id]`)**
   - 100+ hand-crafted linear & radial gradients.
   - Categorized by mood/style (Warm, Cool, Pastel, Neon, Dark, Mono).
   - One-click copy for pure CSS `background: linear-gradient(...)` and raw hex values.

3. **Color Theory Palette Generator (`/generate-palette`)**
   - Algorithmic palette generation based on traditional color harmony theories:
     - **Monochromatic**, **Contrast/Complementary**, **Triadic**, **Tetradic**, **Analogous**.
   - Style adjustments: Classic, Soft & Muted, Deep & Bold.
   - Fine-tuning controls (hue adjustments, brightness/saturation sliders).

4. **Image Color Extractor (`/palette-from-image`)**
   - Browser-side dominant color extraction using **K-Means++ Clustering algorithm**.
   - Privacy-focused: Images are processed entirely in memory via HTML5 Canvas (zero server upload required).
   - Dynamic palette sizes (3 to 12 colors).

5. **Color Identification & Conversion Engines (`/find-color`, `/color/[hex]`)**
   - Fast lookup across 300+ named colors with alias support.
   - Conversions across HEX, RGB, HSL, HSV, CMYK, LAB, XYZ, and CSS variable outputs.
   - Dedicated micro-tools for SEO positioning:
     - `/hex-to-color-name`
     - `/hex-to-rgb`
     - `/what-color-is`
     - `/dark-color-finder`
     - `/brand-color-lookup`

6. **Authentication & User Dashboard (`/login`, `/profile`, `/auth/...`)**
   - Google OAuth 2.0 Integration.
   - User Profile dashboard displaying saved favorite palettes, gradients, and custom user-generated color schemes.
   - Account deletion compliance.

---

## Recommended Next.js Architecture & Stack

| Layer | Next.js Recommended Stack | Implementation Details |
| :--- | :--- | :--- |
| **Framework** | Next.js 14+ (App Router) | Server-side rendering (SSR) for dynamic detail pages & Static Site Generation (SSG / ISR) for catalog pages. |
| **Language** | TypeScript | Strict type checking for Color objects, Gradients, and API payloads. |
| **Styling** | Tailwind CSS v3 / Shadcn UI | Custom theme configuration extending primary brand colors, dark mode default, subtle gradients, and glassmorphism. |
| **State Management** | Zustand or React Query (TanStack Query) | Client-side persistent storage sync (localStorage) for offline favorites and cached API responses. |
| **Authentication** | NextAuth.js (Auth.js v5) | Google OAuth provider integration, replacing custom PHP session endpoints. |
| **Image Processing**| Web Workers + HTML Canvas | Offloading K-Means clustering algorithm to background worker threads to keep UI main thread responsive. |

---

## Target Next.js Directory Structure

```
color-magic-next/
├── src/
│   ├── app/
│   │   ├── layout.tsx                  # Root layout with ThemeProvider & AuthProvider
│   │   ├── page.tsx                    # Landing / Home page
│   │   ├── palettes/
│   │   │   ├── page.tsx                # All Palettes explore page (ISR/SSG)
│   │   ├── palette/
│   │   │   └── [slug]/
│   │   │       └── page.tsx            # Palette Detail page (Dynamic SSR)
│   │   ├── gradients/
│   │   │   ├── page.tsx                # All Gradients page
│   │   ├── gradient/
│   │   │   └── [id]/
│   │   │       └── page.tsx            # Gradient Detail page
│   │   ├── generate-palette/
│   │   │   └── page.tsx                # Color theory generator tool
│   │   ├── palette-from-image/
│   │   │   └── page.tsx                # Image color extractor tool
│   │   ├── find-color/
│   │   │   └── page.tsx                # Interactive color finder
│   │   ├── color/
│   │   │   └── [hex]/
│   │   │       └── page.tsx            # Detailed color analysis page
│   │   ├── hex-to-color-name/
│   │   │   └── page.tsx                # SEO landing page
│   │   ├── hex-to-rgb/
│   │   │   └── page.tsx                # SEO landing page
│   │   ├── what-color-is/
│   │   │   └── page.tsx                # SEO landing page
│   │   ├── dark-color-finder/
│   │   │   └── page.tsx                # SEO landing page
│   │   ├── brand-color-lookup/
│   │   │   └── page.tsx                # SEO landing page
│   │   ├── open-source/
│   │   │   └── page.tsx                # Open source & credits
│   │   ├── login/
│   │   │   └── page.tsx                # Authentication page
│   │   ├── profile/
│   │   │   └── page.tsx                # User profile dashboard
│   │   └── api/
│   │       ├── auth/
│   │       │   └── [...nextauth]/
│   │       │       └── route.ts        # NextAuth handler (Google Provider)
│   │       ├── v1/
│   │       │   ├── palettes/route.ts   # JSON API for Palettes
│   │       │   ├── gradients/route.ts  # JSON API for Gradients
│   │       │   └── colors/route.ts     # JSON API for Color details
│   ├── components/
│   │   ├── layout/
│   │   │   ├── Navbar.tsx
│   │   │   └── Footer.tsx
│   │   ├── ui/
│   │   │   ├── PaletteCard.tsx
│   │   │   ├── GradientCard.tsx
│   │   │   ├── ColorPicker.tsx
│   │   │   └── ColorSwatch.tsx
│   │   └── generators/
│   │       ├── ImageUploader.tsx
│   │       └── TheoryControls.tsx
│   ├── lib/
│   │   ├── color-math.ts               # Color conversion utilities (HEX, RGB, HSL, LAB)
│   │   ├── kmeans.ts                   # Image extraction clustering algorithm
│   │   ├── theory.ts                   # Color harmony calculations
│   │   └── auth.ts                     # Auth options & session utilities
│   └── data/
│       ├── palettes.json
│       ├── gradients.json
│       └── color-names.json
```
