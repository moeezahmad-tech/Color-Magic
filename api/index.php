<?php
/**
 * ColorMagic Public REST API Root Index
 * Base URL: https://colormagic-api.techkreative.com/
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Cache-Control: public, max-age=3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? 'colormagic-api.techkreative.com');
$baseUrl = $scheme . '://' . $host;

$response = [
    'status'        => 'success',
    'name'          => 'ColorMagic API',
    'active_version'=> 'v2',
    'documentation' => $baseUrl . '/README.md',
    'v2_endpoints'  => [
        'health'    => $baseUrl . '/v2/health',
        'colors'    => [
            'url'         => $baseUrl . '/v2/colors',
            'by_hex'      => $baseUrl . '/v2/colors/{hex}',
            'by_slug'     => $baseUrl . '/v2/colors/slug/{slug}',
            'query_params'=> ['q', 'hex', 'slug', 'page', 'limit', 'format']
        ],
        'gradients' => [
            'url'         => $baseUrl . '/v2/gradients',
            'by_id'       => $baseUrl . '/v2/gradients/{id}',
            'query_params'=> ['q', 'style', 'type', 'id', 'page', 'limit']
        ],
        'palettes'  => [
            'url'         => $baseUrl . '/v2/palettes',
            'by_id'       => $baseUrl . '/v2/palettes/{id}',
            'submit'      => 'POST ' . $baseUrl . '/v2/palettes',
            'query_params'=> ['q', 'style', 'id', 'page', 'limit']
        ]
    ],
    'v1_endpoints'  => [
        'gradients'   => $baseUrl . '/v1/gradients',
        'palettes'    => $baseUrl . '/v1/palettes',
        'colors'      => $baseUrl . '/v1/colors',
        'direct_json' => [
            'gradients' => $baseUrl . '/gradients.json',
            'palettes'  => $baseUrl . '/palettes.json',
            'colors'    => $baseUrl . '/color-names.json'
        ]
    ],
    'timestamp'     => date('c')
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
