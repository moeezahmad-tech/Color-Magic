<?php
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/AdminAuth.php';

AdminAuth::clearAdminSession();

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];
    session_unset();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 86400,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

$redirectUrl = htmlspecialchars(($base ?: '') . '/', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Signing out...</title>
    <meta http-equiv="refresh" content="1;url=<?= $redirectUrl ?>">
    <script>
        try {
            // Broadcast logout to all other open tabs immediately
            localStorage.setItem('cm_logout_event', Date.now().toString());
            localStorage.removeItem('cm_user');
            sessionStorage.removeItem('cm_user');
            localStorage.removeItem('cm_admin_token');
            localStorage.removeItem('cm_admin_exp');
            localStorage.removeItem('cm_admin_user');
            sessionStorage.removeItem('cm_admin_token');
            sessionStorage.removeItem('cm_admin_exp');
            sessionStorage.removeItem('cm_admin_user');
        } catch(e) {}
        window.location.replace('<?= $redirectUrl ?>');
    </script>
</head>
<body style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8fafc;">
    <p style="color:#64748b;font-weight:500;">Signing out of Color Magic...</p>
</body>
</html>
<?php
exit();
