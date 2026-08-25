import { RGB } from '@/types';
import { rgbToHex } from './color-math';

/**
 * Extracts dominant color palette from ImageData using K-Means++ Clustering.
 */
export function extractColorsFromCanvas(
  ctx: CanvasRenderingContext2D,
  width: number,
  height: number,
  colorCount: number = 5,
  maxIterations: number = 10
): string[] {
  // Sample pixels at regular grid intervals for performance
  const step = Math.max(1, Math.floor((width * height) / 4000));
  const imageData = ctx.getImageData(0, 0, width, height);
  const data = imageData.data;
  const pixels: RGB[] = [];

  for (let i = 0; i < data.length; i += 4 * step) {
    const a = data[i + 3];
    // Ignore transparent or semi-transparent pixels
    if (a > 128) {
      pixels.push({
        r: data[i],
        g: data[i + 1],
        b: data[i + 2],
      });
    }
  }

  if (pixels.length === 0) {
    return ['#2C3E50', '#E74C3C', '#ECF0F1', '#3498DB', '#2ECC71'];
  }

  const k = Math.min(colorCount, pixels.length);
  // K-Means++ Centroid Initialization
  const centroids: RGB[] = [];
  centroids.push(pixels[Math.floor(Math.random() * pixels.length)]);

  for (let c = 1; c < k; c++) {
    const distances: number[] = pixels.map((p) => {
      let minDist = Infinity;
      for (const cent of centroids) {
        const dist =
          (p.r - cent.r) ** 2 + (p.g - cent.g) ** 2 + (p.b - cent.b) ** 2;
        if (dist < minDist) minDist = dist;
      }
      return minDist;
    });

    const sumDist = distances.reduce((acc, d) => acc + d, 0);
    let randomVal = Math.random() * sumDist;
    let chosenIndex = 0;

    for (let i = 0; i < distances.length; i++) {
      randomVal -= distances[i];
      if (randomVal <= 0) {
        chosenIndex = i;
        break;
      }
    }
    centroids.push(pixels[chosenIndex]);
  }

  // Iterative Clustering
  for (let iter = 0; iter < maxIterations; iter++) {
    const clusters: RGB[][] = Array.from({ length: k }, () => []);

    for (const p of pixels) {
      let minDist = Infinity;
      let clusterIndex = 0;

      for (let ci = 0; ci < centroids.length; ci++) {
        const cent = centroids[ci];
        const dist =
          (p.r - cent.r) ** 2 + (p.g - cent.g) ** 2 + (p.b - cent.b) ** 2;
        if (dist < minDist) {
          minDist = dist;
          clusterIndex = ci;
        }
      }
      clusters[clusterIndex].push(p);
    }

    let changed = false;
    for (let ci = 0; ci < k; ci++) {
      const cluster = clusters[ci];
      if (cluster.length > 0) {
        const sumR = cluster.reduce((acc, p) => acc + p.r, 0);
        const sumG = cluster.reduce((acc, p) => acc + p.g, 0);
        const sumB = cluster.reduce((acc, p) => acc + p.b, 0);

        const newCentroid = {
          r: Math.round(sumR / cluster.length),
          g: Math.round(sumG / cluster.length),
          b: Math.round(sumB / cluster.length),
        };

        if (
          newCentroid.r !== centroids[ci].r ||
          newCentroid.g !== centroids[ci].g ||
          newCentroid.b !== centroids[ci].b
        ) {
          centroids[ci] = newCentroid;
          changed = true;
        }
      }
    }

    if (!changed) break;
  }

  return centroids.map((c) => rgbToHex(c.r, c.g, c.b));
}
