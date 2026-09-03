<?php

namespace ColorMagic\Controllers;

use ColorMagic\Database\Database;
use ColorMagic\Config\Env;
use ColorMagic\Services\ResponseHelper;
use Throwable;

/**
 * Health & Diagnostics Controller
 * Provides system status, database statistics, and latency metrics.
 */
class HealthController extends BaseController
{
    public function check(): void
    {
        $dbStatus = 'connected';
        $driver = 'sqlite';
        $journalMode = 'wal';
        $stats = [
            'colors'    => 0,
            'gradients' => 0,
            'palettes'  => 0
        ];

        try {
            $dbPath = Database::resolveDbPath();
            $dbSize = file_exists($dbPath) ? filesize($dbPath) : 0;
            $db = Database::getConnection();
            $stats['colors']    = (int)$db->query("SELECT COUNT(*) FROM colors")->fetchColumn();
            $stats['gradients'] = (int)$db->query("SELECT COUNT(*) FROM gradients")->fetchColumn();
            $stats['palettes']  = (int)$db->query("SELECT COUNT(*) FROM palettes")->fetchColumn();
            $journalMode        = (string)$db->query("PRAGMA journal_mode")->fetchColumn();
        } catch (Throwable $e) {
            $dbStatus = 'fallback_mode';
            $driver = 'json';
            $journalMode = 'none';
            $dbSize = 0;

            // Load counts from JSON
            $colorPath = dirname(__DIR__, 2) . '/data/color-names.json';
            $gradPath  = dirname(__DIR__, 2) . '/data/gradients.json';
            $palPath   = dirname(__DIR__, 2) . '/data/palettes.json';

            if (file_exists($colorPath)) {
                $c = json_decode(file_get_contents($colorPath), true);
                if (is_array($c)) $stats['colors'] = count($c);
            }
            if (file_exists($gradPath)) {
                $g = json_decode(file_get_contents($gradPath), true);
                if (is_array($g)) $stats['gradients'] = count($g);
            }
            if (file_exists($palPath)) {
                $p = json_decode(file_get_contents($palPath), true);
                if (is_array($p)) $stats['palettes'] = count($p);
            }
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? 'colormagic-api.techkreative.com');
        $baseUrl = $scheme . '://' . $host;

        $response = [
            'status'      => 'healthy',
            'api_name'    => 'ColorMagic REST API',
            'version'     => 'v2',
            'environment' => Env::get('APP_ENV', 'production'),
            'database'    => [
                'status'       => $dbStatus,
                'driver'       => $driver,
                'journal_mode' => $journalMode,
                'size_bytes'   => $dbSize,
                'records'      => $stats
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
