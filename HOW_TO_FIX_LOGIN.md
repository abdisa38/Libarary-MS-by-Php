# 🔧 How to Fix Login Issues

## Problem
Getting "Invalid credentials or account is inactive" error when trying to login.

---

## ✅ SOLUTION (Step by Step)

### **Step 1: Use the Fix Script**

1. **Open your browser**
2. **Type this URL:**
   ```
   http://localhost/Libarary-MS-by-Php/FINAL_FIX.php
   ```
3. **Press Enter**
4. **You should see:**
   - ✓ Database Connection Successful
   - ✓ Password hashes generated
   - ✓ Admin password updated
   - ✓ Librarian password updated
   - ✓ Verification: YES, YES
   - 🎉 SUCCESS! All Passwords Fixed!

5. **Click the "Go to Login Page" button**

6. **Login with:**
   - Username: `admin`
   - Password: `admin123`

7. **IMPORTANT: Delete FINAL_FIX.php after using it**

---

### **Step 2: If Step 1 Doesn't Work - Manual SQL Fix**

1. **Open phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Click on `library_management` database** (left sidebar)

3. **Click SQL tab** (top menu)

4. **Copy and paste this SQL code:**

```sql
-- First, let's see current users
SELECT id, username, email, is_active FROM users;

-- Update admin password
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    is_active = 1 
WHERE username = 'admin';

-- Update librarian password  
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    is_active = 1 
WHERE username = 'librarian';

-- Verify the changes
SELECT username, email, is_active, 
       SUBSTRING(password, 1, 20) as password_start
FROM users 
WHERE username IN ('admin', 'librarian');
```

5. **Click "Go"**

6. **Now passwords are:** (BOTH use same hash temporarily)
   - admin / admin123
   - librarian / librarian123

---

### **Step 3: If You Still Can't Login**

#### **Check Database Directly:**

1. Go to phpMyAdmin → `library_management` → `users` table
2. Click "Browse"
3. Look at admin and librarian rows
4. **Check these columns:**

   | Column | Should Be |
   |--------|-----------|
   | `is_active` | **1** (not 0) |
   | `password` | Starts with `$2y$10$` |
   | `role_id` | **1** for admin, **2** for librarian |

5. **If `is_active` is 0, run this:**
   ```sql
   UPDATE users SET is_active = 1 WHERE username IN ('admin', 'librarian');
   ```

---

### **Step 4: Test Your Registered Account**

If you registered a new account (like "abdi") and can't login:

1. **Check in phpMyAdmin:**
   - Database: `library_management`
   - Table: `users`
   - Find your username

2. **Verify:**
   - `is_active` = **1**
   - `role_id` = **3** (for students)
   - `password` starts with `$2y$10$`

3. **If `is_active` is 0:**
   ```sql
   UPDATE users SET is_active = 1 WHERE username = 'your_username';
   ```

4. **If you forgot your password, reset it:**
   ```sql
   -- Replace 'your_username' and 'newpassword123'
   UPDATE users 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
   WHERE username = 'your_username';
   ```
   Then login with: `newpassword123`

---

## 🐛 Common Issues & Solutions

### Issue 1: "Invalid credentials"
**Cause:** Password hash doesn't match
**Solution:** Run FINAL_FIX.php or use SQL method above

### Issue 2: "Account is inactive"  
**Cause:** `is_active` column is 0
**Solution:** 
```sql
UPDATE users SET is_active = 1 WHERE username = 'your_username';
```

### Issue 3: "User not found"
**Cause:** Username spelled wrong or user doesn't exist
**Solution:** 
- Check exact username in database
- Try with email instead of username
- Make sure database was imported

### Issue 4: Login page shows but nothing happens
**Cause:** PHP errors
**Solution:**
- Check `C:\xampp\apache\logs\error.log`
- Make sure `config/database.php` is correct
- Verify MySQL is running

---

## 🎯 Quick Test Checklist

Before trying to login, verify:

- [ ] XAMPP Apache is **running** (green)
- [ ] XAMPP MySQL is **running** (green)
- [ ] Database `library_management` **exists** in phpMyAdmin
- [ ] Database has **tables** (users, roles, books, etc.)
- [ ] Admin user **exists** in users table
- [ ] Admin `is_active` = **1**
- [ ] Admin password starts with `$2y$`
- [ ] You can access: `http://localhost/Libarary-MS-by-Php`

---

## 📞 Still Not Working?

If you've tried everything and still can't login:

1. **Take screenshots of:**
   - phpMyAdmin → users table (admin row)
   - The login page with error
   - XAMPP Control Panel

2. **Check error logs:**
   ```
   C:\xampp\apache\logs\error.log
   C:\xampp\mysql\data\mysql_error.log
   ```

3. **Verify files exist:**
   - `config/database.php`
   - `models/User.php`
   - `auth/login.php`

4. **Test database connection:**
   Create a file `test_db.php`:
   ```php
   <?php
   try {
       $pdo = new PDO('mysql:host=localhost;dbname=library_management', 'root', '');
       echo "✓ Database connection works!";
       
       $stmt = $pdo->query("SELECT COUNT(*) FROM users");
       $count = $stmt->fetchColumn();
       echo "<br>✓ Found $count users in database";
   } catch (Exception $e) {
       echo "✗ Error: " . $e->getMessage();
   }
   ```
   Access: `http://localhost/Libarary-MS-by-Php/test_db.php`

---

## ⚠️ Security Reminders

After fixing and logging in successfully:

1. **Delete these files:**
   - `FINAL_FIX.php`
   - `test_db.php` (if you created it)
   - `HOW_TO_FIX_LOGIN.md` (this file, optional)

2. **Change default passwords:**
   - Go to Profile → Change Password
   - Use strong passwords

3. **Check file permissions:**
   - `uploads/` folder should be writable
   - Other folders should be read-only

---

**Try FINAL_FIX.php first - it's the easiest solution!** 🚀
