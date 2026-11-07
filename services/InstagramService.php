<?php
/**
 * services/InstagramService.php
 * Meta Graph API v19.0 client for Instagram Content Publishing.
 * Handles single-image publishing (Palettes, Gradients, Colors), token validation,
 * OAuth setup, and synchronizes status with local JSON datasets.
 */

require_once __DIR__ . '/PosterCacheService.php';

class InstagramService {
    private static ?string $appId = null;
    private static ?string $appSecret = null;
    private static ?string $accountId = null;
    private static ?string $accessToken = null;
    private static ?string $username = null;
    private static string $graphApiBase = 'https://graph.facebook.com/v19.0';
    private static string $storageFile = __DIR__ . '/../data/instagram_account.json';

    /**
     * Initialize credentials from env or data/instagram_account.json
     */
    public static function init(): void {
        if (self::$appId !== null && self::$accountId !== null && self::$accessToken !== null) {
            return;
        }

        // 1. Meta App credentials
        self::$appId = getenv('META_APP_ID') ?: ($_ENV['META_APP_ID'] ?? '1731342528338401');
        self::$appSecret = getenv('META_APP_SECRET') ?: ($_ENV['META_APP_SECRET'] ?? '');

        // 2. Try credentials file data/instagram_account.json
        if (file_exists(self::$storageFile)) {
            $saved = json_decode(file_get_contents(self::$storageFile), true) ?: [];
            if (!empty($saved['account_id'])) {
                self::$accountId = $saved['account_id'];
            }
            if (!empty($saved['access_token'])) {
                self::$accessToken = $saved['access_token'];
            }
            if (!empty($saved['username'])) {
                self::$username = $saved['username'];
            }
        }

        // 3. Environment overrides
        if (empty(self::$accountId)) {
            self::$accountId = getenv('INSTAGRAM_ACCOUNT_ID') ?: ($_ENV['INSTAGRAM_ACCOUNT_ID'] ?? '');
        }
        if (empty(self::$accessToken)) {
            self::$accessToken = getenv('INSTAGRAM_ACCESS_TOKEN') ?: ($_ENV['INSTAGRAM_ACCESS_TOKEN'] ?? '');
        }
    }

    /**
     * Perform HTTP request to Meta Graph API
     */
    public static function request(string $endpoint, string $method = 'GET', ?array $params = []): array {
        self::init();

        $url = str_starts_with($endpoint, 'http')
            ? $endpoint
            : rtrim(self::$graphApiBase, '/') . '/' . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($params)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if (!empty($params)) {
                $query = http_build_query($params);
                $url .= (str_contains($url, '?') ? '&' : '?') . $query;
            }
            curl_setopt($ch, CURLOPT_URL, $url);
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

        $errorMsg = $data['error']['message'] ?? "Meta Graph API returned HTTP $httpCode";
        return [
            'success' => false,
            'status'  => $httpCode,
            'error'   => $errorMsg,
            'raw'     => $data
        ];
    }

    /**
     * Inspect an access token's validity, application, and granted scopes
     */
    public static function inspectToken(?string $accessToken = null): array {
        self::init();
        $targetToken = $accessToken ?: self::$accessToken;

        if (empty($targetToken)) {
            return [
                'is_valid' => false,
                'error'    => 'No access token provided to inspect.'
            ];
        }

        $appToken = (self::$appId && self::$appSecret) ? (self::$appId . '|' . self::$appSecret) : $targetToken;

        $res = self::request('debug_token', 'GET', [
            'input_token'  => $targetToken,
            'access_token' => $appToken
        ]);

        if (!$res['success'] || empty($res['data']['data'])) {
            return [
                'is_valid' => false,
                'error'    => $res['error'] ?? 'Failed to inspect token'
            ];
        }

        $info = $res['data']['data'];
        $scopes = $info['scopes'] ?? [];
        $requiredScopes = ['instagram_basic', 'instagram_content_publish'];
        $missingScopes = array_diff($requiredScopes, $scopes);

        return [
            'is_valid'       => (bool) ($info['is_valid'] ?? false),
            'app_id'         => $info['app_id'] ?? '',
            'application'    => $info['application'] ?? '',
            'user_id'        => $info['user_id'] ?? '',
            'scopes'         => $scopes,
            'missing_scopes' => array_values($missingScopes),
            'expires_at'     => isset($info['expires_at']) ? date('c', $info['expires_at']) : null
        ];
    }

    /**
     * Validate Instagram credentials and fetch profile info
     */
    public static function validateCredentials(?string $accountId = null, ?string $accessToken = null): array {
        self::init();
        $targetId = trim($accountId ?: (self::$accountId ?? ''));
        $targetToken = trim($accessToken ?: (self::$accessToken ?? ''));

        if (empty($targetId) || empty($targetToken)) {
            return [
                'connected' => false,
                'error'     => 'Instagram Account ID or Access Token is not set.'
            ];
        }

        // Check if user accidentally entered a username/handle instead of numeric account ID
        if (!ctype_digit($targetId)) {
            return [
                'connected' => false,
                'error'     => "Instagram Account ID must be a numeric ID (e.g. 17841400000000000), not a username/handle ('$targetId'). Meta Graph API requires the numeric ID of your Instagram Professional account."
            ];
        }

        $res = self::request($targetId, 'GET', [
            'fields'       => 'id,username,name,profile_picture_url',
            'access_token' => $targetToken
        ]);

        if ($res['success']) {
            return [
                'connected'           => true,
                'account_id'          => $res['data']['id'] ?? $targetId,
                'username'            => $res['data']['username'] ?? '',
                'name'                => $res['data']['name'] ?? '',
                'profile_picture_url' => $res['data']['profile_picture_url'] ?? ''
            ];
        }

        // If validation failed, diagnose the token
        $diagnosis = self::inspectToken($targetToken);
        if (!empty($diagnosis['missing_scopes'])) {
            $missingList = implode(', ', $diagnosis['missing_scopes']);
            return [
                'connected' => false,
                'error'     => "Access Token is missing required permissions: {$missingList}. Please regenerate the token in Graph API Explorer with 'instagram_basic' and 'instagram_content_publish' checked.",
                'diagnosis' => $diagnosis
            ];
        }

        return [
            'connected' => false,
            'error'     => $res['error'] ?? 'Invalid token or account ID',
            'raw'       => $res['raw'] ?? []
        ];
    }

    /**
     * Save account credentials to data/instagram_account.json
     */
    public static function saveCredentials(string $accountId, string $accessToken, ?string $username = null): array {
        $validation = self::validateCredentials($accountId, $accessToken);
        if (!$validation['connected']) {
            return [
                'success' => false,
                'error'   => 'Validation failed: ' . ($validation['error'] ?? 'Unknown error')
            ];
        }

        $savedData = [
            'account_id'          => $accountId,
            'access_token'        => $accessToken,
            'username'            => $validation['username'] ?? ($username ?: ''),
            'name'                => $validation['name'] ?? '',
            'profile_picture_url' => $validation['profile_picture_url'] ?? '',
            'updated_at'          => date('c')
        ];

        file_put_contents(self::$storageFile, json_encode($savedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Update active in-memory values
        self::$accountId = $accountId;
        self::$accessToken = $accessToken;
        self::$username = $savedData['username'];

        return [
            'success' => true,
            'account' => $savedData
        ];
    }

    /**
     * Get current connection status
     */
    public static function getStatus(): array {
        self::init();

        $hasApp = !empty(self::$appId) && !empty(self::$appSecret);
        $hasToken = !empty(self::$accessToken);
        $hasAccount = !empty(self::$accountId);

        $status = [
            'app_id_configured'  => $hasApp,
            'app_id'             => self::$appId,
            'has_account_id'     => $hasAccount,
            'account_id'         => self::$accountId,
            'has_access_token'   => $hasToken,
            'connected'          => false,
            'username'           => self::$username ?: ''
        ];

        if ($hasAccount && $hasToken) {
            $validation = self::validateCredentials();
            $status['connected'] = $validation['connected'];
            if ($validation['connected']) {
                $status['username'] = $validation['username'] ?? self::$username;
                $status['name'] = $validation['name'] ?? '';
                $status['profile_picture_url'] = $validation['profile_picture_url'] ?? '';
            } else {
                $status['error'] = $validation['error'] ?? 'Token validation error';
            }
        } else {
            $status['error'] = 'Instagram Account ID or Access Token is missing.';
        }

        return $status;
    }

    /**
     * Upload an image to an instant public CDN so Meta can fetch it
     */
    private static function uploadToCdn(string $localFilePath): ?string {
        if (!file_exists($localFilePath) || filesize($localFilePath) < 100) {
            return null;
        }

        $binary = file_get_contents($localFilePath);
        if (!$binary) {
            return null;
        }

        $b64 = base64_encode($binary);
        $ch = curl_init('https://freeimage.host/api/1/upload');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'key'    => '6d207e02198a847aa98d0a2a901485a5',
            'action' => 'upload',
            'source' => $b64,
            'format' => 'json'
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        if ($res) {
            $json = json_decode($res, true);
            if (!empty($json['image']['url'])) {
                return $json['image']['url'];
            }
        }
        return null;
    }

    /**
     * Resolve public image URL for an asset.
     * 1. Checks if a custom base URL (e.g. ngrok tunnel) is provided in .env.
     * 2. Checks if live host (colormagic.techkreative.com) already serves the image with 200 OK.
     * 3. If live host returns 404 (e.g. during local dev before git deployment), uploads local poster to CDN.
     */
    private static function resolvePublicImageUrl(string $path, ?string $localFilePath = null): string {
        $customBase = getenv('META_PUBLIC_IMAGE_BASE_URL') ?: ($_ENV['META_PUBLIC_IMAGE_BASE_URL'] ?? '');
        if (!empty($customBase)) {
            return rtrim($customBase, '/') . '/' . ltrim($path, '/');
        }

        $defaultPublicUrl = 'https://colormagic.techkreative.com/' . ltrim($path, '/');

        // Check if production host is already live with this file
        $ch = curl_init($defaultPublicUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode === 200 && str_starts_with($contentType ?? '', 'image/')) {
            return $defaultPublicUrl;
        }

        // Live host doesn't have the file yet (local dev mode): upload local poster to CDN
        if ($localFilePath) {
            $cdnUrl = self::uploadToCdn($localFilePath);
            if ($cdnUrl) {
                return $cdnUrl;
            }
        }

        return $defaultPublicUrl;
    }

    /**
     * Publish an image container and retrieve permalink
     */
    private static function publishContainer(string $imageUrl, string $caption): array {
        self::init();

        if (empty(self::$accountId) || empty(self::$accessToken)) {
            return [
                'success' => false,
                'error'   => 'Instagram is not configured. Please add your Instagram Account ID & Access Token in the Instagram Settings.'
            ];
        }

        if (!ctype_digit(trim(self::$accountId))) {
            return [
                'success' => false,
                'error'   => "Instagram Account ID must be a numeric ID (e.g. 17841400000000000), not a username/handle ('" . self::$accountId . "'). Meta Graph API requires the numeric Instagram Business Account ID."
            ];
        }

        // 1. Create Media Container
        $createRes = self::request(self::$accountId . '/media', 'POST', [
            'image_url'    => $imageUrl,
            'caption'      => $caption,
            'access_token' => self::$accessToken
        ]);

        if (!$createRes['success'] || empty($createRes['data']['id'])) {
            return [
                'success' => false,
                'error'   => 'Failed to create Instagram media container: ' . ($createRes['error'] ?? 'Unknown error'),
                'details' => $createRes['raw'] ?? []
            ];
        }

        $containerId = $createRes['data']['id'];

        // 2. Poll/wait container status (images are usually ready within 1-2s)
        $maxAttempts = 5;
        $isReady = false;
        for ($i = 0; $i < $maxAttempts; $i++) {
            usleep(1000000); // 1 second
            $statusCheck = self::request($containerId, 'GET', [
                'fields'       => 'status_code',
                'access_token' => self::$accessToken
            ]);

            if ($statusCheck['success']) {
                $statusCode = $statusCheck['data']['status_code'] ?? 'FINISHED';
                if ($statusCode === 'FINISHED') {
                    $isReady = true;
                    break;
                }
                if ($statusCode === 'ERROR') {
                    return [
                        'success' => false,
                        'error'   => 'Media container processing failed on Instagram.'
                    ];
                }
            } else {
                // If checking fails, proceed to attempt publish
                $isReady = true;
                break;
            }
        }

        // 3. Publish Media
        $publishRes = self::request(self::$accountId . '/media_publish', 'POST', [
            'creation_id'  => $containerId,
            'access_token' => self::$accessToken
        ]);

        if (!$publishRes['success'] || empty($publishRes['data']['id'])) {
            return [
                'success' => false,
                'error'   => 'Failed to publish media to Instagram: ' . ($publishRes['error'] ?? 'Unknown error'),
                'details' => $publishRes['raw'] ?? []
            ];
        }

        $mediaId = $publishRes['data']['id'];

        // 4. Fetch live permalink
        $permalink = 'https://www.instagram.com/';
        $mediaDetails = self::request($mediaId, 'GET', [
            'fields'       => 'permalink,timestamp',
            'access_token' => self::$accessToken
        ]);

        if ($mediaDetails['success'] && !empty($mediaDetails['data']['permalink'])) {
            $permalink = $mediaDetails['data']['permalink'];
        }

        return [
            'success'   => true,
            'media_id'  => $mediaId,
            'permalink' => $permalink
        ];
    }

    /**
     * Post a Color Palette to Instagram
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
        $style = $palette['style'] ?? 'Modern';
        $colors = $palette['colors'] ?? [];
        $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($name)), '-');

        // Pre-cache high-resolution poster locally
        $localPoster = PosterCacheService::getPalettePoster($palette);
        $imageUrl = self::resolvePublicImageUrl("posters/palettes/{$slug}.jpg", $localPoster);

        // Format engaging caption
        $colorLines = [];
        foreach ($colors as $c) {
            $colorLines[] = "▫️ " . strtoupper($c);
        }
        $colorListText = implode("\n", $colorLines);

        $caption = "🎨 {$name} Color Palette\n\n"
                 . "Style: {$style}\n\n"
                 . "HEX CODES:\n"
                 . "{$colorListText}\n\n"
                 . "🔗 Explore, test contrast & copy hex codes: https://colormagic.techkreative.com/palette/{$slug} (Link in bio)\n\n"
                 . "#colormagic #colorpalette #colorscheme #graphicdesign #uiux #webdesign #branding #palette #colorinspiration #designinspo";

        $res = self::publishContainer($imageUrl, $caption);

        if ($res['success']) {
            $palettes[$targetIndex]['instagram_uploaded'] = true;
            $palettes[$targetIndex]['instagram_media_id'] = $res['media_id'];
            $palettes[$targetIndex]['instagram_permalink'] = $res['permalink'];
            $palettes[$targetIndex]['instagram_posted_at'] = date('c');

            file_put_contents($palettesFile, json_encode($palettes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return [
                'success'            => true,
                'media_id'           => $res['media_id'],
                'permalink'          => $res['permalink'],
                'title'              => $name,
                'instagram_uploaded' => true
            ];
        }

        return $res;
    }

    /**
     * Post a Gradient to Instagram
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

        $name = $gradient['name'] ?? 'Modern Gradient';
        $style = $gradient['style'] ?? 'Design';
        $type = strtoupper($gradient['type'] ?? 'LINEAR');
        $css = $gradient['css'] ?? '';
        $colors = $gradient['colors'] ?? [];

        $localPoster = PosterCacheService::getGradientPoster($gradient);
        $imageUrl = self::resolvePublicImageUrl("posters/gradients/{$gradient['id']}.jpg", $localPoster);

        $colorLines = [];
        foreach ($colors as $c) {
            $colorLines[] = "▫️ " . strtoupper($c);
        }
        $colorListText = implode("\n", $colorLines);

        $caption = "🌈 {$name} {$type} Gradient\n\n"
                 . "Style: {$style}\n\n"
                 . "HEX CODES:\n"
                 . "{$colorListText}\n\n"
                 . "CSS CODE:\n"
                 . "{$css}\n\n"
                 . "🔗 Copy CSS & generate gradients: https://colormagic.techkreative.com/gradient/{$gradient['id']} (Link in bio)\n\n"
                 . "#colormagic #gradient #gradientdesign #uidesign #webdeveloper #cssgradient #colorscheme #graphicdesign";

        $res = self::publishContainer($imageUrl, $caption);

        if ($res['success']) {
            $gradients[$targetIndex]['instagram_uploaded'] = true;
            $gradients[$targetIndex]['instagram_media_id'] = $res['media_id'];
            $gradients[$targetIndex]['instagram_permalink'] = $res['permalink'];
            $gradients[$targetIndex]['instagram_posted_at'] = date('c');

            file_put_contents($gradientsFile, json_encode($gradients, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return [
                'success'            => true,
                'media_id'           => $res['media_id'],
                'permalink'          => $res['permalink'],
                'title'              => $name,
                'instagram_uploaded' => true
            ];
        }

        return $res;
    }

    /**
     * Post a Color to Instagram
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

        $localPoster = PosterCacheService::getColorPoster($colorData);
        $imageUrl = self::resolvePublicImageUrl("posters/colors/{$slug}.jpg", $localPoster);

        $caption = "✨ {$name} ({$hex})\n\n"
                 . "Explore the color harmony, conversions (RGB, CMYK, HSL) and design contrast for {$name}.\n\n"
                 . "🔗 https://colormagic.techkreative.com/color/{$slug} (Link in bio)\n\n"
                 . "#colormagic #color #colorinspiration #coloroftheday #designinspiration #palette #graphicdesign #uiux";

        $res = self::publishContainer($imageUrl, $caption);

        if ($res['success']) {
            $colorsRaw[$targetKey]['instagram_uploaded'] = true;
            $colorsRaw[$targetKey]['instagram_media_id'] = $res['media_id'];
            $colorsRaw[$targetKey]['instagram_permalink'] = $res['permalink'];
            $colorsRaw[$targetKey]['instagram_posted_at'] = date('c');

            file_put_contents($colorsFile, json_encode($colorsRaw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return [
                'success'            => true,
                'media_id'           => $res['media_id'],
                'permalink'          => $res['permalink'],
                'title'              => $name,
                'instagram_uploaded' => true
            ];
        }

        return $res;
    }

    /**
     * Generate OAuth Login URL for Facebook App
     */
    public static function getOAuthLoginUrl(string $redirectUri): string {
        self::init();
        $params = [
            'client_id'     => self::$appId,
            'redirect_uri'  => $redirectUri,
            'scope'         => 'instagram_basic,instagram_content_publish,pages_show_list,pages_read_engagement',
            'response_type' => 'code',
            'state'         => 'cm_ig_connect_' . time()
        ];
        return 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query($params);
    }

    /**
     * Handle OAuth Code exchange for token and Instagram Business Account discovery
     */
    public static function handleOAuthCallback(string $code, string $redirectUri): array {
        self::init();

        // 1. Exchange code for short-lived user token
        $tokenRes = self::request('oauth/access_token', 'GET', [
            'client_id'     => self::$appId,
            'client_secret' => self::$appSecret,
            'redirect_uri'  => $redirectUri,
            'code'          => $code
        ]);

        if (!$tokenRes['success'] || empty($tokenRes['data']['access_token'])) {
            return [
                'success' => false,
                'error'   => 'Failed to exchange authorization code: ' . ($tokenRes['error'] ?? 'Unknown error'),
                'raw'     => $tokenRes['raw'] ?? []
            ];
        }

        $shortToken = $tokenRes['data']['access_token'];

        // 2. Exchange short-lived token for 60-day long-lived token
        $longTokenRes = self::request('oauth/access_token', 'GET', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => self::$appId,
            'client_secret'     => self::$appSecret,
            'fb_exchange_token' => $shortToken
        ]);

        $longToken = ($longTokenRes['success'] && !empty($longTokenRes['data']['access_token']))
            ? $longTokenRes['data']['access_token']
            : $shortToken;

        // 3. Query Facebook Pages to find linked Instagram Business Account
        $pagesRes = self::request('me/accounts', 'GET', [
            'fields'       => 'name,access_token,instagram_business_account{id,username,name,profile_picture_url}',
            'access_token' => $longToken
        ]);

        $foundAccount = null;
        $pageToken = null;
        if ($pagesRes['success'] && !empty($pagesRes['data']['data'])) {
            foreach ($pagesRes['data']['data'] as $page) {
                if (!empty($page['instagram_business_account']['id'])) {
                    $foundAccount = $page['instagram_business_account'];
                    $pageToken = $page['access_token'] ?? $longToken;
                    break;
                }
            }
        }

        if (!$foundAccount) {
            return [
                'success'      => false,
                'error'        => 'Could not find an Instagram Business/Creator account linked to any Facebook Page on this account. Please ensure your Instagram account is Professional and linked to a Facebook Page, or paste your Account ID manually.',
                'access_token' => $longToken
            ];
        }

        return self::saveCredentials($foundAccount['id'], $pageToken, $foundAccount['username'] ?? null);
    }
}
