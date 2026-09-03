<?php

namespace ColorMagic\Repositories;

use PDO;
use Throwable;
use ColorMagic\Database\Database;

/**
 * Palette Repository (PHP 7.0+ Compatible)
 */
class PaletteRepository
{
    private $db = null;
    private $fallbackData = null;

    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            try {
                $this->db = Database::getConnection();
            } catch (Throwable $t) {
                $this->db = null;
            }
        }
    }

    /**
     * Generate URL slug from palette name
     */
    private function slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Find single palette by ID or by URL slug
     */
    public function findById(string $id)
    {
        $id = trim($id);
        if ($id === '') {
            return null;
        }

        // 1. Try exact ID match first (e.g. "palette_760")
        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("
                    SELECT id, name, style, colors 
                    FROM palettes 
                    WHERE id = :id 
                    LIMIT 1
                ");
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();
                if ($row) {
                    return $this->formatPalette($row);
                }
            } catch (Throwable $t) {
                // Fall through
            }
        }

        // 2. Try exact ID in fallback JSON
        $data = $this->getFallbackData();
        foreach ($data as $item) {
            if (is_array($item) && isset($item['id']) && $item['id'] === $id) {
                return $item;
            }
        }

        // 3. If id looks like a slug (not palette_NNN), try slug-based lookup
        if (strpos($id, 'palette_') !== 0 && strpos($id, 'user_pal_') !== 0) {
            return $this->findBySlug($id);
        }

        return null;
    }

    /**
     * Find a palette by its URL slug (derived from name).
     * Handles duplicate names by checking slug-with-id suffix (e.g. "taxbuzz-palette-760").
     */
    public function findBySlug(string $slug)
    {
        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        $data = $this->getAllForSlugLookup();

        // Build slug -> name count map to detect duplicates
        $nameCount = [];
        foreach ($data as $item) {
            if (!is_array($item) || !isset($item['name'])) continue;
            $s = $this->slugify($item['name']);
            $nameCount[$s] = ($nameCount[$s] ?? 0) + 1;
        }

        // Match: exact slug, or slug-with-id-suffix for duplicates
        foreach ($data as $item) {
            if (!is_array($item) || !isset($item['name'], $item['id'])) continue;
            $s = $this->slugify($item['name']);
            if ($s === $slug) {
                return $item;
            }
            // For duplicate names, check slug + "-" + numeric ID suffix
            if (isset($nameCount[$s]) && $nameCount[$s] > 1) {
                $numericId = str_replace('palette_', '', $item['id']);
                if ($s . '-' . $numericId === $slug) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Get all palette data for slug lookups (prefers DB, falls back to JSON)
     */
    private function getAllForSlugLookup(): array
    {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("SELECT id, name, style, colors FROM palettes ORDER BY id ASC");
                return array_map([$this, 'formatPalette'], $stmt->fetchAll());
            } catch (Throwable $t) {
                // Fall through
            }
        }
        return $this->getFallbackData();
    }

    /**
     * Search and paginate palettes
     */
    public function search(
        string $q = '',
        $style = null,
        int $page = 1,
        int $limit = 50
    ) {
        $page   = max(1, $page);
        $limit  = max(1, min(200, $limit));
        $offset = ($page - 1) * $limit;

        if ($this->db !== null) {
            try {
                $where = [];
                $params = [];

                if (trim($q) !== '') {
                    $where[] = "(name LIKE :q_name OR style LIKE :q_style OR colors LIKE :q_colors)";
                    $term = '%' . trim($q) . '%';
                    $params[':q_name']   = $term;
                    $params[':q_style']  = $term;
                    $params[':q_colors'] = $term;
                }

                if ($style !== null && trim($style) !== '') {
                    $where[] = "LOWER(style) = :style";
                    $params[':style'] = strtolower(trim($style));
                }

                $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

                $countStmt = $this->db->prepare("SELECT COUNT(*) FROM palettes {$whereSql}");
                $countStmt->execute($params);
                $total = (int)$countStmt->fetchColumn();

                $sql = "SELECT id, name, style, colors FROM palettes {$whereSql} ORDER BY id ASC LIMIT :limit OFFSET :offset";
                $stmt = $this->db->prepare($sql);
                foreach ($params as $k => $v) {
                    $stmt->bindValue($k, $v);
                }
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                $rows = $stmt->fetchAll();
                return [
                    'total' => $total,
                    'page'  => $page,
                    'limit' => $limit,
                    'items' => array_map([$this, 'formatPalette'], $rows)
                ];
            } catch (Throwable $t) {
                // Fall through
            }
        }

        // JSON Fallback
        $data = $this->getFallbackData();
        $filtered = array_values(array_filter($data, function ($item) use ($q, $style) {
            if (!is_array($item)) return false;
            if ($style !== null && trim($style) !== '' && strtolower((string)($item['style'] ?? '')) !== strtolower(trim($style))) {
                return false;
            }
            if (trim($q) !== '') {
                $qLower = strtolower(trim($q));
                $nameMatch = strpos(strtolower((string)($item['name'] ?? '')), $qLower) !== false;
                $colorMatch = false;
                if (isset($item['colors']) && is_array($item['colors'])) {
                    foreach ($item['colors'] as $c) {
                        if (strpos(strtolower((string)$c), $qLower) !== false) {
                            $colorMatch = true;
                            break;
                        }
                    }
                }
                if (!$nameMatch && !$colorMatch) return false;
            }
            return true;
        }));

        $total = count($filtered);
        $paged = array_slice($filtered, $offset, $limit);

        return [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'items' => $paged
        ];
    }

    /**
     * Get all curated palettes
     */
    public function all()
    {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("SELECT id, name, style, colors FROM palettes ORDER BY id ASC");
                return array_map([$this, 'formatPalette'], $stmt->fetchAll());
            } catch (Throwable $t) {
                // Fall through
            }
        }

        return $this->getFallbackData();
    }

    /**
     * Submit community palette
     */
    public function submitUserPalette(array $data)
    {
        $id      = 'user_pal_' . uniqid('', true);
        $userId  = $data['user_id'] ?? null;
        $name    = trim($data['name'] ?? 'Untitled Palette');
        $style   = trim($data['style'] ?? 'Custom');
        $colors  = json_encode($data['colors'] ?? [], JSON_UNESCAPED_UNICODE);

        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO user_palettes (id, user_id, name, style, colors, status)
                    VALUES (:id, :user_id, :name, :style, :colors, 'pending')
                ");
                $stmt->execute([
                    ':id'      => $id,
                    ':user_id' => $userId,
                    ':name'    => $name,
                    ':style'   => $style,
                    ':colors'  => $colors,
                ]);
            } catch (Throwable $t) {
                // Ignore
            }
        }

        return [
            'id'     => $id,
            'name'   => $name,
            'style'  => $style,
            'status' => 'pending'
        ];
    }

    /**
     * Load fallback dataset
     */
    private function getFallbackData()
    {
        if ($this->fallbackData !== null) {
            return $this->fallbackData;
        }

        $candidates = [
            dirname(__DIR__, 3) . '/data/palettes.json',
            dirname(__DIR__, 2) . '/data/palettes.json',
            dirname(__DIR__, 3) . '/palettes.json',
            dirname(__DIR__, 2) . '/palettes.json',
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_readable($cand)) {
                $raw = json_decode(file_get_contents($cand), true);
                if (is_array($raw)) {
                    $this->fallbackData = $raw;
                    return $this->fallbackData;
                }
            }
        }

        $this->fallbackData = [];
        return $this->fallbackData;
    }

    /**
     * Format database row
     */
    private function formatPalette(array $row)
    {
        $colors = json_decode($row['colors'] ?? '[]', true);
        if (!is_array($colors)) {
            $colors = [];
        }

        return [
            'id'     => $row['id'],
            'name'   => $row['name'],
            'style'  => $row['style'],
            'colors' => $colors
        ];
    }
}
