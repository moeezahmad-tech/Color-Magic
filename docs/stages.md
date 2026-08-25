# Color Magic - Next.js Migration Roadmap & Project Stages

This document outlines the step-by-step phased execution plan for building and completing **Color Magic** in **Next.js 14+ (App Router)**. Each stage is modular, verifiable, and structured so any AI model or developer can follow it sequentially.

---

## 🏗️ Stage 1: Next.js Workspace Initialization & Core Setup
**Goal**: Establish a solid Next.js 14+ TypeScript project structure with styling, fonts, icons, layout, and global color utilities.

### Action Items:
1. **Initialize Project**:
   - Next.js 14+ App Router with TypeScript and Tailwind CSS (`src/` directory layout).
2. **Design System & Theme Tokens**:
   - Configure custom Tailwind theme tokens (dark mode default, glassmorphism, accent colors).
   - Configure **Inter Variable Font** (`next/font/google`).
   - Setup icon set (`lucide-react` or `react-icons`).
3. **Core Color Mathematics Engine (`src/lib/color-math.ts`)**:
   - Port color utilities from legacy JavaScript (`assets/js/utils.js`).
   - Functions for: HEX to RGB, RGB to HSL, HSL to HEX, Luminance calculation, and Contrast ratio check (WCAG 2.1 AA/AAA compliance).
   - Distance calculation in Lab Color Space ($\Delta E_{76}$) for color name matching.
4. **Static Datasets (`src/data/`)**:
   - Migrate `api/palettes.json`, `api/gradients.json`, and `api/color-names.json` directly into local TypeScript imports or JSON data modules.
5. **Root Layout (`src/app/layout.tsx`)**:
   - Modern, sticky `Navbar` with navigation links and "Tools" dropdown.
   - Global `Footer` with social links, open-source badge, and copyright details.
   - Global Toast Notification provider (for "Copied to Clipboard" feedback).

---

## 🎨 Stage 2: Public Exploration & Catalog Pages
**Goal**: Build high-performance static/ISR catalog pages for exploring palettes and CSS gradients.

### Action Items:
1. **Home / Landing Page (`src/app/page.tsx`)**:
   - Hero section with live color generator CTA.
   - Quick Hex search bar.
   - 6-card feature grid pointing to tools.
   - Featured trending palettes and gradients showcases.
2. **All Palettes Page (`src/app/palettes/page.tsx`)**:
   - Filter bar (**Pastel**, **Vintage**, **Neon**, **Minimalist**, **Earthy**, **Eco**, **Favorites**).
   - Search input (filter by title, tag, or hex).
   - Interactive `PaletteCard` component (hover tooltips, copy swatches, heart favorite toggle).
   - Infinite scroll / pagination.
3. **Specific Palette Page (`src/app/palette/[slug]/page.tsx`)**:
   - Proportioned visual swatch breakdown.
   - WCAG contrast table against light/dark backgrounds.
   - Brightness distribution graph.
   - Code Export Modal (HEX Array, JSON, CSS Variables, SCSS, Tailwind CSS config, SVG download).
4. **All Gradients Page (`src/app/gradients/page.tsx`)**:
   - Style filter tabs (**Warm**, **Cool**, **Purple**, **Nature**, **Pink**, **Dark**, **Pastel**, **Neon**, **Earth**, **Mono**).
   - Linear vs. Radial toggle.
   - CSS copy snippet buttons.
5. **Specific Gradient Page (`src/app/gradient/[id]/page.tsx`)**:
   - Live fullscreen preview.
   - Interactive angle controller slider ($0^\circ$ to $360^\circ$).
   - Stop colors breakdown and raw CSS box.

---

## ⚡ Stage 3: Interactive Generation Tools & Web Engines
**Goal**: Implement client-side color theory math engines, image color extractors, and color finders.

### Action Items:
1. **Color Theory Palette Generator (`src/app/generate-palette/page.tsx`)**:
   - Seed color picker + hex input.
   - Harmony selector (**Monochromatic**, **Contrast/Complementary**, **Triadic**, **Tetradic**, **Analogous**).
   - Variation modes (**Classic**, **Soft & Muted**, **Deep & Bold**).
   - Interactive 5-column palette bar with lock/unlock toggles.
   - Keyboard event listener: Pressing `Space` regenerates unlocked swatches.
2. **Palettes From Image Extractor (`src/app/palette-from-image/page.tsx`)**:
   - Drag-and-drop file uploader zone + preset sample images grid.
   - Client-side Canvas **K-Means++ Clustering Engine** (`src/lib/kmeans.ts`).
   - Dynamic palette size slider (3 to 12 colors).
   - Extracted colors breakdown card with export options.
3. **Find Color Tool (`src/app/find-color/page.tsx`)**:
   - Search by Hex or Name against 300+ named color database (`api/color-names.json`).
   - Display closest human color name, HEX, RGB, HSL, HSV, CMYK values.
   - Tints and Shades scale generator.
4. **Dynamic Color Page (`src/app/color/[hex]/page.tsx`)**:
   - Color detail page with complementary, split-complementary, triadic, and tetradic color wheels.

---

## 🚀 Stage 4: Micro SEO Tools & Open Source Page
**Goal**: Build optimized landing pages targeting search queries and open-source contribution info.

### Action Items:
1. **Micro SEO Tools**:
   - `src/app/hex-to-color-name/page.tsx`
   - `src/app/hex-to-rgb/page.tsx`
   - `src/app/what-color-is/page.tsx`
   - `src/app/dark-color-finder/page.tsx`
   - `src/app/brand-color-lookup/page.tsx` (Searchable brand hex reference: Google, Spotify, Twitter, Apple, etc.)
2. **SEO & Metadata Optimization**:
   - Generate Dynamic Open Graph preview images using `@vercel/og` or Canvas for `/palette/[slug]` and `/color/[hex]`.
   - `sitemap.ts` and `robots.ts` dynamic route generators.
   - Structured JSON-LD schema tags for WebApplication & ColorPalette.
3. **Open Source Page (`src/app/open-source/page.tsx`)**:
   - Project overview, MIT license details, GitHub repository stats, and contributor highlights.

---

## 🔐 Stage 5: Authentication, User Profiles & Database Persistence
**Goal**: Add Google OAuth 2.0, profile dashboards, favorites synchronization, and account management.

### Action Items:
1. **NextAuth.js Integration (`src/app/api/auth/[...nextauth]/route.ts`)**:
   - Configure Google OAuth Provider using environment variables:
     - `GOOGLE_CLIENT_ID`
     - `GOOGLE_CLIENT_SECRET`
     - `NEXTAUTH_SECRET`
2. **Login Page (`src/app/login/page.tsx`)**:
   - Custom guest sign-in screen with "Sign in with Google" button.
3. **Zustand Favorites Sync**:
   - Store local favorites in `localStorage` for anonymous users.
   - Automatically sync local favorites to database upon successful sign-in.
4. **User Dashboard (`src/app/profile/page.tsx`)**:
   - Protected user dashboard displaying profile picture, name, and email.
   - "Saved Palettes" tab & "Saved Gradients" tab.
5. **Account Deletion Route (`src/app/api/user/delete/route.ts`)**:
   - Full account deletion compliance: purges stored user data, destroys NextAuth session, and redirects to `/?msg=account_deleted`.

---

## 🧪 Stage 6: REST API Route Handlers & Production Readiness
**Goal**: Recreate legacy public PHP API endpoints as Next.js App Router API endpoints, run tests, and optimize performance.

### Action Items:
1. **Next.js API Route Handlers**:
   - `src/app/api/v1/palettes/route.ts` (Supports pagination `?page=1&limit=10`, `?q=`, and `?id=`).
   - `src/app/api/v1/gradients/route.ts` (Supports `?style=`, `?type=`, `?q=`, `?id=`).
   - `src/app/api/v1/colors/route.ts` (Supports `?hex=`, `?slug=`, `?q=`).
2. **PWA & Performance Verification**:
   - Next PWA / `manifest.json` configuration.
   - Run production build (`npm run build`) to ensure zero TypeScript errors or SSR hydration warnings.
   - Verify Lighthouse performance score (Target: 95+ Performance, 100 SEO).
