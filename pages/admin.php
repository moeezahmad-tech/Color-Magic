<?php
/**
 * pages/admin.php
 * Backward-compatibility wrapper forwarding to the modular admin directory: admin/index.php
 * On production where admin/ is not deployed, gracefully renders 404.
 */

if (!file_exists(__DIR__ . '/../admin/index.php')) {
    http_response_code(404);
    if (file_exists(__DIR__ . '/404.php')) {
        include __DIR__ . '/404.php';
    } else {
        echo "404 Not Found";
    }
    exit;
}

require __DIR__ . '/../admin/index.php';
