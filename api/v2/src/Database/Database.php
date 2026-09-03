<?php

namespace ColorMagic\Database;

use PDO;
use PDOException;
use Exception;
use Throwable;
use ColorMagic\Config\Env;
use ColorMagic\Services\MigrationService;

/**
 * High-Performance SQLite Connection Manager (PHP 7.0+ Compatible)
 */
class Database
{
    private static $instance = null;
    private static $resolvedDbPath = null;

    /**
     * Get or initialize the PDO SQLite connection singleton
     */
    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Check if PDO SQLite extension is installed
        if (!extension_loaded('pdo_sqlite') && !in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            throw new Exception("PHP PDO SQLite extension (pdo_sqlite) is not enabled on this server.");
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
            ]);

            // Safe PRAGMAs
            try {
                $pdo->exec("PRAGMA journal_mode = WAL;");
                $pdo->exec("PRAGMA synchronous = NORMAL;");
                $pdo->exec("PRAGMA cache_size = -64000;");
                $pdo->exec("PRAGMA temp_store = MEMORY;");
                $pdo->exec("PRAGMA mmap_size = 268435456;");
                $pdo->exec("PRAGMA busy_timeout = 5000;");
            } catch (Throwable $t) {
                // Ignore pragma failures on restricted hosts
            }

            self::$instance = $pdo;

            // Auto-heal / auto-seed if database is new or empty
            try {
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
            } catch (Throwable $t) {
                // Seeder error safe
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
            strncmp($rawPath, '/', 1) === 0 ||
            strncmp($rawPath, '\\', 1) === 0 ||
            (strlen($rawPath) > 2 && $rawPath[1] === ':')
        ) {
            self::$resolvedDbPath = $rawPath;
            return self::$resolvedDbPath;
        }

        $baseDir = __DIR__; // api/v2/src/Database

        $candidates = [
            dirname($baseDir, 2) . DIRECTORY_SEPARATOR . $rawPath, // api/data/colormagic.sqlite
            dirname($baseDir, 3) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . $rawPath,
            dirname($baseDir, 3) . DIRECTORY_SEPARATOR . $rawPath, // monorepo root
            dirname($baseDir, 4) . DIRECTORY_SEPARATOR . $rawPath, // parent directory
        ];

        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $candidates[] = $docRoot . DIRECTORY_SEPARATOR . $rawPath;
            $candidates[] = dirname($docRoot) . DIRECTORY_SEPARATOR . $rawPath;
        }

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
     * Reset connection instance
     */
    public static function reset(): void
    {
        self::$instance = null;
        self::$resolvedDbPath = null;
    }
}
