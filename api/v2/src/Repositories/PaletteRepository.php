<?php

namespace ColorMagic\Repositories;

use PDO;
use ColorMagic\Database\Database;

/**
 * Palette Repository
 * Handles curated color palettes and community palette submissions.
 */
class PaletteRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
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

        $stmt = $this->db->prepare("
            SELECT id, name, style, colors 
            FROM palettes 
            WHERE id = :id 
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->formatPalette($row) : null;
    }

    /**
     * Search and paginate palettes with optional style filter
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

        // Count total matching
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM palettes {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Fetch paginated
        $sql = "SELECT id, name, style, colors FROM palettes {$whereSql} ORDER BY id ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $items = array_map([$this, 'formatPalette'], $rows);

        return [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'items' => $items
        ];
    }

    /**
     * Get all curated palettes
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT id, name, style, colors FROM palettes ORDER BY id ASC");
        return array_map([$this, 'formatPalette'], $stmt->fetchAll());
    }

    /**
     * Submit user palette (Future User Dashboard & Monetization feature)
     */
    public function submitUserPalette(array $data): array
    {
        $id      = 'user_pal_' . uniqid('', true);
        $userId  = $data['user_id'] ?? null;
        $name    = trim($data['name'] ?? 'Untitled Palette');
        $style   = trim($data['style'] ?? 'Custom');
        $colors  = json_encode($data['colors'] ?? [], JSON_UNESCAPED_UNICODE);

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

        return [
            'id'     => $id,
            'name'   => $name,
            'style'  => $style,
            'status' => 'pending'
        ];
    }

    /**
     * Format database row into standardized palette object
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
