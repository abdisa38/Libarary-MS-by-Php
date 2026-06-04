<?php
/**
 * Author Model
 */

class Author {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new author
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO authors (name, biography) VALUES (:name, :biography)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':biography' => $data['biography'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Author creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find author by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM authors WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Author find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all authors
     */
    public function getAll($search = '') {
        try {
            $sql = "SELECT a.*, COUNT(b.id) as book_count 
                    FROM authors a
                    LEFT JOIN books b ON a.id = b.author_id
                    WHERE 1=1";
            
            $params = [];
            
            if ($search) {
                $sql .= " AND a.name LIKE :search";
                $params[':search'] = "%$search%";
            }
            
            $sql .= " GROUP BY a.id ORDER BY a.name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get authors error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update author
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE authors SET name = :name, biography = :biography WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':biography' => $data['biography'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log('Author update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete author
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM authors WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Author delete error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
