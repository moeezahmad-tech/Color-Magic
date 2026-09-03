<?php

namespace ColorMagic\Services;

use PDO;
use Exception;

/**
 * Migration and Seeding Service for ColorMagic SQLite Database
 * Imports JSON datasets into ultra-fast indexed SQLite tables.
 */
class MigrationService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Run all migrations: create schema and seed data if empty
     */
    public function migrate(bool $force = false): array
    {
        $this->createSchema();
        return $this->seedAll($force);
    }

    /**
     * Define SQLite tables and high-performance indexes
     */
    public function createSchema(): void
    {
        $schema = <<<SQL
        -- Colors table
        CREATE TABLE IF NOT EXISTS colors (
            hex TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            slug TEXT NOT NULL,
            aliases TEXT DEFAULT '[]',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE UNIQUE INDEX IF NOT EXISTS idx_colors_slug ON colors(slug);
        CREATE INDEX IF NOT EXISTS idx_colors_name ON colors(name COLLATE NOCASE);

        -- Gradients table
        CREATE TABLE IF NOT EXISTS gradients (
            id TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            style TEXT,
            type TEXT,
            colors TEXT NOT NULL,
            css TEXT NOT NULL,
            angle INTEGER NULL,
            shape TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE INDEX IF NOT EXISTS idx_gradients_style ON gradients(style COLLATE NOCASE);
        CREATE INDEX IF NOT EXISTS idx_gradients_type ON gradients(type COLLATE NOCASE);
        CREATE INDEX IF NOT EXISTS idx_gradients_name ON gradients(name COLLATE NOCASE);

        -- Palettes table
        CREATE TABLE IF NOT EXISTS palettes (
            id TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            style TEXT,
            colors TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE INDEX IF NOT EXISTS idx_palettes_style ON palettes(style COLLATE NOCASE);
        CREATE INDEX IF NOT EXISTS idx_palettes_name ON palettes(name COLLATE NOCASE);

        -- Future Community Submissions / Dashboard table
        CREATE TABLE IF NOT EXISTS user_palettes (
            id TEXT PRIMARY KEY,
            user_id TEXT NULL,
            name TEXT NOT NULL,
            style TEXT NULL,
            colors TEXT NOT NULL,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE INDEX IF NOT EXISTS idx_user_palettes_user ON user_palettes(user_id);
        CREATE INDEX IF NOT EXISTS idx_user_palettes_status ON user_palettes(status);
SQL;

        $this->db->exec($schema);
    }

    /**
     * Seed all datasets from JSON files in an atomic transaction
     */
    public function seedAll(bool $force = false): array
    {
        $stats = [
            'colors' => 0,
            'gradients' => 0,
            'palettes' => 0,
            'skipped' => false
        ];

        // Check if already seeded
        if (!$force) {
            $colorCount = (int)$this->db->query("SELECT COUNT(*) FROM colors")->fetchColumn();
            $gradCount  = (int)$this->db->query("SELECT COUNT(*) FROM gradients")->fetchColumn();
            $palCount   = (int)$this->db->query("SELECT COUNT(*) FROM palettes")->fetchColumn();

            if ($colorCount > 0 && $gradCount > 0 && $palCount > 0) {
                return [
                    'colors' => $colorCount,
                    'gradients' => $gradCount,
                    'palettes' => $palCount,
                    'skipped' => true,
                    'message' => 'Database already populated.'
                ];
            }
        }

        $this->db->beginTransaction();

        try {
            if ($force) {
                $this->db->exec("DELETE FROM colors");
                $this->db->exec("DELETE FROM gradients");
                $this->db->exec("DELETE FROM palettes");
            }

            $stats['colors']    = $this->seedColors();
            $stats['gradients'] = $this->seedGradients();
            $stats['palettes']  = $this->seedPalettes();

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $stats;
    }

    /**
     * Seed colors table from color-names.json
     */
    public function seedColors(): int
    {
        $path = $this->findDataFile('color-names.json');
        if (!$path || !is_file($path)) {
            throw new Exception("Dataset color-names.json not found");
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in color-names.json");
        }

        $stmt = $this->db->prepare("
            INSERT OR REPLACE INTO colors (hex, name, slug, aliases)
            VALUES (:hex, :name, :slug, :aliases)
        ");

        $count = 0;
        foreach ($data as $key => $entry) {
            $hex = strtoupper(ltrim(trim((string)$key), '#'));
            $name = '';
            $slug = '';
            $aliases = '[]';

            if (is_array($entry)) {
                $name = (string)($entry['name'] ?? '');
                $slug = (string)($entry['slug'] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)));
                if (isset($entry['aliases']) && is_array($entry['aliases'])) {
                    $aliases = json_encode($entry['aliases'], JSON_UNESCAPED_UNICODE);
                }
            } else {
                $name = (string)$entry;
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            }

            if ($hex === '' || $name === '') {
                continue;
            }

            $stmt->execute([
                ':hex' => $hex,
                ':name' => $name,
                ':slug' => $slug,
                ':aliases' => $aliases
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Seed gradients table from gradients.json
     */
    public function seedGradients(): int
    {
        $path = $this->findDataFile('gradients.json');
        if (!$path || !is_file($path)) {
            throw new Exception("Dataset gradients.json not found");
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in gradients.json");
        }

        $stmt = $this->db->prepare("
            INSERT OR REPLACE INTO gradients (id, name, style, type, colors, css, angle, shape)
            VALUES (:id, :name, :style, :type, :colors, :css, :angle, :shape)
        ");

        $count = 0;
        foreach ($data as $item) {
            if (!is_array($item) || empty($item['id'])) {
                continue;
            }

            $id = (string)$item['id'];
            $name = (string)($item['name'] ?? '');
            $style = isset($item['style']) ? (string)$item['style'] : null;
            $type = isset($item['type']) ? (string)$item['type'] : 'linear';
            $colors = json_encode($item['colors'] ?? [], JSON_UNESCAPED_UNICODE);
            $css = (string)($item['css'] ?? '');
            $angle = isset($item['angle']) ? (int)$item['angle'] : null;
            $shape = isset($item['shape']) ? (string)$item['shape'] : null;

            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':style' => $style,
                ':type' => $type,
                ':colors' => $colors,
                ':css' => $css,
                ':angle' => $angle,
                ':shape' => $shape
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Seed palettes table from palettes.json
     */
    public function seedPalettes(): int
    {
        $path = $this->findDataFile('palettes.json');
        if (!$path || !is_file($path)) {
            throw new Exception("Dataset palettes.json not found");
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            throw new Exception("Invalid JSON format in palettes.json");
        }

        $stmt = $this->db->prepare("
            INSERT OR REPLACE INTO palettes (id, name, style, colors)
            VALUES (:id, :name, :style, :colors)
        ");

        $count = 0;
        foreach ($data as $item) {
            if (!is_array($item) || empty($item['id'])) {
                continue;
            }

            $id = (string)$item['id'];
            $name = (string)($item['name'] ?? '');
            $style = isset($item['style']) ? (string)$item['style'] : null;
            $colors = json_encode($item['colors'] ?? [], JSON_UNESCAPED_UNICODE);

            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':style' => $style,
                ':colors' => $colors
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Helper to find JSON file - prioritizes self-contained api/data/ folder
     */
    private function findDataFile(string $filename): ?string
    {
        $baseDir = __DIR__; // api/v2/src/Services

        $candidates = [
            dirname($baseDir, 2) . '/data/' . $filename, // api/data/ (Self-contained)
            dirname($baseDir, 2) . '/' . $filename,       // api/
            dirname($baseDir, 3) . '/api/data/' . $filename,
            dirname($baseDir, 3) . '/data/' . $filename,  // monorepo data/
            dirname($baseDir, 4) . '/data/' . $filename,  // parent data/
        ];

        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $candidates[] = $docRoot . '/data/' . $filename;
            $candidates[] = $docRoot . '/' . $filename;
            $candidates[] = dirname($docRoot) . '/data/' . $filename;
        }

        foreach ($candidates as $path) {
            $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
            if (is_file($normalized) && is_readable($normalized)) {
                return $normalized;
            }
        }

        return null;
    }
}
