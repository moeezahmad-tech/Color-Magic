<?php
require_once __DIR__ . '/../components/config.php';
session_unset();
session_destroy();
header('Location: ' . $base . '/');
exit();
