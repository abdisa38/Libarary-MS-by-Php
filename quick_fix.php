<?php
/**
 * QUICK PASSWORD FIX
 * Open this in browser to fix passwords immediately
 */

// Database connection
$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!DOCTYPE html><html><head><title>Quick Fix</title>";
    echo "<style>body{font-family:Arial;max-width:700px;margin:50px auto;padding:20px;background:#f5f5f5;}";
    echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
    echo ".success{color:green;font-weight:bold;}.error{color:red;font-weight:bold;}";
    echo "code{background:#f4f4f4;padding:3px 8px;border-radius:3px;font-family:monospace;}";
    echo "a{background:#4F46E5;color:white;padding:12px 24px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:15px;}";
    echo "a:hover{background:#4338CA;}</style></head><body>";
    
    echo "<div class='box'><h1>🔧 Quick Password Fix</h1></div>";
    
    // Generate fresh password hashes
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $lib_pass = password_hash('librarian123', PASSWORD_DEFAULT);
    
    echo "<div class='box'>";
    echo "<h3>Fixing Admin Password...</h3>";
    $stmt = $conn->prepare("UPDATE users SET password = ?, is_active = 1 WHERE username = 'admin'");
    $stmt->execute([$admin_pass]);
    echo "<p class='success'>✓ Admin password fixed!</p>";
    echo "<p>Username: <code>admin</code> | Password: <code>admin123</code></p>";
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h3>Fixing Librarian Password...</h3>";
    $stmt = $conn->prepare("UPDATE users SET password = ?, is_active = 1 WHERE username = 'librarian'");
    $stmt->execute([$lib_pass]);
    echo "<p class='success'>✓ Librarian password fixed!</p>";
    echo "<p>Username: <code>librarian</code> | Password: <code>librarian123</code></p>";
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h3>Testing Authentication...</h3>";
    
    // Test if passwords work
    $stmt = $conn->query("SELECT username, password FROM users WHERE username IN ('admin', 'librarian')");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $all_good = true;
    foreach ($users as $user) {
        $test_pass = $user['username'] . '123';
        if (password_verify($test_pass, $user['password'])) {
            echo "<p class='success'>✓ {$user['username']} can login</p>";
        } else {
            echo "<p class='error'>✗ {$user['username']} CANNOT login</p>";
            $all_good = false;
        }
    }
    echo "</div>";
    
    if ($all_good) {
        echo "<div class='box' style='background:#d4edda;border-left:4px solid #28a745;'>";
        echo "<h2 style='color:#155724;'>✅ ALL FIXED!</h2>";
        echo "<p style='color:#155724;'>Your passwords are working now. You can login!</p>";
        echo "<a href='auth/login.php'>→ Go to Login Page</a>";
        echo "</div>";
    } else {
        echo "<div class='box' style='background:#f8d7da;border-left:4px solid #dc3545;'>";
        echo "<h2 style='color:#721c24;'>⚠️ Something went wrong</h2>";
        echo "<p style='color:#721c24;'>Please check your database or contact support.</p>";
        echo "</div>";
    }
    
    echo "<div class='box' style='background:#fff3cd;border-left:4px solid #ffc107;'>";
    echo "<p style='color:#856404;'><strong>Security:</strong> Delete <code>quick_fix.php</code> after using!</p>";
    echo "</div>";
    
    echo "</body></html>";
    
} catch(PDOException $e) {
    echo "<!DOCTYPE html><html><head><title>Error</title></head><body style='font-family:Arial;padding:50px;'>";
    echo "<h1 style='color:red;'>❌ Database Connection Error</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>Check:</h3><ul>";
    echo "<li>Is MySQL running in XAMPP?</li>";
    echo "<li>Does database 'library_management' exist?</li>";
    echo "<li>Have you imported database/library.sql?</li>";
    echo "</ul></body></html>";
}
?>
