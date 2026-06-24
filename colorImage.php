<?php

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: public, max-age=2592000'); // Optional: cache for 30 days

// Rest of your code...
// Safety check: Is the GD library actually enabled?
if (!function_exists('imagecreatetruecolor')) {
    die("Error: The PHP GD extension is not enabled. Please enable 'extension=gd' in your php.ini file.");
}

// 1. Get the hex from the URL
$hex = isset($_GET['hex']) ? $_GET['hex'] : 'FF0000';

// 2. Clean and Validate
$hex = str_replace('#', '', $hex);
if (!preg_match('/^[a-f0-9]{6}$/i', $hex)) {
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid Hex Code";
    exit;
}

// 3. Convert Hex to RGB
$r = hexdec(substr($hex, 0, 2));
$g = hexdec(substr($hex, 2, 2));
$b = hexdec(substr($hex, 4, 2));

// 4. Create the Image (150x142)
$img = imagecreatetruecolor(600, 600);

// 5. Fill with Color
$color = imagecolorallocate($img, $r, $g, $b);
imagefill($img, 0, 0, $color);

// 6. Output as WebP
header('Content-Type: image/webp');
// If your PHP version is very old and doesn't support WebP, use imagepng($img)
if (function_exists('imagewebp')) {
    imagewebp($img);
} else {
    // Fallback to PNG if WebP is not supported by your specific GD build
    header('Content-Type: image/png');
    imagepng($img);
}

// 7. Cleanup
imagedestroy($img);
?>