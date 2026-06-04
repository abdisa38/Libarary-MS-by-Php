<?php
/**
 * Librarian Dashboard
 */

session_start();

// Check if user is logged in and is librarian
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../auth/login.php');
    exit();
}

// Redirect to admin dashboard (librarians use the same interface)
header('Location: ../admin/dashboard.php');
exit();
?>
