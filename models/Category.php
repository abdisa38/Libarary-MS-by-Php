<?php
/**
 * Category Model
 */

class Category {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new category
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Category creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find category by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Category find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all categories
     */
    public function getAll($search = '') {
        try {
            $sql = "SELECT c.*, COUNT(b.id) as book_count 
                    FROM categories c
                    LEFT JOIN books b ON c.id = b.category_id
                    WHERE 1=1";
            
            $params = [];
            
            if ($search) {
                $sql .= " AND c.name LIKE :search";
                $params[':search'] = "%$search%";
            }
            
            $sql .= " GROUP BY c.id ORDER BY c.name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get categories error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update category
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log('Category update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete category
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Category delete error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get category statistics
     */
    public function getStatistics() {
        try {
            $sql = "SELECT c.name, COUNT(b.id) as book_count 
                    FROM categories c
                    LEFT JOIN books b ON c.id = b.category_id
                    GROUP BY c.id, c.name
                    ORDER BY book_count DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Category statistics error: ' . $e->getMessage());
            return [];
        }
    }
}
?>
