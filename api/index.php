<?php
/**
 * ColorMagic Public REST API Root Index
 * Base URL: https://api.colormagic.techkreative.com/
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? 'api.colormagic.techkreative.com');
$baseUrl = $scheme . '://' . $host;

$response = [
    'status' => 'success',
    'name' => 'ColorMagic API',
    'version' => 'v1',
    'documentation' => $baseUrl . '/README.md',
    'endpoints' => [
        'gradients' => [
            'url' => $baseUrl . '/v1/gradients',
            'direct_json' => $baseUrl . '/gradients.json',
            'params' => ['style', 'type', 'q', 'page', 'limit']
        ],
        'palettes' => [
            'url' => $baseUrl . '/v1/palettes',
            'direct_json' => $baseUrl . '/palettes.json',
            'params' => ['q', 'page', 'limit']
        ],
        'colors' => [
            'url' => $baseUrl . '/v1/colors',
            'direct_json' => $baseUrl . '/color-names.json',
            'params' => ['hex', 'q', 'page', 'limit']
        ]
    ],
    'timestamp' => date('c')
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
