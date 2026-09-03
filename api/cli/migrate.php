<?php
/**
 * ColorMagic Database Migration & Seeding CLI Tool
 * Usage:
 *   php api/cli/migrate.php
 *   php api/cli/migrate.php --force
 *   php api/cli/migrate.php --stats
 */

declare(strict_types=1);

// Autoloader for ColorMagic namespace
spl_autoload_register(static function (string $class): void {
    $prefix = 'ColorMagic\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/v2/src/' . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use ColorMagic\Config\Env;
use ColorMagic\Database\Database;
use ColorMagic\Services\MigrationService;

echo "====================================================\n";
echo "  ColorMagic SQLite Database Migration & CLI Tool   \n";
echo "====================================================\n\n";

Env::load();

$force = in_array('--force', $argv, true);
$statsOnly = in_array('--stats', $argv, true);

$dbPath = Database::resolveDbPath();
echo "Database Path: {$dbPath}\n";
echo "Loaded Env:    " . (Env::getLoadedFile() ?? 'Default Fallback') . "\n";
echo "Environment:   " . Env::get('APP_ENV', 'production') . "\n\n";

try {
    $db = Database::getConnection();
    $migrator = new MigrationService($db);

    if (!$statsOnly) {
        echo "Running migrations...\n";
        $startTime = microtime(true);
        $result = $migrator->migrate($force);
        $elapsed = round((microtime(true) - $startTime) * 1000, 2);

        if (!empty($result['skipped'])) {
            echo "Notice: " . $result['message'] . " (Use --force to re-seed)\n";
        } else {
            echo "Seeded Colors:    " . $result['colors'] . "\n";
            echo "Seeded Gradients: " . $result['gradients'] . "\n";
            echo "Seeded Palettes:  " . $result['palettes'] . "\n";
            echo "Migration completed in {$elapsed} ms!\n";
        }
    }

    // Verify current statistics
    $colorCount = (int)$db->query("SELECT COUNT(*) FROM colors")->fetchColumn();
    $gradCount  = (int)$db->query("SELECT COUNT(*) FROM gradients")->fetchColumn();
    $palCount   = (int)$db->query("SELECT COUNT(*) FROM palettes")->fetchColumn();
    $dbSizeKb   = round(filesize($dbPath) / 1024, 2);

    echo "\n---------------- Database Stats --------------------\n";
    echo "  Total Colors:    {$colorCount}\n";
    echo "  Total Gradients: {$gradCount}\n";
    echo "  Total Palettes:  {$palCount}\n";
    echo "  Database Size:   {$dbSizeKb} KB\n";
    echo "  WAL Mode:        " . $db->query("PRAGMA journal_mode")->fetchColumn() . "\n";
    echo "----------------------------------------------------\n\n";
    echo "Status: SUCCESS - SQLite ready for production traffic!\n";

} catch (Exception $e) {
    echo "\nError during migration: " . $e->getMessage() . "\n";
    exit(1);
}
