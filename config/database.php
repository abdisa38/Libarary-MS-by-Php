<?php
/**
 * Database Configuration
 * Library Management System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'library_management');

// Application settings
define('APP_NAME', 'Library Management System');
define('APP_URL', 'http://localhost/library-management-system');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('ITEMS_PER_PAGE', 10);
define('DAILY_FINE_RATE', 5); // Fine per day in currency

// Timezone
date_default_timezone_set('Asia/Manila');

// Database connection class
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $conn;
    private $error;

    public function __construct() {
        $this->connect();
    }

    /**
     * Create database connection
     */
    private function connect() {
        $this->conn = null;
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true
            ];
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die('Database connection failed: ' . $this->error);
        }
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Close connection
     */
    public function close() {
        $this->conn = null;
    }
}

// Create global database instance
$database = new Database();
$db = $database->getConnection();

/**
 * Sanitize input data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check user role
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirect with message
 */
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header('Location: ' . $url);
    exit();
}

/**
 * Log activity
 */
function logActivity($db, $user_id, $action, $details = '') {
    try {
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $stmt->execute([$user_id, $action, $details, $ip, $user_agent]);
    } catch (PDOException $e) {
        error_log('Failed to log activity: ' . $e->getMessage());
    }
}

/**
 * Upload file
 */
function uploadFile($file, $folder = 'general') {
    $upload_dir = UPLOAD_PATH . $folder . '/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit.'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $folder . '/' . $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file.'];
}
?>
