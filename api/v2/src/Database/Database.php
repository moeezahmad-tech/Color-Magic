<?php

namespace ColorMagic\Database;

use PDO;
use PDOException;
use Exception;
use ColorMagic\Config\Env;
use ColorMagic\Services\MigrationService;

/**
 * High-Performance SQLite Connection Manager
 * Configured with WAL mode, memory mapping, query caching, and auto-provisioning.
 */
class Database
{
    private static ?PDO $instance = null;
    private static ?string $resolvedDbPath = null;

    /**
     * Get or initialize the PDO SQLite connection singleton
     */
    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        Env::load();
        $dbPath = self::resolveDbPath();

        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $isNewDb = !file_exists($dbPath) || filesize($dbPath) === 0;

        try {
            $pdo = new PDO('sqlite:' . $dbPath, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true,
            ]);

            // High-performance SQLite PRAGMA tuning
            $pdo->exec("PRAGMA journal_mode = WAL;");
            $pdo->exec("PRAGMA synchronous = NORMAL;");
            $pdo->exec("PRAGMA cache_size = -64000;"); // 64MB Cache
            $pdo->exec("PRAGMA temp_store = MEMORY;");
            $pdo->exec("PRAGMA mmap_size = 268435456;"); // 256MB Memory-mapped I/O
            $pdo->exec("PRAGMA busy_timeout = 5000;");

            self::$instance = $pdo;

            // Auto-heal / auto-seed if database is new or empty
            if ($isNewDb) {
                $migrator = new MigrationService($pdo);
                $migrator->migrate();
            } else {
                $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='colors'")->fetchColumn();
                if (!$tables) {
                    $migrator = new MigrationService($pdo);
                    $migrator->migrate();
                }
            }

            return self::$instance;
        } catch (PDOException $e) {
            throw new Exception("SQLite Connection Error: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Resolve database path - prioritizes self-contained api/data/ folder
     */
    public static function resolveDbPath(): string
    {
        if (self::$resolvedDbPath !== null) {
            return self::$resolvedDbPath;
        }

        $rawPath = (string)Env::get('DB_PATH', 'data/colormagic.sqlite');
        $rawPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $rawPath);

        // If rawPath is absolute
        if (
            str_starts_with($rawPath, '/') ||
            str_starts_with($rawPath, '\\') ||
            (strlen($rawPath) > 2 && $rawPath[1] === ':')
        ) {
            self::$resolvedDbPath = $rawPath;
            return self::$resolvedDbPath;
        }

        $baseDir = __DIR__; // api/v2/src/Database

        $candidates = [
            dirname($baseDir, 2) . DIRECTORY_SEPARATOR . $rawPath, // api/data/colormagic.sqlite (Self-contained)
            dirname($baseDir, 3) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . $rawPath,
            dirname($baseDir, 3) . DIRECTORY_SEPARATOR . $rawPath, // monorepo root
            dirname($baseDir, 4) . DIRECTORY_SEPARATOR . $rawPath, // parent directory
        ];

        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $candidates[] = $docRoot . DIRECTORY_SEPARATOR . $rawPath;
            $candidates[] = dirname($docRoot) . DIRECTORY_SEPARATOR . $rawPath;
        }

        // Return first existing path, or default to candidate[0] (api/data/colormagic.sqlite)
        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                self::$resolvedDbPath = $cand;
                return self::$resolvedDbPath;
            }
        }

        self::$resolvedDbPath = $candidates[0];
        return self::$resolvedDbPath;
    }

    /**
     * Reset connection instance (useful for testing or migrations)
     */
    public static function reset(): void
    {
        self::$instance = null;
        self::$resolvedDbPath = null;
    }
}
