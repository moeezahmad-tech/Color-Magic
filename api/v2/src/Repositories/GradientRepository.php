<?php

namespace ColorMagic\Repositories;

use PDO;
use Throwable;
use ColorMagic\Database\Database;

/**
 * Gradient Repository (PHP 7.0+ Compatible)
 */
class GradientRepository
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
     * Find single gradient by ID
     */
    public function findById(string $id)
    {
        $id = trim($id);
        if ($id === '') {
            return null;
        }

        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("
                    SELECT id, name, style, type, colors, css, angle, shape 
                    FROM gradients 
                    WHERE id = :id 
                    LIMIT 1
                ");
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();
                if ($row) {
                    return $this->formatGradient($row);
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
     * Search and paginate gradients
     */
    public function search(
        string $q = '',
        $style = null,
        $type = null,
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
                    $where[] = "(name LIKE :q_name OR style LIKE :q_style OR type LIKE :q_type OR colors LIKE :q_colors)";
                    $term = '%' . trim($q) . '%';
                    $params[':q_name']   = $term;
                    $params[':q_style']  = $term;
                    $params[':q_type']   = $term;
                    $params[':q_colors'] = $term;
                }

                if ($style !== null && trim($style) !== '') {
                    $where[] = "LOWER(style) = :style";
                    $params[':style'] = strtolower(trim($style));
                }

                if ($type !== null && trim($type) !== '') {
                    $where[] = "LOWER(type) = :type";
                    $params[':type'] = strtolower(trim($type));
                }

                $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

                $countStmt = $this->db->prepare("SELECT COUNT(*) FROM gradients {$whereSql}");
                $countStmt->execute($params);
                $total = (int)$countStmt->fetchColumn();

                $sql = "SELECT id, name, style, type, colors, css, angle, shape FROM gradients {$whereSql} ORDER BY id ASC LIMIT :limit OFFSET :offset";
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
                    'items' => array_map([$this, 'formatGradient'], $rows)
                ];
            } catch (Throwable $t) {
                // Fall through
            }
        }

        // JSON Fallback
        $data = $this->getFallbackData();
        $filtered = array_values(array_filter($data, function ($item) use ($q, $style, $type) {
            if (!is_array($item)) return false;
            if ($style !== null && trim($style) !== '' && strtolower((string)($item['style'] ?? '')) !== strtolower(trim($style))) {
                return false;
            }
            if ($type !== null && trim($type) !== '' && strtolower((string)($item['type'] ?? '')) !== strtolower(trim($type))) {
                return false;
            }
            if (trim($q) !== '') {
                $qLower = strtolower(trim($q));
                $nameMatch = strpos(strtolower((string)($item['name'] ?? '')), $qLower) !== false;
                $styleMatch = strpos(strtolower((string)($item['style'] ?? '')), $qLower) !== false;
                $typeMatch = strpos(strtolower((string)($item['type'] ?? '')), $qLower) !== false;
                if (!$nameMatch && !$styleMatch && !$typeMatch) return false;
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
     * Get all gradients
     */
    public function all(): array
    {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("SELECT id, name, style, type, colors, css, angle, shape FROM gradients ORDER BY id ASC");
                return array_map([$this, 'formatGradient'], $stmt->fetchAll());
            } catch (Throwable $t) {
                // Fall through
            }
        }

        return $this->getFallbackData();
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
            dirname(__DIR__, 2) . '/data/gradients.json',
            dirname(__DIR__, 2) . '/gradients.json',
            dirname(__DIR__, 3) . '/data/gradients.json',
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
     * Format row
     */
    private function formatGradient(array $row): array
    {
        $colors = json_decode($row['colors'] ?? '[]', true);
        if (!is_array($colors)) {
            $colors = [];
        }

        $formatted = [
            'id'     => $row['id'],
            'name'   => $row['name'],
            'style'  => $row['style'],
            'type'   => $row['type'],
            'colors' => $colors,
            'css'    => $row['css'],
        ];

        if ($row['angle'] !== null) {
            $formatted['angle'] = (int)$row['angle'];
        }

        if ($row['shape'] !== null) {
            $formatted['shape'] = $row['shape'];
        }

        return $formatted;
    }
}
