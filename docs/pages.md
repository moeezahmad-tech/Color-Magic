# Color Magic - UI/UX Component & Page Breakdown

This document provides a detailed breakdown of each page in **Color Magic**, including UI components, client-side interactivity, state requirements, and Next.js page structure.

---

## 1. Landing Page (`/` -> `app/page.tsx`)

### Components:
- **Navbar**: Sticky header with logo, navigation links, and "Tools" dropdown.
- **Hero Section**: Eyecatcher headline, live color scheme generator CTA button, and search input.
- **Feature Cards Grid**: 6 visual cards pointing to main site capabilities:
  1. Explore Palettes (`/palettes`)
  2. CSS Gradients (`/gradients`)
  3. Color Generator (`/generate-palette`)
  4. Palette from Image (`/palette-from-image`)
  5. Find Color (`/find-color`)
  6. SEO Micro Conversion Tools (`/hex-to-color-name`, `/hex-to-rgb`, etc.)
- **Trending Swatches Carousel**: Displays interactive preview cards for popular color schemes.
- **Footer**: Brand statement, quick links, open-source badge, copyright info.

---

## 2. All Palettes Page (`/palettes` -> `app/palettes/page.tsx`)

### Components:
- **Style Category Bar**: Horizontal tag list (**All**, **Pastel**, **Vintage**, **Neon**, **Minimalist**, **Earthy**, **Eco**, **Favorites**).
- **Search Header**: Filter input with instant client-side filtering.
- **Palette Grid**: Responsive 3-column layout rendering `PaletteCard` components.
- **`PaletteCard` Component**:
  - Multi-bar color swatch with hover tooltips displaying Hex values.
  - One-click copy for swatch or entire palette array.
  - Heart toggle button synced to local storage & profile session.
  - Direct link to dynamic detail route `/palette/[slug]`.

---

## 3. Palette Detail Page (`/palette/[slug]` -> `app/palette/[slug]/page.tsx`)

### Components:
- **Palette Header**: Title, tags, slug name, and favorite button.
- **Large Swatch Visualizer**: Full-width proportioned color bands.
- **Color Swatch Breakdown List**: Individual cards for each color showing:
  - Hex code with copy button.
  - RGB values.
  - Human color name.
- **WCAG Contrast Grid**: Matrix showing background/foreground text legibility scores (AA / AAA ratings).
- **Export Action Bar**: Trigger modals for HEX Array, JSON, CSS Variables, SCSS, Tailwind CSS, SVG export.

---

## 4. CSS Gradients Page (`/gradients` -> `app/gradients/page.tsx`)

### Components:
- **Category Filter**: Category tabs (**Warm**, **Cool**, **Purple**, **Nature**, **Pink**, **Dark**, **Pastel**, **Neon**, **Earth**, **Mono**).
- **Gradient Type Toggle**: Linear vs Radial toggle switches.
- **Gradient Card Grid**: Interactive cards with background CSS previews and quick "Copy CSS" buttons.

---

## 5. Specific Gradient Page (`/gradient/[id]` -> `app/gradient/[id]/page.tsx`)

### Components:
- **Gradient Canvas Visualizer**: Interactive live preview element.
- **Angle Controller**: Interactive radial slider adjusting gradient angle (`0deg` to `360deg`).
- **Stop Color Controls**: Breakdown of start & stop hex colors with individual copy buttons.
- **CSS Code Box**: Syntax-highlighted CSS snippet with direct clipboard copy button.

---

## 6. Palette Generator Page (`/generate-palette` -> `app/generate-palette/page.tsx`)

### Components:
- **Seed Color Picker**: Color picker input and hex input box.
- **Harmony Selector**: Dropdown to choose **Monochromatic**, **Contrast**, **Triade**, **Tetrade**, or **Analogic**.
- **Variation Controls**: Buttons for **Classic**, **Soft & Muted**, **Deep & Bold**.
- **Interactive Palette Bar**: 5 color columns with:
  - Swatch preview.
  - Hex label.
  - Lock/Unlock icon toggle (locks swatch during generation).
  - Drag-and-drop handle (reorder colors).
- **Keyboard Listener**: Spacebar press triggers generator for unlocked swatches.

---

## 7. Palette From Image Page (`/palette-from-image` -> `app/palette-from-image/page.tsx`)

### Components:
- **Drag-and-Drop Image Uploader**: File input zone + preset sample images.
- **Canvas Preview Engine**: HTML5 Canvas rendering user image with color sampling overlays.
- **Color Count Slider**: Adjustable range (3 to 12 colors) driving real-time K-Means++ re-clustering.
- **Extracted Swatches Section**: Interactive color cards with Hex, RGB, HSL details and export actions.

---

## 8. Find Color Page (`/find-color` -> `app/find-color/page.tsx`)

### Components:
- **Hex & Name Search Input**: Dual input matching hex code or color name from 300+ database.
- **Color Details Card**: Preview swatch, official name, closest named color match.
- **Format Converter**: Simultaneous display of HEX, RGB, HSL, HSV, CMYK, LAB.
- **Tints & Shades Palette Generator**: Visual gradient step cards from white to black.

---

## 9. Micro SEO Pages (`app/hex-to-color-name/page.tsx`, etc.)

### Shared Layout & Components:
- **Tool Header**: SEO-optimized title, description, and canonical path.
- **Interactive Converter Tool**: Fast input box + instant copyable results.
- **Educational / Content Section**: Markdown/text explanation of color conversion formulas, color theory concepts, and FAQs for search ranking.

---

## 10. Authentication & Dashboard Pages (`/login`, `/profile`)

### Components:
- **Login Page (`app/login/page.tsx`)**:
  - Brand header.
  - Google OAuth single sign-on button.
  - Security & privacy statement.
- **Profile Dashboard (`app/profile/page.tsx`)**:
  - User Header: Avatar, Name, Email.
  - Saved Palettes tab: Grid of favorited palettes.
  - Saved Gradients tab: Grid of favorited gradients.
  - Account Settings: Clear local storage cache, logout button, and Delete Account action trigger.
