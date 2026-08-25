# Color Magic - Route Map & Next.js App Router Specs

This document defines all application routes, mapping existing PHP `/ .htaccess` routes to their equivalent **Next.js 14+ App Router** page structures, layout wrapping, dynamic parameters, and rendering strategies.

---

## Complete Route Mapping Table

| Web URL Path | Existing PHP File | Target Next.js Route (`src/app/...`) | Render Mode | Access Level | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/` | `pages/index.php` | `app/page.tsx` | SSG / ISR | Public | Home page with Hero section, search input, & feature grid. |
| `/palettes` | `pages/palettes.php` | `app/palettes/page.tsx` | ISR (revalidate: 3600) | Public | Explore 150+ palettes with style filtering & search. |
| `/palette/[slug]` | `pages/palette.php` | `app/palette/[slug]/page.tsx` | Dynamic SSR / ISR | Public | Specific palette detail (e.g. `/palette/copper-archive`). |
| `/gradients` | `pages/gradients.php` | `app/gradients/page.tsx` | ISR (revalidate: 3600) | Public | All CSS Gradients browser with category & type filters. |
| `/gradient/[id]` | `pages/gradient.php` | `app/gradient/[id]/page.tsx` | Dynamic SSR / ISR | Public | Specific gradient detail (e.g. `/gradient/pm_24`). |
| `/generate-palette` | `pages/generate-palette.php` | `app/generate-palette/page.tsx` | Client Component | Public | Color theory generator & Image-to-palette extractor (tabbed/segmented). |
| `/palette-from-image` | `pages/palette-from-image.php` | `app/palette-from-image/page.tsx` | Client Component | Public | Dedicated page / direct route for image color extraction. |
| `/find-color` | `pages/find-color.php` | `app/find-color/page.tsx` | SSG + Client State | Public | Hex code input to discover name, RGB, HSL & metadata. |
| `/color/[hex]` | `pages/color.php` | `app/color/[hex]/page.tsx` | Dynamic SSR | Public | Color details page for a given hex code (e.g. `/color/FF5733`). |
| `/hex-to-color-name` | `pages/hex-to-color-name.php` | `app/hex-to-color-name/page.tsx` | SSG | Public | Micro SEO tool: convert hex code to human color name. |
| `/hex-to-rgb` | `pages/hex-to-rgb.php` | `app/hex-to-rgb/page.tsx` | SSG | Public | Micro SEO tool: convert hex code to RGB values. |
| `/what-color-is` | `pages/what-color-is.php` | `app/what-color-is/page.tsx` | SSG | Public | Micro SEO tool: identify shade/family of any color. |
| `/dark-color-finder` | `pages/dark-color-finder.php` | `app/dark-color-finder/page.tsx` | SSG | Public | Micro SEO tool: discover dark shades & variants. |
| `/brand-color-lookup` | `pages/brand-color-lookup.php` | `app/brand-color-lookup/page.tsx` | SSG | Public | Micro SEO tool: lookup tech & brand color palettes. |
| `/open-source` | `pages/open-source.php` | `app/open-source/page.tsx` | SSG | Public | GitHub project details, stats & top contributors. |
| `/login` | `pages/login.php` | `app/login/page.tsx` | Client Component | Guest Only | User authentication page (Google OAuth 2.0). |
| `/profile` | `pages/profile.php` | `app/profile/page.tsx` | SSR (Protected) | Authenticated | User dashboard displaying saved favorites & account settings. |
| `/auth/logout` | `auth/logout.php` | `app/api/auth/signout/route.ts` | Server Route | Authenticated | Clears NextAuth session cookie and redirects to `/`. |
| `/auth/delete-account`| `auth/delete-account.php` | `app/api/user/delete/route.ts` | Server Route | Authenticated | Deletes user record/data, clears session, redirects to `/?msg=account_deleted`. |

---

## Detailed Route Specifications

### 1. Home (`/`)
- **Path**: `src/app/page.tsx`
- **Features**:
  - Hero Header with tagline & dynamic color generator CTA.
  - Interactive Color Quick-Search input.
  - Featured Palettes showcase (top 6 trending palettes).
  - Featured Gradients showcase (top 6 trending gradients).
  - SEO Micro-tools navigation links.

### 2. All Palettes (`/palettes`)
- **Path**: `src/app/palettes/page.tsx`
- **Query Parameters**:
  - `?filter=all|pastel|vintage|neon|minimalist|earthy|eco|favorites`
  - `?q=search_query`
- **Client Features**:
  - Infinite scroll / pagination.
  - Copy whole palette array or individual hex swatch.
  - LocalStorage / User Session sync for heart favorite button.

### 3. Specific Palette (`/palette/[slug]`)
- **Path**: `src/app/palette/[slug]/page.tsx`
- **Example URLs**:
  - `/palette/copper-archive`
  - `/palette/dust-road`
- **Features**:
  - Big visual swatch breakdown with percentage distribution.
  - WCAG contrast ratios against light (#FFFFFF) and dark (#111827) backgrounds.
  - Brightness curve visualization chart.
  - Code Export Modal: HEX Array, JSON, CSS Variables, SCSS, Tailwind CSS Config.
  - Open Graph preview image path: `/api/og/palette?slug=[slug]`.

### 4. All Gradients (`/gradients`)
- **Path**: `src/app/gradients/page.tsx`
- **Query Parameters**:
  - `?category=warm|cool|purple|nature|pink|dark|pastel|neon|earth|mono`
  - `?type=linear|radial`
- **Features**:
  - Live animated preview cards.
  - CSS code copy button (`background: linear-gradient(135deg, #HEX1, #HEX2)`).

### 5. Specific Gradient (`/gradient/[id]`)
- **Path**: `src/app/gradient/[id]/page.tsx`
- **Example URLs**:
  - `/gradient/pm_24`
  - `/gradient/gradient_22`
- **Features**:
  - Fullscreen gradient preview mode.
  - Angle slider (`0deg` to `360deg`).
  - Gradient type toggle (Linear vs Radial).
  - Stop colors breakdown with copy support.

### 6. Generate Palette (`/generate-palette`)
- **Path**: `src/app/generate-palette/page.tsx`
- **Features & Logic**:
  - Seed color input (Color picker + Hex input).
  - Theory dropdown: **Monochromatic**, **Contrast**, **Triade**, **Tetrade**, **Analogic**.
  - Variation options: **Classic**, **Soft & Muted**, **Deep & Bold**.
  - Spacebar trigger: Pressing Space generates a new random palette using active theory.
  - Lock individual swatches while regenerating unlocked colors.

### 7. Palettes From Image (`/palette-from-image`)
- **Path**: `src/app/palette-from-image/page.tsx`
- **Features & Logic**:
  - File drag-and-drop zone + sample images grid.
  - Dynamic extraction slider (3 to 12 colors).
  - Client-side Canvas K-Means++ color clustering.
  - Export extracted colors as custom palette.

### 8. Find Color (`/find-color`)
- **Path**: `src/app/find-color/page.tsx`
- **Features**:
  - Live search input matching Hex codes or Color Names (from 300+ database).
  - Instant conversion output: RGB, HSL, HSV, CMYK.

### 9. Dynamic Color Page (`/color/[hex]`)
- **Path**: `src/app/color/[hex]/page.tsx`
- **Example URLs**:
  - `/color/FF5733`
  - `/color/007AFF`
- **Features**:
  - Full color breakdown, nearest named color match.
  - Harmonies (Complementary, Split-Complementary, Triadic, Tetradic, Analogous).
  - Tints (blending with white) & Shades (blending with black).

### 10. Micro SEO Conversion Routes
- `/hex-to-color-name`: Fast input converter for hex to closest human color name.
- `/hex-to-rgb`: Conversions between Hex and RGB formats with copy buttons.
- `/what-color-is`: Interactive tool identifying color shade families (e.g. Red, Blue, Neutral).
- `/dark-color-finder`: Color palette tool focusing on low-luminance dark themes.
- `/brand-color-lookup`: Brand color directory (Google, Twitter, Spotify, Apple, etc.).

### 11. Open Source (`/open-source`)
- **Path**: `src/app/open-source/page.tsx`
- **Features**: GitHub repository stats, MIT license terms, top contributor credits (Moeez Ahmad).

### 12. Authentication & Profile Routes
- `/login`: Render Google Sign-in button powered by NextAuth.js.
- `/profile`: Protected dashboard displaying user info, saved palettes, saved gradients, and account settings.
- `/auth/logout` -> `app/api/auth/signout`: Session teardown.
- Account Deletion trigger -> `app/api/user/delete`: Removes user record.
