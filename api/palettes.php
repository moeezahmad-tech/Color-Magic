<?php
/**
 * ColorMagic Palettes API Endpoint (V1 with V2 SQLite Acceleration)
 * Base URL: https://colormagic-api.techkreative.com/v1/palettes
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Cache-Control: public, max-age=86400, stale-while-revalidate=604800');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Try SQLite V2 engine first
try {
    if (file_exists(__DIR__ . '/v2/src/Config/Env.php')) {
        require_once __DIR__ . '/v2/src/Config/Env.php';
        require_once __DIR__ . '/v2/src/Database/Database.php';
        require_once __DIR__ . '/v2/src/Services/MigrationService.php';
        require_once __DIR__ . '/v2/src/Repositories/PaletteRepository.php';

        $repo = new \ColorMagic\Repositories\PaletteRepository();
        $id    = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
        $style = isset($_GET['style']) ? strtolower(trim((string)$_GET['style'])) : '';
        $q     = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

        // Single item lookup by ID
        if ($id !== '') {
            $palette = $repo->findById($id);
            if ($palette) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $palette
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Palette not found'
            ]);
            exit;
        }

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 0;

        if ($limit > 0 || $style !== '' || $q !== '') {
            $result = $repo->search($q, $style !== '' ? $style : null, $page, $limit > 0 ? $limit : 50);
            if ($limit > 0) {
                $totalPages = (int)ceil($result['total'] / $limit);
                echo json_encode([
                    'status' => 'success',
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $result['total'],
                    'total_pages' => $totalPages,
                    'data' => $result['items']
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        // Full dataset request
        $all = $repo->all();
        echo json_encode([
            'status' => 'success',
            'total' => count($all),
            'data' => $all
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
} catch (\Throwable $t) {
    // Fall back to JSON file below
}

// Fallback: Read JSON file
$dataFile = __DIR__ . '/data/palettes.json';
if (!is_file($dataFile)) {
    $dataFile = __DIR__ . '/palettes.json';
}
if (!is_file($dataFile)) {
    $dataFile = dirname(__DIR__) . '/data/palettes.json';
}

if (!is_file($dataFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Palettes dataset not found']);
    exit;
}

$data  = json_decode(file_get_contents($dataFile), true);
$q     = isset($_GET['q']) ? strtolower(trim((string)$_GET['q'])) : '';
$id    = isset($_GET['id']) ? trim((string)$_GET['id']) : '';

if ($id !== '') {
    foreach ($data as $item) {
        if (isset($item['id']) && $item['id'] === $id) {
            echo json_encode(['status' => 'success', 'data' => $item], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Palette not found']);
    exit;
}

$filtered = array_values(array_filter($data, static function ($item) use ($q) {
    if (!is_array($item)) return false;
    if ($q !== '') {
        $nameMatch = strpos(strtolower((string)($item['name'] ?? '')), $q) !== false;
        $colorMatch = false;
        if (isset($item['colors']) && is_array($item['colors'])) {
            foreach ($item['colors'] as $c) {
                if (strpos(strtolower((string)$c), $q) !== false) {
                    $colorMatch = true;
                    break;
                }
            }
        }
        if (!$nameMatch && !$colorMatch) return false;
    }
    return true;
}));

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 0;

if ($limit > 0) {
    $total  = count($filtered);
    $offset = ($page - 1) * $limit;
    echo json_encode([
        'status' => 'success',
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => (int)ceil($total / $limit),
        'data' => array_slice($filtered, $offset, $limit)
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['status' => 'success', 'total' => count($filtered), 'data' => $filtered], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
