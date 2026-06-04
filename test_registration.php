<?php
/**
 * Test Registration and Password System
 */

require_once 'config/database.php';
require_once 'models/User.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Registration System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f9f9f9; font-weight: bold; }
        .btn { display: inline-block; background: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .btn:hover { background: #4338CA; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>";

echo "<h1>🔍 Library Management System - Registration Test</h1>";

// Test 1: Check database connection
echo "<div class='section'>";
echo "<h2>Test 1: Database Connection</h2>";
try {
    $test = $db->query("SELECT 1");
    echo "<p class='success'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</body></html>";
    exit;
}
echo "</div>";

// Test 2: Check if roles table has data
echo "<div class='section'>";
echo "<h2>Test 2: Roles Table</h2>";
try {
    $stmt = $db->query("SELECT * FROM roles");
    $roles = $stmt->fetchAll();
    
    if (empty($roles)) {
        echo "<p class='error'>✗ No roles found in database. Please import the database schema first!</p>";
    } else {
        echo "<p class='success'>✓ Found " . count($roles) . " roles</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Role Name</th><th>Description</th></tr>";
        foreach ($roles as $role) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($role['id']) . "</td>";
            echo "<td>" . htmlspecialchars($role['name']) . "</td>";
            echo "<td>" . htmlspecialchars($role['description']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error checking roles: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Test 3: Check existing users
echo "<div class='section'>";
echo "<h2>Test 3: Existing Users</h2>";
try {
    $stmt = $db->query("SELECT u.id, u.username, u.email, r.name as role_name, u.is_active, u.created_at FROM users u INNER JOIN roles r ON u.role_id = r.id ORDER BY u.id");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p class='error'>✗ No users found. Default users should exist after database import.</p>";
    } else {
        echo "<p class='success'>✓ Found " . count($users) . " users</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Created</th></tr>";
        foreach ($users as $user) {
            $active = $user['is_active'] ? '✓ Yes' : '✗ No';
            $activeClass = $user['is_active'] ? 'success' : 'error';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role_name']) . "</td>";
            echo "<td class='$activeClass'>" . $active . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error checking users: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Test 4: Test password hashing
echo "<div class='section'>";
echo "<h2>Test 4: Password Hashing Test</h2>";
$test_password = 'admin123';
$hashed = password_hash($test_password, PASSWORD_DEFAULT);
$verify = password_verify($test_password, $hashed);

echo "<p>Test password: <code>$test_password</code></p>";
echo "<p>Hashed: <code>" . substr($hashed, 0, 50) . "...</code></p>";
echo "<p>Verification: " . ($verify ? "<span class='success'>✓ Success</span>" : "<span class='error'>✗ Failed</span>") . "</p>";
echo "</div>";

// Test 5: Test actual user passwords
echo "<div class='section'>";
echo "<h2>Test 5: Check User Passwords</h2>";
try {
    $stmt = $db->query("SELECT username, email, password FROM users WHERE email IN ('admin@library.com', 'librarian@library.com')");
    $users = $stmt->fetchAll();
    
    echo "<table>";
    echo "<tr><th>Username</th><th>Email</th><th>Password Hash</th><th>Can Login with Default?</th></tr>";
    
    foreach ($users as $user) {
        $default_password = $user['username'] . '123'; // admin123 or librarian123
        $can_login = password_verify($default_password, $user['password']);
        $status = $can_login ? "<span class='success'>✓ Yes</span>" : "<span class='error'>✗ No (Run fix_passwords.php)</span>";
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td><code>" . substr($user['password'], 0, 30) . "...</code></td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Note:</strong> If passwords cannot login with defaults, run the <code>fix_passwords.php</code> script.</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Test 6: Test creating a new user
echo "<div class='section'>";
echo "<h2>Test 6: Create Test User</h2>";

$test_username = 'testuser_' . time();
$test_email = 'test' . time() . '@test.com';

try {
    $userModel = new User($db);
    
    // Check if User class works
    echo "<p class='success'>✓ User model loaded successfully</p>";
    
    // Try to create a test user
    $data = [
        'role_id' => 3, // Student
        'username' => $test_username,
        'email' => $test_email,
        'password' => 'test123',
        'full_name' => 'Test User',
        'phone' => '1234567890',
        'address' => 'Test Address'
    ];
    
    $user_id = $userModel->create($data);
    
    if ($user_id) {
        echo "<p class='success'>✓ Test user created successfully! ID: $user_id</p>";
        echo "<p>Username: <code>$test_username</code></p>";
        echo "<p>Password: <code>test123</code></p>";
        
        // Try to authenticate
        $auth = $userModel->authenticate($test_username, 'test123');
        if ($auth) {
            echo "<p class='success'>✓ Authentication test PASSED - User can login!</p>";
        } else {
            echo "<p class='error'>✗ Authentication test FAILED - User cannot login!</p>";
        }
        
        // Clean up - delete test user
        $userModel->delete($user_id);
        echo "<p>✓ Test user deleted (cleanup)</p>";
        
    } else {
        echo "<p class='error'>✗ Failed to create test user</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Summary and Actions
echo "<div class='section'>";
echo "<h2>📋 Summary & Next Steps</h2>";
echo "<ol>";
echo "<li>If <strong>Test 5</strong> shows passwords cannot login: <a href='fix_passwords.php' class='btn'>Run Password Fix Script</a></li>";
echo "<li>After fixing passwords, try logging in: <a href='auth/login.php' class='btn'>Go to Login Page</a></li>";
echo "<li>If you registered a new account and it's not working, check if <code>is_active = 1</code> in the database</li>";
echo "<li>Check that your user role is correct (3 = student, 2 = librarian, 1 = admin)</li>";
echo "</ol>";

echo "<h3>Common Issues:</h3>";
echo "<ul>";
echo "<li><strong>\"Invalid credentials\"</strong> = Wrong password or password hash doesn't match</li>";
echo "<li><strong>\"Account is inactive\"</strong> = User's <code>is_active</code> field is 0</li>";
echo "<li><strong>Cannot login after registration</strong> = Check if role_id is set correctly (should be 3 for students)</li>";
echo "</ul>";

echo "<p style='color: red; margin-top: 20px;'><strong>Security Note:</strong> Delete this test file (<code>test_registration.php</code>) after debugging!</p>";
echo "</div>";

echo "</body></html>";
?>
