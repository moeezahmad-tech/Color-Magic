<?php
/**
 * /auth/favorites.php
 * Handles CRUD and syncing of favorites between browser and MySQL database.
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/UserDb.php';

header('Content-Type: application/json; charset=utf-8');

$userEmail = $_SESSION['user']['email'] ?? null;
if (!$userEmail) {
    echo json_encode([
        'success'  => false,
        'loggedIn' => false,
        'message'  => 'User not logged in'
    ]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $favorites = UserDb::getUserFavorites($userEmail);
    echo json_encode([
        'success'  => true,
        'loggedIn' => true,
        'data'     => $favorites
    ]);
    exit;
}

if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true) ?: $_POST;

    $action = $payload['action'] ?? 'toggle';
    $type   = $payload['type'] ?? ''; // 'color', 'palette', 'gradient'
    $id     = $payload['id'] ?? '';
    $data   = $payload['data'] ?? null;

    if ($action === 'sync') {
        $clientFavs = [
            'colors'    => $payload['colors'] ?? [],
            'palettes'  => $payload['palettes'] ?? [],
            'gradients' => $payload['gradients'] ?? []
        ];
        $synced = UserDb::syncFavorites($userEmail, $clientFavs);
        echo json_encode([
            'success'  => true,
            'loggedIn' => true,
            'data'     => $synced
        ]);
        exit;
    }

    if (!in_array($type, ['color', 'palette', 'gradient'], true) || empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid type or id'
        ]);
        exit;
    }

    if ($action === 'add') {
        UserDb::addFavorite($userEmail, $type, $id, $data);
        $added = true;
    } elseif ($action === 'remove') {
        UserDb::removeFavorite($userEmail, $type, $id);
        $added = false;
    } else {
        $added = UserDb::toggleFavorite($userEmail, $type, $id, $data);
    }

    $updatedFavs = UserDb::getUserFavorites($userEmail);

    echo json_encode([
        'success'    => true,
        'loggedIn'   => true,
        'isFavorite' => $added,
        'data'       => $updatedFavs
    ]);
    exit;
}
