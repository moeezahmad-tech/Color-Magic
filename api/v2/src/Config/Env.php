<?php

namespace ColorMagic\Config;

/**
 * Lightweight Environment Configuration Loader (PHP 7.0+ Compatible)
 */
class Env
{
    private static $env = [];
    private static $loaded = false;

    /**
     * Load environment variables from standalone or sibling .env files
     */
    public static function load()
    {
        if (self::$loaded) {
            return;
        }

        $baseDir = dirname(__DIR__, 2); // api/
        $candidates = [
            $baseDir . '/.env.api.colormagic',
            dirname($baseDir) . '/.env.api.colormagic',
            dirname($baseDir, 2) . '/.env.api.colormagic',
            $baseDir . '/.env',
            dirname($baseDir) . '/.env'
        ];

        foreach ($candidates as $filePath) {
            if (file_exists($filePath) && is_readable($filePath)) {
                self::parseEnvFile($filePath);
                self::$loaded = true;
                return;
            }
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable with fallback
     */
    public static function get(string $key, $default = null)
    {
        self::load();
        return self::$env[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Parse key=value lines from .env
     */
    private static function parseEnvFile(string $filePath)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                self::$env[$key] = $value;
            }
        }
    }
}
