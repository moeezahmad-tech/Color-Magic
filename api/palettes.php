<?php
/**
 * ColorMagic Palettes API Endpoint
 * Base URL: https://api.colormagic.techkreative.com/v1/palettes
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dataFile = __DIR__ . '/palettes.json';

if (!is_file($dataFile)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Palettes dataset not found'
    ]);
    exit;
}

$raw = file_get_contents($dataFile);
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid palettes dataset format'
    ]);
    exit;
}

$q  = isset($_GET['q']) ? strtolower(trim((string)$_GET['q'])) : '';
$id = isset($_GET['id']) ? trim((string)$_GET['id']) : '';

// Single item lookup by ID
if ($id !== '') {
    foreach ($data as $item) {
        if (isset($item['id']) && $item['id'] === $id) {
            echo json_encode([
                'status' => 'success',
                'data' => $item
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Palette not found'
    ]);
    exit;
}

// Filter dataset
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
        if (!$nameMatch && !$colorMatch) {
            return false;
        }
    }
    return true;
}));

// Pagination
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 0;

if ($limit > 0) {
    $total = count($filtered);
    $offset = ($page - 1) * $limit;
    $paged = array_slice($filtered, $offset, $limit);

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
    'total' => count($filtered),
    'data' => $filtered
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
