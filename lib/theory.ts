import { hexToRgb, rgbToHsl, hslToRgb, rgbToHex, normalizeHex } from './color-math';

export type HarmonyType = 'monochromatic' | 'complementary' | 'triadic' | 'tetradic' | 'analogous';
export type VariationType = 'classic' | 'soft' | 'bold';

/**
 * Calculates color harmony angles based on seed hex code.
 */
export function generateHarmonyColors(
  seedHex: string,
  harmony: HarmonyType = 'complementary',
  variation: VariationType = 'classic'
): string[] {
  const normSeed = normalizeHex(seedHex);
  const rgb = hexToRgb(normSeed);
  const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

  let hues: number[] = [];

  switch (harmony) {
    case 'monochromatic':
      hues = [hsl.h, hsl.h, hsl.h, hsl.h, hsl.h];
      break;
    case 'complementary':
      // Seed, Complementary (180deg), +30, -30, +150
      hues = [
        hsl.h,
        (hsl.h + 180) % 360,
        (hsl.h + 30) % 360,
        (hsl.h + 210) % 360,
        (hsl.h + 330) % 360,
      ];
      break;
    case 'triadic':
      // Seed, +120, +240, +60, +180
      hues = [
        hsl.h,
        (hsl.h + 120) % 360,
        (hsl.h + 240) % 360,
        (hsl.h + 60) % 360,
        (hsl.h + 180) % 360,
      ];
      break;
    case 'tetradic':
      // Seed, +90, +180, +270, +45
      hues = [
        hsl.h,
        (hsl.h + 90) % 360,
        (hsl.h + 180) % 360,
        (hsl.h + 270) % 360,
        (hsl.h + 45) % 360,
      ];
      break;
    case 'analogous':
      // Seed, -30, -15, +15, +30
      hues = [
        hsl.h,
        (hsl.h + 330) % 360,
        (hsl.h + 345) % 360,
        (hsl.h + 15) % 360,
        (hsl.h + 30) % 360,
      ];
      break;
  }

  // Apply variations (saturation & lightness modifiers)
  return hues.map((h, index) => {
    let s = hsl.s;
    let l = hsl.l;

    if (harmony === 'monochromatic') {
      const step = index * 15;
      l = Math.max(15, Math.min(85, 20 + step * 1.5));
    } else {
      if (variation === 'soft') {
        s = Math.max(20, s * 0.55);
        l = Math.min(85, Math.max(45, l * 1.1));
      } else if (variation === 'bold') {
        s = Math.min(100, Math.max(75, s * 1.3));
        l = Math.max(25, Math.min(65, l));
      } else {
        // Classic variation offsets for depth
        if (index === 0) l = Math.min(90, Math.max(40, l));
        else if (index === 1) l = Math.max(20, l * 0.7);
        else if (index === 2) l = Math.min(92, l * 1.2);
        else if (index === 3) s = Math.max(30, s * 0.8);
      }
    }

    const rgbResult = hslToRgb(h, s, l);
    return rgbToHex(rgbResult.r, rgbResult.g, rgbResult.b);
  });
}
