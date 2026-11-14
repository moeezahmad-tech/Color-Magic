<?php
if (!headers_sent()) {
    header('Cache-Control: public, max-age=3600, stale-while-revalidate=86400');
    header('Cloudflare-CDN-Cache-Control: public, max-age=86400, stale-while-revalidate=604800');
}
include '../../components/config.php';
/** @var string $base */

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function normalizeHex(string $hex): string
{
    return strtoupper(ltrim(trim($hex), '#'));
}

function hexToRgb(string $hex): array
{
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2)),
    ];
}

function hexToHsl(string $hex): array
{
    $rgb = hexToRgb($hex);
    $r = $rgb['r'] / 255;
    $g = $rgb['g'] / 255;
    $b = $rgb['b'] / 255;

    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $l = ($max + $min) / 2;

    if ($max === $min) {
        return ['h' => 0, 's' => 0, 'l' => (int) round($l * 100)];
    }

    $d = $max - $min;
    $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

    if ($max === $r) {
        $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
    } elseif ($max === $g) {
        $h = ($b - $r) / $d + 2;
    } else {
        $h = ($r - $g) / $d + 4;
    }

    $h /= 6;

    return [
        'h' => (int) round($h * 360),
        's' => (int) round($s * 100),
        'l' => (int) round($l * 100),
    ];
}

function relativeLuminance(string $hex): float
{
    $rgb = hexToRgb($hex);
    $channels = [$rgb['r'], $rgb['g'], $rgb['b']];
    $linear = array_map(static function (int $v): float {
        $x = $v / 255;
        return $x <= 0.03928 ? ($x / 12.92) : pow(($x + 0.055) / 1.055, 2.4);
    }, $channels);

    return ($linear[0] * 0.2126) + ($linear[1] * 0.7152) + ($linear[2] * 0.0722);
}

function getPerceivedLuminance(string $hex): float
{
    $rgb = hexToRgb($hex);
    return (0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b']) / 255;
}

function bestContrastText(string $hex): string
{
    return getPerceivedLuminance($hex) > 0.62 ? '#111111' : '#FFFFFF';
}

function colorDistanceSq(string $hexA, string $hexB): int
{
    $a = hexToRgb($hexA);
    $b = hexToRgb($hexB);
    $dr = $a['r'] - $b['r'];
    $dg = $a['g'] - $b['g'];
    $db = $a['b'] - $b['b'];
    return ($dr * $dr) + ($dg * $dg) + ($db * $db);
}

function mixColor(array $base, array $mix, float $weight): string
{
    $r = (int) round($base['r'] * $weight + $mix['r'] * (1 - $weight));
    $g = (int) round($base['g'] * $weight + $mix['g'] * (1 - $weight));
    $b = (int) round($base['b'] * $weight + $mix['b'] * (1 - $weight));
    return sprintf('%02x%02x%02x', $r, $g, $b);
}

$hexParam = isset($_GET['hex']) ? (string) $_GET['hex'] : '';
$slugParam = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$normalizedHex = normalizeHex($hexParam);
$isValidHex = (bool) preg_match('/^[A-F0-9]{6}$/', $normalizedHex);
$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$isProduction = $host === 'colormagic.techkreative.com';

if (!$isValidHex) {
    $normalizedHex = '000000';
}

$hexWithHash = '#' . $normalizedHex;

$colorNames = [];
$paletteData = [];

$colorNamesPath = __DIR__ . '/../../data/color-names.json';
if (file_exists($colorNamesPath)) {
    $decoded = json_decode((string) file_get_contents($colorNamesPath), true);
    if (is_array($decoded)) {
        $colorNames = $decoded;
    }
}

// If accessed via slug (?slug=midnight-blue), look up the hex from the slug
if ($slugParam !== '' && $hexParam === '') {
    $slugMap = [];
    foreach ($colorNames as $key => $entry) {
        $cleanHex = is_array($entry) ? ($entry['hex'] ?? $key) : $key;
        $cleanSlug = is_array($entry) ? ($entry['slug'] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string)($entry['name'] ?? '')))) : strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string)$entry));
        $slugMap[$cleanSlug] = $cleanHex;
    }
    if (isset($slugMap[$slugParam])) {
        $normalizedHex = strtoupper(ltrim($slugMap[$slugParam], '#'));
        $isValidHex = true;
        $hexWithHash = '#' . $normalizedHex;
    } else {
        header('HTTP/1.1 404 Not Found');
        exit;
    }
}

$hexNameEntry = $colorNames[strtoupper($normalizedHex)] ?? null;
$hexName = is_array($hexNameEntry) ? ($hexNameEntry['name'] ?? ('Hex ' . $hexWithHash)) : ('Hex ' . $hexWithHash);
$colorSlug = is_array($hexNameEntry) ? ($hexNameEntry['slug'] ?? null) : null;
$hasNamedTitle = $hexName !== '' && !preg_match('/^Hex\s*#[0-9a-f]{6}$/i', $hexName);

$palettePath = __DIR__ . '/../../data/palettes.json';
if (file_exists($palettePath)) {
    $decoded = json_decode((string) file_get_contents($palettePath), true);
    if (is_array($decoded)) {
        $paletteData = $decoded;
    }
}

// If accessed via hex URL and this color has a named slug, 301 redirect to the slug URL
if ($slugParam === '' && $colorSlug !== null) {
    $scheme = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    ) ? 'https' : 'http';
    $httpHost = (string) ($_SERVER['HTTP_HOST'] ?? 'colormagic.techkreative.com');
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/color.php');
    /** @var string $base */
    $basePath = $base;
    $slugUrl = $scheme . '://' . $httpHost . $basePath . '/color/' . $colorSlug . '/';
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $slugUrl);
    exit;
}
$rgb = hexToRgb($normalizedHex);
$hsl = hexToHsl($normalizedHex);
$contrast = bestContrastText($normalizedHex);

$shades = [];
for ($i = 1; $i <= 10; $i++) {
    $shades[] = mixColor($rgb, ['r' => 0, 'g' => 0, 'b' => 0], $i / 10);
}

$tints = [];
for ($i = 10; $i >= 1; $i--) {
    $tints[] = mixColor($rgb, ['r' => 255, 'g' => 255, 'b' => 255], $i / 10);
}

$tones = [];
for ($i = 1; $i <= 10; $i++) {
    $tones[] = mixColor($rgb, ['r' => 128, 'g' => 128, 'b' => 128], $i / 10);
}

$darkestShade = '#' . $shades[0];
$lightestTint = '#' . $tints[9];
$leastSaturatedTone = '#' . $tones[0];

$exactPalettes = [];
$nearPalettes = [];

foreach ($paletteData as $palette) {
    if (!is_array($palette) || !isset($palette['colors']) || !is_array($palette['colors'])) {
        continue;
    }

    $paletteHexes = [];
    foreach ($palette['colors'] as $paletteHex) {
        if (!is_string($paletteHex)) {
            continue;
        }
        $n = normalizeHex($paletteHex);
        if ((bool) preg_match('/^[A-F0-9]{6}$/', $n)) {
            $paletteHexes[] = $n;
        }
    }

    if ($paletteHexes === []) {
        continue;
    }

    $palette['colors'] = array_map(
        static function (string $h): string {
            return '#' . $h;
        },
        $paletteHexes
    );

    if (in_array($normalizedHex, $paletteHexes, true)) {
        $exactPalettes[] = $palette;
        continue;
    }

    $closest = PHP_INT_MAX;
    foreach ($paletteHexes as $pHex) {
        $distance = colorDistanceSq($normalizedHex, $pHex);
        if ($distance < $closest) {
            $closest = $distance;
        }
    }

    $palette['_distance'] = $closest;
    $nearPalettes[] = $palette;
}

usort(
    $nearPalettes,
    static function (array $a, array $b): int {
        return ($a['_distance'] ?? PHP_INT_MAX) <=> ($b['_distance'] ?? PHP_INT_MAX);
    }
);

$relatedPalettes = $exactPalettes;
foreach ($nearPalettes as $nearPalette) {
    if (count($relatedPalettes) >= 6) {
        break;
    }
    $relatedPalettes[] = $nearPalette;
}

$styles = [];
foreach ($relatedPalettes as $palette) {
    if (!empty($palette['style']) && is_string($palette['style'])) {
        $styles[] = $palette['style'];
    }
}
$styles = array_values(array_unique($styles));
$styleSnippet = $styles !== [] ? implode(', ', array_slice($styles, 0, 3)) : 'curated';

$hasColorName = isset($colorNames[strtoupper($normalizedHex)]) && is_array($colorNames[strtoupper($normalizedHex)]);
$metaTitle = $hasColorName
    ? $hexName . ' (' . $hexWithHash . ') Hex Code & Palettes | Color Magic'
    : $hexName . ' Hex Code & Palettes | Color Magic';
$metaDescription = 'Explore ' . $hexName . ' (' . $hexWithHash . ') with RGB(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ') and HSL(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%). Discover ' . count($relatedPalettes) . ' related ' . $styleSnippet . ' color palettes from our colors library.';

$baseUrl = 'https://colormagic.techkreative.com';
$canonicalUrl = $colorSlug !== null
    ? $baseUrl . '/color/' . $colorSlug . '/'
    : $baseUrl . '/color/' . strtolower($normalizedHex) . '/';
$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/color.php');
/** @var string $base */
$basePath = $base;

$assetBase = $basePath;
$homePageUrl = $baseUrl . '/';
$openSourceUrl = $assetBase . '/open-source';
$manifestUrl = $assetBase . '/manifest.json';
$faviconUrl = $assetBase . '/assets/images/logo.png';
$logoUrl = $assetBase . '/assets/images/logo.png';
$siteStylesUrl = $assetBase . '/assets/css/style.css?v=1.0';
$tailwindConfigUrl = $assetBase . '/assets/js/tailwind-config.js';
$colorRouteBase = $assetBase . '/color/';
$posterSlug = $colorSlug !== null ? $colorSlug : strtolower($normalizedHex);
$posterPath = '/posters/colors/' . $posterSlug . '.jpg';
$posterUrl = $basePath . $posterPath;
$absolutePosterUrl = $baseUrl . $posterPath;
$dynamicColorImageUrl = $absolutePosterUrl;
$ogImageUrl = $baseUrl . '/assets/og-preview.png';

// WCAG Contrast Ratios
function contrastRatio(float $l1, float $l2): float
{
    $lighter = max($l1, $l2);
    $darker = min($l1, $l2);
    return ($lighter + 0.05) / ($darker + 0.05);
}

$colorLuminance = relativeLuminance($normalizedHex);
$whiteLuminance = 1.0;
$blackLuminance = 0.0;
$contrastWithWhite = round(contrastRatio($colorLuminance, $whiteLuminance), 2);
$contrastWithBlack = round(contrastRatio($colorLuminance, $blackLuminance), 2);

$wcagAANormal = 4.5;
$wcagAALarge = 3.0;
$wcagAAA = 7.0;

$whitePassesAA = $contrastWithWhite >= $wcagAANormal;
$whitePassesAALarge = $contrastWithWhite >= $wcagAALarge;
$whitePassesAAA = $contrastWithWhite >= $wcagAAA;
$blackPassesAA = $contrastWithBlack >= $wcagAANormal;
$blackPassesAALarge = $contrastWithBlack >= $wcagAALarge;
$blackPassesAAA = $contrastWithBlack >= $wcagAAA;

// Color Harmonies (based on HSL hue rotation)
$hue = $hsl['h'];
$complementaryHue = ($hue + 180) % 360;
$analogous1 = ($hue + 30) % 360;
$analogous2 = ($hue + 330) % 360;
$triadic1 = ($hue + 120) % 360;
$triadic2 = ($hue + 240) % 360;
$split1 = ($hue + 150) % 360;
$split2 = ($hue + 210) % 360;
$tetradic1 = ($hue + 60) % 360;
$tetradic2 = ($hue + 180) % 360;
$tetradic3 = ($hue + 240) % 360;

function hslToHex(int $h, int $s, int $l): string
{
    $s /= 100;
    $l /= 100;
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - $c / 2;
    if ($h < 60) {
        $r = $c;
        $g = $x;
        $b = 0;
    } elseif ($h < 120) {
        $r = $x;
        $g = $c;
        $b = 0;
    } elseif ($h < 180) {
        $r = 0;
        $g = $c;
        $b = $x;
    } elseif ($h < 240) {
        $r = 0;
        $g = $x;
        $b = $c;
    } elseif ($h < 300) {
        $r = $x;
        $g = 0;
        $b = $c;
    } else {
        $r = $c;
        $g = 0;
        $b = $x;
    }
    return sprintf('%02X%02X%02X', (int) round(($r + $m) * 255), (int) round(($g + $m) * 255), (int) round(($b + $m) * 255));
}

$complementaryHex = '#' . hslToHex($complementaryHue, $hsl['s'], $hsl['l']);
$analogous1Hex = '#' . hslToHex($analogous1, $hsl['s'], $hsl['l']);
$analogous2Hex = '#' . hslToHex($analogous2, $hsl['s'], $hsl['l']);
$triadic1Hex = '#' . hslToHex($triadic1, $hsl['s'], $hsl['l']);
$triadic2Hex = '#' . hslToHex($triadic2, $hsl['s'], $hsl['l']);
$split1Hex = '#' . hslToHex($split1, $hsl['s'], $hsl['l']);
$split2Hex = '#' . hslToHex($split2, $hsl['s'], $hsl['l']);
$tetradic1Hex = '#' . hslToHex($tetradic1, $hsl['s'], $hsl['l']);
$tetradic2Hex = '#' . hslToHex($tetradic2, $hsl['s'], $hsl['l']);
$tetradic3Hex = '#' . hslToHex($tetradic3, $hsl['s'], $hsl['l']);

// CMYK Calculations
$rNorm = $rgb['r'] / 255;
$gNorm = $rgb['g'] / 255;
$bNorm = $rgb['b'] / 255;
$kVal = 1 - max($rNorm, $gNorm, $bNorm);
$cVal = $kVal < 1 ? (1 - $rNorm - $kVal) / (1 - $kVal) : 0;
$mVal = $kVal < 1 ? (1 - $gNorm - $kVal) / (1 - $kVal) : 0;
$yVal = $kVal < 1 ? (1 - $bNorm - $kVal) / (1 - $kVal) : 0;
$cmyk = [
    'c' => (int) round($cVal * 100),
    'm' => (int) round($mVal * 100),
    'y' => (int) round($yVal * 100),
    'k' => (int) round($kVal * 100),
];

// HSV / HSB
$maxCh = max($rNorm, $gNorm, $bNorm);
$minCh = min($rNorm, $gNorm, $bNorm);
$deltaCh = $maxCh - $minCh;
$hsvS = $maxCh > 0 ? ($deltaCh / $maxCh) : 0;
$hsv = [
    'h' => $hsl['h'],
    's' => (int) round($hsvS * 100),
    'v' => (int) round($maxCh * 100),
];

// Decimal
$colorDecimal = hexdec($normalizedHex);

// Color Psychology and Meaning Engine
function getColorPsychology(int $h, int $s, int $l, string $name, string $hex): array
{
    if ($s < 12) {
        return [
            'family' => 'Achromatic / Neutral',
            'meaning' => "$name ($hex) is a sophisticated, understated neutral tone. Neutrals communicate balance, minimalism, and timeless elegance. They provide a calm backdrop that allows bold accent elements to shine without overwhelming the viewer.",
            'use' => 'Ideal for clean backgrounds, editorial layouts, modern SaaS interfaces, and body typography that requires effortless readability.',
            'pairing' => 'Pairs effortlessly with vibrant jewel tones (like emerald green or sapphire blue) or rich monochromatic textures.'
        ];
    }
    if ($h >= 345 || $h < 15) {
        return [
            'family' => 'Red / Crimson',
            'meaning' => "$name ($hex) belongs to the vibrant red color spectrum, representing passion, dynamic energy, courage, and bold action. In color psychology, red naturally stimulates appetite, raises heart rate, and creates an immediate sense of urgency.",
            'use' => 'Exceptional for high-conversion primary Call-to-Action (CTA) buttons, discount badges, alert notifications, and high-impact branding.',
            'pairing' => 'Pairs beautifully with crisp whites, deep slate charcoal, and warm complementary greens for maximum contrast.'
        ];
    }
    if ($h >= 15 && $h < 45) {
        return [
            'family' => 'Orange / Amber',
            'meaning' => "$name ($hex) is an energetic, welcoming warm color blending red's passion with yellow's cheerful brightness. It radiates enthusiasm, social connection, adventure, and playful confidence.",
            'use' => 'Widely used in creative agencies, food & beverage branding, interactive onboarding flows, and energetic marketing highlights.',
            'pairing' => 'Pairs stunningly with deep ocean navy, midnight blues, and warm earth tones like terracotta.'
        ];
    }
    if ($h >= 45 && $h < 70) {
        return [
            'family' => 'Yellow / Gold',
            'meaning' => "$name ($hex) is a luminous, high-energy hue embodying optimism, intellect, warmth, and joyful vitality. It captures attention faster than any other color on the visible spectrum.",
            'use' => 'Perfect for attention-grabbing hero accents, star ratings, positive notification banners, and warm creative interfaces.',
            'pairing' => 'Complements deep purple, charcoal gray, and rich royal blue for high-end graphic balance.'
        ];
    }
    if ($h >= 70 && $h < 165) {
        return [
            'family' => 'Green / Emerald',
            'meaning' => "$name ($hex) resides in the restorative green family, universally symbolizing nature, growth, financial prosperity, health, and emotional balance.",
            'use' => 'The go-to choice for finance apps, eco-friendly brands, success confirmations, health platforms, and organic product packaging.',
            'pairing' => 'Pairs harmoniously with crisp earth tones, ivory whites, and complementary magenta or berry accents.'
        ];
    }
    if ($h >= 165 && $h < 260) {
        return [
            'family' => 'Blue / Azure',
            'meaning' => "$name ($hex) is part of the dependable blue family, evoking deep trust, calm authority, security, and intellectual focus. It is statistically the world's most favored corporate color.",
            'use' => 'Ideal for fintech, healthcare, SaaS dashboards, navigation headers, and trustworthy enterprise digital products.',
            'pairing' => 'Pairs effortlessly with warm coral, crisp white, gold accents, and subtle neutral grays.'
        ];
    }
    if ($h >= 260 && $h < 315) {
        return [
            'family' => 'Purple / Violet',
            'meaning' => "$name ($hex) embodies luxury, creative imagination, spiritual wisdom, and modern digital innovation. Combining the stability of blue with the energy of red, it conveys prestige and uniqueness.",
            'use' => 'Popular in AI tech brands, luxury beauty packaging, gaming interfaces, and cutting-edge creative portfolios.',
            'pairing' => 'Looks magnificent with soft neon greens, gold metallic accents, and deep obsidian dark mode surfaces.'
        ];
    }
    return [
        'family' => 'Pink / Magenta',
        'meaning' => "$name ($hex) is a spirited, modern pink expressing empathy, youthfulness, creative flair, and playful sophistication.",
        'use' => 'Excellent for lifestyle applications, modern e-commerce brands, vibrant mobile apps, and trendy gradient highlights.',
        'pairing' => 'Pairs wonderfully with rich teal, clean mint greens, deep purples, and soft pastel backgrounds.'
    ];
}

$psychology = getColorPsychology($hsl['h'], $hsl['s'], $hsl['l'], $hexName, $hexWithHash);

$faqItems = [
    [
        'q' => "What is the color name for $hexWithHash?",
        'a' => "The color $hexWithHash is recognized as " . ($hasColorName ? $hexName : "a unique hexadecimal color") . ". It has an RGB representation of rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']}) and HSL coordinates of {$hsl['h']}° hue, {$hsl['s']}% saturation, and {$hsl['l']}% lightness."
    ],
    [
        'q' => "What are the exact RGB, CMYK, and HSV values of $hexWithHash?",
        'a' => "For web and digital design, $hexWithHash has RGB values of ({$rgb['r']}, {$rgb['g']}, {$rgb['b']}) and HSV of ({$hsv['h']}°, {$hsv['s']}%, {$hsv['v']}%). For commercial print design, its four-color process CMYK values are Cyan: {$cmyk['c']}%, Magenta: {$cmyk['m']}%, Yellow: {$cmyk['y']}%, and Key/Black: {$cmyk['k']}%."
    ],
    [
        'q' => "What is the complementary color of $hexName ($hexWithHash)?",
        'a' => "The exact optical complementary color of $hexWithHash is $complementaryHex. This sits directly opposite on the standard 360-degree color wheel, providing the highest visual contrast when paired together."
    ],
    [
        'q' => "Is $hexWithHash accessible under WCAG 2.1 contrast guidelines?",
        'a' => "On a pure white background (#FFFFFF), $hexWithHash has a contrast ratio of {$contrastWithWhite}:1 (" . ($whitePassesAA ? 'Passes AA' : 'Fails AA') . "). On a black background (#000000), it achieves a {$contrastWithBlack}:1 contrast ratio (" . ($blackPassesAA ? 'Passes AA' : 'Fails AA') . "). The most legible text overlay for this color is " . ($contrast === '#FFFFFF' ? 'White text (#FFFFFF)' : 'Dark text (#111111)') . "."
    ]
];

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebPage',
            'name' => 'Hex ' . $hexWithHash . ' Color Palette, Conversions & Harmonies',
            'url' => $canonicalUrl,
            'description' => $metaDescription,
            'primaryImageOfPage' => $absolutePosterUrl,
            'image' => $absolutePosterUrl,
            'mainEntity' => [
                '@type' => 'DefinedTerm',
                'name' => $hexName,
                'identifier' => $hexWithHash,
                'description' => 'RGB(' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ') | HSL(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%)',
            ],
        ],
        [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(static function (array $faq): array {
                return [
                    '@type' => 'Question',
                    'name' => $faq['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['a']
                    ]
                ];
            }, $faqItems)
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo e($metaTitle); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>" />
    <meta name="keywords"
        content="color palette generator, hex code details, rgb to hsl, designer tools, TechKreative" />
    <meta name="author" content="Color Magic" />
    <meta name="robots" content="<?php echo $isValidHex ? 'index, follow' : 'noindex, follow'; ?>" />

    <?php if ($isProduction): ?>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-537L4MR968"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', 'G-537L4MR968');
        </script>
    <?php endif; ?>

    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo e($metaTitle); ?>" />
    <meta property="og:description" content="<?php echo e($metaDescription); ?>" />
    <meta property="og:url" content="<?php echo e($canonicalUrl); ?>" />
    <meta property="og:image" content="<?php echo e($absolutePosterUrl); ?>" />
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" content="<?php echo e($metaTitle); ?>" />
    <meta property="twitter:description" content="<?php echo e($metaDescription); ?>" />
    <meta property="twitter:image" content="<?php echo e($absolutePosterUrl); ?>" />

    <link rel="manifest" href="<?php echo e($manifestUrl); ?>" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="icon" type="image/png" href="<?php echo e($faviconUrl); ?>" />
    <link rel="apple-touch-icon" href="<?php echo e($faviconUrl); ?>" />

    <!-- Resource Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />

    <!-- Preload Fonts & Non-Blocking CSS -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" /></noscript>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" /></noscript>
    <link rel="stylesheet" href="<?php echo e($siteStylesUrl); ?>" />
    <script>
        (function(){
            var w = console.warn;
            console.warn = function() {
                if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].indexOf('cdn.tailwindcss.com') !== -1) return;
                return w.apply(console, arguments);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config" src="<?php echo e($tailwindConfigUrl); ?>"></script>

    <script
        type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

    <style>
        /* ── Critical Layout Stability (Zero CLS) ── */
        header {
            min-height: 64px;
        }
        .copy-feedback {
            transition: all 0.25s ease;
        }

        .copy-feedback.show {
            opacity: 1;
            transform: translateY(0);
        }

        .sb-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .sb-btn .sb-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .sb-btn:hover .sb-icon {
            transform: scale(1.12);
        }

        .sb-btn.sb-inactive {
            color: #475569;
            background: transparent;
        }

        .sb-btn.sb-inactive:hover {
            background: #fdf2f8;
            color: #ec4899;
        }

        .sb-btn.sb-inactive .sb-icon {
            background: #fdf2f8;
            color: #ec4899;
        }

        .sb-btn.sb-active {
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: #fff;
            box-shadow: 0 8px 24px -6px rgba(236, 72, 153, 0.45);
        }

        .sb-btn.sb-active .sb-icon {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .sb-btn.sb-active:hover {
            box-shadow: 0 12px 28px -6px rgba(124, 58, 237, 0.45);
            transform: translateY(-1px);
        }

        .dark .sb-btn.sb-inactive {
            color: #94a3b8;
        }

        .dark .sb-btn.sb-inactive:hover {
            background: #1e293b;
            color: #ec4899;
        }

        .dark .sb-btn.sb-inactive .sb-icon {
            background: #1e293b;
            color: #ec4899;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out;
        }

        /* ── Swatch hover styles (shared with Explore Palettes) ── */
        .swatch {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .swatch-hex {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .swatch:hover .swatch-hex {
            opacity: 1;
        }

        @keyframes swatchBtnIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .swatch-icon-btn {
            opacity: 0;
            transform: translateY(-6px);
        }

        .swatch:hover .swatch-icon-btn {
            animation: swatchBtnIn 0.18s ease forwards;
        }

        .swatch:hover .swatch-btn-1 {
            animation-delay: 0s;
        }

        .swatch:hover .swatch-btn-2 {
            animation-delay: 0.06s;
        }

        .swatch:hover .swatch-btn-3 {
            animation-delay: 0.12s;
        }

        .swatch-hex.copied-state {
            opacity: 1 !important;
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            font-weight: 700;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white min-h-screen">
    <?php include '../../components/navbar.php'; ?>
    <!-- ══ MOBILE OVERLAY ════════════════════════════════════════════════════════ -->
    <div id="mobileMenuOverlay"
        class="fixed inset-0 z-[60] bg-white/98 dark:bg-background-dark/98 backdrop-blur-lg hidden flex-col p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <a href="<?php echo e($homePageUrl); ?>" class="flex items-center gap-2 text-primary">
                <img src="<?php echo e($logoUrl); ?>" alt="Color Magic Logo" class="h-8 w-8 object-contain" />
                <span class="text-xl font-bold tracking-tight"><span
                        class="text-slate-900 dark:text-white">Color</span><span
                        class="bg-gradient-to-r from-secondary to-primary bg-clip-text text-transparent">Magic</span></span>
            </a>
            <button id="closeMobileMenuBtn"
                class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                aria-label="Close menu">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        <div class="space-y-2">
            <a href="<?php echo e($homePageUrl); ?>" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-house-door"></i></span>
                <div><span class="block font-bold">Home</span><span class="text-xs opacity-60">Go to homepage</span>
                </div>
            </a>
            <a href="<?php echo e($assetBase); ?>/palettes" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-palette"></i></span>
                <div><span class="block font-bold">Explore Palettes</span><span class="text-xs opacity-60">Browse
                        collections</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/gradients" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-rainbow"></i></span>
                <div><span class="block font-bold">Gradients</span><span class="text-xs opacity-60">Browse CSS
                        gradients</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/find-color" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-eyedropper"></i></span>
                <div><span class="block font-bold">Find Color</span><span class="text-xs opacity-60">Hex to name &amp;
                        info</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/generate-palette" class="sb-btn sb-inactive w-full"><span
                    class="sb-icon"><i class="bi bi-stars"></i></span>
                <div><span class="block font-bold">Generate Palette</span><span class="text-xs opacity-60">Create color
                        schemes</span></div>
            </a>
            <a href="<?php echo e($assetBase); ?>/profile" rel="nofollow" class=" sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-heart-fill"></i></span>
                <div><span class="block font-bold">Favorites</span><span class="text-xs opacity-60">Your saved colors,
                        palettes &amp; gradients</span></div>
            </a>
            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
            <a href="<?php echo e($openSourceUrl); ?>" class="sb-btn sb-inactive w-full"><span class="sb-icon"><i
                        class="bi bi-github"></i></span>
                <div><span class="block font-bold">Open Source</span><span class="text-xs opacity-60">View on
                        GitHub</span></div>
            </a>
        </div>
    </div>

    <main class="w-full max-w-7xl mx-auto pt-24 px-6 py-8 md:py-10">
        <!-- Top Navigation: Breadcrumb -->
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a class="hover:text-primary transition-colors" href="<?php echo e($homePageUrl); ?>">Home</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <a class="hover:text-primary transition-colors" href="<?php echo e($assetBase); ?>/find-color">Colors</a>
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span class="text-slate-900 dark:text-white font-medium"><?php echo e($hexName); ?> (<?php echo e($hexWithHash); ?>)</span>
            </nav>
        </div>

        <section
            class="grid grid-cols-1 lg:grid-cols-2 lg:auto-rows-fr gap-6 md:gap-8 bg-gradient-to-br from-pink-50 via-white to-purple-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-5 md:p-7 shadow-sm border border-pink-100 dark:border-slate-800">
            <!-- Large color preview -->
            <div class="h-full">
                <div class="relative h-72 md:h-80 lg:h-full min-h-[280px] md:min-h-[340px] p-6 flex items-end overflow-hidden rounded-2xl shadow-xl shadow-black/10 dark:shadow-black/30"
                    style="background-color: <?php echo e($hexWithHash); ?>;">
                    <div class="relative z-10 select-none p-2 sm:p-3" style="color: <?php echo e($contrast); ?>;">
                        <p class="text-xs sm:text-sm font-bold uppercase tracking-[0.22em] opacity-85 mb-2">COLOR MAGIC</p>
                        <?php if ($hasNamedTitle): ?>
                            <p class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight"><?php echo e($hexName); ?></p>
                            <p class="text-xl sm:text-2xl font-bold opacity-90 mt-1 font-mono tracking-tight"><?php echo e($hexWithHash); ?></p>
                        <?php else: ?>
                            <p class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-none"><?php echo e($hexWithHash); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Info panel: Consistent Hierarchy -->
            <div class="flex flex-col gap-5 justify-between">
                <!-- 1. Heading, 2. Subheading, 3. Label / Description text -->
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight"><?php echo e($hexName); ?> Color Details</h1>
                    <h2 class="text-lg md:text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1"><?php echo e($hexWithHash); ?> · RGB(<?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?>)</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                        Complete color profile for <?php echo e($hexWithHash); ?> with copy-ready values, harmonies, and related palettes.
                    </p>
                </div>

                <!-- 2. Details: Metric Cards with 1-Click Copy -->
                <div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5 md:gap-3">
                        <button type="button"
                            class="copy-btn metric-card group relative text-left bg-slate-50 hover:bg-white dark:bg-slate-800/60 dark:hover:bg-slate-800 rounded-xl px-2.5 py-3 border border-slate-200 hover:border-primary/50 dark:border-slate-700/60 dark:hover:border-primary/50 transition-all shadow-sm hover:shadow min-w-0 flex flex-col justify-between"
                            data-copy="<?php echo e($hexWithHash); ?>"
                            data-label="Copied HEX!"
                            title="Click to copy HEX">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 group-hover:text-primary transition-colors">HEX</span>
                                <i class="copy-icon bi bi-clipboard text-slate-400 group-hover:text-primary text-xs transition-colors"></i>
                            </div>
                            <p class="font-mono font-bold text-xs sm:text-[11px] md:text-xs lg:text-[13px] xl:text-sm mt-1.5 whitespace-nowrap tracking-tight"><?php echo e($hexWithHash); ?></p>
                        </button>

                        <button type="button"
                            class="copy-btn metric-card group relative text-left bg-slate-50 hover:bg-white dark:bg-slate-800/60 dark:hover:bg-slate-800 rounded-xl px-2.5 py-3 border border-slate-200 hover:border-primary/50 dark:border-slate-700/60 dark:hover:border-primary/50 transition-all shadow-sm hover:shadow min-w-0 flex flex-col justify-between"
                            data-copy="rgb(<?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?>)"
                            data-label="Copied RGB!"
                            title="Click to copy RGB">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 group-hover:text-primary transition-colors">RGB</span>
                                <i class="copy-icon bi bi-clipboard text-slate-400 group-hover:text-primary text-xs transition-colors"></i>
                            </div>
                            <p class="font-mono font-bold text-xs sm:text-[11px] md:text-xs lg:text-[13px] xl:text-sm mt-1.5 whitespace-nowrap tracking-tight"><?php echo e((string) $rgb['r']); ?>, <?php echo e((string) $rgb['g']); ?>, <?php echo e((string) $rgb['b']); ?></p>
                        </button>

                        <button type="button"
                            class="copy-btn metric-card group relative text-left bg-slate-50 hover:bg-white dark:bg-slate-800/60 dark:hover:bg-slate-800 rounded-xl px-2.5 py-3 border border-slate-200 hover:border-primary/50 dark:border-slate-700/60 dark:hover:border-primary/50 transition-all shadow-sm hover:shadow min-w-0 flex flex-col justify-between"
                            data-copy="hsl(<?php echo e((string) $hsl['h']); ?>, <?php echo e((string) $hsl['s']); ?>%, <?php echo e((string) $hsl['l']); ?>%)"
                            data-label="Copied HSL!"
                            title="Click to copy HSL">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 group-hover:text-primary transition-colors">HSL</span>
                                <i class="copy-icon bi bi-clipboard text-slate-400 group-hover:text-primary text-xs transition-colors"></i>
                            </div>
                            <p class="font-mono font-bold text-xs sm:text-[11px] md:text-xs lg:text-[13px] xl:text-sm mt-1.5 whitespace-nowrap tracking-tight"><?php echo e((string) $hsl['h']); ?>°, <?php echo e((string) $hsl['s']); ?>%, <?php echo e((string) $hsl['l']); ?>%</p>
                        </button>

                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl px-2.5 py-3 border border-slate-200 dark:border-slate-700/60 min-w-0 flex flex-col justify-between">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Contrast</span>
                                <span class="w-2.5 h-2.5 rounded-full border border-slate-300 dark:border-slate-600 shadow-sm" style="background-color: <?php echo $contrast; ?>;"></span>
                            </div>
                            <p class="font-bold text-xs sm:text-[11px] md:text-xs lg:text-[13px] xl:text-sm mt-1.5 whitespace-nowrap tracking-tight"><?php echo $contrast === '#FFFFFF' ? 'White text' : 'Dark text'; ?></p>
                        </div>
                    </div>
                    <div class="h-4 mt-1.5">
                        <p id="copyFeedback"
                            class="copy-feedback opacity-0 -translate-y-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 transition-all">
                            Copied to clipboard</p>
                    </div>
                </div>

                <!-- 3. Primary Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button id="downloadColorPngBtn"
                        class="w-full px-5 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/25 hover:shadow-primary/40 active:scale-[0.99]"
                        title="Download color image"
                        aria-label="Download <?= e($hexName) ?> swatch image">
                        <i class="bi bi-download text-base" aria-hidden="true"></i>
                        <span>Download Image</span>
                    </button>
                    <button id="favColorHeroBtn"
                        class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 active:scale-[0.99]"
                        title="Favorite this color"
                        aria-label="Favorite <?= e($hexName) ?>">
                        <i class="bi bi-heart text-base" aria-hidden="true"></i>
                        <span>Favorite</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- SECTION: Official Color Swatch Poster Showcase (Google Images & Pinterest Indexing) -->
        <section class="bg-gradient-to-br from-purple-50/60 via-white to-pink-50/60 dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-purple-100/80 dark:border-slate-800 flex flex-col md:flex-row items-center gap-8 my-8" aria-labelledby="color-poster-title">
            <div class="w-full md:w-5/12 max-w-xs shrink-0 mx-auto">
                <a href="<?= $posterUrl ?>" target="_blank" rel="noopener noreferrer" class="block group relative rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-700 hover:scale-[1.02] transition-transform duration-300">
                    <img src="<?= $posterUrl ?>" 
                         alt="<?= e($hexName) ?> (<?= e($hexWithHash) ?>) Pantone Style Swatch Card Poster" 
                         width="1600" height="2000" 
                         loading="lazy" decoding="async" fetchpriority="low"
                         class="w-full h-auto object-cover" />
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <span class="px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-900 text-xs font-bold shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                            <i class="bi bi-arrows-fullscreen"></i> View High-Res
                        </span>
                    </div>
                </a>
            </div>
            <div class="flex-1 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800">
                    <i class="bi bi-image"></i> Official Swatch Card Graphic
                </div>
                <h2 id="color-poster-title" class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                    <?= e($hexName) ?> Swatch Poster
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Download or share the official <strong><?= e($hexName) ?></strong> (<?= e($hexWithHash) ?>) Pantone-style swatch card graphic with technical color specifications (RGB, HSL, CMYK) and typographic hierarchy.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="<?= $posterUrl ?>" download="<?= e($posterSlug) ?>-color-swatch.jpg" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-xs font-semibold shadow-md transition-all">
                        <i class="bi bi-download"></i> Download Poster (JPEG)
                    </a>
                    <button onclick="window.ColorMagic && window.ColorMagic.exportColorImage ? window.ColorMagic.exportColorImage({hex: '<?= e($hexWithHash) ?>', name: '<?= addslashes($hexName) ?>', rgb: <?= json_encode($rgb) ?>, hsl: <?= json_encode($hsl) ?>}) : window.open('<?= $posterUrl ?>')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-all">
                        <i class="bi bi-file-earmark-image"></i> Export PNG (Retina)
                    </button>
                    <?php if (!empty($hexNameEntry['pinterest_pin_id'])): ?>
                        <a href="https://www.pinterest.com/pin/<?= e($hexNameEntry['pinterest_pin_id']) ?>/" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 text-xs font-semibold border border-red-200 dark:border-red-900/60 transition-all" title="View pin on Pinterest">
                            <i class="bi bi-pinterest text-red-500"></i> View on Pinterest
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="mt-12" aria-labelledby="shades-tints-title">
            <h2 id="shades-tints-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Shades and Tints of
                <?php echo e($hexWithHash); ?></h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A shade is achieved by adding black to any pure hue, while a tint is created by mixing white to any pure
                hue.
                <span
                    class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($darkestShade); ?></span>
                is the darkest color, while
                <span
                    class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($lightestTint); ?></span>
                is the lightest one.
            </p>

            <div class="mb-8">
                <div class="flex h-24 md:h-32 rounded-2xl overflow-hidden shadow-sm">
                    <?php foreach ($shades as $shadeHex): ?>
                        <a href="<?php echo e($colorRouteBase . $shadeHex . '/'); ?>"
                            class="flex-1 transition-opacity hover:opacity-90 relative group"
                            style="background-color: #<?php echo e($shadeHex); ?>;"
                            aria-label="View shade <?php echo e('#' . $shadeHex); ?>">
                            <span
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 text-white text-xs font-mono font-bold backdrop-blur-sm transition-opacity">#<?php echo e($shadeHex); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="text-center text-sm text-slate-500 mt-2 font-medium">Shade Color Variation</p>
            </div>

            <div class="mb-10 lg:mb-12">
                <div class="flex h-24 md:h-32 rounded-2xl overflow-hidden shadow-sm">
                    <?php foreach ($tints as $tintHex): ?>
                        <a href="<?php echo e($colorRouteBase . $tintHex . '/'); ?>"
                            class="flex-1 transition-opacity hover:opacity-90 relative group"
                            style="background-color: #<?php echo e($tintHex); ?>;"
                            aria-label="View tint <?php echo e('#' . $tintHex); ?>">
                            <span
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 text-white text-xs font-mono font-bold backdrop-blur-sm transition-opacity">#<?php echo e($tintHex); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="text-center text-sm text-slate-500 mt-2 font-medium">Tint Color Variation</p>
            </div>
        </section>

        <section class="mt-8 md:mt-12 mb-12 lg:mb-16" aria-labelledby="tones-title">
            <h2 id="tones-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-4">Tones of
                <?php echo e($hexWithHash); ?></h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A tone is produced by adding gray to any pure hue. In this case,
                <span
                    class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($leastSaturatedTone); ?></span>
                is the less saturated color, while
                <span
                    class="inline-flex items-center mx-1 px-2 py-0.5 rounded text-sm bg-slate-100 dark:bg-slate-800 font-mono"><?php echo e($hexWithHash); ?></span>
                is the most saturated one.
            </p>

            <div>
                <div class="flex h-24 md:h-32 rounded-2xl overflow-hidden shadow-sm">
                    <?php foreach ($tones as $toneHex): ?>
                        <a href="<?php echo e($colorRouteBase . $toneHex . '/'); ?>"
                            class="flex-1 transition-opacity hover:opacity-90 relative group"
                            style="background-color: #<?php echo e($toneHex); ?>;"
                            aria-label="View tone <?php echo e('#' . $toneHex); ?>">
                            <span
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/40 text-white text-xs font-mono font-bold backdrop-blur-sm transition-opacity">#<?php echo e($toneHex); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <p class="text-center text-sm text-slate-500 mt-2 font-medium">Tone Color Variation</p>
            </div>
        </section>

        <!-- Color Harmonies Suite -->
        <section class="mt-12" aria-labelledby="harmonies-title">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <h2 id="harmonies-title" class="text-2xl md:text-3xl font-bold tracking-tight">Color Harmonies for <?php echo e($hexWithHash); ?></h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Balanced color combinations based on geometric relationships across the 360° color wheel.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <!-- Complementary -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Complementary</span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">180° Shift</span>
                    </div>
                    <div class="flex h-16 rounded-xl overflow-hidden mb-3 border border-slate-200/50 dark:border-slate-700/50">
                        <a href="<?php echo e($colorRouteBase . ltrim($hexWithHash, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[11px] font-mono font-bold text-white shadow-inner" style="background-color: <?php echo e($hexWithHash); ?>; color: <?php echo e($contrast); ?>;"><?php echo e($hexWithHash); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($complementaryHex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[11px] font-mono font-bold text-white shadow-inner hover:opacity-90 transition" style="background-color: <?php echo e($complementaryHex); ?>; color: <?php echo e(bestContrastText($complementaryHex)); ?>;"><?php echo e($complementaryHex); ?></a>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Direct opposite on the color wheel. Delivers the strongest visual contrast and dynamic energy.</p>
                </div>

                <!-- Analogous -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Analogous</span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">±30° Adjacent</span>
                    </div>
                    <div class="flex h-16 rounded-xl overflow-hidden mb-3 border border-slate-200/50 dark:border-slate-700/50">
                        <a href="<?php echo e($colorRouteBase . ltrim($analogous1Hex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold hover:opacity-90 transition" style="background-color: <?php echo e($analogous1Hex); ?>; color: <?php echo e(bestContrastText($analogous1Hex)); ?>;"><?php echo e($analogous1Hex); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($hexWithHash, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold" style="background-color: <?php echo e($hexWithHash); ?>; color: <?php echo e($contrast); ?>;"><?php echo e($hexWithHash); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($analogous2Hex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold hover:opacity-90 transition" style="background-color: <?php echo e($analogous2Hex); ?>; color: <?php echo e(bestContrastText($analogous2Hex)); ?>;"><?php echo e($analogous2Hex); ?></a>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Colors directly neighboring on the wheel. Creates serene, comfortable, cohesive compositions.</p>
                </div>

                <!-- Split-Complementary -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Split-Complementary</span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">150° / 210°</span>
                    </div>
                    <div class="flex h-16 rounded-xl overflow-hidden mb-3 border border-slate-200/50 dark:border-slate-700/50">
                        <a href="<?php echo e($colorRouteBase . ltrim($hexWithHash, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold" style="background-color: <?php echo e($hexWithHash); ?>; color: <?php echo e($contrast); ?>;"><?php echo e($hexWithHash); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($split1Hex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold hover:opacity-90 transition" style="background-color: <?php echo e($split1Hex); ?>; color: <?php echo e(bestContrastText($split1Hex)); ?>;"><?php echo e($split1Hex); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($split2Hex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold hover:opacity-90 transition" style="background-color: <?php echo e($split2Hex); ?>; color: <?php echo e(bestContrastText($split2Hex)); ?>;"><?php echo e($split2Hex); ?></a>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Variation of complementary with high visual nuance and less tension than pure opposite pairs.</p>
                </div>

                <!-- Triadic -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Triadic</span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">120° Triangle</span>
                    </div>
                    <div class="flex h-16 rounded-xl overflow-hidden mb-3 border border-slate-200/50 dark:border-slate-700/50">
                        <a href="<?php echo e($colorRouteBase . ltrim($hexWithHash, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold" style="background-color: <?php echo e($hexWithHash); ?>; color: <?php echo e($contrast); ?>;"><?php echo e($hexWithHash); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($triadic1Hex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold hover:opacity-90 transition" style="background-color: <?php echo e($triadic1Hex); ?>; color: <?php echo e(bestContrastText($triadic1Hex)); ?>;"><?php echo e($triadic1Hex); ?></a>
                        <a href="<?php echo e($colorRouteBase . ltrim($triadic2Hex, '#') . '/'); ?>" class="flex-1 flex items-center justify-center text-[10px] font-mono font-bold hover:opacity-90 transition" style="background-color: <?php echo e($triadic2Hex); ?>; color: <?php echo e(bestContrastText($triadic2Hex)); ?>;"><?php echo e($triadic2Hex); ?></a>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Equidistant 3-point color triad. Vibrant and balanced even when using pale or unsaturated versions.</p>
                </div>
            </div>
        </section>

        <!-- Complete Color Conversions Table -->
        <section class="mt-12" aria-labelledby="conversions-title">
            <h2 id="conversions-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Color Conversions & Codes for <?php echo e($hexWithHash); ?></h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Standard representation values for web design, digital graphics, CSS styling, and CMYK commercial printing.</p>

            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-xs uppercase font-bold text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="px-5 py-3.5">Color Space / Format</th>
                                <th class="px-5 py-3.5">Syntax / Value</th>
                                <th class="px-5 py-3.5">Details</th>
                                <th class="px-5 py-3.5 text-right">Copy</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-mono">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold font-sans text-slate-900 dark:text-white">HEX</td>
                                <td class="px-5 py-3.5 font-bold text-primary"><?php echo e($hexWithHash); ?></td>
                                <td class="px-5 py-3.5 font-sans text-xs text-slate-500 dark:text-slate-400">6-character hexadecimal code</td>
                                <td class="px-5 py-3.5 text-right"><button type="button" class="copy-btn px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-sans font-medium" data-copy="<?php echo e($hexWithHash); ?>">Copy</button></td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold font-sans text-slate-900 dark:text-white">RGB</td>
                                <td class="px-5 py-3.5">rgb(<?php echo (string)$rgb['r']; ?>, <?php echo (string)$rgb['g']; ?>, <?php echo (string)$rgb['b']; ?>)</td>
                                <td class="px-5 py-3.5 font-sans text-xs text-slate-500 dark:text-slate-400">Red, Green, Blue (0-255)</td>
                                <td class="px-5 py-3.5 text-right"><button type="button" class="copy-btn px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-sans font-medium" data-copy="rgb(<?php echo (string)$rgb['r']; ?>, <?php echo (string)$rgb['g']; ?>, <?php echo (string)$rgb['b']; ?>)">Copy</button></td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold font-sans text-slate-900 dark:text-white">HSL</td>
                                <td class="px-5 py-3.5">hsl(<?php echo (string)$hsl['h']; ?>°, <?php echo (string)$hsl['s']; ?>%, <?php echo (string)$hsl['l']; ?>%)</td>
                                <td class="px-5 py-3.5 font-sans text-xs text-slate-500 dark:text-slate-400">Hue, Saturation, Lightness</td>
                                <td class="px-5 py-3.5 text-right"><button type="button" class="copy-btn px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-sans font-medium" data-copy="hsl(<?php echo (string)$hsl['h']; ?>, <?php echo (string)$hsl['s']; ?>%, <?php echo (string)$hsl['l']; ?>%)">Copy</button></td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold font-sans text-slate-900 dark:text-white">HSV / HSB</td>
                                <td class="px-5 py-3.5">hsv(<?php echo (string)$hsv['h']; ?>°, <?php echo (string)$hsv['s']; ?>%, <?php echo (string)$hsv['v']; ?>%)</td>
                                <td class="px-5 py-3.5 font-sans text-xs text-slate-500 dark:text-slate-400">Hue, Saturation, Value/Brightness</td>
                                <td class="px-5 py-3.5 text-right"><button type="button" class="copy-btn px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-sans font-medium" data-copy="hsv(<?php echo (string)$hsv['h']; ?>, <?php echo (string)$hsv['s']; ?>%, <?php echo (string)$hsv['v']; ?>%)">Copy</button></td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold font-sans text-slate-900 dark:text-white">CMYK (Print)</td>
                                <td class="px-5 py-3.5">cmyk(<?php echo (string)$cmyk['c']; ?>%, <?php echo (string)$cmyk['m']; ?>%, <?php echo (string)$cmyk['y']; ?>%, <?php echo (string)$cmyk['k']; ?>%)</td>
                                <td class="px-5 py-3.5 font-sans text-xs text-slate-500 dark:text-slate-400">Cyan, Magenta, Yellow, Black process</td>
                                <td class="px-5 py-3.5 text-right"><button type="button" class="copy-btn px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-sans font-medium" data-copy="cmyk(<?php echo (string)$cmyk['c']; ?>%, <?php echo (string)$cmyk['m']; ?>%, <?php echo (string)$cmyk['y']; ?>%, <?php echo (string)$cmyk['k']; ?>%)">Copy</button></td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-5 py-3.5 font-bold font-sans text-slate-900 dark:text-white">Decimal</td>
                                <td class="px-5 py-3.5"><?php echo (string)$colorDecimal; ?></td>
                                <td class="px-5 py-3.5 font-sans text-xs text-slate-500 dark:text-slate-400">Integer decimal value</td>
                                <td class="px-5 py-3.5 text-right"><button type="button" class="copy-btn px-2.5 py-1 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-sans font-medium" data-copy="<?php echo (string)$colorDecimal; ?>">Copy</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Color Psychology & Design Use Guide -->
        <section class="mt-12" aria-labelledby="psychology-title">
            <h2 id="psychology-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Color Psychology & Design Guide</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">How <?php echo e($hexName); ?> (<?php echo e($hexWithHash); ?>) communicates emotion and functions in digital user interfaces.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-secondary flex items-center justify-center text-lg mb-4">
                        <i class="bi bi-lightbulb-fill"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Meaning & Emotion</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo e($psychology['meaning']); ?></p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-950/40 text-primary flex items-center justify-center text-lg mb-4">
                        <i class="bi bi-window-fullscreen"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">UI & Web Applications</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo e($psychology['use']); ?></p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center text-lg mb-4">
                        <i class="bi bi-palette-fill"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Pairing Suggestions</h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo e($psychology['pairing']); ?></p>
                </div>
            </div>
        </section>

        <!-- WCAG 2.1 Accessibility & Readability Analysis -->
        <section class="mt-12" aria-labelledby="accessibility-title">
            <h2 id="accessibility-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-2">WCAG 2.1 Contrast & Accessibility</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Readability verification according to Web Content Accessibility Guidelines (WCAG) 2.1 contrast compliance standards.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- White text on Color -->
                <div class="rounded-2xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-800 flex flex-col justify-between" style="background-color: <?php echo e($hexWithHash); ?>; color: #FFFFFF;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs uppercase font-bold tracking-wider opacity-90">White on Color</span>
                            <span class="text-xs font-mono font-extrabold px-2 py-0.5 rounded bg-black/20"><?php echo e($contrastWithWhite); ?>:1</span>
                        </div>
                        <p class="text-sm font-semibold leading-relaxed">Sample headline text for readability.</p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-white/20 flex items-center justify-between text-xs font-bold">
                        <span>AA Normal: <?php echo $whitePassesAA ? 'PASS' : 'FAIL'; ?></span>
                        <span>AAA: <?php echo $whitePassesAAA ? 'PASS' : 'FAIL'; ?></span>
                    </div>
                </div>

                <!-- Dark text on Color -->
                <div class="rounded-2xl p-5 shadow-sm border border-slate-200/60 dark:border-slate-800 flex flex-col justify-between" style="background-color: <?php echo e($hexWithHash); ?>; color: #0F172A;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs uppercase font-bold tracking-wider opacity-90">Dark on Color</span>
                            <span class="text-xs font-mono font-extrabold px-2 py-0.5 rounded bg-white/30"><?php echo e($contrastWithBlack); ?>:1</span>
                        </div>
                        <p class="text-sm font-semibold leading-relaxed">Sample headline text for readability.</p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-black/15 flex items-center justify-between text-xs font-bold">
                        <span>AA Normal: <?php echo $blackPassesAA ? 'PASS' : 'FAIL'; ?></span>
                        <span>AAA: <?php echo $blackPassesAAA ? 'PASS' : 'FAIL'; ?></span>
                    </div>
                </div>

                <!-- Color on White Background -->
                <div class="rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Color on White</span>
                            <span class="text-xs font-mono font-extrabold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300"><?php echo e($contrastWithWhite); ?>:1</span>
                        </div>
                        <p class="text-sm font-semibold leading-relaxed" style="color: <?php echo e($hexWithHash); ?>;">Sample colored text on white canvas.</p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-slate-600 dark:text-slate-400">
                        <span>AA Large: <?php echo $whitePassesAALarge ? 'PASS' : 'FAIL'; ?></span>
                        <span>AA: <?php echo $whitePassesAA ? 'PASS' : 'FAIL'; ?></span>
                    </div>
                </div>

                <!-- Color on Dark Background -->
                <div class="rounded-2xl p-5 shadow-sm border border-slate-800 bg-[#0F172A] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Color on Slate</span>
                            <span class="text-xs font-mono font-extrabold px-2 py-0.5 rounded bg-slate-800 text-slate-300"><?php echo e($contrastWithBlack); ?>:1</span>
                        </div>
                        <p class="text-sm font-semibold leading-relaxed" style="color: <?php echo e($hexWithHash); ?>;">Sample colored text on dark canvas.</p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-slate-400">
                        <span>AA Large: <?php echo $blackPassesAALarge ? 'PASS' : 'FAIL'; ?></span>
                        <span>AA: <?php echo $blackPassesAA ? 'PASS' : 'FAIL'; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CSS & Developer Snippets -->
        <section class="mt-12" aria-labelledby="developer-code-title">
            <h2 id="developer-code-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-2">CSS & Developer Implementation</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Ready-to-use style declarations and utility classes for your web projects.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 font-mono text-xs">
                <!-- CSS -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 font-sans">
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">CSS Styles</span>
                        <button type="button" class="copy-btn px-2 py-0.5 rounded text-[11px] bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-medium" data-copy="background-color: <?php echo e($hexWithHash); ?>; color: <?php echo e($contrast); ?>;">Copy</button>
                    </div>
                    <pre class="bg-slate-50 dark:bg-slate-950 p-3 rounded-xl overflow-x-auto text-slate-700 dark:text-slate-300"><code>/* Background &amp; Text */
background-color: <?php echo e($hexWithHash); ?>;
color: <?php echo e($contrast); ?>;
border-color: <?php echo e($hexWithHash); ?>;</code></pre>
                </div>

                <!-- Tailwind -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 font-sans">
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">Tailwind CSS</span>
                        <button type="button" class="copy-btn px-2 py-0.5 rounded text-[11px] bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-medium" data-copy="bg-[<?php echo e($hexWithHash); ?>] text-[<?php echo e($contrast); ?>] border-[<?php echo e($hexWithHash); ?>]">Copy</button>
                    </div>
                    <pre class="bg-slate-50 dark:bg-slate-950 p-3 rounded-xl overflow-x-auto text-slate-700 dark:text-slate-300"><code>&lt;!-- Tailwind Classes --&gt;
bg-[<?php echo e($hexWithHash); ?>]
text-[<?php echo e($contrast); ?>]
border-[<?php echo e($hexWithHash); ?>]</code></pre>
                </div>

                <!-- SVG -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 font-sans">
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">SVG Vector</span>
                        <button type="button" class="copy-btn px-2 py-0.5 rounded text-[11px] bg-slate-100 dark:bg-slate-800 hover:bg-primary hover:text-white transition font-medium" data-copy='&lt;rect width="100" height="100" fill="<?php echo e($hexWithHash); ?>" /&gt;'>Copy</button>
                    </div>
                    <pre class="bg-slate-50 dark:bg-slate-950 p-3 rounded-xl overflow-x-auto text-slate-700 dark:text-slate-300"><code>&lt;!-- SVG Fill --&gt;
&lt;rect fill="<?php echo e($hexWithHash); ?>" /&gt;
&lt;path stroke="<?php echo e($hexWithHash); ?>" /&gt;</code></pre>
                </div>
            </div>
        </section>

        <!-- Frequently Asked Questions (FAQ) -->
        <section class="mt-12" aria-labelledby="faq-title">
            <h2 id="faq-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Frequently Asked Questions</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Quick answers and technical specs for <?php echo e($hexName); ?> (<?php echo e($hexWithHash); ?>).</p>

            <div class="space-y-3">
                <?php foreach ($faqItems as $faq): ?>
                    <details class="group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm transition [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between cursor-pointer font-bold text-base text-slate-900 dark:text-white">
                            <span><?php echo e($faq['q']); ?></span>
                            <span class="ml-4 flex-shrink-0 text-slate-400 group-open:rotate-180 transition-transform">
                                <i class="bi bi-chevron-down"></i>
                            </span>
                        </summary>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed pr-6 border-t border-slate-100 dark:border-slate-800 pt-3">
                            <?php echo e($faq['a']); ?>
                        </p>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="mt-8" aria-labelledby="related-palettes-title">
            <h2 id="related-palettes-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-5">
                Related Color Palettes
            </h2>

            <?php if ($relatedPalettes === []): ?>
                <div
                    class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900">
                    No related palettes found in the current library.
                </div>
            <?php else: ?>

                <!-- Rendered by JS using the shared createPaletteCard() component -->
                <div id="relatedPalettesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8"></div>

                <script>
                    window._relatedPalettes = <?php echo json_encode(array_values($relatedPalettes), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                </script>

            <?php endif; ?>

        </section>

        <!-- Related Gradients -->
        <section class="mt-8 md:mt-12" aria-labelledby="related-gradients-title">
            <h2 id="related-gradients-title" class="text-2xl md:text-3xl font-bold tracking-tight mb-2">
                Related Gradients
            </h2>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                CSS gradients that contain or closely match <?php echo e($hexWithHash); ?>.
            </p>
            <!-- Skeleton Loader (visible while fetching API) -->
            <div id="relatedGradientsSkeleton" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 animate-pulse">
                <div class="h-64 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 flex flex-col gap-3">
                    <div class="h-44 rounded-xl bg-slate-100 dark:bg-slate-800 w-full"></div>
                    <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-1/2"></div>
                </div>
                <div class="h-64 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 flex flex-col gap-3">
                    <div class="h-44 rounded-xl bg-slate-100 dark:bg-slate-800 w-full"></div>
                    <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-1/2"></div>
                </div>
                <div class="h-64 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 flex flex-col gap-3">
                    <div class="h-44 rounded-xl bg-slate-100 dark:bg-slate-800 w-full"></div>
                    <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-1/2"></div>
                </div>
            </div>

            <div id="relatedGradientsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5"></div>
            <div id="relatedGradientsEmpty"
                class="hidden rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900">
                No gradients found matching this color.
            </div>
        </section>

        <!-- Related Tools -->
        <section class="mt-8 md:mt-12 mb-8" aria-labelledby="related-tools-title">
            <h2 id="related-tools-title" class="text-xl md:text-2xl font-bold tracking-tight mb-4">Related Color Tools
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="<?php echo e($assetBase); ?>/hex-to-rgb"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <i class="bi bi-sliders text-secondary text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Hex to RGB Converter</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Convert this color to RGB instantly</p>
                    </div>
                </a>
                <a href="<?php echo e($assetBase); ?>/find-color"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <i class="bi bi-eyedropper text-emerald-500 text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Find Color</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Look up any hex code details</p>
                    </div>
                </a>
                <a href="<?php echo e($assetBase); ?>/generate-palette"
                    class="flex items-center gap-3 p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:shadow-md transition">
                    <i class="bi bi-stars text-pink-500 text-lg"></i>
                    <div>
                        <p class="font-bold text-sm">Generate Palette</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Create schemes from this color</p>
                    </div>
                </a>
            </div>
        </section>



    </main>
    <?php include "../../components/footer.php"; ?>

    <!-- Shared palette component (same as explore page) -->
    <script>
        // Base paths must be set before component scripts read them
        window.CM_COLOR_BASE    = '<?php echo e($colorRouteBase); ?>';
        window.CM_PALETTE_BASE  = '<?php echo e($assetBase); ?>/palette/';
        window.CM_GRADIENT_BASE = '<?php echo e($assetBase); ?>/gradient/';
    </script>
    <script src="<?php echo e($assetBase); ?>/assets/js/utils.js?v=3.1"></script>
    <script src="<?php echo e($assetBase); ?>/assets/js/services/favorites.js?v=3.1"></script>
    <script src="<?php echo e($assetBase); ?>/assets/js/image-export.js?v=3.1"></script>
    <script src="<?php echo e($assetBase); ?>/assets/js/components/palette-card.js?v=<?= cm_asset_v('assets/js/components/palette-card.js') ?>"></script>
    <script src="<?php echo e($assetBase); ?>/assets/js/components/gradient-card.js?v=3.1"></script>

    <script>
        // ── Copy HEX / RGB / HSL buttons & metric cards ──────────────────────
        document.querySelectorAll('.copy-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var text = button.getAttribute('data-copy') || '';
                var label = button.getAttribute('data-label') || (text.indexOf('rgb') === 0 ? 'Copied RGB!' : (text.indexOf('hsl') === 0 ? 'Copied HSL!' : 'Copied HEX!'));

                if (button.classList.contains('metric-card')) {
                    var icon = button.querySelector('.copy-icon');
                    var origIcon = icon ? icon.className : '';
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text);
                    } else {
                        var ta = document.createElement('textarea');
                        ta.value = text;
                        ta.style.position = 'fixed';
                        ta.style.left = '-9999px';
                        document.body.appendChild(ta);
                        ta.focus();
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    }
                    if (icon) {
                        icon.className = 'copy-icon bi bi-check2 text-emerald-500 text-xs font-bold transition-all';
                    }
                    button.classList.add('ring-2', 'ring-emerald-500/40', 'border-emerald-500/40');
                    var feedback = document.getElementById('copyFeedback');
                    if (feedback) {
                        feedback.textContent = label;
                        feedback.classList.remove('opacity-0', '-translate-y-1');
                        setTimeout(function () {
                            feedback.classList.add('opacity-0', '-translate-y-1');
                        }, 1800);
                    }
                    setTimeout(function () {
                        if (icon) icon.className = origIcon;
                        button.classList.remove('ring-2', 'ring-emerald-500/40', 'border-emerald-500/40');
                    }, 1600);
                    return;
                }

                if (window.ColorMagic && window.ColorMagic.animateCopy) {
                    window.ColorMagic.animateCopy(button, text, label);
                } else {
                    navigator.clipboard.writeText(text);
                }
            });
        });

        // ── Favorite color hero button ────────────────────────────────────
        (function () {
            var favBtn = document.getElementById('favColorHeroBtn');
            if (!favBtn || !window.ColorMagic || !window.ColorMagic.ColorFavorites) return;
            var hex = '<?php echo e($hexWithHash); ?>';
            function updateBtn() {
                var isFav = window.ColorMagic.ColorFavorites.isFavorite(hex);
                var icon = favBtn.querySelector('i');
                var span = favBtn.querySelector('span');
                if (icon) icon.className = 'bi ' + (isFav ? 'bi-heart-fill text-red-500' : 'bi-heart');
                if (span) span.textContent = isFav ? 'Favorited' : 'Favorite';
                favBtn.classList.toggle('text-red-500', isFav);
            }
            updateBtn();
            favBtn.addEventListener('click', function () {
                window.ColorMagic.ColorFavorites.toggleFavorite(hex);
                var isFav = window.ColorMagic.ColorFavorites.isFavorite(hex);
                if (window.ColorMagic.animateFavorite) {
                    window.ColorMagic.animateFavorite(favBtn, isFav);
                } else {
                    updateBtn();
                }
            });
        })();

        // ── Download Pantone Card button ──────────────────────────────────
        (function () {
            var downloadBtn = document.getElementById('downloadColorPngBtn');
            if (!downloadBtn || !window.ColorMagic || !window.ColorMagic.exportColorImage) return;

            var hex = '<?php echo e($hexWithHash); ?>';
            var name = '<?php echo e($hexName); ?>';
            var rgb = { r: <?php echo $rgb['r']; ?>, g: <?php echo $rgb['g']; ?>, b: <?php echo $rgb['b']; ?> };
            var hsl = { h: <?php echo $hsl['h']; ?>, s: <?php echo $hsl['s']; ?>, l: <?php echo $hsl['l']; ?> };

            downloadBtn.addEventListener('click', function () {
                if (window.ColorMagic.animateDownload) {
                    window.ColorMagic.animateDownload(downloadBtn, function () {
                        window.ColorMagic.exportColorImage({ hex: hex, name: name, rgb: rgb, hsl: hsl });
                    });
                } else {
                    window.ColorMagic.exportColorImage({ hex: hex, name: name, rgb: rgb, hsl: hsl });
                }
            });
        })();

        // ── Render related palettes using the shared createPaletteCard() ──────
        (function renderRelatedPalettes() {
            var grid = document.getElementById('relatedPalettesGrid');
            var data = window._relatedPalettes;
            if (!grid || !data || !window.ColorMagic || !window.ColorMagic.createPaletteCard) return;

            window.ColorMagic.markDuplicateSlugs(data);

            var fragment = document.createDocumentFragment();
            data.forEach(function (palette) {
                fragment.appendChild(window.ColorMagic.createPaletteCard(palette));
            });
            grid.appendChild(fragment);
        })();

        // ── Delegated handlers for related palette cards ──────────────────────
        document.addEventListener('click', function (e) {

            // Copy entire palette
            var copyBtn = e.target.closest('.copy-palette-btn');
            if (copyBtn) {
                var colors = copyBtn.getAttribute('data-colors') || '';
                var icon = copyBtn.querySelector('i');
                var origClass = icon ? icon.className : '';
                navigator.clipboard.writeText(colors).then(function () {
                    if (icon) icon.className = 'bi bi-check-circle-fill text-xl';
                    copyBtn.classList.add('text-green-500');
                    setTimeout(function () {
                        if (icon) icon.className = origClass;
                        copyBtn.classList.remove('text-green-500');
                    }, 2000);
                }).catch(function () {
                    if (icon) { icon.className = 'bi bi-x-circle text-xl'; setTimeout(function () { icon.className = origClass; }, 2000); }
                });
                return;
            }

            // Copy single swatch HEX
            var hexBtn = e.target.closest('.swatch-copy-hex');
            if (hexBtn) {
                var hexVal = hexBtn.getAttribute('data-hex') || '';
                var hIcon = hexBtn.querySelector('i');
                var origHTML = hexBtn.innerHTML;
                navigator.clipboard.writeText(hexVal).then(function () {
                    if (hIcon) hIcon.className = 'bi bi-check-circle-fill';
                    hexBtn.classList.add('text-green-600');
                    setTimeout(function () { hexBtn.innerHTML = origHTML; hexBtn.classList.remove('text-green-600'); }, 1500);
                }).catch(function () {
                    hexBtn.innerHTML = '<i class="bi bi-x-circle"></i>';
                    setTimeout(function () { hexBtn.innerHTML = origHTML; }, 1500);
                });
                return;
            }

            // Favorite single color
            var favColorBtn = e.target.closest('.swatch-fav-color');
            if (favColorBtn && window.ColorMagic && window.ColorMagic.ColorFavorites) {
                var favHex = favColorBtn.getAttribute('data-hex') || '';
                var added = window.ColorMagic.ColorFavorites.toggleFavorite(favHex);
                var fIcon = favColorBtn.querySelector('i');
                if (fIcon) fIcon.className = added ? 'bi bi-heart-fill text-red-500' : 'bi bi-heart';
                favColorBtn.title = added ? 'Remove from favorites' : 'Add to favorites';
                return;
            }

            // Favorite palette toggle
            var favPaletteBtn = e.target.closest('.favorite-btn');
            if (favPaletteBtn && window.ColorMagic && window.ColorMagic.Favorites) {
                var paletteId = favPaletteBtn.getAttribute('data-palette-id');
                window.ColorMagic.Favorites.toggleFavorite(paletteId);
                window.ColorMagic.Favorites.updateFavoriteButton(favPaletteBtn, paletteId);
                return;
            }
        });
    </script>
    <script>
        // ── Related Gradients ──────────────────────────────────────────────
        (function () {
            var currentHex = '<?php echo strtolower($normalizedHex); ?>';
            var grid = document.getElementById('relatedGradientsGrid');
            var emptyEl = document.getElementById('relatedGradientsEmpty');
            var gradientBase = '<?php echo e($assetBase); ?>/gradient/';

            if (!grid) return;

            var gradPromise = (window.ColorMagic && window.ColorMagic.api)
                ? window.ColorMagic.api.getGradients({ limit: 1000 })
                : fetch('/api/gradients.json').then(function(r){ return r.json(); }).then(function(d){ return { data: d }; });

            gradPromise
                .then(function (res) {
                    var data = (res && res.data) ? res.data : (Array.isArray(res) ? res : []);
                    var exact = [];
                    var near = [];

                    data.forEach(function (g) {
                        var hasExact = false;
                        var minDist = Infinity;

                        g.colors.forEach(function (c) {
                            var gHex = c.replace('#', '').toLowerCase();
                            if (gHex === currentHex) {
                                hasExact = true;
                            } else {
                                var d = colorDist(currentHex, gHex);
                                if (d < minDist) minDist = d;
                            }
                        });

                        if (hasExact) {
                            exact.push(g);
                        } else if (minDist < 15000) {
                            g._dist = minDist;
                            near.push(g);
                        }
                    });

                    near.sort(function (a, b) { return a._dist - b._dist; });
                    var results = exact.concat(near).slice(0, 6);

                    var skeleton = document.getElementById('relatedGradientsSkeleton');
                    if (skeleton) skeleton.classList.add('hidden');

                    if (results.length === 0) {
                        grid.classList.add('hidden');
                        if (emptyEl) emptyEl.classList.remove('hidden');
                        return;
                    }

                    var isFavFn = window.ColorMagic && window.ColorMagic.GradientFavorites && window.ColorMagic.GradientFavorites.isFavorite;

                    results.forEach(function (g) {
                        if (window.ColorMagic && window.ColorMagic.createGradientCard) {
                            grid.appendChild(window.ColorMagic.createGradientCard(g));
                        }
                    });
                })
                .catch(function () {
                    var skeleton = document.getElementById('relatedGradientsSkeleton');
                    if (skeleton) skeleton.classList.add('hidden');
                    grid.classList.add('hidden');
                    if (emptyEl) emptyEl.classList.remove('hidden');
                });

            function colorDist(a, b) {
                var ar = parseInt(a.substring(0, 2), 16), ag = parseInt(a.substring(2, 4), 16), ab = parseInt(a.substring(4, 6), 16);
                var br = parseInt(b.substring(0, 2), 16), bg = parseInt(b.substring(2, 4), 16), bb = parseInt(b.substring(4, 6), 16);
                var dr = ar - br, dg = ag - bg, db = ab - bb;
                return dr * dr + dg * dg + db * db;
            }
        })();
    </script>
    <script>
        // Mobile menu toggle
        (function () {
            const openBtn = document.getElementById('mobileMenuBtn');
            const closeBtn = document.getElementById('closeMobileMenuBtn');
            const overlay = document.getElementById('mobileMenuOverlay');
            if (openBtn && closeBtn && overlay) {
                openBtn.addEventListener('click', function () { overlay.style.display = 'flex'; });
                closeBtn.addEventListener('click', function () { overlay.style.display = 'none'; });
            }
        })();
    </script>
</body>

</html>