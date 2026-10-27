<?php
/**
 * AdminAuth - Administrative Authentication & JWT Session Service
 * Provides secure 12-hour JWT session generation, validation, refresh, and cookie management.
 */

require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/UserDb.php';

class AdminAuth {
    const TOKEN_LIFETIME = 43200; // 12 hours (morning to evening)
    const COOKIE_NAME    = 'cm_admin_jwt';

    /**
     * Retrieve the secret signing key for JWT HMAC-SHA256
     */
    private static function getSecretKey(): string {
        $key = getenv('API_SECRET_KEY') ?: ($_ENV['API_SECRET_KEY'] ?? '');
        if (empty($key)) {
            $key = 'colormagic_sec_live_9a7b2c4e8f1d3e6a';
        }
        return $key;
    }

    /**
     * Get configured list of admin email addresses from environment or fallback
     * @return array
     */
    public static function getAdminEmails(): array {
        $envEmails = getenv('ADMIN_EMAILS') ?: ($_ENV['ADMIN_EMAILS'] ?? '');
        $emails = [];
        if (!empty($envEmails)) {
            $parts = explode(',', $envEmails);
            foreach ($parts as $p) {
                $trimmed = strtolower(trim($p));
                if ($trimmed !== '') {
                    $emails[] = $trimmed;
                }
            }
        }

        // Default primary admin email
        $defaultAdmin = 'moeezahmad.tech@gmail.com';
        if (!in_array($defaultAdmin, $emails, true)) {
            $emails[] = $defaultAdmin;
        }

        return $emails;
    }

    /**
     * Check if a given email address has administrative privileges
     * @param string|null $email
     * @return bool
     */
    public static function isAdminEmail(?string $email): bool {
        if (empty($email)) {
            return false;
        }
        $normalized = strtolower(trim($email));

        // 1. Check environment admin list
        if (in_array($normalized, self::getAdminEmails(), true)) {
            return true;
        }

        // 2. Check database role
        $dbUser = UserDb::getUserByEmail($normalized);
        if ($dbUser && !empty($dbUser['role']) && strtolower($dbUser['role']) === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Base64Url encode string
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64Url decode string
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Generate a signed 12-hour JWT for an admin user
     * @param array $user
     * @param int $durationSeconds (default 12 hours)
     * @return array ['token' => string, 'exp' => int]
     */
    public static function generateJwt(array $user, int $durationSeconds = self::TOKEN_LIFETIME): array {
        $now = time();
        $exp = $now + $durationSeconds;

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'iss'   => 'ColorMagic',
            'sub'   => $user['id'] ?? ($user['db_id'] ?? $user['email']),
            'email' => $user['email'],
            'name'  => $user['name'] ?? '',
            'role'  => 'admin',
            'iat'   => $now,
            'exp'   => $exp,
            'jti'   => bin2hex(random_bytes(16))
        ];

        $headerEncoded  = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signature      = hash_hmac('sha256', "{$headerEncoded}.{$payloadEncoded}", self::getSecretKey(), true);
        $sigEncoded     = self::base64UrlEncode($signature);

        return [
            'token' => "{$headerEncoded}.{$payloadEncoded}.{$sigEncoded}",
            'exp'   => $exp
        ];
    }

    /**
     * Verify a JWT string
     * @param string|null $jwt
     * @return array|null Returns payload array if valid, null otherwise
     */
    public static function verifyJwt(?string $jwt): ?array {
        if (empty($jwt) || !is_string($jwt)) {
            return null;
        }

        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }

        list($headerEnc, $payloadEnc, $sigEnc) = $parts;

        // Verify HMAC-SHA256 signature
        $expectedSig = hash_hmac('sha256', "{$headerEnc}.{$payloadEnc}", self::getSecretKey(), true);
        $providedSig = self::base64UrlDecode($sigEnc);

        if (!hash_equals($expectedSig, $providedSig)) {
            return null;
        }

        $payloadJson = self::base64UrlDecode($payloadEnc);
        $payload = json_decode($payloadJson, true);

        if (!$payload || !is_array($payload)) {
            return null;
        }

        // Check expiration
        if (empty($payload['exp']) || $payload['exp'] < time()) {
            return null;
        }

        // Verify admin role and active admin status
        if (($payload['role'] ?? '') !== 'admin') {
            return null;
        }

        if (!self::isAdminEmail($payload['email'] ?? null)) {
            return null;
        }

        return $payload;
    }

    /**
     * Set the admin JWT as an HttpOnly, secure cookie
     * @param string $token
     * @param int $expTimestamp
     */
    public static function setAdminCookie(string $token, int $expTimestamp): void {
        $isHttps = (
            (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        );

        // Standard PHP setcookie with SameSite support
        if (!headers_sent()) {
            if (PHP_VERSION_ID >= 70300) {
                setcookie(self::COOKIE_NAME, $token, [
                    'expires'  => $expTimestamp,
                    'path'     => '/',
                    'domain'   => '',
                    'secure'   => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                setcookie(self::COOKIE_NAME, $token, $expTimestamp, '/; samesite=Lax', '', $isHttps, true);
            }
        }
        $_COOKIE[self::COOKIE_NAME] = $token;
    }

    /**
     * Clear the admin JWT cookie
     */
    public static function clearAdminCookie(): void {
        if (!headers_sent()) {
            $isHttps = (
                (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            );
            if (PHP_VERSION_ID >= 70300) {
                setcookie(self::COOKIE_NAME, '', [
                    'expires'  => time() - 86400,
                    'path'     => '/',
                    'domain'   => '',
                    'secure'   => $isHttps,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            setcookie(self::COOKIE_NAME, '', time() - 86400, '/');
            setcookie(self::COOKIE_NAME, '', time() - 86400, '/', '', $isHttps, true);
        }
        unset($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Issue full admin session (sets JWT, cookie, session vars)
     * @param array $user
     * @return array
     */
    public static function issueAdminSession(array $user): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $res = self::generateJwt($user, self::TOKEN_LIFETIME);
        self::setAdminCookie($res['token'], $res['exp']);

        // Update database user role to admin
        if (!empty($user['email'])) {
            UserDb::setUserRole($user['email'], 'admin');
        }

        // Store in session
        $_SESSION['admin_jwt']  = $res['token'];
        $_SESSION['admin_exp']  = $res['exp'];
        $_SESSION['admin_user'] = [
            'id'      => $user['id'] ?? ($user['db_id'] ?? null),
            'email'   => $user['email'],
            'name'    => $user['name'] ?? '',
            'picture' => $user['picture'] ?? null,
            'role'    => 'admin'
        ];
        if (isset($_SESSION['user'])) {
            $_SESSION['user']['is_admin'] = true;
            $_SESSION['user']['role']     = 'admin';
        }

        return [
            'success'    => true,
            'token'      => $res['token'],
            'exp'        => $res['exp'],
            'expires_in' => self::TOKEN_LIFETIME,
            'user'       => $_SESSION['admin_user']
        ];
    }

    /**
     * Clear all admin authentication credentials
     */
    public static function clearAdminSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::clearAdminCookie();
        unset($_SESSION['admin_jwt']);
        unset($_SESSION['admin_exp']);
        unset($_SESSION['admin_user']);
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']['is_admin']);
        }
    }

    /**
     * Get the currently authenticated admin, verifying JWT and active user session
     * @return array|null Returns admin payload if authorized, null otherwise
     */
    public static function getCurrentAdmin(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // CRITICAL: If the user is NOT actively logged in to Color Magic via session, they cannot be an admin!
        if (empty($_SESSION['user']['email'])) {
            self::clearAdminCookie();
            return null;
        }

        $userEmail = strtolower(trim($_SESSION['user']['email']));

        // Verify the logged-in user email has administrative privileges
        if (!self::isAdminEmail($userEmail)) {
            self::clearAdminCookie();
            return null;
        }

        // 1. Check HttpOnly cookie
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;

        // 2. Check Authorization Bearer header
        if (!$token && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            if (preg_match('/Bearer\s(\S+)/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                $token = $matches[1];
            }
        }

        // 3. Check Session token
        if (!$token && !empty($_SESSION['admin_jwt'])) {
            $token = $_SESSION['admin_jwt'];
        }

        if ($token) {
            $payload = self::verifyJwt($token);
            // Payload must match the currently logged-in user email
            if ($payload && strtolower(trim($payload['email'] ?? '')) === $userEmail) {
                return $payload;
            }
        }

        // If user is verified admin in session, issue or sync admin session
        $issue = self::issueAdminSession($_SESSION['user']);
        return self::verifyJwt($issue['token']);
    }
}
