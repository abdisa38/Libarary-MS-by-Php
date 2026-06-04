<?php
/**
 * Borrowing Model
 */

class Borrowing {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new borrowing
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Insert borrowing record
            $sql = "INSERT INTO borrowings (student_id, book_id, borrow_date, due_date, status, created_by) 
                    VALUES (:student_id, :book_id, :borrow_date, :due_date, 'borrowed', :created_by)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':student_id' => $data['student_id'],
                ':book_id' => $data['book_id'],
                ':borrow_date' => $data['borrow_date'],
                ':due_date' => $data['due_date'],
                ':created_by' => $data['created_by']
            ]);
            
            $borrowing_id = $this->db->lastInsertId();
            
            // Decrease available copies (trigger handles this, but we can do it explicitly too)
            // The trigger will handle this automatically
            
            $this->db->commit();
            return $borrowing_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Borrowing creation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find borrowing by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT b.*, s.student_id, s.full_name as student_name, s.email as student_email,
                    bk.isbn, bk.title as book_title, a.name as author_name,
                    u.full_name as processed_by_name
                    FROM borrowings b
                    INNER JOIN students s ON b.student_id = s.id
                    INNER JOIN books bk ON b.book_id = bk.id
                    INNER JOIN authors a ON bk.author_id = a.id
                    INNER JOIN users u ON b.created_by = u.id
                    WHERE b.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Borrowing find error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all borrowings
     */
    public function getAll($status = null, $search = '', $page = 1, $limit = 10) {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT b.*, s.student_id, s.full_name as student_name,
                    bk.isbn, bk.title as book_title, a.name as author_name,
                    DATEDIFF(CURDATE(), b.due_date) as days_overdue
                    FROM borrowings b
                    INNER JOIN students s ON b.student_id = s.id
                    INNER JOIN books bk ON b.book_id = bk.id
                    INNER JOIN authors a ON bk.author_id = a.id
                    WHERE 1=1";
            
            $params = [];
            
            if ($status) {
                $sql .= " AND b.status = :status";
                $params[':status'] = $status;
            }
            
            if ($search) {
                $sql .= " AND (s.full_name LIKE :search OR s.student_id LIKE :search OR bk.title LIKE :search OR bk.isbn LIKE :search)";
                $params[':search'] = "%$search%";
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
            error_log('Get borrowings error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count borrowings
     */
    public function count($status = null, $search = '') {
        try {
            $sql = "SELECT COUNT(*) FROM borrowings b
                    INNER JOIN students s ON b.student_id = s.id
                    INNER JOIN books bk ON b.book_id = bk.id
                    WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND b.status = :status";
                $params[':status'] = $status;
            }
            
            if ($search) {
                $sql .= " AND (s.full_name LIKE :search OR s.student_id LIKE :search OR bk.title LIKE :search OR bk.isbn LIKE :search)";
                $params[':search'] = "%$search%";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Count borrowings error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Return book
     */
    public function returnBook($id, $user_id) {
        try {
            $this->db->beginTransaction();
            
            // Get borrowing details
            $borrowing = $this->findById($id);
            if (!$borrowing) {
                throw new Exception('Borrowing not found');
            }
            
            // Calculate fine if overdue
            $return_date = date('Y-m-d');
            $due_date = $borrowing['due_date'];
            $fine_amount = 0;
            
            if ($return_date > $due_date) {
                $days_late = (strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24);
                $fine_amount = $days_late * DAILY_FINE_RATE;
            }
            
            // Update borrowing status
            $sql = "UPDATE borrowings SET status = 'returned', return_date = :return_date WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id, ':return_date' => $return_date]);
            
            // Insert return record
            $sql = "INSERT INTO returns (borrowing_id, return_date, fine_amount, fine_status, processed_by) 
                    VALUES (:borrowing_id, :return_date, :fine_amount, :fine_status, :processed_by)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':borrowing_id' => $id,
                ':return_date' => $return_date,
                ':fine_amount' => $fine_amount,
                ':fine_status' => $fine_amount > 0 ? 'unpaid' : 'paid',
                ':processed_by' => $user_id
            ]);
            
            // Insert fine record if applicable
            if ($fine_amount > 0) {
                $sql = "INSERT INTO fines (borrowing_id, student_id, amount, reason, status) 
                        VALUES (:borrowing_id, :student_id, :amount, :reason, 'unpaid')";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':borrowing_id' => $id,
                    ':student_id' => $borrowing['student_id'],
                    ':amount' => $fine_amount,
                    ':reason' => 'Overdue book'
                ]);
            }
            
            // Increase available copies (trigger handles this)
            
            $this->db->commit();
            return [
                'success' => true,
                'fine_amount' => $fine_amount
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Return book error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Check if student has already borrowed a book
     */
    public function hasActiveBorrowing($student_id, $book_id) {
        try {
            $sql = "SELECT COUNT(*) FROM borrowings 
                    WHERE student_id = :student_id AND book_id = :book_id AND status = 'borrowed'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':student_id' => $student_id, ':book_id' => $book_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log('Check borrowing error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get borrowings by student
     */
    public function getByStudent($student_id, $status = null) {
        try {
            $sql = "SELECT b.*, bk.title as book_title, bk.isbn, a.name as author_name,
                    DATEDIFF(CURDATE(), b.due_date) as days_overdue
                    FROM borrowings b
                    INNER JOIN books bk ON b.book_id = bk.id
                    INNER JOIN authors a ON bk.author_id = a.id
                    WHERE b.student_id = :student_id";
            
            $params = [':student_id' => $student_id];
            
            if ($status) {
                $sql .= " AND b.status = :status";
                $params[':status'] = $status;
            }
            
            $sql .= " ORDER BY b.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get student borrowings error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get overdue borrowings
     */
    public function getOverdue() {
        try {
            $sql = "SELECT b.*, s.student_id, s.full_name as student_name, s.email,
                    bk.title as book_title, bk.isbn,
                    DATEDIFF(CURDATE(), b.due_date) as days_overdue
                    FROM borrowings b
                    INNER JOIN students s ON b.student_id = s.id
                    INNER JOIN books bk ON b.book_id = bk.id
                    WHERE b.status = 'borrowed' AND b.due_date < CURDATE()
                    ORDER BY b.due_date ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Get overdue error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update overdue status
     */
    public function updateOverdueStatus() {
        try {
            $sql = "UPDATE borrowings SET status = 'overdue' 
                    WHERE status = 'borrowed' AND due_date < CURDATE()";
            $stmt = $this->db->query($sql);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Update overdue status error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get borrowing statistics
     */
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total borrowed
            $sql = "SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed'";
            $stmt = $this->db->query($sql);
            $stats['total_borrowed'] = $stmt->fetchColumn();
            
            // Total returned
            $sql = "SELECT COUNT(*) FROM borrowings WHERE status = 'returned'";
            $stmt = $this->db->query($sql);
            $stats['total_returned'] = $stmt->fetchColumn();
            
            // Overdue
            $sql = "SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed' AND due_date < CURDATE()";
            $stmt = $this->db->query($sql);
            $stats['overdue'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (PDOException $e) {
            error_log('Statistics error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get monthly report
     */
    public function getMonthlyReport($year, $month) {
        try {
            $sql = "SELECT COUNT(*) as count, DATE(borrow_date) as date
                    FROM borrowings
                    WHERE YEAR(borrow_date) = :year AND MONTH(borrow_date) = :month
                    GROUP BY DATE(borrow_date)
                    ORDER BY date";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':year' => $year, ':month' => $month]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Monthly report error: ' . $e->getMessage());
            return [];
        }
    }
}
?>
