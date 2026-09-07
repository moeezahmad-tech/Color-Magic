<?php
require_once __DIR__ . '/../components/config.php';
session_unset();
session_destroy();
$redirectUrl = htmlspecialchars($base . '/', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Signing out...</title>
    <meta http-equiv="refresh" content="1;url=<?= $redirectUrl ?>">
    <script>
        try {
            localStorage.removeItem('cm_user');
            sessionStorage.removeItem('cm_user');
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
