<?php
require_once __DIR__ . '/config.php';

if (isset($_GET['code'])) {
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenData['access_token']]);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userInfoResponse, true);

        if (isset($userData['email'])) {
            $userObj = [
                'id'           => $userData['id'] ?? null,
                'name'         => $userData['name'] ?? null,
                'email'        => $userData['email'],
                'picture'      => $userData['picture'] ?? null,
                'logged_in_at' => time()
            ];

            // Save or update user in MySQL database
            require_once __DIR__ . '/UserDb.php';
            $dbUser = UserDb::saveUser($userObj);
            if ($dbUser && !empty($dbUser['id'])) {
                $userObj['db_id'] = $dbUser['id'];
                $userObj['created_at'] = $dbUser['created_at'] ?? null;
            }

            $_SESSION['user'] = $userObj;

            // Save user data to client localStorage & sessionStorage then redirect
            $userJson = json_encode($userObj, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            $redirectUrl = htmlspecialchars($base . '/', ENT_QUOTES, 'UTF-8');
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="utf-8">
                <title>Signing in...</title>
                <meta http-equiv="refresh" content="1;url=<?= $redirectUrl ?>">
                <script>
                    try {
                        const user = <?= $userJson ?>;
                        localStorage.setItem('cm_user', JSON.stringify(user));
                        sessionStorage.setItem('cm_user', JSON.stringify(user));
                    } catch (e) {
                        console.error('Storage error:', e);
                    }
                    window.location.replace('<?= $redirectUrl ?>');
                </script>
            </head>
            <body style="font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8fafc;">
                <p style="color:#64748b;font-weight:500;">Signing you in to Color Magic...</p>
            </body>
            </html>
            <?php
            exit();
        }
    } else {
        error_log("Google OAuth Token Failure: " . ($response ?: $curlErr));
        $desc = urlencode($tokenData['error_description'] ?? ($tokenData['error'] ?? ($curlErr ?: 'token_exchange_failed')));
        header('Location: ' . $base . '/login?error=auth_failed&reason=' . $desc);
        exit();
    }
}

// If auth failed, redirect back to login page with an error
header('Location: ' . $base . '/login?error=auth_failed');
exit();
