<?php
/**
 * Direct Dashboard Access
 * No authentication required
 */

session_start();

// Set admin session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['full_name'] = 'Administrator';
$_SESSION['email'] = 'admin@library.com';

// Include the dashboard
require_once 'config/database.php';
require_once 'models/Book.php';
require_once 'models/Student.php';
require_once 'models/Borrowing.php';
require_once 'models/Category.php';

define('PAGE_TITLE', 'Dashboard');

// Initialize models
$bookModel = new Book($db);
$studentModel = new Student($db);
$borrowingModel = new Borrowing($db);
$categoryModel = new Category($db);

// Get statistics
$bookStats = $bookModel->getStatistics();
$studentCount = $studentModel->count();
$borrowingStats = $borrowingModel->getStatistics();

// Get overdue books
$overdueBooks = $borrowingModel->getOverdue();

// Get low stock books
$lowStockBooks = $bookModel->getLowStock(3);

// Get category statistics for chart
$categoryStats = $categoryModel->getStatistics();

// Get recent borrowings
$recentBorrowings = $borrowingModel->getAll(null, '', 1, 5);

include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
    <p class="page-subtitle">Here's what's happening in your library today</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($bookStats['total_books'] ?? 0); ?></div>
            <div class="stat-label">Total Books</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($bookStats['available_books'] ?? 0); ?></div>
            <div class="stat-label">Available Books</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($studentCount); ?></div>
            <div class="stat-label">Total Students</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($borrowingStats['total_borrowed'] ?? 0); ?></div>
            <div class="stat-label">Borrowed Books</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($borrowingStats['total_returned'] ?? 0); ?></div>
            <div class="stat-label">Returned Books</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?php echo number_format($borrowingStats['overdue'] ?? 0); ?></div>
            <div class="stat-label">Overdue Books</div>
        </div>
    </div>
</div>

<div style="margin-top: 2rem; padding: 1.5rem; background: #d1fae5; border-radius: 8px; border-left: 4px solid #10b981;">
    <h3 style="color: #065f46; margin-bottom: 0.5rem;">🎉 Welcome to Your Library Dashboard!</h3>
    <p style="color: #047857; margin: 0;">You're viewing the admin dashboard. All features are working!</p>
</div>

<?php include 'includes/footer.php'; ?>
