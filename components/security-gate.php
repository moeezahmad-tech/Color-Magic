<?php
/**
 * Color Magic - Custom Human Verification Gate & Anti-Bot Protection
 *
 * Progressive Dual-Trigger Anti-Bot Gate:
 * 1. Free Browsing Allowance: Normal visitors browse up to 5 pages with 0 interruption.
 * 2. Velocity / Speed Limit: Rapid automated requests (e.g. >5 requests in 8s) trigger the gate immediately.
 * 3. 30-Day Persistence: Verified humans receive a 30-day cryptographic cookie + localStorage sync.
 */

if (!function_exists('cm_get_client_ip')) {
    function cm_get_client_ip(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

if (!function_exists('cm_get_ip_subnet')) {
    function cm_get_ip_subnet(string $ip): string {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.0';
        }
        return substr($ip, 0, 16);
    }
}

if (!function_exists('cm_get_security_secret')) {
    function cm_get_security_secret(): string {
        return getenv('API_SECRET_KEY') ?: 'colormagic_sec_live_9a7b2c4e8f1d3e6a';
    }
}

if (!function_exists('cm_is_gate_enabled')) {
    function cm_is_gate_enabled(): bool {
        $envVal = getenv('SECURITY_GATE_ENABLED');
        if ($envVal === false || $envVal === '') {
            return true; // Enabled by default
        }
        return filter_var($envVal, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('cm_is_whitelisted_request')) {
    function cm_is_whitelisted_request(): bool {
        if (php_sapi_name() === 'cli') {
            return true;
        }

        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        $uriPath = parse_url($rawUri, PHP_URL_PATH) ?? '/';
        $cleanPath = trim($uriPath, '/');
        $scriptName = basename($_SERVER['SCRIPT_FILENAME'] ?? '');

        // Allow verification endpoints
        if (
            strpos($cleanPath, 'verify-human') !== false ||
            $scriptName === 'verify-human.php' ||
            isset($_GET['cm_verify_action'])
        ) {
            return true;
        }

        // Allow static assets (CSS, JS, images, fonts, icons, json)
        if (preg_match('/\.(css|js|png|jpg|jpeg|webp|gif|svg|ico|woff|woff2|ttf|eot|xml|txt|json)$/i', $cleanPath)) {
            return true;
        }

        // Allow verification files, sitemaps, robots
        if (
            $cleanPath === 'robots.txt' ||
            $cleanPath === 'sitemap.xml' ||
            strpos($cleanPath, 'sitemap') === 0 ||
            strpos($cleanPath, 'BingSiteAuth.xml') === 0 ||
            preg_match('/^(pinterest|google|yandex).*\.(html|txt)$/i', $cleanPath)
        ) {
            return true;
        }

        // Search engine crawlers (preserves Google, Bing, Yandex SEO indexing)
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $allowedBots = [
            'Googlebot', 'Google-InspectionTool', 'GoogleOther', 'Googlebot-Image', 'Storebot-Google',
            'bingbot', 'Baiduspider', 'YandexBot', 
            'DuckDuckBot', 'Applebot', 'Pinterestbot', 'Twitterbot', 'facebookexternalhit'
        ];
        foreach ($allowedBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('cm_is_human_verified')) {
    function cm_is_human_verified(): bool {
        $cookie = $_COOKIE['cm_human_verified'] ?? '';
        if (empty($cookie) || strpos($cookie, '.') === false) {
            return false;
        }

        list($payloadB64, $signature) = explode('.', $cookie, 2);
        $payloadJson = base64_decode($payloadB64, true);
        if (!$payloadJson) {
            return false;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload) || !isset($payload['t']) || !isset($payload['ip'])) {
            return false;
        }

        // Check 30-day expiration
        $ttl = 30 * 86400;
        if (time() - (int)$payload['t'] > $ttl) {
            return false;
        }

        // Validate HMAC signature
        $secret = cm_get_security_secret();
        $expectedSig = hash_hmac('sha256', $payloadB64, $secret);
        if (!hash_equals($expectedSig, $signature)) {
            return false;
        }

        return true;
    }
}

if (!function_exists('cm_issue_human_cookie')) {
    function cm_issue_human_cookie(): string {
        $ip = cm_get_client_ip();
        $subnet = cm_get_ip_subnet($ip);
        $payload = [
            't' => time(),
            'ip' => $subnet
        ];
        $payloadB64 = base64_encode(json_encode($payload));
        $secret = cm_get_security_secret();
        $signature = hash_hmac('sha256', $payloadB64, $secret);
        $token = $payloadB64 . '.' . $signature;

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                   (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        $expiry = time() + (30 * 86400); // 30 days
        setcookie('cm_human_verified', $token, [
            'expires' => $expiry,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);
        $_COOKIE['cm_human_verified'] = $token;

        // Reset session tracking on successful verification
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['cm_free_page_count'] = 0;
            $_SESSION['cm_req_timestamps'] = [];
        }

        return $token;
    }
}

if (!function_exists('cm_check_security_gate')) {
    function cm_check_security_gate(string $base = ''): void {
        if (!cm_is_gate_enabled()) {
            return;
        }

        if (cm_is_whitelisted_request()) {
            return;
        }

        // Verified humans bypass all rate & page count checks
        if (cm_is_human_verified()) {
            return;
        }

        // --- Progressive Dual-Trigger Anti-Bot Gate ---
        // 1. Free Page Views: Allow 5 free page views before requiring verification
        // 2. Velocity / Speed Limit: More than 5 requests in 8 seconds trips the gate immediately
        $maxFreePages = 5;
        $rateLimitMax = 5;
        $rateLimitWindow = 8; // seconds

        $now = time();

        // Check Trigger A: Velocity / Rapid Bot Request Spike
        if (!isset($_SESSION['cm_req_timestamps']) || !is_array($_SESSION['cm_req_timestamps'])) {
            $_SESSION['cm_req_timestamps'] = [];
        }

        $_SESSION['cm_req_timestamps'] = array_filter(
            $_SESSION['cm_req_timestamps'],
            function($ts) use ($now, $rateLimitWindow) {
                return ($now - $ts) <= $rateLimitWindow;
            }
        );
        $_SESSION['cm_req_timestamps'][] = $now;

        $isSpeedTripped = count($_SESSION['cm_req_timestamps']) > $rateLimitMax;

        // Check Trigger B: Free Browsing Page Count
        if (!isset($_SESSION['cm_free_page_count'])) {
            $_SESSION['cm_free_page_count'] = 0;
        }
        $_SESSION['cm_free_page_count']++;

        $isPageCountTripped = $_SESSION['cm_free_page_count'] > $maxFreePages;

        // Both safe? Let user continue smooth browsing!
        if (!$isSpeedTripped && !$isPageCountTripped) {
            return;
        }

        // Trigger tripped! Redirect to verification challenge
        $reason = $isSpeedTripped ? 'speed' : 'limit';
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
        $verifyUrl = ($base !== '' ? $base : '') . '/verify-human?redirect=' . urlencode($currentUri) . '&reason=' . $reason;

        if (!headers_sent()) {
            header('Location: ' . $verifyUrl, true, 302);
            header('Cache-Control: no-cache, no-store, must-revalidate');
            exit;
        } else {
            echo '<script>window.location.href = ' . json_encode($verifyUrl) . ';</script>';
            echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($verifyUrl) . '"></noscript>';
            exit;
        }
    }
}
