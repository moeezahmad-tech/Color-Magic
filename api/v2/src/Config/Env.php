<?php

namespace ColorMagic\Config;

/**
 * High-performance Environment Loader & Configuration Manager
 * Supports self-contained api/ folder as well as monorepo & server sibling layouts.
 */
class Env
{
    private static array $env = [];
    private static bool $loaded = false;
    private static ?string $loadedFile = null;

    /**
     * Load environment variables from .env.api.colormagic or fallbacks
     */
    public static function load(?string $customPath = null): void
    {
        if (self::$loaded && $customPath === null) {
            return;
        }

        $candidates = [];

        if ($customPath !== null) {
            $candidates[] = $customPath;
        }

        $baseDir = __DIR__; // api/v2/src/Config

        // 1. Direct API folder .env.api.colormagic (Self-contained)
        $candidates[] = dirname($baseDir, 2) . '/.env.api.colormagic'; // api/.env.api.colormagic
        $candidates[] = dirname($baseDir, 2) . '/.env';                 // api/.env

        // 2. Server parent / Monorepo root .env.api.colormagic
        $candidates[] = dirname($baseDir, 3) . '/.env.api.colormagic'; // monorepo root
        $candidates[] = dirname($baseDir, 4) . '/.env.api.colormagic'; // parent directory

        // 3. Document Root paths
        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $candidates[] = $docRoot . '/.env.api.colormagic';
            $candidates[] = $docRoot . '/.env';
            $candidates[] = dirname($docRoot) . '/.env.api.colormagic';
        }

        foreach ($candidates as $filePath) {
            $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath);
            if (is_file($normalized) && is_readable($normalized)) {
                self::parseEnvFile($normalized);
                self::$loadedFile = $normalized;
                break;
            }
        }

        // Populate with system environment variables
        foreach ($_ENV as $k => $v) {
            if (!isset(self::$env[$k])) {
                self::$env[$k] = $v;
            }
        }
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'APP_') || str_starts_with($k, 'DB_') || str_starts_with($k, 'API_')) {
                if (!isset(self::$env[$k])) {
                    self::$env[$k] = $v;
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Parse KEY=VALUE format
     */
    private static function parseEnvFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key   = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            // Strip enclosing quotes
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            self::$env[$key] = self::castValue($value);
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = self::$env[$key];
            }
        }
    }

    /**
     * Auto-cast common boolean, null, numeric strings
     */
    private static function castValue(string $val): mixed
    {
        $lower = strtolower($val);
        if ($lower === 'true' || $lower === '(true)') return true;
        if ($lower === 'false' || $lower === '(false)') return false;
        if ($lower === 'null' || $lower === '(null)') return null;
        if (is_numeric($val) && !str_starts_with($val, '0')) {
            return str_contains($val, '.') ? (float)$val : (int)$val;
        }
        return $val;
    }

    /**
     * Get environment value with fallback
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Get loaded configuration file path
     */
    public static function getLoadedFile(): ?string
    {
        return self::$loadedFile;
    }

    /**
     * Return all loaded config key-value pairs
     */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }
        return self::$env;
    }
}
