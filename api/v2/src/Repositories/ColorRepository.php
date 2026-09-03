<?php

namespace ColorMagic\Repositories;

use PDO;
use Throwable;
use ColorMagic\Database\Database;

/**
 * Color Repository (PHP 7.0+ Compatible)
 */
class ColorRepository
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
     * Find single color by Hex code
     */
    public function findByHex(string $hex)
    {
        $cleanHex = strtoupper(ltrim(trim($hex), '#'));
        if ($cleanHex === '') {
            return null;
        }

        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("
                    SELECT hex, name, slug, aliases 
                    FROM colors 
                    WHERE hex = :hex 
                    LIMIT 1
                ");
                $stmt->execute([':hex' => $cleanHex]);
                $row = $stmt->fetch();
                if ($row) {
                    return $this->formatColor($row);
                }
            } catch (Throwable $t) {
                // Fall through
            }
        }

        $data = $this->getFallbackData();
        if (isset($data[$cleanHex])) {
            $entry = $data[$cleanHex];
            $name = is_array($entry) ? ($entry['name'] ?? '') : (string)$entry;
            $slug = is_array($entry) ? ($entry['slug'] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name))) : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $aliases = is_array($entry) && isset($entry['aliases']) && is_array($entry['aliases']) ? $entry['aliases'] : [];

            return [
                'hex'     => '#' . $cleanHex,
                'raw_hex' => $cleanHex,
                'name'    => $name,
                'slug'    => $slug,
                'aliases' => $aliases
            ];
        }

        return null;
    }

    /**
     * Find single color by Slug
     */
    public function findBySlug(string $slug)
    {
        $cleanSlug = strtolower(trim($slug));
        if ($cleanSlug === '') {
            return null;
        }

        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare("
                    SELECT hex, name, slug, aliases 
                    FROM colors 
                    WHERE slug = :slug 
                    LIMIT 1
                ");
                $stmt->execute([':slug' => $cleanSlug]);
                $row = $stmt->fetch();
                if ($row) {
                    return $this->formatColor($row);
                }
            } catch (Throwable $t) {
                // Fall through
            }
        }

        $data = $this->getFallbackData();
        foreach ($data as $key => $entry) {
            $name = is_array($entry) ? ($entry['name'] ?? '') : (string)$entry;
            $s = is_array($entry) && isset($entry['slug']) ? (string)$entry['slug'] : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            if ($s === $cleanSlug) {
                $cleanHex = strtoupper(ltrim((string)$key, '#'));
                $aliases = is_array($entry) && isset($entry['aliases']) && is_array($entry['aliases']) ? $entry['aliases'] : [];
                return [
                    'hex'     => '#' . $cleanHex,
                    'raw_hex' => $cleanHex,
                    'name'    => $name,
                    'slug'    => $s,
                    'aliases' => $aliases
                ];
            }
        }

        return null;
    }

    /**
     * Search and paginate colors
     */
    public function search(string $q = '', int $page = 1, int $limit = 50): array
    {
        $page   = max(1, $page);
        $limit  = max(1, min(200, $limit));
        $offset = ($page - 1) * $limit;
        $q      = trim($q);

        if ($this->db !== null) {
            try {
                $params = [];
                $whereSql = "";
                if ($q !== '') {
                    $whereSql = "WHERE name LIKE :q_name OR hex LIKE :q_hex OR slug LIKE :q_slug";
                    $searchTerm = '%' . $q . '%';
                    $params[':q_name'] = $searchTerm;
                    $params[':q_hex']  = '%' . strtoupper(ltrim($q, '#')) . '%';
                    $params[':q_slug'] = '%' . strtolower($q) . '%';
                }

                $countStmt = $this->db->prepare("SELECT COUNT(*) FROM colors {$whereSql}");
                $countStmt->execute($params);
                $total = (int)$countStmt->fetchColumn();

                $sql = "SELECT hex, name, slug, aliases FROM colors {$whereSql} ORDER BY name ASC LIMIT :limit OFFSET :offset";
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
                    'items' => array_map([$this, 'formatColor'], $rows)
                ];
            } catch (Throwable $t) {
                // Fall through
            }
        }

        // JSON Fallback
        $data = $this->getFallbackData();
        $list = [];
        foreach ($data as $key => $entry) {
            $cleanHex = strtoupper(ltrim((string)$key, '#'));
            $name = is_array($entry) ? ($entry['name'] ?? '') : (string)$entry;
            $slug = is_array($entry) && isset($entry['slug']) ? (string)$entry['slug'] : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $aliases = is_array($entry) && isset($entry['aliases']) && is_array($entry['aliases']) ? $entry['aliases'] : [];

            if ($q !== '') {
                $qLower = strtolower($q);
                $nameMatch = strpos(strtolower($name), $qLower) !== false;
                $hexMatch  = strpos(strtolower($cleanHex), $qLower) !== false;
                $slugMatch = strpos(strtolower($slug), $qLower) !== false;
                if (!$nameMatch && !$hexMatch && !$slugMatch) {
                    continue;
                }
            }

            $list[] = [
                'hex'     => '#' . $cleanHex,
                'raw_hex' => $cleanHex,
                'name'    => $name,
                'slug'    => $slug,
                'aliases' => $aliases
            ];
        }

        $total = count($list);
        $paged = array_slice($list, $offset, $limit);

        return [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'items' => $paged
        ];
    }

    /**
     * Get all colors
     */
    public function all(bool $asDictionary = false): array
    {
        if ($this->db !== null) {
            try {
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
            } catch (Throwable $t) {
                // Fall through
            }
        }

        $data = $this->getFallbackData();
        if ($asDictionary) {
            return $data;
        }

        $list = [];
        foreach ($data as $key => $entry) {
            $cleanHex = strtoupper(ltrim((string)$key, '#'));
            $name = is_array($entry) ? ($entry['name'] ?? '') : (string)$entry;
            $slug = is_array($entry) && isset($entry['slug']) ? (string)$entry['slug'] : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $aliases = is_array($entry) && isset($entry['aliases']) && is_array($entry['aliases']) ? $entry['aliases'] : [];

            $list[] = [
                'hex'     => '#' . $cleanHex,
                'raw_hex' => $cleanHex,
                'name'    => $name,
                'slug'    => $slug,
                'aliases' => $aliases
            ];
        }
        return $list;
    }

    /**
     * Load JSON dataset fallback
     */
    private function getFallbackData(): array
    {
        if ($this->fallbackData !== null) {
            return $this->fallbackData;
        }

        $candidates = [
            dirname(__DIR__, 2) . '/data/color-names.json',
            dirname(__DIR__, 2) . '/color-names.json',
            dirname(__DIR__, 3) . '/data/color-names.json',
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
