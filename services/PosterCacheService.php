<?php
/**
 * services/PosterCacheService.php
 * Automated caching & generation service for high-res visual posters
 * (Palettes, Gradients, Colors).
 *
 * Saves generated images to:
 *   assets/generated/posters/palettes/{slug}.jpg
 *   assets/generated/posters/gradients/{id}.jpg
 *   assets/generated/posters/colors/{slug}.jpg
 */

require_once __DIR__ . '/PinterestImageGenerator.php';

class PosterCacheService {

    private static string $baseDir = __DIR__ . '/../assets/generated/posters';

    /**
     * Ensure the cache directory exists
     */
    private static function ensureDir(string $subDir): string {
        $dir = self::$baseDir . '/' . trim($subDir, '/');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    /**
     * Generate or fetch Palette Poster
     */
    public static function getPalettePoster(array $palette, bool $force = false): string {
        $name = $palette['name'] ?? 'palette';
        $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($name)), '-');
        if (empty($slug)) $slug = $palette['id'] ?? 'palette_1';

        $dir = self::ensureDir('palettes');
        $filePath = $dir . '/' . $slug . '.jpg';

        if (!$force && file_exists($filePath) && filesize($filePath) > 1000) {
            return $filePath;
        }

        $base64 = PinterestImageGenerator::generatePaletteImage($palette);
        $binary = base64_decode($base64);
        file_put_contents($filePath, $binary);

        return $filePath;
    }

    /**
     * Generate or fetch Gradient Poster
     */
    public static function getGradientPoster(array $gradient, bool $force = false): string {
        $id = $gradient['id'] ?? 'gradient_1';
        $dir = self::ensureDir('gradients');
        $filePath = $dir . '/' . $id . '.jpg';

        if (!$force && file_exists($filePath) && filesize($filePath) > 1000) {
            return $filePath;
        }

        $base64 = PinterestImageGenerator::generateGradientImage($gradient);
        $binary = base64_decode($base64);
        file_put_contents($filePath, $binary);

        return $filePath;
    }

    /**
     * Generate or fetch Named Color Poster
     */
    public static function getColorPoster(array $colorData, bool $force = false): string {
        $hex = strtoupper(ltrim($colorData['hex'] ?? '000000', '#'));
        $slug = $colorData['slug'] ?? strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', (string)($colorData['name'] ?? $hex)), '-'));
        if (empty($slug)) $slug = strtolower($hex);

        $dir = self::ensureDir('colors');
        $filePath = $dir . '/' . $slug . '.jpg';

        if (!$force && file_exists($filePath) && filesize($filePath) > 1000) {
            return $filePath;
        }

        $base64 = PinterestImageGenerator::generateColorImage($colorData);
        $binary = base64_decode($base64);
        file_put_contents($filePath, $binary);

        return $filePath;
    }
}

