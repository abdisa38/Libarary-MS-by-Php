<?php
/**
 * Admin Dashboard
 */

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../config/database.php';
require_once '../models/Book.php';
require_once '../models/Student.php';
require_once '../models/Borrowing.php';
require_once '../models/Category.php';

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

include '../includes/header.php';
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

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Recent Borrowings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Borrowings</h3>
            <a href="borrowings.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentBorrowings)): ?>
                <div class="empty-state">
                    <p class="text-muted">No borrowings yet</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBorrowings as $borrowing): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($borrowing['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($borrowing['book_title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($borrowing['due_date'])); ?></td>
                                <td>
                                    <?php
                                    $statusClass = match($borrowing['status']) {
                                        'borrowed' => 'badge-info',
                                        'returned' => 'badge-success',
                                        'overdue' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($borrowing['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overdue Books -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Overdue Books</h3>
            <span class="badge badge-danger"><?php echo count($overdueBooks); ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($overdueBooks)): ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <p class="text-muted">No overdue books! 🎉</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($overdueBooks, 0, 5) as $overdue): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($overdue['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($overdue['book_title']); ?></td>
                                <td>
                                    <span class="badge badge-danger">
                                        <?php echo $overdue['days_overdue']; ?> days
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 1.5rem;">
    <!-- Low Stock Books -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Low Stock Alert</h3>
            <span class="badge badge-warning"><?php echo count($lowStockBooks); ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($lowStockBooks)): ?>
                <div class="empty-state">
                    <p class="text-muted">All books are well stocked</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockBooks as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                                <td>
                                    <span class="badge badge-warning">
                                        <?php echo $book['available_copies']; ?> left
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Books by Category</h3>
        </div>
        <div class="card-body">
            <?php if (empty($categoryStats)): ?>
                <div class="empty-state">
                    <p class="text-muted">No categories yet</p>
                </div>
            <?php else: ?>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($categoryStats as $stat): ?>
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.875rem; font-weight: 500;"><?php echo htmlspecialchars($stat['name']); ?></span>
                            <span style="font-size: 0.875rem; color: var(--gray);"><?php echo $stat['book_count']; ?> books</span>
                        </div>
                        <div style="height: 8px; background-color: var(--gray-lightest); border-radius: 4px; overflow: hidden;">
                            <?php
                            $totalBooks = $bookStats['total_books'] ?? 1;
                            $percentage = ($stat['book_count'] / $totalBooks) * 100;
                            ?>
                            <div style="height: 100%; width: <?php echo $percentage; ?>%; background-color: var(--primary-color);"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
