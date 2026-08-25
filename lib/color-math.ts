import { RGB, HSL, HSV, CMYK, LAB, ColorName } from '@/types';

/**
 * Normalizes hex string, ensuring # prefix and 6 digits.
 */
export function normalizeHex(hex: string): string {
  let cleaned = hex.trim().replace(/^#/, '');
  if (cleaned.length === 3) {
    cleaned = cleaned.split('').map((c) => c + c).join('');
  }
  if (cleaned.length !== 6) {
    cleaned = '000000';
  }
  return `#${cleaned.toUpperCase()}`;
}

export function hexToRgb(hex: string): RGB {
  const cleanHex = normalizeHex(hex).slice(1);
  const num = parseInt(cleanHex, 16);
  return {
    r: (num >> 16) & 255,
    g: (num >> 8) & 255,
    b: num & 255,
  };
}

export function rgbToHex(r: number, g: number, b: number): string {
  const clamp = (val: number) => Math.max(0, Math.min(255, Math.round(val)));
  const hexR = clamp(r).toString(16).padStart(2, '0');
  const hexG = clamp(g).toString(16).padStart(2, '0');
  const hexB = clamp(b).toString(16).padStart(2, '0');
  return `#${hexR}${hexG}${hexB}`.toUpperCase();
}

export function rgbToHsl(r: number, g: number, b: number): HSL {
  const rNorm = r / 255;
  const gNorm = g / 255;
  const bNorm = b / 255;

  const max = Math.max(rNorm, gNorm, bNorm);
  const min = Math.min(rNorm, gNorm, bNorm);
  let h = 0;
  let s = 0;
  const l = (max + min) / 2;

  if (max !== min) {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case rNorm:
        h = (gNorm - bNorm) / d + (gNorm < bNorm ? 6 : 0);
        break;
      case gNorm:
        h = (bNorm - rNorm) / d + 2;
        break;
      case bNorm:
        h = (rNorm - gNorm) / d + 4;
        break;
    }
    h /= 6;
  }

  return {
    h: Math.round(h * 360),
    s: Math.round(s * 100),
    l: Math.round(l * 100),
  };
}

export function hslToRgb(h: number, s: number, l: number): RGB {
  const hNorm = (h % 360) / 360;
  const sNorm = Math.max(0, Math.min(100, s)) / 100;
  const lNorm = Math.max(0, Math.min(100, l)) / 100;

  if (sNorm === 0) {
    const val = Math.round(lNorm * 255);
    return { r: val, g: val, b: val };
  }

  const hue2rgb = (p: number, q: number, t: number) => {
    let tNorm = t;
    if (tNorm < 0) tNorm += 1;
    if (tNorm > 1) tNorm -= 1;
    if (tNorm < 1 / 6) return p + (q - p) * 6 * tNorm;
    if (tNorm < 1 / 2) return q;
    if (tNorm < 2 / 3) return p + (q - p) * (2 / 3 - tNorm) * 6;
    return p;
  };

  const q = lNorm < 0.5 ? lNorm * (1 + sNorm) : lNorm + sNorm - lNorm * sNorm;
  const p = 2 * lNorm - q;

  const r = hue2rgb(p, q, hNorm + 1 / 3);
  const g = hue2rgb(p, q, hNorm);
  const b = hue2rgb(p, q, hNorm - 1 / 3);

  return {
    r: Math.round(r * 255),
    g: Math.round(g * 255),
    b: Math.round(b * 255),
  };
}

export function rgbToHsv(r: number, g: number, b: number): HSV {
  const rNorm = r / 255;
  const gNorm = g / 255;
  const bNorm = b / 255;

  const max = Math.max(rNorm, gNorm, bNorm);
  const min = Math.min(rNorm, gNorm, bNorm);
  const d = max - min;

  let h = 0;
  const s = max === 0 ? 0 : d / max;
  const v = max;

  if (max !== min) {
    switch (max) {
      case rNorm:
        h = (gNorm - bNorm) / d + (gNorm < bNorm ? 6 : 0);
        break;
      case gNorm:
        h = (bNorm - rNorm) / d + 2;
        break;
      case bNorm:
        h = (rNorm - gNorm) / d + 4;
        break;
    }
    h /= 6;
  }

  return {
    h: Math.round(h * 360),
    s: Math.round(s * 100),
    v: Math.round(v * 100),
  };
}

export function rgbToCmyk(r: number, g: number, b: number): CMYK {
  const rNorm = r / 255;
  const gNorm = g / 255;
  const bNorm = b / 255;

  const k = 1 - Math.max(rNorm, gNorm, bNorm);
  if (k === 1) {
    return { c: 0, m: 0, y: 0, k: 100 };
  }

  const c = (1 - rNorm - k) / (1 - k);
  const m = (1 - gNorm - k) / (1 - k);
  const y = (1 - bNorm - k) / (1 - k);

  return {
    c: Math.round(c * 100),
    m: Math.round(m * 100),
    y: Math.round(y * 100),
    k: Math.round(k * 100),
  };
}

export function rgbToXyz(r: number, g: number, b: number) {
  let rN = r / 255;
  let gN = g / 255;
  let bN = b / 255;

  rN = rN > 0.04045 ? Math.pow((rN + 0.055) / 1.055, 2.4) : rN / 12.92;
  gN = gN > 0.04045 ? Math.pow((gN + 0.055) / 1.055, 2.4) : gN / 12.92;
  bN = bN > 0.04045 ? Math.pow((bN + 0.055) / 1.055, 2.4) : bN / 12.92;

  rN *= 100;
  gN *= 100;
  bN *= 100;

  const x = rN * 0.4124 + gN * 0.3576 + bN * 0.1805;
  const y = rN * 0.2126 + gN * 0.7152 + bN * 0.0722;
  const z = rN * 0.0193 + gN * 0.1192 + bN * 0.9505;

  return { x, y, z };
}

export function xyzToLab(x: number, y: number, z: number): LAB {
  // Reference White D65
  let xN = x / 95.047;
  let yN = y / 100.0;
  let zN = z / 108.883;

  const f = (val: number) =>
    val > 0.008856 ? Math.pow(val, 1 / 3) : 7.787 * val + 16 / 116;

  xN = f(xN);
  yN = f(yN);
  zN = f(zN);

  return {
    l: 116 * yN - 16,
    a: 500 * (xN - yN),
    b: 200 * (yN - zN),
  };
}

export function rgbToLab(r: number, g: number, b: number): LAB {
  const xyz = rgbToXyz(r, g, b);
  return xyzToLab(xyz.x, xyz.y, xyz.z);
}

/**
 * Calculates Delta E 76 Lab Color Space distance between two colors.
 */
export function deltaE76(lab1: LAB, lab2: LAB): number {
  const dL = lab1.l - lab2.l;
  const dA = lab1.a - lab2.a;
  const dB = lab1.b - lab2.b;
  return Math.sqrt(dL * dL + dA * dA + dB * dB);
}

/**
 * WCAG 2.1 relative luminance calculation.
 */
export function getLuminance(r: number, g: number, b: number): number {
  const a = [r, g, b].map((v) => {
    const norm = v / 255;
    return norm <= 0.03928 ? norm / 12.92 : Math.pow((norm + 0.055) / 1.055, 2.4);
  });
  return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

/**
 * Calculates WCAG 2.1 contrast ratio between two luminance values.
 */
export function getContrastRatio(lum1: number, lum2: number): number {
  const lighter = Math.max(lum1, lum2);
  const darker = Math.min(lum1, lum2);
  return Number(((lighter + 0.05) / (darker + 0.05)).toFixed(2));
}

/**
 * Finds closest color name matching hex code from database using Delta E 76.
 */
export function findClosestColorName(
  targetHex: string,
  namesDatabase: ColorName[]
): { name: string; hex: string; distance: number } {
  const normalizedTarget = normalizeHex(targetHex);
  const targetRgb = hexToRgb(normalizedTarget);
  const targetLab = rgbToLab(targetRgb.r, targetRgb.g, targetRgb.b);

  let closestName = 'Unknown Color';
  let closestHex = normalizedTarget;
  let minDistance = Infinity;

  for (const item of namesDatabase) {
    const itemHex = normalizeHex(item.hex);
    if (itemHex === normalizedTarget) {
      return { name: item.name, hex: itemHex, distance: 0 };
    }
    const itemRgb = hexToRgb(itemHex);
    const itemLab = rgbToLab(itemRgb.r, itemRgb.g, itemRgb.b);
    const dist = deltaE76(targetLab, itemLab);

    if (dist < minDistance) {
      minDistance = dist;
      closestName = item.name;
      closestHex = itemHex;
    }
  }

  return {
    name: closestName,
    hex: closestHex,
    distance: Number(minDistance.toFixed(2)),
  };
}

/**
 * Generates Tints (mixing with white) and Shades (mixing with black).
 */
export function generateTintsAndShades(hex: string, count: number = 8) {
  const rgb = hexToRgb(hex);
  const tints: string[] = [];
  const shades: string[] = [];

  for (let i = 1; i <= count; i++) {
    const factor = i / (count + 1);
    // Tint (towards 255)
    const tintR = Math.round(rgb.r + (255 - rgb.r) * factor);
    const tintG = Math.round(rgb.g + (255 - rgb.g) * factor);
    const tintB = Math.round(rgb.b + (255 - rgb.b) * factor);
    tints.push(rgbToHex(tintR, tintG, tintB));

    // Shade (towards 0)
    const shadeR = Math.round(rgb.r * (1 - factor));
    const shadeG = Math.round(rgb.g * (1 - factor));
    const shadeB = Math.round(rgb.b * (1 - factor));
    shades.push(rgbToHex(shadeR, shadeG, shadeB));
  }

  return { tints, shades };
}
