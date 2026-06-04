<?php
/**
 * Logout
 */

session_start();

require_once '../config/database.php';

// Log activity if user is logged in
if (isset($_SESSION['user_id'])) {
    logActivity($db, $_SESSION['user_id'], 'logout', 'User logged out');
}

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: login.php');
exit();
?>
