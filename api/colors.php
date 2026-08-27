<?php
/**
 * ColorMagic Colors API Endpoint
 * Base URL: https://api.colormagic.techkreative.com/v1/colors
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dataFile = __DIR__ . '/color-names.json';

if (!is_file($dataFile)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Color names dataset not found'
    ]);
    exit;
}

$raw = file_get_contents($dataFile);
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid color names dataset format'
    ]);
    exit;
}

$hex  = isset($_GET['hex']) ? strtoupper(ltrim(trim((string)$_GET['hex']), '#')) : '';
$slug = isset($_GET['slug']) ? strtolower(trim((string)$_GET['slug'])) : '';
$q    = isset($_GET['q']) ? strtolower(trim((string)$_GET['q'])) : '';

// Lookup single color by hex key
if ($hex !== '') {
    if (isset($data[$hex])) {
        echo json_encode([
            'status' => 'success',
            'data' => array_merge(['hex' => '#' . $hex], is_array($data[$hex]) ? $data[$hex] : ['name' => $data[$hex]])
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
    echo json_encode([
        'status' => 'error',
        'message' => 'Color not found for slug ' . $slug
    ]);
    exit;
}

// Filter dataset by query term
$list = [];
foreach ($data as $key => $entry) {
    $item = is_array($entry) ? array_merge(['hex' => '#' . $key], $entry) : ['hex' => '#' . $key, 'name' => $entry];
    if ($q !== '') {
        $nameMatch = strpos(strtolower((string)($item['name'] ?? '')), $q) !== false;
        $hexMatch  = strpos(strtolower((string)$key), $q) !== false;
        if (!$nameMatch && !$hexMatch) {
            continue;
        }
    }
    $list[] = $item;
}

// Pagination
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 0;

if ($limit > 0) {
    $total  = count($list);
    $offset = ($page - 1) * $limit;
    $paged  = array_slice($list, $offset, $limit);

    echo json_encode([
        'status' => 'success',
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => (int)ceil($total / $limit),
        'data' => $paged
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// Full response if no limit specified
echo json_encode([
    'status' => 'success',
    'total' => count($list),
    'data' => $data
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
