-- Library Management System Database
-- MySQL Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_management;

-- Table: roles
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    profile_picture VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    remember_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: authors
CREATE TABLE IF NOT EXISTS authors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    biography TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: publishers
CREATE TABLE IF NOT EXISTS publishers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    address TEXT,
    email VARCHAR(150),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: books
CREATE TABLE IF NOT EXISTS books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    publisher_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    available_copies INT NOT NULL DEFAULT 0,
    book_cover VARCHAR(255),
    shelf_number VARCHAR(50),
    description TEXT,
    date_added DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE CASCADE,
    INDEX idx_isbn (isbn),
    INDEX idx_title (title),
    INDEX idx_author (author_id),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: students
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    year_level VARCHAR(20),
    address TEXT,
    profile_picture VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_email (email),
    INDEX idx_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: borrowings
CREATE TABLE IF NOT EXISTS borrowings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_book (book_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: returns
CREATE TABLE IF NOT EXISTS returns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    borrowing_id INT NOT NULL,
    return_date DATE NOT NULL,
    fine_amount DECIMAL(10, 2) DEFAULT 0.00,
    fine_status ENUM('paid', 'unpaid') DEFAULT 'unpaid',
    remarks TEXT,
    processed_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (borrowing_id) REFERENCES borrowings(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_borrowing (borrowing_id),
    INDEX idx_return_date (return_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: fines
CREATE TABLE IF NOT EXISTS fines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    borrowing_id INT NOT NULL,
    student_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    reason TEXT,
    status ENUM('paid', 'unpaid') DEFAULT 'unpaid',
    paid_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (borrowing_id) REFERENCES borrowings(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: activity_logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: settings
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'System Administrator with full access'),
('librarian', 'Library staff managing books and borrowings'),
('student', 'Student user with limited access');

-- Insert default admin user (password: admin123)
INSERT INTO users (role_id, username, email, password, full_name, phone, address) VALUES
(1, 'admin', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '1234567890', 'Admin Office');

-- Insert default librarian (password: librarian123)
INSERT INTO users (role_id, username, email, password, full_name, phone, address) VALUES
(2, 'librarian', 'librarian@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Head Librarian', '0987654321', 'Library Office');

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Fiction', 'Fiction books and novels'),
('Non-Fiction', 'Non-fiction and factual books'),
('Science', 'Science and technology books'),
('History', 'Historical books and references'),
('Biography', 'Biographical works'),
('Technology', 'Technology and computer science'),
('Literature', 'Classic and modern literature'),
('Business', 'Business and economics'),
('Self-Help', 'Self-improvement and motivation'),
('Education', 'Educational materials and textbooks');

-- Insert sample authors
INSERT INTO authors (name, biography) VALUES
('J.K. Rowling', 'British author best known for the Harry Potter series'),
('George Orwell', 'English novelist and essayist'),
('Stephen King', 'American author of horror and suspense'),
('Jane Austen', 'English novelist known for romantic fiction'),
('Mark Twain', 'American writer and humorist'),
('Charles Dickens', 'English writer and social critic'),
('Ernest Hemingway', 'American novelist and short-story writer'),
('William Shakespeare', 'English poet and playwright'),
('Agatha Christie', 'English writer known for detective novels'),
('J.R.R. Tolkien', 'English writer and philologist');

-- Insert sample publishers
INSERT INTO publishers (name, address, email, phone) VALUES
('Penguin Random House', '1745 Broadway, New York, NY 10019', 'info@penguinrandomhouse.com', '212-782-9000'),
('HarperCollins', '195 Broadway, New York, NY 10007', 'info@harpercollins.com', '212-207-7000'),
('Simon & Schuster', '1230 Avenue of the Americas, New York, NY 10020', 'info@simonandschuster.com', '212-698-7000'),
('Macmillan Publishers', '120 Broadway, New York, NY 10271', 'info@macmillan.com', '646-307-5151'),
('Hachette Book Group', '1290 Avenue of the Americas, New York, NY 10104', 'info@hbgusa.com', '212-364-1100');

-- Insert sample books
INSERT INTO books (isbn, title, author_id, category_id, publisher_id, quantity, available_copies, shelf_number, description, date_added) VALUES
('978-0545010221', 'Harry Potter and the Philosopher''s Stone', 1, 1, 1, 5, 5, 'A-01', 'The first book in the Harry Potter series', '2024-01-01'),
('978-0451524935', '1984', 2, 1, 2, 3, 3, 'A-02', 'Dystopian social science fiction novel', '2024-01-02'),
('978-0307474278', 'The Shining', 3, 1, 3, 4, 4, 'A-03', 'Horror novel by Stephen King', '2024-01-03'),
('978-0141439518', 'Pride and Prejudice', 4, 7, 1, 3, 3, 'B-01', 'Romantic novel of manners', '2024-01-04'),
('978-0486280615', 'The Adventures of Tom Sawyer', 5, 1, 2, 2, 2, 'B-02', 'Novel about a boy growing up along the Mississippi River', '2024-01-05'),
('978-0141439723', 'Great Expectations', 6, 7, 1, 3, 3, 'B-03', 'Coming-of-age novel', '2024-01-06'),
('978-0684801223', 'The Old Man and the Sea', 7, 1, 4, 2, 2, 'C-01', 'Short novel about an aging fisherman', '2024-01-07'),
('978-0743477116', 'Romeo and Juliet', 8, 7, 5, 4, 4, 'C-02', 'Tragic love story', '2024-01-08'),
('978-0062073488', 'Murder on the Orient Express', 9, 1, 2, 3, 3, 'C-03', 'Detective novel featuring Hercule Poirot', '2024-01-09'),
('978-0547928227', 'The Hobbit', 10, 1, 3, 5, 5, 'D-01', 'Fantasy novel and children''s book', '2024-01-10');

-- Insert sample students
INSERT INTO students (student_id, full_name, email, phone, department, year_level, address) VALUES
('STU-2024-001', 'John Michael Santos', 'john.santos@student.edu', '09123456789', 'Computer Science', '3rd Year', '123 Main St, City'),
('STU-2024-002', 'Maria Clara Cruz', 'maria.cruz@student.edu', '09234567890', 'Information Technology', '2nd Year', '456 Oak Ave, City'),
('STU-2024-003', 'Jose Rizal Reyes', 'jose.reyes@student.edu', '09345678901', 'Engineering', '4th Year', '789 Pine Rd, City'),
('STU-2024-004', 'Anna Marie Garcia', 'anna.garcia@student.edu', '09456789012', 'Business Administration', '1st Year', '321 Elm St, City'),
('STU-2024-005', 'Pedro Miguel Torres', 'pedro.torres@student.edu', '09567890123', 'Computer Science', '3rd Year', '654 Maple Dr, City');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('daily_fine_rate', '5.00', 'Daily fine rate for overdue books'),
('borrow_duration', '14', 'Default borrow duration in days'),
('max_borrow_limit', '5', 'Maximum books a student can borrow'),
('library_name', 'City Public Library', 'Name of the library'),
('library_email', 'info@library.com', 'Library contact email'),
('library_phone', '1234567890', 'Library contact phone'),
('library_address', '123 Library Street, City', 'Library address');

-- Create views for reporting
CREATE OR REPLACE VIEW vw_borrowed_books AS
SELECT 
    b.id,
    b.borrow_date,
    b.due_date,
    b.return_date,
    b.status,
    s.student_id,
    s.full_name AS student_name,
    s.email AS student_email,
    bk.isbn,
    bk.title AS book_title,
    a.name AS author_name,
    c.name AS category_name,
    DATEDIFF(CURDATE(), b.due_date) AS days_overdue,
    u.full_name AS processed_by
FROM borrowings b
INNER JOIN students s ON b.student_id = s.id
INNER JOIN books bk ON b.book_id = bk.id
INNER JOIN authors a ON bk.author_id = a.id
INNER JOIN categories c ON bk.category_id = c.id
INNER JOIN users u ON b.created_by = u.id;

CREATE OR REPLACE VIEW vw_overdue_books AS
SELECT * FROM vw_borrowed_books
WHERE status = 'borrowed' AND due_date < CURDATE();

CREATE OR REPLACE VIEW vw_book_inventory AS
SELECT 
    bk.id,
    bk.isbn,
    bk.title,
    a.name AS author,
    c.name AS category,
    p.name AS publisher,
    bk.quantity,
    bk.available_copies,
    (bk.quantity - bk.available_copies) AS borrowed_copies,
    bk.shelf_number
FROM books bk
INNER JOIN authors a ON bk.author_id = a.id
INNER JOIN categories c ON bk.category_id = c.id
INNER JOIN publishers p ON bk.publisher_id = p.id;

-- Trigger to update book availability on borrow
DELIMITER $$
CREATE TRIGGER after_borrow_insert
AFTER INSERT ON borrowings
FOR EACH ROW
BEGIN
    UPDATE books SET available_copies = available_copies - 1 WHERE id = NEW.book_id;
END$$

CREATE TRIGGER after_return_update
AFTER UPDATE ON borrowings
FOR EACH ROW
BEGIN
    IF NEW.status = 'returned' AND OLD.status != 'returned' THEN
        UPDATE books SET available_copies = available_copies + 1 WHERE id = NEW.book_id;
    END IF;
END$$

DELIMITER ;
