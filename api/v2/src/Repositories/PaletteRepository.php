<?php

namespace ColorMagic\Repositories;

use PDO;
use Throwable;
use ColorMagic\Database\Database;

/**
 * Palette Repository
 * Handles curated color palettes and community submissions with fail-safe JSON fallback.
 */
class PaletteRepository
{
    private ?PDO $db = null;
    private ?array $fallbackData = null;

    public function __construct(?PDO $db = null)
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
     * Find single palette by ID
     */
    public function findById(string $id): ?array
    {
        $id = trim($id);
        if ($id === '') {
            return null;
        }

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

        $data = $this->getFallbackData();
        foreach ($data as $item) {
            if (is_array($item) && isset($item['id']) && $item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Search and paginate palettes
     */
    public function search(
        string $q = '',
        ?string $style = null,
        int $page = 1,
        int $limit = 50
    ): array {
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
        $filtered = array_values(array_filter($data, static function ($item) use ($q, $style) {
            if (!is_array($item)) return false;
            if ($style !== null && trim($style) !== '' && strtolower((string)($item['style'] ?? '')) !== strtolower(trim($style))) {
                return false;
            }
            if (trim($q) !== '') {
                $qLower = strtolower(trim($q));
                $nameMatch = str_contains(strtolower((string)($item['name'] ?? '')), $qLower);
                $colorMatch = false;
                if (isset($item['colors']) && is_array($item['colors'])) {
                    foreach ($item['colors'] as $c) {
                        if (str_contains(strtolower((string)$c), $qLower)) {
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
    public function all(): array
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
    public function submitUserPalette(array $data): array
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
    private function getFallbackData(): array
    {
        if ($this->fallbackData !== null) {
            return $this->fallbackData;
        }

        $candidates = [
            dirname(__DIR__, 2) . '/data/palettes.json',
            dirname(__DIR__, 2) . '/palettes.json',
            dirname(__DIR__, 3) . '/data/palettes.json',
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
    private function formatPalette(array $row): array
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
