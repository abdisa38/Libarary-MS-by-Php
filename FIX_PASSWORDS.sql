-- Fix Default Admin and Librarian Passwords
-- Run this SQL in phpMyAdmin to fix the password hashes
-- 
-- Instructions:
-- 1. Open phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Select the 'library_management' database
-- 3. Click on 'SQL' tab
-- 4. Copy and paste this entire file
-- 5. Click 'Go'
-- 6. Try logging in with admin / admin123

-- These are properly hashed passwords for:
-- admin123 and librarian123

-- Update Admin password (admin123)
UPDATE users 
SET password = '$2y$10$vFj4P3QYkQGCxfLWHv4EheaL3YCzP6kNGk5L3JnU9mZv.K5xZDHoS',
    is_active = 1 
WHERE email = 'admin@library.com';

-- Update Librarian password (librarian123)  
UPDATE users 
SET password = '$2y$10$8L0MqUWGR5fXJ8GqI2kZSuvH.xFEz8wKJH7P4L.L9Zf5vL3xm.4yC',
    is_active = 1
WHERE email = 'librarian@library.com';

-- Verify the updates
SELECT id, username, email, is_active, 
       SUBSTRING(password, 1, 20) as password_hash_start,
       (SELECT name FROM roles WHERE id = users.role_id) as role
FROM users 
WHERE email IN ('admin@library.com', 'librarian@library.com');

-- If you see output with updated password hashes starting with '$2y$10$v' and '$2y$10$8'
-- then the passwords have been updated successfully!
-- 
-- You can now login with:
-- Username: admin    Password: admin123
-- Username: librarian    Password: librarian123
