<?php
/**
 * services/PinterestService.php
 * Official Pinterest API v5 client for Color Magic.
 * Handles Pin and Board creation, rate-limiting, and synchronization with palettes/gradients JSON.
 */

class PinterestService {
    private static ?string $token = null;
    private static ?string $appId = null;
    private static ?string $apiBase = null;

    /**
     * Initialize credentials from environment
     */
    private static function init(): void {
        if (self::$token !== null) {
            return;
        }

        // 1. Try env variables
        self::$appId = getenv('PINTEREST_APP_ID') ?: ($_ENV['PINTEREST_APP_ID'] ?? '1622531');
        
        $token = getenv('PINTEREST_ACCESS_TOKEN') ?: ($_ENV['PINTEREST_ACCESS_TOKEN'] ?? '');
        if (empty($token)) {
            $token = getenv('PINTEREST_SANDBOX_TOKEN') ?: ($_ENV['PINTEREST_SANDBOX_TOKEN'] ?? '');
        }

        // Fallback to reading .env.colormagic if not in environment
        if (empty($token)) {
            $envPath = __DIR__ . '/../.env.colormagic';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (str_starts_with($line, '#')) continue;
                    if (str_starts_with($line, 'PINTEREST_SANDBOX_TOKEN=')) {
                        $token = trim(substr($line, strlen('PINTEREST_SANDBOX_TOKEN=')));
                    }
                    if (str_starts_with($line, 'PINTEREST_ACCESS_TOKEN=')) {
                        $token = trim(substr($line, strlen('PINTEREST_ACCESS_TOKEN=')));
                    }
                    if (str_starts_with($line, 'PINTEREST_APP_ID=')) {
                        self::$appId = trim(substr($line, strlen('PINTEREST_APP_ID=')));
                    }
                }
            }
        }

        if (empty($token)) {
            $token = '';
        }

        self::$token = $token;

        // Determine if sandbox or production endpoint
        if (str_starts_with($token, 'pina_')) {
            self::$apiBase = 'https://api-sandbox.pinterest.com/v5';
        } else {
            self::$apiBase = 'https://api.pinterest.com/v5';
        }
    }

    /**
     * Perform HTTP request to Pinterest API v5
     */
    public static function request(string $endpoint, string $method = 'GET', ?array $payload = null): array {
        self::init();

        $url = rtrim(self::$apiBase, '/') . '/' . ltrim($endpoint, '/');
        $headers = [
            'Authorization: Bearer ' . self::$token,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        } elseif ($method === 'PATCH' || $method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'status'  => $httpCode,
                'error'   => 'cURL Error: ' . $curlError
            ];
        }

        $data = json_decode($response, true) ?: [];

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'status'  => $httpCode,
                'data'    => $data
            ];
        }

        $errorMsg = $data['message'] ?? ($data['error'] ?? "Pinterest API returned HTTP $httpCode");
        return [
            'success' => false,
            'status'  => $httpCode,
            'error'   => $errorMsg,
            'raw'     => $data
        ];
    }

    /**
     * Resolve Board ID and Section ID for a category ('Color Palettes', 'Color Gradients', 'Colors')
     */
    public static function resolveTarget(string $category): array {
        $cacheFile = __DIR__ . '/../data/pinterest_boards.json';
        $cache = file_exists($cacheFile) ? (json_decode(file_get_contents($cacheFile), true) ?: []) : [];

        $mainBoardId = $cache['main_board_id'] ?? '1092193415823453337';
        $sectionId = $cache['sections'][$category] ?? null;

        // If section is known, return main board + section
        if (!empty($sectionId)) {
            return [
                'board_id'         => $mainBoardId,
                'board_section_id' => $sectionId
            ];
        }

        // Fallback to dedicated board if cached
        if (!empty($cache['boards'][$category])) {
            return [
                'board_id'         => $cache['boards'][$category],
                'board_section_id' => null
            ];
        }

        // Otherwise resolve or create
        $boardId = self::getOrCreateBoard($category);
        return [
            'board_id'         => $boardId,
            'board_section_id' => null
        ];
    }

    /**
     * Get or create a Pinterest Board for Color Magic
     */
    public static function getOrCreateBoard(string $boardName = 'Color Magic Palettes'): string {
        $cacheFile = __DIR__ . '/../data/pinterest_boards.json';
        $cache = file_exists($cacheFile) ? (json_decode(file_get_contents($cacheFile), true) ?: []) : [];

        if (!empty($cache['boards'][$boardName])) {
            return $cache['boards'][$boardName];
        }

        // Fetch existing boards
        $res = self::request('boards');
        if ($res['success'] && !empty($res['data']['items'])) {
            foreach ($res['data']['items'] as $b) {
                if (strtolower($b['name']) === strtolower($boardName)) {
                    $cache['boards'][$boardName] = $b['id'];
                    file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT));
                    return $b['id'];
                }
            }
        }

        // Create new board if not found
        $createRes = self::request('boards', 'POST', [
            'name'        => $boardName,
            'description' => 'Curated color palettes, schemes, gradients, and harmonies by Color Magic.',
            'privacy'     => 'PUBLIC'
        ]);

        if ($createRes['success'] && !empty($createRes['data']['id'])) {
            $boardId = $createRes['data']['id'];
            $cache['boards'][$boardName] = $boardId;
            file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT));
            return $boardId;
        }

        return '1092193415823453337';
    }

    /**
     * Post a Palette to Pinterest and update data/palettes.json
     *
     * @param string $paletteId (e.g. 'palette_1')
     * @return array Result
     */
    public static function postPalette(string $paletteId): array {
        $palettesFile = __DIR__ . '/../data/palettes.json';
        if (!file_exists($palettesFile)) {
            return ['success' => false, 'error' => 'palettes.json not found'];
        }

        $palettes = json_decode(file_get_contents($palettesFile), true);
        if (!is_array($palettes)) {
            return ['success' => false, 'error' => 'Failed to parse palettes.json'];
        }

        // Find palette
        $targetIndex = null;
        $palette = null;
        foreach ($palettes as $idx => $p) {
            if (($p['id'] ?? '') === $paletteId) {
                $targetIndex = $idx;
                $palette = $p;
                break;
            }
        }

        if ($palette === null) {
            return ['success' => false, 'error' => "Palette '$paletteId' not found"];
        }

        $name = $palette['name'] ?? 'Curated Color Scheme';
        $style = $palette['style'] ?? 'Design';
        $colors = $palette['colors'] ?? [];
        $colorList = implode(', ', $colors);
        $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($name)), '-');

        $title = "$name Color Palette — Color Magic";
        $description = "$name: A curated $style color palette with hex codes $colorList. Generate palettes, check contrast, and explore hex conversions at Color Magic.";
        $link = "https://colormagic.techkreative.com/palette/$slug";

        require_once __DIR__ . '/PinterestImageGenerator.php';
        $base64Image = PinterestImageGenerator::generatePaletteImage($palette);

        $target = self::resolveTarget('Color Palettes');
        $boardId = $target['board_id'];
        $sectionId = $target['board_section_id'];

        $pinPayload = [
            'title'        => $title,
            'description'  => $description,
            'link'         => $link,
            'board_id'     => $boardId,
            'media_source' => [
                'source_type'  => 'image_base64',
                'content_type' => 'image/jpeg',
                'data'         => $base64Image
            ]
        ];

        if (!empty($sectionId)) {
            $pinPayload['board_section_id'] = $sectionId;
        }

        $res = self::request('pins', 'POST', $pinPayload);

        if ($res['success']) {
            $pinId = $res['data']['id'] ?? '';
            // Update local JSON with uploaded = true
            $palettes[$targetIndex]['uploaded'] = true;
            $palettes[$targetIndex]['pinterest_pin_id'] = $pinId;
            $palettes[$targetIndex]['pinterest_posted_at'] = date('c');
            $palettes[$targetIndex]['pinterest_board_id'] = $boardId;

            file_put_contents($palettesFile, json_encode($palettes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return [
                'success'   => true,
                'pin_id'    => $pinId,
                'board_id'  => $boardId,
                'link'      => $link,
                'title'     => $title,
                'uploaded'  => true
            ];
        }

        return [
            'success' => false,
            'error'   => $res['error'] ?? 'Failed to create pin on Pinterest',
            'details' => $res['raw'] ?? []
        ];
    }

    /**
     * Post a Gradient to Pinterest and update data/gradients.json
     *
     * @param string $gradientId (e.g. 'gradient_1')
     * @return array Result
     */
    public static function postGradient(string $gradientId): array {
        $gradientsFile = __DIR__ . '/../data/gradients.json';
        if (!file_exists($gradientsFile)) {
            return ['success' => false, 'error' => 'gradients.json not found'];
        }

        $gradients = json_decode(file_get_contents($gradientsFile), true);
        if (!is_array($gradients)) {
            return ['success' => false, 'error' => 'Failed to parse gradients.json'];
        }

        // Find gradient
        $targetIndex = null;
        $gradient = null;
        foreach ($gradients as $idx => $g) {
            if (($g['id'] ?? '') === $gradientId) {
                $targetIndex = $idx;
                $gradient = $g;
                break;
            }
        }

        if ($gradient === null) {
            return ['success' => false, 'error' => "Gradient '$gradientId' not found"];
        }

        $name = $gradient['name'] ?? 'CSS Gradient';
        $style = $gradient['style'] ?? 'Modern';
        $type = strtoupper($gradient['type'] ?? 'LINEAR');
        $css = $gradient['css'] ?? '';
        $colors = $gradient['colors'] ?? [];
        $colorList = implode(', ', $colors);

        $title = "$name $type Gradient — Color Magic";
        $description = "CSS code: $css. Colors: $colorList. Curated $style gradient for modern web & UI design from Color Magic.";
        $link = "https://colormagic.techkreative.com/gradient/" . ($gradient['id'] ?? 'gradient_1');

        require_once __DIR__ . '/PinterestImageGenerator.php';
        $base64Image = PinterestImageGenerator::generateGradientImage($gradient);

        $target = self::resolveTarget('Color Gradients');
        $boardId = $target['board_id'];
        $sectionId = $target['board_section_id'];

        $pinPayload = [
            'title'        => $title,
            'description'  => $description,
            'link'         => $link,
            'board_id'     => $boardId,
            'media_source' => [
                'source_type'  => 'image_base64',
                'content_type' => 'image/jpeg',
                'data'         => $base64Image
            ]
        ];

        if (!empty($sectionId)) {
            $pinPayload['board_section_id'] = $sectionId;
        }

        $res = self::request('pins', 'POST', $pinPayload);

        if ($res['success']) {
            $pinId = $res['data']['id'] ?? '';
            // Update local JSON with uploaded = true
            $gradients[$targetIndex]['uploaded'] = true;
            $gradients[$targetIndex]['pinterest_pin_id'] = $pinId;
            $gradients[$targetIndex]['pinterest_posted_at'] = date('c');
            $gradients[$targetIndex]['pinterest_board_id'] = $boardId;

            file_put_contents($gradientsFile, json_encode($gradients, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return [
                'success'   => true,
                'pin_id'    => $pinId,
                'board_id'  => $boardId,
                'link'      => $link,
                'title'     => $title,
                'uploaded'  => true
            ];
        }

        return [
            'success' => false,
            'error'   => $res['error'] ?? 'Failed to create pin on Pinterest',
            'details' => $res['raw'] ?? []
        ];
    }

    /**
     * Post a Named Color to Pinterest (Board: Colors)
     *
     * @param string $hexOrSlug
     * @return array Result
     */
    public static function postColor(string $hexOrSlug): array {
        $colorsFile = __DIR__ . '/../data/color-names.json';
        if (!file_exists($colorsFile)) {
            return ['success' => false, 'error' => 'color-names.json not found'];
        }

        $colorsRaw = json_decode(file_get_contents($colorsFile), true) ?: [];
        $targetKey = null;
        $colorData = null;

        $cleanSearch = strtolower(trim(ltrim($hexOrSlug, '#')));
        foreach ($colorsRaw as $key => $item) {
            $h = strtolower($item['hex'] ?? $key);
            $s = strtolower($item['slug'] ?? '');
            if ($h === $cleanSearch || $s === $cleanSearch) {
                $targetKey = $key;
                $colorData = $item;
                break;
            }
        }

        if (!$colorData) {
            return ['success' => false, 'error' => "Color '$hexOrSlug' not found in color-names.json"];
        }

        $name = $colorData['name'] ?? ('#' . strtoupper($colorData['hex'] ?? $targetKey));
        $hex = '#' . strtoupper($colorData['hex'] ?? $targetKey);
        $slug = $colorData['slug'] ?? strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));

        $title = "$name ($hex) Color Meaning & Codes — Color Magic";
        $description = "$name ($hex): Explore hex, RGB, CMYK and HSL color values, visual harmonies, and contrast ratios at Color Magic.";
        $link = "https://colormagic.techkreative.com/color/$slug";

        require_once __DIR__ . '/PinterestImageGenerator.php';
        $base64Image = PinterestImageGenerator::generateColorImage($colorData);

        $target = self::resolveTarget('Colors');
        $boardId = $target['board_id'];
        $sectionId = $target['board_section_id'];

        $pinPayload = [
            'title'        => $title,
            'description'  => $description,
            'link'         => $link,
            'board_id'     => $boardId,
            'media_source' => [
                'source_type'  => 'image_base64',
                'content_type' => 'image/jpeg',
                'data'         => $base64Image
            ]
        ];

        if (!empty($sectionId)) {
            $pinPayload['board_section_id'] = $sectionId;
        }

        $res = self::request('pins', 'POST', $pinPayload);

        if ($res['success']) {
            $pinId = $res['data']['id'] ?? '';
            // Update local JSON with uploaded = true
            $colorsRaw[$targetKey]['uploaded'] = true;
            $colorsRaw[$targetKey]['pinterest_pin_id'] = $pinId;
            $colorsRaw[$targetKey]['pinterest_posted_at'] = date('c');
            $colorsRaw[$targetKey]['pinterest_board_id'] = $boardId;

            file_put_contents($colorsFile, json_encode($colorsRaw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return [
                'success'   => true,
                'pin_id'    => $pinId,
                'board_id'  => $boardId,
                'link'      => $link,
                'title'     => $title,
                'uploaded'  => true
            ];
        }

        return [
            'success' => false,
            'error'   => $res['error'] ?? 'Failed to create pin on Pinterest',
            'details' => $res['raw'] ?? []
        ];
    }
}
