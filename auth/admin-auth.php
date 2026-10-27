<?php
/**
 * /auth/admin-auth.php
 * Administrative API for status verification, 12-hour session refresh, and logout.
 */

require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/AdminAuth.php';
require_once __DIR__ . '/UserDb.php';

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

$action = $_GET['action'] ?? ($_POST['action'] ?? 'check');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

switch ($action) {
    case 'check':
        $admin = AdminAuth::getCurrentAdmin();
        if ($admin) {
            echo json_encode([
                'success'    => true,
                'is_admin'   => true,
                'email'      => $admin['email'],
                'name'       => $admin['name'] ?? '',
                'exp'        => $admin['exp'] ?? null,
                'time_left'  => max(0, ($admin['exp'] ?? time()) - time()),
                'user'       => $admin
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success'  => false,
                'is_admin' => false,
                'error'    => 'Not authenticated as admin'
            ]);
        }
        break;

    case 'refresh':
        // Re-authentication endpoint:
        // Validates that the active session user has an authorized admin email, then regenerates the 12-hour JWT.
        $user = $_SESSION['user'] ?? null;
        $email = $user['email'] ?? null;

        if (empty($email) || !AdminAuth::isAdminEmail($email)) {
            AdminAuth::clearAdminSession();
            http_response_code(401);
            echo json_encode([
                'success'  => false,
                'error'    => 'Unauthorized: Active admin login required to refresh session',
                'redirect' => $base . '/'
            ]);
            exit;
        }

        // Fetch fresh user record from database
        $dbUser = UserDb::getUserByEmail($email);
        $userData = [
            'id'      => $dbUser['id'] ?? ($user['id'] ?? null),
            'email'   => $email,
            'name'    => $dbUser['name'] ?? ($user['name'] ?? ''),
            'picture' => $dbUser['picture'] ?? ($user['picture'] ?? null),
            'role'    => 'admin'
        ];

        // Issue new 12-hour session
        $res = AdminAuth::issueAdminSession($userData);

        echo json_encode([
            'success'    => true,
            'message'    => 'Admin session successfully renewed for 12 hours',
            'token'      => $res['token'],
            'exp'        => $res['exp'],
            'expires_in' => $res['expires_in'],
            'user'       => $res['user']
        ]);
        break;

    case 'logout':
        AdminAuth::clearAdminSession();
        echo json_encode([
            'success'  => true,
            'message'  => 'Admin logged out successfully',
            'redirect' => $base . '/'
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
