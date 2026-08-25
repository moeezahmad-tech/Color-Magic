export interface Palette {
  id: string;
  slug: string;
  name: string;
  style: string;
  tags: string[];
  colors: string[];
  likes?: number;
  createdAt?: string;
}

export interface Gradient {
  id: string;
  name: string;
  style: string;
  type: string;
  angle?: string | number;
  shape?: string;
  colors: string[];
  css?: string;
}

export interface ColorName {
  hex: string;
  name: string;
  slug?: string;
  aliases?: string[];
}

export interface RGB {
  r: number;
  g: number;
  b: number;
}

export interface HSL {
  h: number;
  s: number;
  l: number;
}

export interface HSV {
  h: number;
  s: number;
  v: number;
}

export interface CMYK {
  c: number;
  m: number;
  y: number;
  k: number;
}

export interface LAB {
  l: number;
  a: number;
  b: number;
}

export interface ColorDetails {
  hex: string;
  name: string;
  slug: string;
  rgb: RGB;
  hsl: HSL;
  hsv: HSV;
  cmyk: CMYK;
  lab: LAB;
  luminance: number;
  isDark: boolean;
  contrastWhite: number;
  contrastBlack: number;
}

export interface SwatchTheory {
  hex: string;
  locked: boolean;
}
