<?php
/**
 * UserDb - MySQL Database Service for User Authentication & Persistence
 * Manages table creation, user syncing, and account deletion.
 */
class UserDb {
    private static $pdo = null;

    /**
     * Get or establish MySQL PDO connection
     * @return PDO|null
     */
    public static function getConnection() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Check if PDO MySQL driver is available
        if (!extension_loaded('pdo') || !in_array('mysql', PDO::getAvailableDrivers(), true)) {
            return null;
        }

        $host = getenv('DB_SERVER') ?: (getenv('DB_HOST') ?: null);
        $user = getenv('DB_USERNAME') ?: (getenv('DB_USER') ?: null);
        $pass = getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: null);
        $db   = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: null);
        $port = getenv('DB_PORT') ?: '3306';

        // Check in candidate env files if not set in server environment
        if (!$host || !$user) {
            $baseFiles = [
                __DIR__ . '/../../.env.colormagic', // Production parent directory
                __DIR__ . '/../.env.colormagic',    // Project root
                __DIR__ . '/../../.env',           // Fallback
                __DIR__ . '/../.env'               // Fallback
            ];
            $localFiles = [
                __DIR__ . '/../../.env.colormagic.local',
                __DIR__ . '/../.env.colormagic.local',
                __DIR__ . '/../.env.local'
            ];

            $env = [];
            foreach ($baseFiles as $file) {
                if (file_exists($file) && is_readable($file)) {
                    $parsed = function_exists('cm_read_env_file') ? cm_read_env_file($file) : @parse_ini_file($file);
                    if ($parsed) {
                        $env = array_merge($env, $parsed);
                        break;
                    }
                }
            }
            foreach ($localFiles as $file) {
                if (file_exists($file) && is_readable($file)) {
                    $parsed = function_exists('cm_read_env_file') ? cm_read_env_file($file) : @parse_ini_file($file);
                    if ($parsed) {
                        $env = array_merge($env, $parsed);
                        break;
                    }
                }
            }

            if (!empty($env)) {
                $host = $host ?: ($env['DB_SERVER'] ?? ($env['DB_HOST'] ?? ($env['MYSQL_HOST'] ?? null)));
                $user = $user ?: ($env['DB_USERNAME'] ?? ($env['DB_USER'] ?? ($env['MYSQL_USER'] ?? null)));
                $pass = $pass ?: ($env['DB_PASSWORD'] ?? ($env['DB_PASS'] ?? ($env['MYSQL_PASSWORD'] ?? '')));
                $db   = $db   ?: ($env['DB_NAME'] ?? ($env['DB_DATABASE'] ?? ($env['MYSQL_DATABASE'] ?? null)));
                $port = $port ?: ($env['DB_PORT'] ?? ($env['MYSQL_PORT'] ?? '3306'));
            }
        }

        // In StackCP hosting, the Database Name equals the Username if not specified separately
        if (!$db && $user) {
            $db = $user;
        }

        if (!$host || !$db || !$user) {
            return null;
        }

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT            => 5
            ]);
            self::initTable(self::$pdo);
            return self::$pdo;
        } catch (\Throwable $e) {
            error_log("UserDb Connection Notice: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Automatically ensure `users` and `user_favorites` tables exist
     */
    private static function initTable(PDO $pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            google_id VARCHAR(100) NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NULL,
            picture VARCHAR(500) NULL,
            role VARCHAR(50) DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_google_id (google_id),
            INDEX idx_email (email),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS user_favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_type ENUM('color', 'palette', 'gradient') NOT NULL,
            item_id VARCHAR(100) NOT NULL,
            item_data JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_user_favorite (user_id, item_type, item_id),
            INDEX idx_user_type (user_id, item_type),
            CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sql);

        // Ensure role column exists if table was created previously without it
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user' AFTER picture");
        } catch (\Throwable $e) {
            // Ignored if column already exists
        }
    }

    /**
     * Store or update user upon successful login and return updated database user record
     * @param array $userData
     * @return array|null
     */
    public static function saveUser(array $userData) {
        if (empty($userData['email'])) {
            return null;
        }

        $pdo = self::getConnection();
        if (!$pdo) {
            return null;
        }

        $role = $userData['role'] ?? 'user';

        try {
            $sql = "INSERT INTO users (google_id, email, name, picture, role, last_login)
                    VALUES (:google_id, :email, :name, :picture, :role, NOW())
                    ON DUPLICATE KEY UPDATE
                        google_id = COALESCE(VALUES(google_id), google_id),
                        name = COALESCE(VALUES(name), name),
                        picture = COALESCE(VALUES(picture), picture),
                        role = CASE WHEN VALUES(role) = 'admin' THEN 'admin' ELSE role END,
                        last_login = NOW()";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':google_id' => $userData['id'] ?? null,
                ':email'     => $userData['email'],
                ':name'      => $userData['name'] ?? null,
                ':picture'   => $userData['picture'] ?? null,
                ':role'      => $role,
            ]);

            return self::getUserByEmail($userData['email']);
        } catch (\Throwable $e) {
            error_log("UserDb saveUser error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch user by email address
     * @param string $email
     * @return array|null
     */
    public static function getUserByEmail(string $email) {
        $pdo = self::getConnection();
        if (!$pdo) return null;

        try {
            $stmt = $pdo->prepare("SELECT id, google_id, email, name, picture, role, created_at, last_login FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch() ?: null;
        } catch (\Throwable $e) {
            error_log("UserDb getUserByEmail error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update user role (e.g. 'admin' or 'user')
     * @param string $email
     * @param string $role
     * @return bool
     */
    public static function setUserRole(string $email, string $role = 'admin') {
        $pdo = self::getConnection();
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE email = :email");
            return $stmt->execute([':role' => $role, ':email' => $email]);
        } catch (\Throwable $e) {
            error_log("UserDb setUserRole error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user from MySQL database
     * @param string $email
     * @return bool
     */
    public static function deleteUser(string $email) {
        $pdo = self::getConnection();
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE email = :email");
            return $stmt->execute([':email' => $email]);
        } catch (\Throwable $e) {
            error_log("UserDb deleteUser error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all favorites for a user by email
     * @param string $email
     * @return array
     */
    public static function getUserFavorites(string $email) {
        $user = self::getUserByEmail($email);
        $result = ['colors' => [], 'palettes' => [], 'gradients' => []];
        if (!$user) return $result;

        $pdo = self::getConnection();
        if (!$pdo) return $result;

        try {
            $stmt = $pdo->prepare("SELECT item_type, item_id, item_data FROM user_favorites WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->execute([':user_id' => $user['id']]);
            while ($row = $stmt->fetch()) {
                $type = $row['item_type'] . 's'; // 'colors', 'palettes', 'gradients'
                if (isset($result[$type])) {
                    $result[$type][] = $row['item_id'];
                }
            }
        } catch (\Throwable $e) {
            error_log("UserDb getUserFavorites error: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Add a favorite item for user
     * @param string $email
     * @param string $type 'color' | 'palette' | 'gradient'
     * @param string $itemId
     * @param mixed $itemData optional metadata
     * @return bool
     */
    public static function addFavorite(string $email, string $type, string $itemId, $itemData = null) {
        $user = self::getUserByEmail($email);
        if (!$user) return false;

        $pdo = self::getConnection();
        if (!$pdo) return false;

        try {
            $dataJson = $itemData !== null ? json_encode($itemData) : null;
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_favorites (user_id, item_type, item_id, item_data, created_at) VALUES (:user_id, :item_type, :item_id, :item_data, NOW())");
            return $stmt->execute([
                ':user_id'   => $user['id'],
                ':item_type' => $type,
                ':item_id'   => $itemId,
                ':item_data' => $dataJson
            ]);
        } catch (\Throwable $e) {
            error_log("UserDb addFavorite error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a favorite item for user
     * @param string $email
     * @param string $type
     * @param string $itemId
     * @return bool
     */
    public static function removeFavorite(string $email, string $type, string $itemId) {
        $user = self::getUserByEmail($email);
        if (!$user) return false;

        $pdo = self::getConnection();
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND item_type = :item_type AND item_id = :item_id");
            return $stmt->execute([
                ':user_id'   => $user['id'],
                ':item_type' => $type,
                ':item_id'   => $itemId
            ]);
        } catch (\Throwable $e) {
            error_log("UserDb removeFavorite error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle favorite item for user
     * @param string $email
     * @param string $type
     * @param string $itemId
     * @param mixed $itemData
     * @return bool true if added, false if removed
     */
    public static function toggleFavorite(string $email, string $type, string $itemId, $itemData = null) {
        $user = self::getUserByEmail($email);
        if (!$user) return false;

        $pdo = self::getConnection();
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = :user_id AND item_type = :item_type AND item_id = :item_id LIMIT 1");
            $stmt->execute([
                ':user_id'   => $user['id'],
                ':item_type' => $type,
                ':item_id'   => $itemId
            ]);
            if ($stmt->fetch()) {
                self::removeFavorite($email, $type, $itemId);
                return false;
            } else {
                self::addFavorite($email, $type, $itemId, $itemData);
                return true;
            }
        } catch (\Throwable $e) {
            error_log("UserDb toggleFavorite error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync client-side favorites with database favorites and return combined union
     * @param string $email
     * @param array $clientFavs ['colors' => [...], 'palettes' => [...], 'gradients' => [...]]
     * @return array
     */
    public static function syncFavorites(string $email, array $clientFavs) {
        $user = self::getUserByEmail($email);
        if (!$user) return $clientFavs;

        // Insert all client-side favorites into DB
        $map = ['colors' => 'color', 'palettes' => 'palette', 'gradients' => 'gradient'];
        foreach ($map as $key => $type) {
            if (!empty($clientFavs[$key]) && is_array($clientFavs[$key])) {
                foreach ($clientFavs[$key] as $itemId) {
                    if (is_string($itemId) && trim($itemId) !== '') {
                        self::addFavorite($email, $type, trim($itemId));
                    }
                }
            }
        }

        // Return combined list from DB
        return self::getUserFavorites($email);
    }
}
