<?php
/**
 * Fix Default Admin and Librarian Passwords
 * Run this file once to update the passwords
 */

require_once 'config/database.php';

echo "<h1>Password Fix Script</h1>";
echo "<p>This script will reset the default admin and librarian passwords.</p>";

try {
    // Admin password: admin123
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = :password WHERE email = 'admin@library.com'";
    $stmt = $db->prepare($sql);
    $stmt->execute([':password' => $admin_password]);
    echo "<p style='color: green;'>✓ Admin password updated successfully</p>";
    
    // Librarian password: librarian123
    $librarian_password = password_hash('librarian123', PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = :password WHERE email = 'librarian@library.com'";
    $stmt = $db->prepare($sql);
    $stmt->execute([':password' => $librarian_password]);
    echo "<p style='color: green;'>✓ Librarian password updated successfully</p>";
    
    echo "<hr>";
    echo "<h2>You can now login with:</h2>";
    echo "<h3>Admin:</h3>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Email: <strong>admin@library.com</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "</ul>";
    
    echo "<h3>Librarian:</h3>";
    echo "<ul>";
    echo "<li>Username: <strong>librarian</strong></li>";
    echo "<li>Email: <strong>librarian@library.com</strong></li>";
    echo "<li>Password: <strong>librarian123</strong></li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><strong>Next step:</strong> <a href='auth/login.php'>Go to Login Page</a></p>";
    echo "<p style='color: red;'><strong>Important:</strong> Delete this file (fix_passwords.php) after using it for security reasons!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure you have imported the database first!</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1, h2, h3 {
        color: #333;
    }
    ul {
        background: white;
        padding: 20px;
        border-radius: 5px;
        list-style: none;
    }
    li {
        padding: 5px 0;
    }
    a {
        display: inline-block;
        background: #4F46E5;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 10px;
    }
    a:hover {
        background: #4338CA;
    }
</style>
