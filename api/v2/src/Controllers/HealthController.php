<?php

namespace ColorMagic\Controllers;

use ColorMagic\Database\Database;
use ColorMagic\Config\Env;
use ColorMagic\Services\ResponseHelper;
use Exception;

/**
 * Health & Diagnostics Controller
 * Provides system status, database statistics, and latency metrics.
 */
class HealthController extends BaseController
{
    public function check(): void
    {
        $dbStatus = 'connected';
        $stats = [];
        $dbPath = Database::resolveDbPath();
        $dbSize = file_exists($dbPath) ? filesize($dbPath) : 0;

        try {
            $db = Database::getConnection();
            $stats['colors_count']    = (int)$db->query("SELECT COUNT(*) FROM colors")->fetchColumn();
            $stats['gradients_count'] = (int)$db->query("SELECT COUNT(*) FROM gradients")->fetchColumn();
            $stats['palettes_count']  = (int)$db->query("SELECT COUNT(*) FROM palettes")->fetchColumn();
            $stats['journal_mode']    = (string)$db->query("PRAGMA journal_mode")->fetchColumn();
        } catch (Exception $e) {
            $dbStatus = 'error: ' . $e->getMessage();
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? 'api.colormagic.techkreative.com');
        $baseUrl = $scheme . '://' . $host;

        $response = [
            'status'      => 'healthy',
            'api_name'    => 'ColorMagic REST API',
            'version'     => 'v2',
            'environment' => Env::get('APP_ENV', 'production'),
            'database'    => [
                'status'       => $dbStatus,
                'driver'       => 'sqlite',
                'journal_mode' => $stats['journal_mode'] ?? 'unknown',
                'size_bytes'   => $dbSize,
                'records'      => [
                    'colors'    => $stats['colors_count'] ?? 0,
                    'gradients' => $stats['gradients_count'] ?? 0,
                    'palettes'  => $stats['palettes_count'] ?? 0,
                ]
            ],
            'endpoints' => [
                'health'    => "{$baseUrl}/v2/health",
                'colors'    => "{$baseUrl}/v2/colors",
                'color_hex' => "{$baseUrl}/v2/colors/{hex}",
                'gradients' => "{$baseUrl}/v2/gradients",
                'grad_id'   => "{$baseUrl}/v2/gradients/{id}",
                'palettes'  => "{$baseUrl}/v2/palettes",
                'pal_id'    => "{$baseUrl}/v2/palettes/{id}",
            ],
            'timestamp'   => date('c')
        ];

        ResponseHelper::success($response);
    }
}
