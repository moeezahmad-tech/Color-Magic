<?php
// Safety check: Is the GD library actually enabled?
if (!function_exists('imagecreatetruecolor')) {
    die("Error: The PHP GD extension is not enabled. Please enable 'extension=gd' in your php.ini file.");
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function normalizeHex(string $hex): string
{
    return strtoupper(ltrim(trim($hex), '#'));
}

// 1. Get the palette ID from the URL
$paletteId = isset($_GET['id']) ? (string) $_GET['id'] : '';

if (empty($paletteId)) {
    header("HTTP/1.1 400 Bad Request");
    echo "Missing palette ID";
    exit;
}

// 2. Load palette data
$palettePath = 'https://api.colormagic.techkreative.com/palettes.json';
$jsonContent = @file_get_contents($palettePath);
if ($jsonContent === false) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Palette data not found";
    exit;
}

$decoded = json_decode((string) $jsonContent, true);
if (!is_array($decoded)) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Invalid palette data";
    exit;
}

// 3. Find the palette
$palette = null;
foreach ($decoded as $p) {
    if (is_array($p) && isset($p['id']) && $p['id'] === $paletteId) {
        $palette = $p;
        break;
    }
}

if (!$palette || !isset($palette['colors']) || !is_array($palette['colors']) || count($palette['colors']) === 0) {
    header("HTTP/1.1 404 Not Found");
    echo "Palette not found";
    exit;
}

// 4. Extract and validate colors
$colors = [];
foreach ($palette['colors'] as $hexColor) {
    if (!is_string($hexColor)) {
        continue;
    }
    $normalized = normalizeHex($hexColor);
    if (preg_match('/^[A-F0-9]{6}$/', $normalized)) {
        $colors[] = '#' . $normalized;
    }
}

if (count($colors) === 0) {
    header("HTTP/1.1 400 Bad Request");
    echo "No valid colors in palette";
    exit;
}

// 5. Image dimensions
$imgWidth = 1200;
$imgHeight = 800;
$textHeight = 120;
$colorBlockHeight = $imgHeight - $textHeight;
$colorBlockWidth = $imgWidth / count($colors);

// 6. Create image
$img = imagecreatetruecolor($imgWidth, $imgHeight);
$backgroundColor = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $backgroundColor);

// 7. Draw color strips and text
$textColor = imagecolorallocate($img, 50, 50, 50);
$font =8; 

foreach ($colors as $index => $hexColor) {
    // Parse hex color
    $hex = str_replace('#', '', $hexColor);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Allocate color
    $color = imagecolorallocate($img, $r, $g, $b);
    
    // Draw color strip
    $x1 = (int) ($index * $colorBlockWidth);
    $y1 = 0;
    $x2 = (int) (($index + 1) * $colorBlockWidth) - 1;
    $y2 = $colorBlockHeight - 1;
    
    imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color);
    
    // Draw color code text at the bottom
    $textX = $x1 + (int) ($colorBlockWidth / 2) - 40;
    $textY = $colorBlockHeight + 30;
    imagestring($img, $font, $textX, $textY, $hexColor, $textColor);
}

// 8. Output as WebP
header('Content-Type: image/webp');
header('Cache-Control: public, max-age=86400');
header('Pragma: public');

if (function_exists('imagewebp')) {
    imagewebp($img, null, 80);
} else {
    // Fallback to PNG if WebP is not supported
    header('Content-Type: image/png');
    imagepng($img);
}

// 9. Cleanup
imagedestroy($img);
?>
