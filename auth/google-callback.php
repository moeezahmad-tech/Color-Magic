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
            $_SESSION['user'] = [
                'id' => $userData['id'] ?? null,
                'name' => $userData['name'] ?? null,
                'email' => $userData['email'],
                'picture' => $userData['picture'] ?? null,
            ];
            
            // Redirect to home/dashboard on success
            header('Location: ' . $base . '/');
            exit();
        }
    }
}

// If auth failed, redirect back to login page with an error
header('Location: ' . $base . '/login?error=auth_failed');
exit();
