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
    <h1 class="page-title">🎉 Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
    <p class="page-subtitle">Your account has been created successfully</p>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<div style="margin-bottom: 2rem; padding: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">
    <h2 style="margin-bottom: 1rem; font-size: 1.75rem;">Welcome to the Library Management System!</h2>
    <p style="font-size: 1.125rem; margin-bottom: 1.5rem; opacity: 0.95;">Your student account is now active. You can browse books and manage your borrowings.</p>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
        <div style="background: rgba(255,255,255,0.2); padding: 1.25rem; border-radius: 8px;">
            <h3 style="margin-bottom: 0.5rem; font-size: 2rem;">📚</h3>
            <p style="font-size: 0.875rem; opacity: 0.9;">Browse Available Books</p>
        </div>
        <div style="background: rgba(255,255,255,0.2); padding: 1.25rem; border-radius: 8px;">
            <h3 style="margin-bottom: 0.5rem; font-size: 2rem;">📖</h3>
            <p style="font-size: 0.875rem; opacity: 0.9;">Borrow Your Favorites</p>
        </div>
        <div style="background: rgba(255,255,255,0.2); padding: 1.25rem; border-radius: 8px;">
            <h3 style="margin-bottom: 0.5rem; font-size: 2rem;">📋</h3>
            <p style="font-size: 0.875rem; opacity: 0.9;">Track Your History</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Getting Started</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; gap: 1.5rem;">
            <div style="display: flex; gap: 1rem; align-items: start; padding: 1.25rem; background: #f8f9fa; border-radius: 8px;">
                <div style="width: 48px; height: 48px; background: #667eea; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                    1
                </div>
                <div>
                    <h4 style="margin-bottom: 0.5rem; color: #333;">Visit the Library</h4>
                    <p style="color: #666; margin: 0; font-size: 0.875rem;">Contact the librarian to borrow books from our collection.</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: start; padding: 1.25rem; background: #f8f9fa; border-radius: 8px;">
                <div style="width: 48px; height: 48px; background: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                    2
                </div>
                <div>
                    <h4 style="margin-bottom: 0.5rem; color: #333;">Borrow Books</h4>
                    <p style="color: #666; margin: 0; font-size: 0.875rem;">The librarian will process your borrowing and set the due date.</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: start; padding: 1.25rem; background: #f8f9fa; border-radius: 8px;">
                <div style="width: 48px; height: 48px; background: #f59e0b; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                    3
                </div>
                <div>
                    <h4 style="margin-bottom: 0.5rem; color: #333;">Return On Time</h4>
                    <p style="color: #666; margin: 0; font-size: 0.875rem;">Make sure to return books before the due date to avoid fines.</p>
                </div>
            </div>
        </div>
    </div>
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
            <p style="color: #666; font-size: 0.875rem;">Visit the library to borrow your first book!</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
