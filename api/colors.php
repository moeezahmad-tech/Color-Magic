<?php
/**
 * ColorMagic Colors API Endpoint (V1 with V2 SQLite Acceleration)
 * Base URL: https://api.colormagic.techkreative.com/v1/colors
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
        require_once __DIR__ . '/v2/src/Repositories/ColorRepository.php';

        $repo = new \ColorMagic\Repositories\ColorRepository();
        $hex  = isset($_GET['hex']) ? strtoupper(ltrim(trim((string)$_GET['hex']), '#')) : '';
        $slug = isset($_GET['slug']) ? strtolower(trim((string)$_GET['slug'])) : '';
        $q    = isset($_GET['q']) ? trim((string)$_GET['q']) : '';

        // Lookup single color by hex
        if ($hex !== '') {
            $color = $repo->findByHex($hex);
            if ($color) {
                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'hex' => $color['hex'],
                        'name' => $color['name'],
                        'slug' => $color['slug'],
                        'aliases' => $color['aliases']
                    ]
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Color not found for hex #' . $hex
            ]);
            exit;
        }

        // Lookup single color by slug
        if ($slug !== '') {
            $color = $repo->findBySlug($slug);
            if ($color) {
                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'hex' => $color['hex'],
                        'name' => $color['name'],
                        'slug' => $color['slug'],
                        'aliases' => $color['aliases']
                    ]
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Color not found for slug ' . $slug
            ]);
            exit;
        }

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 0;

        if ($limit > 0 || $q !== '') {
            $result = $repo->search($q, $page, $limit > 0 ? $limit : 50);
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
        $allDict = $repo->all(true);
        echo json_encode([
            'status' => 'success',
            'total' => count($allDict),
            'data' => $allDict
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
} catch (\Throwable $t) {
    // Fall back to JSON file below
}

// Fallback: Read JSON file
$dataFile = __DIR__ . '/data/color-names.json';
if (!is_file($dataFile)) {
    $dataFile = __DIR__ . '/color-names.json';
}
if (!is_file($dataFile)) {
    $dataFile = dirname(__DIR__) . '/data/color-names.json';
}

if (!is_file($dataFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Color names dataset not found']);
    exit;
}

$data = json_decode(file_get_contents($dataFile), true);
$hex  = isset($_GET['hex']) ? strtoupper(ltrim(trim((string)$_GET['hex']), '#')) : '';
$slug = isset($_GET['slug']) ? strtolower(trim((string)$_GET['slug'])) : '';
$q    = isset($_GET['q']) ? strtolower(trim((string)$_GET['q'])) : '';

if ($hex !== '') {
    if (isset($data[$hex])) {
        echo json_encode([
            'status' => 'success',
            'data' => array_merge(['hex' => '#' . $hex], is_array($data[$hex]) ? $data[$hex] : ['name' => $data[$hex]])
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Color not found for hex #' . $hex]);
    exit;
}

if ($slug !== '') {
    foreach ($data as $key => $entry) {
        if (is_array($entry) && isset($entry['slug']) && strtolower((string)$entry['slug']) === $slug) {
            echo json_encode([
                'status' => 'success',
                'data' => array_merge(['hex' => '#' . $key], $entry)
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Color not found for slug ' . $slug]);
    exit;
}

$list = [];
foreach ($data as $key => $entry) {
    $item = is_array($entry) ? array_merge(['hex' => '#' . $key], $entry) : ['hex' => '#' . $key, 'name' => $entry];
    if ($q !== '') {
        $nameMatch = strpos(strtolower((string)($item['name'] ?? '')), $q) !== false;
        $hexMatch  = strpos(strtolower((string)$key), $q) !== false;
        if (!$nameMatch && !$hexMatch) continue;
    }
    $list[] = $item;
}

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 0;

if ($limit > 0) {
    $total  = count($list);
    $offset = ($page - 1) * $limit;
    echo json_encode([
        'status' => 'success',
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => (int)ceil($total / $limit),
        'data' => array_slice($list, $offset, $limit)
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['status' => 'success', 'total' => count($list), 'data' => $data], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
