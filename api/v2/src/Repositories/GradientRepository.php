<?php

namespace ColorMagic\Repositories;

use PDO;
use ColorMagic\Database\Database;

/**
 * Gradient Repository
 * Provides fast indexed retrieval and filtering for CSS gradients.
 */
class GradientRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Find single gradient by ID
     */
    public function findById(string $id): ?array
    {
        $id = trim($id);
        if ($id === '') {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT id, name, style, type, colors, css, angle, shape 
            FROM gradients 
            WHERE id = :id 
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->formatGradient($row) : null;
    }

    /**
     * Search and paginate gradients with style/type filters
     */
    public function search(
        string $q = '',
        ?string $style = null,
        ?string $type = null,
        int $page = 1,
        int $limit = 50
    ): array {
        $page   = max(1, $page);
        $limit  = max(1, min(200, $limit));
        $offset = ($page - 1) * $limit;

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

        // Count total matching
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM gradients {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Fetch paginated
        $sql = "SELECT id, name, style, type, colors, css, angle, shape FROM gradients {$whereSql} ORDER BY id ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $items = array_map([$this, 'formatGradient'], $rows);

        return [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'items' => $items
        ];
    }

    /**
     * Get all gradients
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT id, name, style, type, colors, css, angle, shape FROM gradients ORDER BY id ASC");
        return array_map([$this, 'formatGradient'], $stmt->fetchAll());
    }

    /**
     * Format database row into standardized gradient object
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
