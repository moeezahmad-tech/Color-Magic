<div align="center">
  
  # ✨ Color Magic

  <p align="center">
    <strong>The Ultimate Open-Source Color Toolkit for Designers and Developers</strong>
  </p>

  <p align="center">
    <a href="https://nextjs.org/"><img src="https://img.shields.io/badge/Next.js-15-black?style=flat-square&logo=next.js" alt="Next.js" /></a>
    <a href="https://react.dev/"><img src="https://img.shields.io/badge/React-19-blue?style=flat-square&logo=react" alt="React" /></a>
    <a href="https://tailwindcss.com/"><img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" /></a>
    <a href="https://www.typescriptlang.org/"><img src="https://img.shields.io/badge/TypeScript-007ACC?style=flat-square&logo=typescript&logoColor=white" alt="TypeScript" /></a>
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

**Color Magic** is a modern, fast, and free open-source color toolkit built specifically for the creative community. It consolidates every color-related utility you need—from extracting palettes out of images to discovering WCAG-compliant contrasts—into a single, blazing-fast web application.

Zero paywalls. Zero sign-ups. 100% privacy-first.

<br />

## 🚀 Core Features & Tools

Explore the comprehensive suite of tools built into Color Magic.

| Tool & Icon | Description & Capabilities | Quick Link |
| :--- | :--- | :---: |
| 🔍 **Hex Color Finder** | Analyzes any hex code to provide precise color names, RGB/HSL values, and WCAG accessibility contrast ratios. | [`/find-color`](/find-color) |
| 🏷️ **Hex to Color Name** | Instantly identifies human-readable names for obscure hex codes using an integrated database of 1000+ colors. | [`/hex-to-color-name`](/hex-to-color-name) |
| 🔄 **Format Converter** | Lightning-fast, bi-directional converter between Hex and RGB formats with live, real-time color previews. | [`/hex-to-rgb`](/hex-to-rgb) |
| 🖼️ **Image Palette** | Extracts dominant colors from user-uploaded images using a privacy-first, client-side K-Means++ clustering algorithm. | [`/palette-from-image`](/palette-from-image) |
| 🎨 **Color Identifier** | An interactive playground to visualize, identify, and dissect unknown color codes on the fly. | [`/what-color-is`](/what-color-is) |
| 🌙 **Dark Mode Finder** | Curated discovery tool specifically tuned to find deep, low-luminance shades and midnight tones for dark UIs. | [`/dark-color-finder`](/dark-color-finder) |
| 💼 **Brand Lookup** | A dedicated repository of official hex codes and primary palettes from major tech brands and corporations. | [`/brand-color-lookup`](/brand-color-lookup) |
| ✨ **Palette Generator** | Automated palette creation utilizing color theory (Analogous, Complementary, Triadic, Tetradic algorithms). | [`/generate-palette`](/generate-palette) |
| 🌈 **CSS Gradients** | Browse, filter, and instantly copy 100+ beautifully hand-crafted linear and radial CSS gradients. | [`/gradients`](/gradients) |

<br />

## 💻 Technical Architecture

Color Magic is built on a robust, modern frontend stack designed for maximum performance and developer experience.

| Layer | Technology | Purpose |
| :--- | :--- | :--- |
| **Framework** | **Next.js 15** | App Router architecture, Server & Client Components, API Routes. |
| **UI Library** | **React 19** | Core component rendering and interactive state management. |
| **Styling** | **Tailwind CSS** | Utility-first styling for a highly responsive, custom design system. |
| **State** | **Zustand** | Lightweight global state management, utilizing `persist` for local storage. |
| **Icons** | **Lucide React** | Beautiful, consistent, and scalable SVG iconography. |
| **Language** | **TypeScript** | Strict end-to-end type safety across components and API responses. |

<br />

## 📡 API Reference & Endpoints

Color Magic provides several internal API routes for data fetching. These are designed to be extremely lightweight.

| Endpoint | Method | Description |
| :--- | :---: | :--- |
| `/api/v1/colors` | `GET` | Fetches the comprehensive database of 1000+ named colors. Supports query filtering. |
| `/api/v1/palettes` | `GET` | Retrieves curated and popular color palettes for the exploration dashboard. |
| `/api/v1/gradients` | `GET` | Retrieves the curated collection of linear and radial CSS gradients. |
| `/api/auth/[...nextauth]` | `POST/GET` | Handles session management and OAuth authentication flows via NextAuth. |
| `/api/user/delete` | `GET` | Securely handles the purging of user data and invalidation of active sessions. |

<br />

## 🔐 Privacy & Local Processing

Color Magic prioritizes user privacy and performance:
- **Zero Server Uploads**: The **Palette from Image** tool processes images entirely inside your browser using the HTML5 Canvas API. Your images never touch a remote server.
- **Local Storage Engine**: Your saved favorite palettes and gradients are stored directly in your browser using Zustand's `persist` middleware. 
- **Standalone Math Utilities**: Color conversions and contrast checking algorithms run locally via zero-dependency math functions (`/lib/color-math.ts`).

<br />

## 🛠️ Getting Started (Local Development)

Follow these steps to set up the project locally on your machine.

### Prerequisites
- **Node.js** (v18.0 or higher recommended)
- **npm** or **yarn** or **pnpm**

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/moeezahmad-tech/Color-Magic.git
   cd Color-Magic
   ```

2. **Install dependencies**
   ```bash
   npm install
   # or yarn install
   ```

3. **Start the development server**
   ```bash
   npm run dev
   ```

4. **View the application**
   Open your browser and navigate to `http://localhost:3000`.

<br />

## 🤝 Contributing

We welcome contributions from the community! Whether it's a bug fix, a new color tool, or a documentation improvement, your help makes this project better for everyone.

1. **Fork** the repository.
2. **Create a branch** for your feature: `git checkout -b feature/your-feature-name`
3. **Commit your changes**: `git commit -m "Add some feature"`
4. **Push to your branch**: `git push origin feature/your-feature-name`
5. **Open a Pull Request** against the `main` branch.

<br />

## 📄 License

This project is open-source and available under the **MIT License**. You are free to use, modify, and distribute this software as you see fit.

---

<div align="center">
  <p>Crafted with 🖤 for the Open Source Community.</p>
</div>
