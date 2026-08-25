# Color Magic - Project Rules, Quality Guidelines & Best Practices

This document specifies mandatory coding standards, architectural constraints, state persistence policies, and SEO guidelines for maintaining high quality and consistency across **Color Magic**.

---

## 🛑 1. Core Architectural Constraints & Rules

1. **No External Color Libraries for Core Calculations**:
   - Color math (HEX/RGB/HSL conversion, WCAG contrast ratios, Lab $\Delta E_{76}$ distance, color harmony angles) **must** use the native TypeScript utility functions in `src/lib/color-math.ts`.
   - Do not pull in heavy external NPM color conversion packages. Keep client bundles ultra-lean.

2. **Client-Side Image Processing Privacy**:
   - The **Palette from Image** extractor (`src/app/palette-from-image/page.tsx`) **must run 100% in the browser** via HTML5 Canvas or Web Workers (`src/lib/kmeans.ts`).
   - Never upload user images to a server or third-party storage API.

3. **URL Cleanliness & Dynamic Slugs**:
   - Slugs must be strictly lowercase, hyphen-separated string identifiers (e.g., `/palette/copper-archive`, `/color/midnight-blue`).
   - Hex code parameters must be stripped of leading `#` symbols in URLs (e.g., `/color/FF5733`).

4. **Strict Type Safety**:
   - All models (Palettes, Gradients, Named Colors, User Profiles) must have explicit TypeScript types defined in `src/types/index.ts`.

---

## 💾 2. State Management & Offline Persistence Policy

1. **Favorites Storage Dual-Sync Strategy**:
   - **Unauthenticated / Guest Users**: Favorites stored in `localStorage` under key `colormagic_favorite_palettes` and `colormagic_favorite_gradients`.
   - **Authenticated Users**: LocalStorage favorites are automatically merged into database record upon sign-in.
   - UI components must listen to Zustand store updates to instantly update heart toggle states site-wide.

2. **Toast Feedback Rules**:
   - Every copy action (Copy Hex, Copy RGB, Copy CSS Gradient, Copy Tailwind Config) **must** trigger a subtle 2-second toast message (e.g. `"Copied #2C3E50 to clipboard!"`).

---

## 🌐 3. SEO & Ranking Optimization Rules

1. **Heading Hierarchy**:
   - Exactly one `<h1>` per page.
   - Descriptive `<h2>` tags for tool sections and color breakdowns.

2. **Canonical Links & Open Graph**:
   - All pages must define explicit canonical URLs pointing to `https://colormagic.techkreative.com/...`.
   - Dynamic pages (`/palette/[slug]` and `/color/[hex]`) must generate dynamic Open Graph preview images (`og:image`).

3. **Micro SEO Landing Pages**:
   - Conversion tools (`/hex-to-color-name`, `/hex-to-rgb`, `/what-color-is`, `/dark-color-finder`, `/brand-color-lookup`) must include rich educational markdown/HTML content below the interactive tool to maximize organic search rankings.

---

## 🧪 4. Code Standards & Linting Rules

- Standard Next.js ESLint configuration.
- Strict null checks in TypeScript.
- No unused variables or explicit `any` types.
