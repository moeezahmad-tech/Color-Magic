<?php

namespace ColorMagic\Services;

use PDO;
use Exception;
use Throwable;

/**
 * High-Performance SQLite Database Migration & Seeding Service (PHP 7.0+ Compatible)
 */
class MigrationService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run full database migration and seed data
     */
    public function migrate()
    {
        $this->createTables();
        $this->createIndexes();

        $colorsCount    = $this->seedColors();
        $gradientsCount = $this->seedGradients();
        $palettesCount  = $this->seedPalettes();

        return [
            'status'    => 'success',
            'colors'    => $colorsCount,
            'gradients' => $gradientsCount,
            'palettes'  => $palettesCount,
        ];
    }

    /**
     * Create relational tables
     */
    public function createTables()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS colors (
                hex TEXT PRIMARY KEY NOT NULL,
                name TEXT NOT NULL,
                slug TEXT NOT NULL,
                aliases TEXT NOT NULL DEFAULT '[]',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS gradients (
                id TEXT PRIMARY KEY NOT NULL,
                name TEXT NOT NULL,
                style TEXT NOT NULL,
                type TEXT NOT NULL,
                colors TEXT NOT NULL,
                css TEXT NOT NULL,
                angle INTEGER DEFAULT NULL,
                shape TEXT DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS palettes (
                id TEXT PRIMARY KEY NOT NULL,
                name TEXT NOT NULL,
                style TEXT NOT NULL,
                colors TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS user_palettes (
                id TEXT PRIMARY KEY NOT NULL,
                user_id TEXT DEFAULT NULL,
                name TEXT NOT NULL,
                style TEXT NOT NULL DEFAULT 'Custom',
                colors TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    /**
     * Create composite indexes
     */
    public function createIndexes()
    {
        $this->pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_colors_slug ON colors(slug);
            CREATE INDEX IF NOT EXISTS idx_colors_name ON colors(name);

            CREATE INDEX IF NOT EXISTS idx_gradients_style ON gradients(style);
            CREATE INDEX IF NOT EXISTS idx_gradients_type ON gradients(type);

            CREATE INDEX IF NOT EXISTS idx_palettes_style ON palettes(style);
            CREATE INDEX IF NOT EXISTS idx_user_palettes_status ON user_palettes(status);
        ");
    }

    /**
     * Seed colors from color-names.json
     */
    private function seedColors()
    {
        $filePath = $this->resolveDataPath('color-names.json');
        if (!file_exists($filePath)) {
            return 0;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return 0;
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT OR REPLACE INTO colors (hex, name, slug, aliases)
                VALUES (:hex, :name, :slug, :aliases)
            ");

            $count = 0;
            foreach ($data as $hex => $item) {
                $cleanHex = strtoupper(ltrim((string)$hex, '#'));
                $name = is_array($item) ? ($item['name'] ?? '') : (string)$item;
                $slug = is_array($item) ? ($item['slug'] ?? $this->slugify($name)) : $this->slugify($name);
                $aliases = is_array($item) && isset($item['aliases']) && is_array($item['aliases'])
                    ? json_encode($item['aliases'], JSON_UNESCAPED_UNICODE)
                    : '[]';

                $stmt->execute([
                    ':hex'     => $cleanHex,
                    ':name'    => $name,
                    ':slug'    => $slug,
                    ':aliases' => $aliases,
                ]);
                $count++;
            }

            $this->pdo->commit();
            return $count;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Seed gradients from gradients.json
     */
    private function seedGradients()
    {
        $filePath = $this->resolveDataPath('gradients.json');
        if (!file_exists($filePath)) {
            return 0;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return 0;
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT OR REPLACE INTO gradients (id, name, style, type, colors, css, angle, shape)
                VALUES (:id, :name, :style, :type, :colors, :css, :angle, :shape)
            ");

            $count = 0;
            foreach ($data as $item) {
                if (!isset($item['id'], $item['name'])) {
                    continue;
                }

                $stmt->execute([
                    ':id'     => (string)$item['id'],
                    ':name'   => (string)$item['name'],
                    ':style'  => (string)($item['style'] ?? 'General'),
                    ':type'   => (string)($item['type'] ?? 'linear'),
                    ':colors' => json_encode($item['colors'] ?? [], JSON_UNESCAPED_UNICODE),
                    ':css'    => (string)($item['css'] ?? ''),
                    ':angle'  => isset($item['angle']) ? (int)$item['angle'] : null,
                    ':shape'  => isset($item['shape']) ? (string)$item['shape'] : null,
                ]);
                $count++;
            }

            $this->pdo->commit();
            return $count;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Seed palettes from palettes.json
     */
    private function seedPalettes()
    {
        $filePath = $this->resolveDataPath('palettes.json');
        if (!file_exists($filePath)) {
            return 0;
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return 0;
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT OR REPLACE INTO palettes (id, name, style, colors)
                VALUES (:id, :name, :style, :colors)
            ");

            $count = 0;
            foreach ($data as $item) {
                if (!isset($item['id'], $item['name'])) {
                    continue;
                }

                $stmt->execute([
                    ':id'     => (string)$item['id'],
                    ':name'   => (string)$item['name'],
                    ':style'  => (string)($item['style'] ?? 'General'),
                    ':colors' => json_encode($item['colors'] ?? [], JSON_UNESCAPED_UNICODE),
                ]);
                $count++;
            }

            $this->pdo->commit();
            return $count;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Generate URL slug
     */
    private function slugify(string $text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text ?: 'n-a';
    }

    /**
     * Resolve data file path
     */
    private function resolveDataPath(string $fileName)
    {
        $candidates = [
            dirname(__DIR__, 3) . '/data/' . $fileName,
            dirname(__DIR__, 2) . '/data/' . $fileName,
            dirname(__DIR__, 3) . '/' . $fileName,
            dirname(__DIR__, 2) . '/' . $fileName,
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                return $cand;
            }
        }

        return $candidates[0];
    }
}
