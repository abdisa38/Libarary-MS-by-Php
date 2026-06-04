<?php
/**
 * User Model
 */

class User {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO users (role_id, username, email, password, full_name, phone, address, profile_picture) 
                    VALUES (:role_id, :username, :email, :password, :full_name, :phone, :address, :profile_picture)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':role_id' => $data['role_id'],
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':full_name' => $data['full_name'],
                ':phone' => $data['phone'] ?? null,
                ':address' => $data['address'] ?? null,
                ':profile_picture' => $data['profile_picture'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('User creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT u.*, r.name as role_name FROM users u 
                    INNER JOIN roles r ON u.role_id = r.id 
                    WHERE u.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('User find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT u.*, r.name as role_name FROM users u 
                    INNER JOIN roles r ON u.role_id = r.id 
                    WHERE u.email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('User find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find user by username
     */
    public function findByUsername($username) {
        try {
            $sql = "SELECT u.*, r.name as role_name FROM users u 
                    INNER JOIN roles r ON u.role_id = r.id 
                    WHERE u.username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('User find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($login, $password) {
        try {
            $sql = "SELECT u.*, r.name as role_name FROM users u 
                    INNER JOIN roles r ON u.role_id = r.id 
                    WHERE (u.email = :login OR u.username = :login) AND u.is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                return $user;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Authentication error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE users SET 
                    full_name = :full_name,
                    phone = :phone,
                    address = :address,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':full_name' => $data['full_name'],
                ':phone' => $data['phone'] ?? null,
                ':address' => $data['address'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log('User update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update password
     */
    public function updatePassword($id, $password) {
        try {
            $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $e) {
            error_log('Password update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update profile picture
     */
    public function updateProfilePicture($id, $filename) {
        try {
            $sql = "UPDATE users SET profile_picture = :profile_picture, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':profile_picture' => $filename]);
        } catch (PDOException $e) {
            error_log('Profile picture update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update last login
     */
    private function updateLastLogin($id) {
        try {
            $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Last login update error: ' . $e->getMessage());
        }
    }
    
    /**
     * Set remember token
     */
    public function setRememberToken($id, $token) {
        try {
            $sql = "UPDATE users SET remember_token = :token WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':token' => $token]);
        } catch (PDOException $e) {
            error_log('Remember token error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find by remember token
     */
    public function findByRememberToken($token) {
        try {
            $sql = "SELECT u.*, r.name as role_name FROM users u 
                    INNER JOIN roles r ON u.role_id = r.id 
                    WHERE u.remember_token = :token AND u.is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Token find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set reset token
     */
    public function setResetToken($email, $token) {
        try {
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $sql = "UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':email' => $email,
                ':token' => $token,
                ':expiry' => $expiry
            ]);
        } catch (PDOException $e) {
            error_log('Reset token error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find by reset token
     */
    public function findByResetToken($token) {
        try {
            $sql = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Token find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clear reset token
     */
    public function clearResetToken($id) {
        try {
            $sql = "UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Clear token error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users
     */
    public function getAll($role_id = null, $search = '', $page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT u.*, r.name as role_name FROM users u 
                    INNER JOIN roles r ON u.role_id = r.id WHERE 1=1";
            
            $params = [];
            
            if ($role_id) {
                $sql .= " AND u.role_id = :role_id";
                $params[':role_id'] = $role_id;
            }
            
            if ($search) {
                $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search OR u.username LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            $sql .= " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get users error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count users
     */
    public function count($role_id = null, $search = '') {
        try {
            $sql = "SELECT COUNT(*) FROM users u WHERE 1=1";
            $params = [];
            
            if ($role_id) {
                $sql .= " AND u.role_id = :role_id";
                $params[':role_id'] = $role_id;
            }
            
            if ($search) {
                $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search OR u.username LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Count users error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('User delete error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Toggle user status
     */
    public function toggleStatus($id) {
        try {
            $sql = "UPDATE users SET is_active = NOT is_active WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Toggle status error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
