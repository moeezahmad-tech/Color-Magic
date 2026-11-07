<?php
/**
 * services/PinterestImageGenerator.php
 * Generates exact pixel-perfect poster graphics matching the site's download button
 * (image-export.js) for Color Palettes, CSS Gradients, and Named Colors.
 *
 * Professional typography, high resolution, crisp architectural dividing lines,
 * generous editorial spacing, and zero blurry shadows.
 */

class PinterestImageGenerator {

    private static function getFont(string $weight = 'bold'): string {
        $base = __DIR__ . '/../assets/Inter/static/';
        $map = [
            'black'     => $base . 'Inter_28pt-Black.ttf',
            'extrabold' => $base . 'Inter_28pt-ExtraBold.ttf',
            'bold'      => $base . 'Inter_28pt-Bold.ttf',
            'semibold'  => $base . 'Inter_28pt-SemiBold.ttf',
            'medium'    => $base . 'Inter_28pt-Medium.ttf',
            'regular'   => $base . 'Inter_28pt-Regular.ttf',
        ];

        $target = $map[$weight] ?? $map['bold'];
        if (file_exists($target)) {
            return $target;
        }

        // Fallback to any available font
        foreach ($map as $f) {
            if (file_exists($f)) return $f;
        }

        return 'C:/Windows/Fonts/arialbd.ttf';
    }

    private static function hexToRgb(string $hex): array {
        $h = strtoupper(ltrim(trim($hex), '#'));
        if (strlen($h) === 3) {
            $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        }
        return [
            'r' => hexdec(substr($h, 0, 2)),
            'g' => hexdec(substr($h, 2, 2)),
            'b' => hexdec(substr($h, 4, 2))
        ];
    }

    private static function getLuminance(string $hex): float {
        $rgb = self::hexToRgb($hex);
        return (0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b']) / 255.0;
    }

    /**
     * Exact 1:1 reproduction of window.ColorMagic.exportPaletteImage from image-export.js
     * High-res 1800x2100 (6:7 ratio), decreasing bands, generous brand-to-title gap,
     * crisp high-contrast HEX in each band, and website watermark.
     */
    public static function generatePaletteImage(array $palette): string {
        $W = 1800;
        $H = 2100;

        $im = imagecreatetruecolor($W, $H);
        imagesavealpha($im, true);

        $colors = $palette['colors'] ?? ['#1B5E20', '#2E7D32', '#66BB6A', '#C8E6C9'];
        $name = strtoupper(trim($palette['name'] ?? 'Color Palette'));
        $n = count($colors);

        // Decreasing heights progression: h0 = 4H / 3n, hn-1 = h0 / 2
        $heights = [];
        if ($n <= 1) {
            $heights = [$H];
        } else {
            $h0 = (4.0 * $H) / (3.0 * $n);
            $d = $h0 / (2.0 * ($n - 1));
            for ($i = 0; $i < $n; $i++) {
                $heights[] = $h0 - $i * $d;
            }
        }

        // Calculate Y boundaries
        $yBounds = [0];
        $curY = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $curY += $heights[$i];
            $yBounds[] = (int) round($curY);
        }
        $yBounds[$n] = $H;

        $fontBlack = self::getFont('black');
        $fontBold = self::getFont('bold');
        $fontSemi = self::getFont('semibold');

        // 1. Draw horizontal color bands
        for ($i = 0; $i < $n; $i++) {
            $hex = $colors[$i];
            $rgb = self::hexToRgb($hex);
            $bandColor = imagecolorallocate($im, $rgb['r'], $rgb['g'], $rgb['b']);

            $y0 = $yBounds[$i];
            $y1 = $yBounds[$i + 1];

            imagefilledrectangle($im, 0, $y0, $W, $y1, $bandColor);

            // Clean, flat text contrast (zero drop shadows)
            $lum = self::getLuminance($hex);
            $isLight = ($lum > 0.60);
            $textCol = $isLight ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);

            // Clean HEX code near bottom-left of band
            $cleanHex = strtoupper(ltrim($hex, '#'));
            $padX = 100;
            $hexY = $y1 - 85;

            // Size 56px in 1800x2100 resolution
            imagettftext($im, 56, 0, $padX, $hexY, $textCol, $fontBlack, $cleanHex);
        }

        // 2. Band 0 Top: Brand Tag ("COLOR MAGIC") + Large Palette Name
        $topPadX = 100;
        $topPadY = 88;
        $firstColor = $colors[0] ?? '#FFFFFF';
        $firstLum = self::getLuminance($firstColor);
        $topTextCol = ($firstLum > 0.60) ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);

        // Line 1: Brand kicker: "COLOR MAGIC"
        $kickerY = $topPadY + 28;
        imagettftext($im, 26, 0, $topPadX, $kickerY, $topTextCol, $fontBold, "COLOR MAGIC");

        // Line 2: Large Palette Name with generous breathing room below kicker
        $nameFontSize = 74;
        $nameY = $kickerY + $nameFontSize + 26;

        // Auto-scale font size if name is long
        $bbox = imagettfbbox($nameFontSize, 0, $fontBlack, $name);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $maxWidth = $W - ($topPadX * 2);
        if ($textWidth > $maxWidth && $textWidth > 0) {
            $nameFontSize = (int) max(36, floor($nameFontSize * ($maxWidth / $textWidth)));
        }

        imagettftext($im, $nameFontSize, 0, $topPadX, $nameY, $topTextCol, $fontBlack, $name);

        // 3. Last Band Bottom: Website URL at bottom-right
        $lastColor = $colors[count($colors) - 1] ?? '#000000';
        $lastLum = self::getLuminance($lastColor);
        $urlColor = ($lastLum > 0.60) ? imagecolorallocate($im, 30, 41, 59) : imagecolorallocate($im, 241, 245, 249);

        $urlText = "colormagic.techkreative.com";
        $urlBbox = imagettfbbox(24, 0, $fontSemi, $urlText);
        $urlW = abs($urlBbox[4] - $urlBbox[0]);
        $urlX = $W - $topPadX - $urlW;
        $urlY = $H - 80;

        imagettftext($im, 24, 0, $urlX, $urlY, $urlColor, $fontSemi, $urlText);

        ob_start();
        imagejpeg($im, null, 98);
        $data = ob_get_clean();
        imagedestroy($im);

        return base64_encode($data);
    }

    /**
     * Exact reproduction of window.ColorMagic.renderGradientCanvas from image-export.js
     * High-res 1648x2048, crisp dividing line between gradient and bottom color swatches,
     * dividing line between swatches, generous spacing after brand kicker, and zero blurry shadows.
     */
    public static function generateGradientImage(array $gradient): string {
        $W = 1648;
        $H = 2048;

        $im = imagecreatetruecolor($W, $H);
        imagesavealpha($im, true);

        $name = strtoupper(trim($gradient['name'] ?? 'Gradient'));
        $colors = $gradient['colors'] ?? ['#FF512F', '#DD2476'];
        $type = strtolower($gradient['type'] ?? 'linear');

        $fontBlack = self::getFont('black');
        $fontBold = self::getFont('bold');
        $fontSemi = self::getFont('semibold');

        // 1. Top Section: Gradient (top ~66.8% of total height)
        $gradientH = (int) round($H * 0.668);

        $rgbStops = array_map(fn($h) => self::hexToRgb($h), $colors);
        $numStops = count($rgbStops);

        for ($y = 0; $y < $gradientH; $y++) {
            $t = $y / max(1, $gradientH - 1);
            $segment = $t * ($numStops - 1);
            $idx = min($numStops - 2, (int) floor($segment));
            $segT = $segment - $idx;

            $c1 = $rgbStops[$idx];
            $c2 = $rgbStops[$idx + 1] ?? $c1;

            $r = (int) round($c1['r'] + ($c2['r'] - $c1['r']) * $segT);
            $g = (int) round($c1['g'] + ($c2['g'] - $c1['g']) * $segT);
            $b = (int) round($c1['b'] + ($c2['b'] - $c1['b']) * $segT);

            $lineCol = imagecolorallocate($im, $r, $g, $b);
            imageline($im, 0, $y, $W, $y, $lineCol);
        }

        // Top-left: Brand Tag & Gradient Title
        $topPadX = 92;
        $topPadY = 104;
        $firstLum = self::getLuminance($colors[0]);
        $topTitleColor = ($firstLum > 0.85) ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);

        // Line 1: COLOR MAGIC with generous space
        $kickerY = $topPadY + 28;
        imagettftext($im, 28, 0, $topPadX, $kickerY, $topTitleColor, $fontBold, "COLOR MAGIC");

        // Line 2: Large Gradient Title with generous breathing room below kicker
        $nameFontSize = 74;
        $nameY = $kickerY + $nameFontSize + 28;

        $nameBbox = imagettfbbox($nameFontSize, 0, $fontBlack, $name);
        $nameW = abs($nameBbox[4] - $nameBbox[0]);
        $maxNameW = $W - ($topPadX * 2);
        if ($nameW > $maxNameW && $nameW > 0) {
            $nameFontSize = (int) max(36, floor($nameFontSize * ($maxNameW / $nameW)));
        }
        imagettftext($im, $nameFontSize, 0, $topPadX, $nameY, $topTitleColor, $fontBlack, $name);

        // Watermark on bottom-right of gradient section (above dividing line)
        $lastColor = $colors[count($colors) - 1] ?? '#000000';
        $lastLum = self::getLuminance($lastColor);
        $urlColor = ($lastLum > 0.80) ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);

        $urlText = "colormagic.techkreative.com";
        $urlBbox = imagettfbbox(24, 0, $fontSemi, $urlText);
        $urlW = abs($urlBbox[4] - $urlBbox[0]);
        imagettftext($im, 24, 0, $W - $topPadX - $urlW, $gradientH - 42, $urlColor, $fontSemi, $urlText);

        // 2. Bottom Section: Color Blocks (bottom ~33.2% of total height)
        $blockAreaY = $gradientH;
        $blockAreaH = $H - $gradientH;

        $colW = (int) floor($W / count($colors));
        foreach ($colors as $idx => $hex) {
            $rgb = self::hexToRgb($hex);
            $blockCol = imagecolorallocate($im, $rgb['r'], $rgb['g'], $rgb['b']);

            $x1 = $idx * $colW;
            $x2 = ($idx === count($colors) - 1) ? $W : ($x1 + $colW);

            imagefilledrectangle($im, $x1, $blockAreaY, $x2, $H, $blockCol);

            $lum = self::getLuminance($hex);
            $isLight = ($lum > 0.50);
            $textColor = $isLight ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);
            $labelColor = $isLight ? imagecolorallocate($im, 75, 85, 99) : imagecolorallocate($im, 209, 213, 219);

            $padX = $x1 + 60;
            $textY = $blockAreaY + (int) round($blockAreaH * 0.35);

            // Clean HEX title
            $cleanHex = '#' . strtoupper(ltrim($hex, '#'));
            imagettftext($im, 38, 0, $padX, $textY, $textColor, $fontBlack, $cleanHex);

            // HEX Label
            imagettftext($im, 22, 0, $padX, $textY + 48, $labelColor, $fontBold, "HEX");
            imagettftext($im, 22, 0, $padX + 70, $textY + 48, $textColor, $fontBold, strtoupper(ltrim($hex, '#')));
        }

        // 3. Crisp dividing lines:
        // Clear difference between gradient canvas & bottom color blocks, plus between each swatch box
        $avgLum = array_sum(array_map([self::class, 'getLuminance'], $colors)) / count($colors);
        $dividerCol = ($avgLum > 0.82) ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);

        // Horizontal line between gradient and bottom swatch blocks (4px thick)
        imagefilledrectangle($im, 0, $gradientH - 2, $W, $gradientH + 2, $dividerCol);

        // Vertical lines between each swatch block (4px thick)
        foreach ($colors as $idx => $hex) {
            if ($idx > 0) {
                $divX = $idx * $colW;
                imagefilledrectangle($im, $divX - 2, $blockAreaY, $divX + 2, $H, $dividerCol);
            }
        }

        ob_start();
        imagejpeg($im, null, 98);
        $data = ob_get_clean();
        imagedestroy($im);

        return base64_encode($data);
    }

    /**
     * Exact reproduction of window.ColorMagic.renderColorCanvas from image-export.js
     * High-res 1600x2000, Pantone swatch style, clean typography, zero blurry shadows.
     */
    public static function generateColorImage(array $colorData): string {
        $W = 1600;
        $H = 2000;

        $im = imagecreatetruecolor($W, $H);
        imagesavealpha($im, true);

        $hex = $colorData['hex'] ?? '#3498DB';
        $cleanHex = strtoupper(ltrim($hex, '#'));
        $name = strtoupper(trim($colorData['name'] ?? ('#' . $cleanHex)));
        $rgb = self::hexToRgb($cleanHex);

        $fontBlack = self::getFont('black');
        $fontBold = self::getFont('bold');
        $fontSemi = self::getFont('semibold');

        // Full-bleed color background
        $bgCol = imagecolorallocate($im, $rgb['r'], $rgb['g'], $rgb['b']);
        imagefill($im, 0, 0, $bgCol);

        // Contrast calculation
        $isLight = (self::getLuminance($cleanHex) > 0.62);
        $textPrimary = $isLight ? imagecolorallocate($im, 17, 24, 39) : imagecolorallocate($im, 255, 255, 255);
        $textSecondary = $isLight ? imagecolorallocate($im, 31, 41, 55) : imagecolorallocate($im, 243, 244, 246);
        $textMuted = $isLight ? imagecolorallocate($im, 107, 114, 128) : imagecolorallocate($im, 156, 163, 175);

        $startX = 160;

        // Brand Tag with generous spacing
        imagettftext($im, 36, 0, $startX, 1200, $textSecondary, $fontBold, "COLOR MAGIC");

        // Large Color Name
        $nameFontSize = 100;
        $nameBbox = imagettfbbox($nameFontSize, 0, $fontBlack, $name);
        $nameW = abs($nameBbox[4] - $nameBbox[0]);
        $maxNameW = $W - ($startX * 2);
        if ($nameW > $maxNameW && $nameW > 0) {
            $nameFontSize = (int) max(48, floor($nameFontSize * ($maxNameW / $nameW)));
        }
        imagettftext($im, $nameFontSize, 0, $startX, 1340, $textPrimary, $fontBlack, $name);

        // Hex Code
        imagettftext($im, 58, 0, $startX, 1435, $textSecondary, $fontBold, '#' . $cleanHex);

        // Specs row
        $specsY = 1550;
        $rgbStr = "RGB {$rgb['r']}, {$rgb['g']}, {$rgb['b']}";
        imagettftext($im, 26, 0, $startX, $specsY, $textMuted, $fontSemi, $rgbStr);

        // Watermark
        $urlText = "colormagic.techkreative.com";
        imagettftext($im, 24, 0, $startX, 1850, $textMuted, $fontSemi, $urlText);

        ob_start();
        imagejpeg($im, null, 98);
        $data = ob_get_clean();
        imagedestroy($im);

        return base64_encode($data);
    }
}
