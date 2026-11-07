<?php
/**
 * /auth/user-profile.php
 * Handles fetching and updating user profile (Name, Avatar, City, Country, Bio)
 * and synchronizing favorites with database.
 */

declare(strict_types=1);

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
    $data = UserDb::getUserProfileAndFavorites($userEmail);
    if (!$data['user']) {
        echo json_encode([
            'success'  => false,
            'loggedIn' => false,
            'message'  => 'User record not found'
        ]);
        exit;
    }

    $avatar = $data['user']['avatar'] ?? '1.webp';
    $data['user']['avatar_url'] = $base . '/assets/avatars/' . $avatar;
    $data['user']['picture'] = $data['user']['avatar_url'];

    echo json_encode([
        'success'   => true,
        'loggedIn'  => true,
        'user'      => $data['user'],
        'favorites' => $data['favorites']
    ]);
    exit;
}

if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true) ?: $_POST;

    $updatedUser = UserDb::updateUserProfile($userEmail, $payload);
    if (!$updatedUser) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update profile'
        ]);
        exit;
    }

    $avatar = $updatedUser['avatar'] ?? '1.webp';
    $avatarUrl = $base . '/assets/avatars/' . $avatar;

    // Synchronize session
    $_SESSION['user']['name'] = $updatedUser['name'];
    $_SESSION['user']['username'] = $updatedUser['username'];
    $_SESSION['user']['pronouns'] = $updatedUser['pronouns'];
    $_SESSION['user']['avatar'] = $avatar;
    $_SESSION['user']['city'] = $updatedUser['city'];
    $_SESSION['user']['country'] = $updatedUser['country'];
    $_SESSION['user']['bio'] = $updatedUser['bio'];
    $_SESSION['user']['website'] = $updatedUser['website'];
    $_SESSION['user']['picture'] = $avatarUrl;

    $updatedUser['avatar_url'] = $avatarUrl;
    $updatedUser['picture'] = $avatarUrl;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user'    => $updatedUser
    ]);
    exit;
}

