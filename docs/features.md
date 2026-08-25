# Color Magic - Feature Specifications & Implementation Details

This document outlines the detailed logic, user workflows, and state specifications for every feature in **Color Magic**.

---

## 1. Explore & Filter Color Palettes (`/palettes`)

### User Workflow
1. User lands on `/palettes`.
2. Can filter by style tags (**Pastel**, **Vintage**, **Neon**, **Minimalist**, **Earthy**, **Eco**).
3. Can search by text query (matches palette title, tag, or hex code).
4. Can toggle favorite (heart icon) to save palette to local storage / account profile.
5. Clicking a palette card opens the detailed view (`/palette/[slug]`).

### State & Performance Specifications
- **Data Source**: `api/palettes.json` (contains 150+ curated palettes).
- **Favorites Storage**:
  - Key: `colormagic_favorite_palettes` in `localStorage`.
  - Next.js Integration: Zustand store with `persist` middleware, synced to database when authenticated.
- **Card Actions**:
  - Click on color bar -> copies hex code to clipboard & triggers toast notification (`"Copied #FF5733!"`).
  - Click on palette title -> navigates to `/palette/[slug]`.

---

## 2. Dynamic Palette Detail & Export (`/palette/[slug]`)

### Visual Specs & Analytics
- **Hero Palette View**: Displays swatches with flex-grow layout.
- **WCAG Contrast Checker**: Calculates luminance ratio for each color against `#FFFFFF` (White text) and `#111827` (Dark text). Passes AA (ratio >= 4.5) or AAA (ratio >= 7.0).
- **Brightness Spectrum**: Displays visual graph showing shade distribution across swatches.

### Export Engine Formats
- **HEX Array**: `["#2C3E50", "#E74C3C", "#ECF0F1", "#3498DB", "#2ECC71"]`
- **JSON**: Raw JSON structure with tags and titles.
- **CSS Variables**:
  ```css
  :root {
    --color-1: #2C3E50;
    --color-2: #E74C3C;
    --color-3: #ECF0F1;
    --color-4: #3498DB;
    --color-5: #2ECC71;
  }
  ```
- **Tailwind CSS Config**:
  ```javascript
  module.exports = {
    theme: {
      extend: {
        colors: {
          custom: {
            100: '#ECF0F1',
            500: '#3498DB',
            900: '#2C3E50',
          }
        }
      }
    }
  }
  ```
- **SVG / Image Download**: Dynamic client-side SVG generation & canvas download.

---

## 3. CSS Gradient Browser & Detail (`/gradients`, `/gradient/[id]`)

### Data Structure (`api/gradients.json`)
```json
{
  "id": "pm_24",
  "name": "Peachy Sunrise",
  "category": "warm",
  "type": "linear",
  "angle": "135deg",
  "colors": ["#FF7E5F", "#FEB47B"]
}
```

### Features
- **Filter Tabs**: All, Warm, Cool, Purple, Nature, Pink, Dark, Pastel, Neon, Earth, Mono.
- **CSS Output Engine**: Generates cross-browser CSS string:
  `background: linear-gradient(135deg, #FF7E5F 0%, #FEB47B 100%);`
- **Interactive Angle Controller**: Live slider updates angle in real time on detail page (`/gradient/[id]`).

---

## 4. Color Theory Palette Generator (`/generate-palette`)

### Generator Engine & Algorithms (`assets/js/generate-palette.js`)
The generator generates 5 visually harmonious colors based on a seed color (HEX/HSL) and selected harmony model.

#### Harmony Mathematics:
1. **Monochromatic**: Keeps Hue (\(H\)) constant, varies Saturation (\(S\)) and Lightness (\(L\)).
   - \(L_1 = L - 30\%\), \(L_2 = L - 15\%\), \(L_3 = L\), \(L_4 = L + 15\%\), \(L_5 = L + 30\%\).
2. **Complementary / Contrast**: Seed color \(H\), opposite color \((H + 180^\circ) \pmod{360^\circ}\), plus intermediate luminance steps.
3. **Triadic**: Three colors evenly spaced at \(120^\circ\) intervals on the color wheel:
   - \(H_1 = H\), \(H_2 = (H + 120^\circ) \pmod{360^\circ}\), \(H_3 = (H + 240^\circ) \pmod{360^\circ}\).
4. **Tetradic (Rectangle)**: Four colors arranged in two complementary pairs:
   - \(H_1 = H\), \(H_2 = (H + 60^\circ) \pmod{360^\circ}\), \(H_3 = (H + 180^\circ) \pmod{360^\circ}\), \(H_4 = (H + 240^\circ) \pmod{360^\circ}\).
5. **Analogous**: Colors adjacent to each other on the color wheel:
   - \(H_1 = (H - 40^\circ) \pmod{360^\circ}\), \(H_2 = (H - 20^\circ) \pmod{360^\circ}\), \(H_3 = H\), \(H_4 = (H + 20^\circ) \pmod{360^\circ}\), \(H_5 = (H + 40^\circ) \pmod{360^\circ}\).

#### Variation Controls:
- **Classic**: Balanced saturation (60-80%) & lightness (40-60%).
- **Soft & Muted**: Lower saturation (20-40%) & high lightness (70-90%).
- **Deep & Bold**: High saturation (80-100%) & low lightness (20-40%).

#### Key Interaction:
- **Spacebar**: Pressing `Space` recalculates un-locked swatches instantly.
- **Lock Swatch Toggle**: Users can lock individual slots while regenerating the remaining colors.

---

## 5. Image Color Extractor (`/palette-from-image`)

### Client-Side Extraction Algorithm (`assets/js/palette-from-image.js`)
1. User uploads an image or picks a preset image.
2. Image is loaded onto an invisible HTML5 `<canvas>` element.
3. Canvas pixels are sampled (sampling step rate scaled for performance).
4. Pixel RGB values are processed using **K-Means++ Clustering**:
   - Initial centroids selected using standard distance probability.
   - Iterative clustering assigns pixels to nearest centroid until convergence.
   - Cluster centers become dominant palette colors.
5. Extracted colors are sorted by luminance or dominance percentage.
6. User can dynamically adjust slider (3 to 12 colors) to re-cluster on the fly.

---

## 6. Color Identification & SEO Tools (`/find-color`, Micro Tools)

### Color Database (`api/color-names.json`)
- Maps 300+ Hex codes to official color names (e.g. `#FF5733` -> `"Persimmon"` / `"Coral Red"`).
- Nearest color distance computed using **Euclidean Distance in Lab Color Space (\(\Delta E_{76}\))** to guarantee human-perceptual accuracy.

### Micro Conversion Pages:
- **`/hex-to-color-name`**: Input field for Hex; outputs match name + exact hex.
- **`/hex-to-rgb`**: Input field for Hex; outputs `rgb(r, g, b)` and copyable object.
- **`/what-color-is`**: Identifies base color family (Red, Orange, Yellow, Green, Cyan, Blue, Purple, Magenta, Neutral).
- **`/dark-color-finder`**: Generates 10 dark shades (lightness 5%-35%) of any input color.
- **`/brand-color-lookup`**: Searchable reference of official tech & brand hex codes (Google Blue `#4285F4`, Spotify Green `#1DB954`, etc.).

---

## 7. Authentication, Authorization & User Dashboard

### NextAuth.js Configuration (`/app/api/auth/[...nextauth]/route.ts`)
- **OAuth Provider**: Google OAuth 2.0.
- **Client Credentials**: Loaded from environment variables:
  - `CLINET_ID_COLORMAGIC`
  - `CLIENT_SECRET_COLORMAGIC`
- **Authorized Callbacks & Origins**:
  - `https://colormagic.techkreative.com/auth/google-callback.php` (Legacy)
  - `https://colormagic.techkreative.com/api/auth/callback/google` (Next.js)
  - `http://localhost:3000/api/auth/callback/google` (Next.js Local Dev)

### User Profile & Data Persist (`/profile`)
- Displays user info (Name, Email, Profile Avatar).
- **Saved Favorites**: Synchronizes `localStorage` favorites to database upon sign-in.
- **Account Deletion Flow (`/auth/delete-account`)**:
  - Action destroys active session.
  - Removes stored user profile and associated saved palettes from persistent database.
  - Redirects user to `/?msg=account_deleted`.
