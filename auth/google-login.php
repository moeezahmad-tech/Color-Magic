<?php
header('X-Robots-Tag: noindex, nofollow', true);
header('Cache-Control: private, no-cache, no-store, must-revalidate');
require_once __DIR__ . '/config.php';

header('Location: ' . getGoogleAuthUrl());
exit();
