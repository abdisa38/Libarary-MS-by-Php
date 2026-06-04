<?php
/**
 * Library Management System
 * Main Entry Point
 */

// Start session
session_start();

// Include configuration
require_once 'config/database.php';

// Redirect based on user role
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'librarian':
            header('Location: librarian/dashboard.php');
            break;
        case 'student':
            header('Location: student/dashboard.php');
            break;
        default:
            header('Location: auth/login.php');
    }
    exit();
} else {
    header('Location: auth/login.php');
    exit();
}
?>
