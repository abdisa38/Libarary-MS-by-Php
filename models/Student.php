<?php
/**
 * Student Model
 */

class Student {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new student
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO students (student_id, full_name, email, phone, department, year_level, address, profile_picture) 
                    VALUES (:student_id, :full_name, :email, :phone, :department, :year_level, :address, :profile_picture)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':student_id' => $data['student_id'],
                ':full_name' => $data['full_name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'] ?? null,
                ':department' => $data['department'] ?? null,
                ':year_level' => $data['year_level'] ?? null,
                ':address' => $data['address'] ?? null,
                ':profile_picture' => $data['profile_picture'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Student creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find student by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM students WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Student find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find student by student ID
     */
    public function findByStudentId($student_id) {
        try {
            $sql = "SELECT * FROM students WHERE student_id = :student_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':student_id' => $student_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Student find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find student by email
     */
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM students WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Student find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all students
     */
    public function getAll($search = '', $department = null, $page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT * FROM students WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (full_name LIKE :search OR student_id LIKE :search OR email LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($department) {
                $sql .= " AND department = :department";
                $params[':department'] = $department;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get students error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count students
     */
    public function count($search = '', $department = null) {
        try {
            $sql = "SELECT COUNT(*) FROM students WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (full_name LIKE :search OR student_id LIKE :search OR email LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($department) {
                $sql .= " AND department = :department";
                $params[':department'] = $department;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Count students error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update student
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE students SET 
                    full_name = :full_name,
                    email = :email,
                    phone = :phone,
                    department = :department,
                    year_level = :year_level,
                    address = :address,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':full_name' => $data['full_name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'] ?? null,
                ':department' => $data['department'] ?? null,
                ':year_level' => $data['year_level'] ?? null,
                ':address' => $data['address'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log('Student update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update profile picture
     */
    public function updateProfilePicture($id, $filename) {
        try {
            $sql = "UPDATE students SET profile_picture = :profile_picture, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':profile_picture' => $filename]);
        } catch (PDOException $e) {
            error_log('Profile picture update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete student
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM students WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Student delete error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Toggle student status
     */
    public function toggleStatus($id) {
        try {
            $sql = "UPDATE students SET is_active = NOT is_active WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Toggle status error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get student statistics
     */
    public function getStudentStatistics($student_id) {
        try {
            $stats = [];
            
            // Total borrowed
            $sql = "SELECT COUNT(*) FROM borrowings WHERE student_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $student_id]);
            $stats['total_borrowed'] = $stmt->fetchColumn();
            
            // Currently borrowed
            $sql = "SELECT COUNT(*) FROM borrowings WHERE student_id = :id AND status = 'borrowed'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $student_id]);
            $stats['current_borrowed'] = $stmt->fetchColumn();
            
            // Overdue books
            $sql = "SELECT COUNT(*) FROM borrowings WHERE student_id = :id AND status = 'borrowed' AND due_date < CURDATE()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $student_id]);
            $stats['overdue'] = $stmt->fetchColumn();
            
            // Total fines
            $sql = "SELECT SUM(amount) FROM fines WHERE student_id = :id AND status = 'unpaid'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $student_id]);
            $stats['unpaid_fines'] = $stmt->fetchColumn() ?? 0;
            
            return $stats;
        } catch (PDOException $e) {
            error_log('Statistics error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search students (for AJAX)
     */
    public function search($term, $limit = 10) {
        try {
            $sql = "SELECT id, student_id, full_name, email, department 
                    FROM students 
                    WHERE full_name LIKE :term OR student_id LIKE :term OR email LIKE :term
                    AND is_active = 1
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':term', "%$term%");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Search error: ' . $e->getMessage());
            return [];
        }
    }
}
?>
