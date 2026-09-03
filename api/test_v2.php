<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "TEST V2 START<br>";
try {
    require_once __DIR__ . '/v2/index.php';
} catch (\Throwable $e) {
    echo "CAUGHT THROWABLE: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "TEST V2 END";
