<?php
require_once __DIR__ . '/../components/config.php';

// In a real application with a database, you would execute a query here
// to delete the user's record from the database based on $_SESSION['user']['email'] or ID.
// Example: $db->query("DELETE FROM users WHERE email = ?", [$_SESSION['user']['email']]);

session_unset();
session_destroy();

header('Location: ' . $base . '/?msg=account_deleted');
exit();
