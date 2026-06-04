<?php
/**
 * Fix Default Admin and Librarian Passwords
 * Run this file once to update the passwords
 */

require_once 'config/database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Passwords - Library Management System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        ul { background: #f9f9f9; padding: 20px; border-radius: 5px; list-style: none; }
        li { padding: 5px 0; }
        a { display: inline-block; background: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        a:hover { background: #4338CA; }
        code { background: #f4f4f4; padding: 2px 8px; border-radius: 3px; font-family: monospace; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f9f9f9; font-weight: bold; }
    </style>
</head>
<body>

<div class="section">
    <h1>🔧 Password Fix Script</h1>
    <p>This script will reset the default admin and librarian passwords with properly hashed passwords.</p>
</div>

<?php
try {
    echo "<div class='section'>";
    echo "<h2>Step 1: Generating Password Hashes</h2>";
    
    // Generate new password hashes
    $admin_password = 'admin123';
    $librarian_password = 'librarian123';
    
    $admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    $librarian_hash = password_hash($librarian_password, PASSWORD_DEFAULT);
    
    echo "<p class='success'>✓ Password hashes generated successfully</p>";
    echo "<table>";
    echo "<tr><th>Account</th><th>Password</th><th>Hash (first 50 chars)</th></tr>";
    echo "<tr><td>Admin</td><td><code>$admin_password</code></td><td><code>" . substr($admin_hash, 0, 50) . "...</code></td></tr>";
    echo "<tr><td>Librarian</td><td><code>$librarian_password</code></td><td><code>" . substr($librarian_hash, 0, 50) . "...</code></td></tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Step 2: Updating Database</h2>";
    
    // Update Admin password
    $sql = "UPDATE users SET password = :password, is_active = 1 WHERE email = 'admin@library.com'";
    $stmt = $db->prepare($sql);
    $result1 = $stmt->execute([':password' => $admin_hash]);
    $affected1 = $stmt->rowCount();
    
    if ($result1 && $affected1 > 0) {
        echo "<p class='success'>✓ Admin password updated successfully (Rows affected: $affected1)</p>";
    } else {
        echo "<p class='error'>✗ Failed to update admin password or admin user not found</p>";
    }
    
    // Update Librarian password
    $sql = "UPDATE users SET password = :password, is_active = 1 WHERE email = 'librarian@library.com'";
    $stmt = $db->prepare($sql);
    $result2 = $stmt->execute([':password' => $librarian_hash]);
    $affected2 = $stmt->rowCount();
    
    if ($result2 && $affected2 > 0) {
        echo "<p class='success'>✓ Librarian password updated successfully (Rows affected: $affected2)</p>";
    } else {
        echo "<p class='error'>✗ Failed to update librarian password or librarian user not found</p>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Step 3: Verification</h2>";
    
    // Verify the passwords work
    require_once 'models/User.php';
    $userModel = new User($db);
    
    // Test admin login
    $admin_auth = $userModel->authenticate('admin', 'admin123');
    if ($admin_auth) {
        echo "<p class='success'>✓ Admin authentication test PASSED</p>";
    } else {
        echo "<p class='error'>✗ Admin authentication test FAILED</p>";
    }
    
    // Test librarian login
    $librarian_auth = $userModel->authenticate('librarian', 'librarian123');
    if ($librarian_auth) {
        echo "<p class='success'>✓ Librarian authentication test PASSED</p>";
    } else {
        echo "<p class='error'>✗ Librarian authentication test FAILED</p>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p>Your passwords have been reset. You can now login with:</p>";
    
    echo "<h3>Admin Account:</h3>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Email: <strong>admin@library.com</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "</ul>";
    
    echo "<h3>Librarian Account:</h3>";
    echo "<ul>";
    echo "<li>Username: <strong>librarian</strong></li>";
    echo "<li>Email: <strong>librarian@library.com</strong></li>";
    echo "<li>Password: <strong>librarian123</strong></li>";
    echo "</ul>";
    
    echo "<p><a href='auth/login.php'>→ Go to Login Page</a></p>";
    echo "<p style='color: #666; margin-top: 20px;'><strong>Note:</strong> You can login with either username or email.</p>";
    echo "</div>";
    
    echo "<div class='section' style='background: #fff3cd; border-left: 4px solid #ffc107;'>";
    echo "<h3>🔒 Security Warning</h3>";
    echo "<p style='color: #856404;'><strong>Important:</strong> Delete this file (<code>fix_passwords.php</code>) immediately after using it for security reasons!</p>";
    echo "<p style='color: #856404;'>To delete: Go to your project folder and remove <code>fix_passwords.php</code></p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='section'>";
    echo "<h2 class='error'>❌ Database Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running in XAMPP Control Panel</li>";
    echo "<li>Check if database 'library_management' exists</li>";
    echo "<li>Verify you have imported the database/library.sql file</li>";
    echo "<li>Check config/database.php for correct database credentials</li>";
    echo "</ul>";
    echo "</div>";
}
?>

</body>
</html>

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
