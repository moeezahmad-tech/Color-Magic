<?php

namespace ColorMagic\Repositories;

use PDO;
use ColorMagic\Database\Database;

/**
 * Color Repository
 * Provides indexed O(1) lookups and sub-millisecond paginated queries.
 */
class ColorRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Find single color by Hex code
     */
    public function findByHex(string $hex): ?array
    {
        $cleanHex = strtoupper(ltrim(trim($hex), '#'));
        if ($cleanHex === '') {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT hex, name, slug, aliases 
            FROM colors 
            WHERE hex = :hex 
            LIMIT 1
        ");
        $stmt->execute([':hex' => $cleanHex]);
        $row = $stmt->fetch();

        return $row ? $this->formatColor($row) : null;
    }

    /**
     * Find single color by Slug
     */
    public function findBySlug(string $slug): ?array
    {
        $cleanSlug = strtolower(trim($slug));
        if ($cleanSlug === '') {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT hex, name, slug, aliases 
            FROM colors 
            WHERE slug = :slug 
            LIMIT 1
        ");
        $stmt->execute([':slug' => $cleanSlug]);
        $row = $stmt->fetch();

        return $row ? $this->formatColor($row) : null;
    }

    /**
     * Search and paginate colors
     */
    public function search(string $q = '', int $page = 1, int $limit = 50): array
    {
        $page  = max(1, $page);
        $limit = max(1, min(200, $limit));
        $offset = ($page - 1) * $limit;

        $params = [];
        $whereSql = "";

        $q = trim($q);
        if ($q !== '') {
            $whereSql = "WHERE name LIKE :q_name OR hex LIKE :q_hex OR slug LIKE :q_slug";
            $searchTerm = '%' . $q . '%';
            $params[':q_name'] = $searchTerm;
            $params[':q_hex']  = '%' . strtoupper(ltrim($q, '#')) . '%';
            $params[':q_slug'] = '%' . strtolower($q) . '%';
        }

        // Count total matching
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM colors {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Fetch paginated
        $sql = "SELECT hex, name, slug, aliases FROM colors {$whereSql} ORDER BY name ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $items = array_map([$this, 'formatColor'], $rows);

        return [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'items' => $items
        ];
    }

    /**
     * Get all colors (as dictionary keyed by hex or list)
     */
    public function all(bool $asDictionary = false): array
    {
        $stmt = $this->db->query("SELECT hex, name, slug, aliases FROM colors ORDER BY name ASC");
        $rows = $stmt->fetchAll();

        if ($asDictionary) {
            $dict = [];
            foreach ($rows as $row) {
                $formatted = $this->formatColor($row);
                $dict[$row['hex']] = [
                    'hex'     => $formatted['hex'],
                    'name'    => $formatted['name'],
                    'slug'    => $formatted['slug'],
                    'aliases' => $formatted['aliases']
                ];
            }
            return $dict;
        }

        return array_map([$this, 'formatColor'], $rows);
    }

    /**
     * Format database row into standardized API color object
     */
    private function formatColor(array $row): array
    {
        $aliases = json_decode($row['aliases'] ?? '[]', true);
        if (!is_array($aliases)) {
            $aliases = [];
        }

        return [
            'hex'     => '#' . $row['hex'],
            'raw_hex' => $row['hex'],
            'name'    => $row['name'],
            'slug'    => $row['slug'],
            'aliases' => $aliases
        ];
    }
}
