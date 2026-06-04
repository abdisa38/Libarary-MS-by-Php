<?php
/**
 * Book Model
 */

class Book {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new book
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO books (isbn, title, author_id, category_id, publisher_id, quantity, available_copies, book_cover, shelf_number, description, date_added) 
                    VALUES (:isbn, :title, :author_id, :category_id, :publisher_id, :quantity, :available_copies, :book_cover, :shelf_number, :description, :date_added)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':isbn' => $data['isbn'],
                ':title' => $data['title'],
                ':author_id' => $data['author_id'],
                ':category_id' => $data['category_id'],
                ':publisher_id' => $data['publisher_id'],
                ':quantity' => $data['quantity'],
                ':available_copies' => $data['quantity'], // Initially all copies are available
                ':book_cover' => $data['book_cover'] ?? null,
                ':shelf_number' => $data['shelf_number'] ?? null,
                ':description' => $data['description'] ?? null,
                ':date_added' => $data['date_added'] ?? date('Y-m-d')
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Book creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find book by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT b.*, a.name as author_name, c.name as category_name, p.name as publisher_name 
                    FROM books b
                    INNER JOIN authors a ON b.author_id = a.id
                    INNER JOIN categories c ON b.category_id = c.id
                    INNER JOIN publishers p ON b.publisher_id = p.id
                    WHERE b.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Book find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find book by ISBN
     */
    public function findByISBN($isbn) {
        try {
            $sql = "SELECT * FROM books WHERE isbn = :isbn";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':isbn' => $isbn]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Book find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all books
     */
    public function getAll($search = '', $category_id = null, $author_id = null, $page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT b.*, a.name as author_name, c.name as category_name, p.name as publisher_name 
                    FROM books b
                    INNER JOIN authors a ON b.author_id = a.id
                    INNER JOIN categories c ON b.category_id = c.id
                    INNER JOIN publishers p ON b.publisher_id = p.id
                    WHERE 1=1";
            
            $params = [];
            
            if ($search) {
                $sql .= " AND (b.title LIKE :search OR b.isbn LIKE :search OR a.name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($category_id) {
                $sql .= " AND b.category_id = :category_id";
                $params[':category_id'] = $category_id;
            }
            
            if ($author_id) {
                $sql .= " AND b.author_id = :author_id";
                $params[':author_id'] = $author_id;
            }
            
            $sql .= " ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get books error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count books
     */
    public function count($search = '', $category_id = null, $author_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM books b
                    INNER JOIN authors a ON b.author_id = a.id
                    WHERE 1=1";
            $params = [];
            
            if ($search) {
                $sql .= " AND (b.title LIKE :search OR b.isbn LIKE :search OR a.name LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            if ($category_id) {
                $sql .= " AND b.category_id = :category_id";
                $params[':category_id'] = $category_id;
            }
            
            if ($author_id) {
                $sql .= " AND b.author_id = :author_id";
                $params[':author_id'] = $author_id;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Count books error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update book
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE books SET 
                    isbn = :isbn,
                    title = :title,
                    author_id = :author_id,
                    category_id = :category_id,
                    publisher_id = :publisher_id,
                    quantity = :quantity,
                    shelf_number = :shelf_number,
                    description = :description,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':isbn' => $data['isbn'],
                ':title' => $data['title'],
                ':author_id' => $data['author_id'],
                ':category_id' => $data['category_id'],
                ':publisher_id' => $data['publisher_id'],
                ':quantity' => $data['quantity'],
                ':shelf_number' => $data['shelf_number'] ?? null,
                ':description' => $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log('Book update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update book cover
     */
    public function updateCover($id, $filename) {
        try {
            $sql = "UPDATE books SET book_cover = :book_cover, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':book_cover' => $filename]);
        } catch (PDOException $e) {
            error_log('Book cover update error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update available copies
     */
    public function updateAvailableCopies($id, $change) {
        try {
            $sql = "UPDATE books SET available_copies = available_copies + :change WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':change' => $change]);
        } catch (PDOException $e) {
            error_log('Update copies error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete book
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM books WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Book delete error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if book is available
     */
    public function isAvailable($id) {
        try {
            $sql = "SELECT available_copies FROM books WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result && $result['available_copies'] > 0;
        } catch (PDOException $e) {
            error_log('Availability check error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get low stock books
     */
    public function getLowStock($threshold = 2) {
        try {
            $sql = "SELECT b.*, a.name as author_name, c.name as category_name 
                    FROM books b
                    INNER JOIN authors a ON b.author_id = a.id
                    INNER JOIN categories c ON b.category_id = c.id
                    WHERE b.available_copies <= :threshold
                    ORDER BY b.available_copies ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':threshold' => $threshold]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Low stock error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get statistics
     */
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total books
            $sql = "SELECT COUNT(*) as total FROM books";
            $stmt = $this->db->query($sql);
            $stats['total_books'] = $stmt->fetchColumn();
            
            // Total available
            $sql = "SELECT SUM(available_copies) as available FROM books";
            $stmt = $this->db->query($sql);
            $stats['available_books'] = $stmt->fetchColumn() ?? 0;
            
            // Total borrowed
            $sql = "SELECT SUM(quantity - available_copies) as borrowed FROM books";
            $stmt = $this->db->query($sql);
            $stats['borrowed_books'] = $stmt->fetchColumn() ?? 0;
            
            return $stats;
        } catch (PDOException $e) {
            error_log('Statistics error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search books (for AJAX)
     */
    public function search($term, $limit = 10) {
        try {
            $sql = "SELECT b.id, b.isbn, b.title, a.name as author_name, b.available_copies 
                    FROM books b
                    INNER JOIN authors a ON b.author_id = a.id
                    WHERE b.title LIKE :term OR b.isbn LIKE :term OR a.name LIKE :term
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
