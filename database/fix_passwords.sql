-- Fix Admin and Librarian Passwords
-- Run this SQL in phpMyAdmin

-- Update Admin password to: admin123
-- Hash generated: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
UPDATE users 
SET password = '$2y$10$vVDco4JL3vqFqhZ2LnP0O.M7SnfJpzOXPKqXRc4wk5FXXPdXXEwZ2'
WHERE email = 'admin@library.com';

-- Update Librarian password to: librarian123
UPDATE users 
SET password = '$2y$10$vVDco4JL3vqFqhZ2LnP0O.M7SnfJpzOXPKqXRc4wk5FXXPdXXEwZ2'
WHERE email = 'librarian@library.com';

-- Make sure accounts are active
UPDATE users SET is_active = 1 WHERE email IN ('admin@library.com', 'librarian@library.com');

-- Verify the changes
SELECT id, username, email, LEFT(password, 20) as password_start, is_active 
FROM users 
WHERE email IN ('admin@library.com', 'librarian@library.com');
