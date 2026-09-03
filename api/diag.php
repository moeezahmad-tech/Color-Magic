<?php
header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$data = [
    'status'       => 'ok',
    'php_version'  => PHP_VERSION,
    'sqlite_pdo'   => extension_loaded('pdo_sqlite'),
    'sqlite3'      => extension_loaded('sqlite3'),
    'pdo_drivers'  => class_exists('PDO') ? PDO::getAvailableDrivers() : [],
    'dir'          => __DIR__,
    'v2_exists'    => is_dir(__DIR__ . '/v2'),
    'data_exists'  => is_dir(__DIR__ . '/data'),
    'files_in_v2'  => is_dir(__DIR__ . '/v2') ? scandir(__DIR__ . '/v2') : [],
];

// Test requiring v2/index.php in a sandbox or checking errors
$testError = null;
try {
    if (file_exists(__DIR__ . '/v2/src/Config/Env.php')) {
        require_once __DIR__ . '/v2/src/Config/Env.php';
        $data['env_loaded'] = true;
    }
} catch (\Throwable $e) {
    $data['env_error'] = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}

echo json_encode($data, JSON_PRETTY_PRINT);
