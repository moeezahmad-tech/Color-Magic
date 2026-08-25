# Color Magic - Design System, Styling Tokens & Typography Guidelines

This document specifies the exact visual design system, color tokens, typography rules, component styling behavior, micro-animations, and visual consistency rules to guarantee 100% fidelity when building **Color Magic** in Next.js.

---

## 🎨 Color Tokens & Palette Variables

### CSS Custom Properties (`assets/css/style.css`)
```css
:root {
  --cm-bg: #ffffff;
  --cm-ink: #0f172a;          /* Slate 900 */
  --cm-primary: #ec4899;      /* Pink 500 */
  --cm-secondary: #7c3aed;    /* Violet 600 */
  --cm-soft: #fdf2f8;         /* Pink 50 */
}
```

### Brand Gradients
1. **Primary Button / Brand Accent**:
   - `background: linear-gradient(135deg, #ec4899 0%, #7c3aed 100%)`
   - Hover state shadow: `box-shadow: 0 15px 35px -5px rgba(124, 58, 237, 0.45)`
   - Active state shadow: `box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.45)`

2. **Card Border Highlight**:
   - `border: 1px solid rgba(236, 72, 153, 0.12)`
   - Hover border: `border-color: rgba(236, 72, 153, 0.35)`

3. **Sticky Header Backdrop**:
   - `background: rgba(255, 255, 255, 0.86)`
   - `backdrop-filter: blur(12px)`
   - `border-bottom: 1px solid rgba(236, 72, 153, 0.2)`

---

## 🔤 Typography & Font Specs

### Font Family: **Inter** (Google Font)
Self-hosted Variable Font with optical sizing (`opsz: 14..32`, `wght: 100..900`).

```tsx
// Next.js Font Config (src/app/layout.tsx)
import { Inter } from 'next/font/google';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-inter',
  display: 'swap',
});
```

### Font Classes & Weight Tokens:
- **Main Heading (`.theme-main-heading`)**:
  - `font-family: 'Inter', sans-serif;`
  - `font-weight: 800;` (ExtraBold)
  - `letter-spacing: -0.025em;`
- **Body Text (`.theme-text`)**:
  - `font-family: 'Inter', sans-serif;`
  - `font-weight: 400;` (Regular)
  - `color: #0f172a;`
- **Section Label / Subtitle (`.theme-label`)**:
  - `font-family: 'Inter', sans-serif;`
  - `font-weight: 500;` (Medium)
  - `text-transform: uppercase;`
  - `letter-spacing: 2px;`
  - `font-size: 0.75rem;` (12px)

---

## ⚡ Micro-Animations & Interactivity Rules

### 1. Swatch Expand Animation (`.palette-card`)
- **Default State**: Multi-color swatches in a row with equal flex-grow (`flex: 1`).
- **Hover State**: Hovered color swatch expands to `flex-grow: 2.5` with smooth physics transition:
  - `transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);`

### 2. Swatch Action Icons (Staggered Entrance)
- When a user hovers over a swatch, action buttons (Copy Hex, Copy RGB, Dynamic Detail) slide down from top with staggered delays:
  - `.swatch-btn-1`: delay `0s`
  - `.swatch-btn-2`: delay `0.06s`
  - `.swatch-btn-3`: delay `0.12s`
  - Keyframe:
    ```css
    @keyframes swatchBtnIn {
      from { opacity: 0; transform: translateY(-6px); }
      to { opacity: 1; transform: translateY(0); }
    }
    ```

### 3. Copied Badge Toast Feedback
- When clicked, hex text changes to `.copied-state`:
  - `background: rgba(34, 197, 94, 0.9) !important;` (Emerald Green)
  - `color: #ffffff;`
  - Text updates to `"COPIED!"` for 1.5 seconds.

### 4. Ripple Hover Effect on Buttons (`.nav-btn`)
- Circular pseudo-element expands from button center on hover:
  - `transition: width 0.6s, height 0.6s;`

---

## 🧩 Component Visual Consistency Guidelines

| Component | Default Styling | Hover / Focus State |
| :--- | :--- | :--- |
| **Filter Tag Pill** | `border: 1px solid rgba(236, 72, 153, 0.16); bg-white; text-slate-700` | `bg-primary (#ec4899); text-white; shadow: 0 10px 26px -10px rgba(236, 72, 153, 0.65)` |
| **Palette Card Container** | `rounded-2xl; bg-white; border: 1px solid rgba(236, 72, 153, 0.12); shadow-sm` | `shadow-xl; border-color: rgba(236, 72, 153, 0.3); transform: translateY(-3px)` |
| **Gradient Card Preview** | `rounded-2xl; h-48; shadow-md; relative; overflow-hidden` | `scale-[1.02]; shadow-2xl` |
| **Search Input** | `bg-white/80; border: 1px solid rgba(236, 72, 153, 0.2); rounded-xl` | `focus:border-[#ec4899]; focus:ring-4 focus:ring-pink-500/10` |
| **Favorite Heart Icon** | `text-slate-400; transition: color 0.2s` | `text-pink-500; scale-110` (Filled when favorited) |

---

## 🎯 Mobile Responsiveness Guidelines

1. **Header & Navigation**:
   - Desktop: Full link bar with "Tools" dropdown.
   - Mobile (< 768px): Hamburger button opening a glassmorphic sidebar menu (`backdrop-filter: blur(10px)`).
2. **Grid Spacing**:
   - Palettes grid: 1 col on mobile, 2 cols on tablet, 3 cols on desktop.
   - Micro-tools feature cards: 1 col on mobile, 2 cols on tablet, 3 cols on desktop.
3. **Touch Friendly Target**:
   - Minimum button touch target: `44px x 44px`.
