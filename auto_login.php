<?php
/**
 * AUTO LOGIN - Bypass authentication temporarily
 * Go directly to admin dashboard
 * DELETE THIS FILE AFTER USE!
 */

session_start();

// Auto login as admin - NO PASSWORD NEEDED
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['full_name'] = 'System Administrator';
$_SESSION['email'] = 'admin@library.com';

// Redirect to admin dashboard
header('Location: admin/dashboard.php');
exit();
?>
