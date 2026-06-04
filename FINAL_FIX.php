<?php
/**
 * FINAL FIX - Password Reset Tool
 * USE THIS BEFORE DELETING!
 */

$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = '';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Final Password Fix</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            background: white; 
            max-width: 800px; 
            width: 100%;
            border-radius: 12px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 32px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .step { 
            background: #f8f9fa; 
            padding: 20px; 
            margin: 15px 0; 
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .step h3 { color: #333; margin-bottom: 10px; }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 15px 0;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
        code { 
            background: #2d2d2d; 
            color: #f8f8f2;
            padding: 3px 8px; 
            border-radius: 4px; 
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover { 
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .credentials {
            background: white;
            border: 2px solid #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .credentials h4 { color: #495057; margin-bottom: 10px; }
        .credentials ul { list-style: none; }
        .credentials li { padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .credentials li:last-child { border-bottom: none; }
        .credentials strong { color: #667eea; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🔧 Final Password Fix</h1>
        <p>Reset admin and librarian passwords to default values</p>
    </div>
    
    <div class="content">
        <?php
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="success">';
            echo '<strong>✓ Database Connection Successful</strong><br>';
            echo 'Connected to: ' . $dbname;
            echo '</div>';
            
            // Step 1: Generate new password hashes
            echo '<div class="step">';
            echo '<h3>Step 1: Generating Password Hashes</h3>';
            
            $admin_new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $librarian_new_hash = password_hash('librarian123', PASSWORD_DEFAULT);
            
            echo '<p>✓ Password hashes generated using PHP <code>password_hash()</code></p>';
            echo '<table>';
            echo '<tr><th>Account</th><th>Password</th><th>Hash Preview</th></tr>';
            echo '<tr><td>Admin</td><td><code>admin123</code></td><td><code>' . substr($admin_new_hash, 0, 30) . '...</code></td></tr>';
            echo '<tr><td>Librarian</td><td><code>librarian123</code></td><td><code>' . substr($librarian_new_hash, 0, 30) . '...</code></td></tr>';
            echo '</table>';
            echo '</div>';
            
            // Step 2: Update passwords in database
            echo '<div class="step">';
            echo '<h3>Step 2: Updating Database</h3>';
            
            // Update admin
            $stmt = $pdo->prepare("UPDATE users SET password = :password, is_active = 1 WHERE username = 'admin'");
            $stmt->execute([':password' => $admin_new_hash]);
            $admin_updated = $stmt->rowCount();
            
            // Update librarian
            $stmt = $pdo->prepare("UPDATE users SET password = :password, is_active = 1 WHERE username = 'librarian'");
            $stmt->execute([':password' => $librarian_new_hash]);
            $librarian_updated = $stmt->rowCount();
            
            if ($admin_updated > 0) {
                echo '<p>✓ Admin password updated (Rows: ' . $admin_updated . ')</p>';
            } else {
                echo '<p class="error">✗ Admin not found or password already correct</p>';
            }
            
            if ($librarian_updated > 0) {
                echo '<p>✓ Librarian password updated (Rows: ' . $librarian_updated . ')</p>';
            } else {
                echo '<p class="error">✗ Librarian not found or password already correct</p>';
            }
            echo '</div>';
            
            // Step 3: Verify passwords work
            echo '<div class="step">';
            echo '<h3>Step 3: Verification Test</h3>';
            
            $stmt = $pdo->query("SELECT id, username, email, password, is_active FROM users WHERE username IN ('admin', 'librarian')");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $all_working = true;
            
            echo '<table>';
            echo '<tr><th>Username</th><th>Email</th><th>Active</th><th>Password Works?</th></tr>';
            
            foreach ($users as $user) {
                $test_password = $user['username'] . '123';
                $password_works = password_verify($test_password, $user['password']);
                $all_working = $all_working && $password_works;
                
                $status = $password_works ? '✓ YES' : '✗ NO';
                $statusClass = $password_works ? 'success' : 'error';
                $activeStatus = $user['is_active'] ? '✓ Active' : '✗ Inactive';
                
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($user['username']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                echo '<td>' . $activeStatus . '</td>';
                echo '<td class="' . $statusClass . '"><strong>' . $status . '</strong></td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
            
            // Results
            if ($all_working) {
                echo '<div class="success">';
                echo '<h3>🎉 SUCCESS! All Passwords Fixed!</h3>';
                echo '<p>Your admin and librarian accounts are now working.</p>';
                echo '</div>';
                
                echo '<div class="credentials">';
                echo '<h4>Admin Account:</h4>';
                echo '<ul>';
                echo '<li>Username: <strong>admin</strong></li>';
                echo '<li>Email: <strong>admin@library.com</strong></li>';
                echo '<li>Password: <strong>admin123</strong></li>';
                echo '</ul>';
                echo '</div>';
                
                echo '<div class="credentials">';
                echo '<h4>Librarian Account:</h4>';
                echo '<ul>';
                echo '<li>Username: <strong>librarian</strong></li>';
                echo '<li>Email: <strong>librarian@library.com</strong></li>';
                echo '<li>Password: <strong>librarian123</strong></li>';
                echo '</ul>';
                echo '</div>';
                
                echo '<div style="text-align: center; margin-top: 30px;">';
                echo '<a href="auth/login.php" class="btn">→ Go to Login Page</a>';
                echo '</div>';
                
                echo '<div class="warning" style="margin-top: 30px;">';
                echo '<strong>⚠️ SECURITY WARNING</strong><br>';
                echo 'Delete this file immediately: <code>FINAL_FIX.php</code><br>';
                echo 'Keeping this file is a security risk!';
                echo '</div>';
                
            } else {
                echo '<div class="error">';
                echo '<h3>❌ Verification Failed</h3>';
                echo '<p>Passwords were updated but verification failed. This might be a PHP version issue.</p>';
                echo '<p><strong>Manual Fix:</strong> Go to phpMyAdmin and run:</p>';
                echo '<code style="display: block; padding: 15px; margin: 10px 0;">UPDATE users SET is_active = 1 WHERE username IN (\'admin\', \'librarian\');</code>';
                echo '</div>';
            }
            
        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '<h3>❌ Database Error</h3>';
            echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<h4>Troubleshooting:</h4>';
            echo '<ul>';
            echo '<li>Make sure MySQL is running in XAMPP Control Panel</li>';
            echo '<li>Check if database <code>library_management</code> exists</li>';
            echo '<li>Verify you imported <code>database/library.sql</code> in phpMyAdmin</li>';
            echo '<li>Check database credentials in <code>config/database.php</code></li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="footer">
        <p>Library Management System - Password Reset Tool</p>
        <p style="color: #6c757d; font-size: 12px; margin-top: 10px;">After successful login, delete this file from your project folder</p>
    </div>
</div>

</body>
</html>
