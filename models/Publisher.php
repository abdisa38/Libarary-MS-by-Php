<?php
/**
 * Publisher Model
 */

class Publisher {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new publisher
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO publishers (name, address, email, phone) 
                    VALUES (:name, :address, :email, :phone)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':address' => $data['address'] ?? null,
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Publisher creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find publisher by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM publishers WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Publisher find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all publishers
     */
    public function getAll($search = '') {
        try {
            $sql = "SELECT p.*, COUNT(b.id) as book_count 
                    FROM publishers p
                    LEFT JOIN books b ON p.id = b.publisher_id
                    WHERE 1=1";
            
            $params = [];
            
            if ($search) {
                $sql .= " AND p.name LIKE :search";
                $params[':search'] = "%$search%";
            }
            
            $sql .= " GROUP BY p.id ORDER BY p.name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get publishers error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update publisher
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE publishers SET 
                    name = :name, 
                    address = :address, 
                    email = :email, 
                    phone = :phone 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':address' => $data['address'] ?? null,
                ':email' => $data['email'] ?? null,
                ':phone' => $data['phone'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log('Publisher update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete publisher
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM publishers WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Publisher delete error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
