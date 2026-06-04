<?php
/**
 * Student Dashboard
 */

session_start();

// Check if user is logged in and is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../config/database.php';

define('PAGE_TITLE', 'Student Dashboard');

include '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
    <p class="page-subtitle">Browse books and manage your borrowings</p>
</div>

<div class="alert alert-info">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
    </svg>
    Student portal is under construction. Please contact the librarian to borrow books.
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">My Borrowings</h3>
    </div>
    <div class="card-body">
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <h3>No Active Borrowings</h3>
            <p>You don't have any books borrowed at the moment.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
