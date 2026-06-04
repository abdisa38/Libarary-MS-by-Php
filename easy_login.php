<?php
/**
 * EASY LOGIN - Simple login without database password check
 * Just type "admin" and ANY password to login
 * DELETE THIS FILE AFTER USE!
 */

session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    
    // Get user from database by username only (no password check)
    try {
        $stmt = $db->prepare("SELECT u.*, r.name as role_name FROM users u 
                              INNER JOIN roles r ON u.role_id = r.id 
                              WHERE u.username = :username AND u.is_active = 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Login successful - no password check!
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect to appropriate dashboard
            switch ($user['role_name']) {
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
                    header('Location: index.php');
            }
            exit();
        } else {
            $error = 'User not found or inactive';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easy Login - Library Management</title>
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
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
            font-size: 13px;
        }
        .warning strong { display: block; margin-bottom: 5px; }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .quick-links {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }
        .quick-links h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 12px;
            text-align: center;
        }
        .quick-links a {
            display: block;
            background: #f8f9fa;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            text-align: center;
            transition: background 0.3s;
            font-size: 14px;
        }
        .quick-links a:hover {
            background: #e9ecef;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 13px;
            text-align: center;
        }
        code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🚀 Easy Login</h1>
    <p class="subtitle">No password required - just username!</p>
    
    <div class="warning">
        <strong>⚠️ Temporary Login Only</strong>
        This bypasses password checks. Delete this file after testing!
    </div>

    <?php if ($error): ?>
        <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Just Type Username:</label>
            <input type="text" id="username" name="username" required autofocus placeholder="admin or librarian">
        </div>

        <div class="form-group">
            <label for="password">Password (any text works):</label>
            <input type="password" id="password" name="password" value="anything" placeholder="Type anything or leave as is">
        </div>

        <button type="submit" class="btn">→ Login Instantly</button>
    </form>

    <div class="info">
        💡 Type <code>admin</code> and click login. Password doesn't matter!
    </div>

    <div class="quick-links">
        <h3>Or use instant login:</h3>
        <a href="auto_login.php">🎯 Auto Login as Admin (No Form)</a>
        <a href="auth/login.php">🔐 Regular Login Page</a>
    </div>
</div>

</body>
</html>
