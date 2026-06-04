# 📦 Installation Guide - Library Management System

## Prerequisites

Before you begin, ensure you have:
- ✅ Windows, macOS, or Linux operating system
- ✅ XAMPP installed (PHP 8.0+ and MySQL 5.7+)
- ✅ Git installed (optional, for cloning)
- ✅ Modern web browser (Chrome, Firefox, Edge, Safari)

---

## 🚀 Quick Start Guide

### Step 1: Download and Install XAMPP

1. **Download XAMPP:**
   - Visit: https://www.apachefriends.org/download.html
   - Download the version for your operating system
   - Choose PHP 8.0 or higher

2. **Install XAMPP:**
   - Run the installer
   - Follow installation wizard
   - Install to default location: `C:\xampp` (Windows) or `/Applications/XAMPP` (Mac)

3. **Start Services:**
   - Open XAMPP Control Panel
   - Click **Start** for Apache
   - Click **Start** for MySQL
   - Verify both services are running (green status)

---

### Step 2: Get the Project Files

**Option A: Clone from GitHub (Recommended)**
```bash
cd C:\xampp\htdocs
git clone https://github.com/yourusername/Libarary-MS-by-Php.git library-management-system
```

**Option B: Download ZIP**
1. Download ZIP from GitHub
2. Extract to `C:\xampp\htdocs\library-management-system`

---

### Step 3: Create Database

1. **Open phpMyAdmin:**
   - Open browser
   - Go to: `http://localhost/phpmyadmin`

2. **Create Database:**
   - Click **New** in the left sidebar
   - Database name: `library_management`
   - Collation: `utf8mb4_unicode_ci`
   - Click **Create**

3. **Import Database Schema:**
   - Select `library_management` database
   - Click **Import** tab
   - Click **Choose File**
   - Navigate to: `library-management-system/database/library.sql`
   - Click **Go**
   - Wait for "Import has been successfully finished"

---

### Step 4: Configure the System

1. **Database Configuration:**
   - Open: `config/database.php`
   - Verify settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Add password if you set one
   define('DB_NAME', 'library_management');
   ```

2. **Create Upload Directories:**
   The system needs writable directories for uploads.
   
   **Windows (Command Prompt):**
   ```cmd
   cd C:\xampp\htdocs\library-management-system
   mkdir uploads\books
   mkdir uploads\students
   mkdir uploads\users
   ```

   **Mac/Linux (Terminal):**
   ```bash
   cd /Applications/XAMPP/htdocs/library-management-system
   mkdir -p uploads/{books,students,users}
   chmod -R 755 uploads
   ```

---

### Step 5: Access the System

1. **Open Browser:**
   - Navigate to: `http://localhost/library-management-system`
   - You should be redirected to the login page

2. **Login with Default Credentials:**

   **Admin Account:**
   - Username: `admin`
   - Password: `admin123`

   **Librarian Account:**
   - Username: `librarian`
   - Password: `librarian123`

3. **First Login:**
   - Change default passwords immediately
   - Go to Profile → Change Password

---

## 🎯 Post-Installation Setup

### 1. Change Default Passwords

**For Admin:**
1. Login as admin
2. Click user avatar (top right)
3. Select "Profile"
4. Click "Change Password"
5. Enter new strong password
6. Save changes

### 2. Configure System Settings

1. Go to: **Settings** (Admin only)
2. Update:
   - Library Name
   - Contact Information
   - Daily Fine Rate
   - Borrow Duration (days)
   - Max Borrow Limit

### 3. Add Initial Data

**Categories:**
1. Go to: **Categories**
2. Add common categories:
   - Fiction, Non-Fiction, Science, History, etc.

**Authors:**
1. Go to: **Authors**
2. Add popular authors

**Publishers:**
1. Go to: **Publishers**
2. Add publisher information

**Books:**
1. Go to: **Books**
2. Click "Add New Book"
3. Fill in book details
4. Upload book cover (optional)
5. Save

**Students:**
1. Go to: **Students**
2. Click "Add New Student"
3. Enter student information
4. Save

---

## ⚙️ Advanced Configuration

### Enable Pretty URLs (Optional)

The `.htaccess` file is already included. Ensure:

1. **Enable mod_rewrite in Apache:**
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Remove `#` to uncomment
   - Save and restart Apache

2. **Verify AllowOverride:**
   - In same `httpd.conf` file
   - Find: `AllowOverride None`
   - Change to: `AllowOverride All`
   - Save and restart Apache

### PHP Configuration (if needed)

Edit `C:\xampp\php\php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M

; Enable these extensions if not already enabled
extension=mysqli
extension=pdo_mysql
extension=gd
extension=mbstring
```

Restart Apache after changes.

---

## 🔐 Security Checklist

After installation:

- [ ] Change all default passwords
- [ ] Update database credentials if needed
- [ ] Set proper file permissions
- [ ] Review `.htaccess` security headers
- [ ] Disable PHP error display in production:
  ```php
  // In config/database.php
  ini_set('display_errors', 0);
  error_reporting(0);
  ```
- [ ] Enable HTTPS in production
- [ ] Regular database backups

---

## 🐛 Troubleshooting

### Issue: "Database connection failed"

**Solution:**
1. Verify MySQL is running in XAMPP
2. Check database name is correct: `library_management`
3. Verify username (usually `root`) and password
4. Test connection in phpMyAdmin

### Issue: "Access denied for user 'root'@'localhost'"

**Solution:**
1. Open phpMyAdmin
2. Go to: User accounts
3. Edit root user
4. Set password or remove password
5. Update `config/database.php`

### Issue: "Cannot upload files"

**Solution:**
1. Check `uploads` folder exists
2. Verify folder permissions (should be writable)
3. Check PHP `upload_max_filesize` setting
4. Verify disk space available

### Issue: "Page not found (404)"

**Solution:**
1. Check XAMPP Apache is running
2. Verify project folder location: `htdocs/library-management-system`
3. Use correct URL: `http://localhost/library-management-system`
4. Check `.htaccess` file exists

### Issue: "Session errors"

**Solution:**
1. Clear browser cookies
2. Close all browser tabs
3. Restart browser
4. Login again

### Issue: ".htaccess not working"

**Solution:**
1. Enable `mod_rewrite` in Apache (see Advanced Configuration)
2. Change `AllowOverride None` to `AllowOverride All`
3. Restart Apache

---

## 📱 Testing the Installation

### Test Checklist:

1. **Authentication:**
   - [ ] Login works
   - [ ] Logout works
   - [ ] Register new user works
   - [ ] Forgot password generates reset link

2. **Admin Functions:**
   - [ ] Dashboard displays statistics
   - [ ] Can add/edit/delete books
   - [ ] Can add/edit/delete categories
   - [ ] Can add/edit/delete students
   - [ ] Can process borrowings
   - [ ] Can process returns

3. **File Uploads:**
   - [ ] Book cover upload works
   - [ ] Student photo upload works
   - [ ] Files saved in correct directory

4. **Responsive Design:**
   - [ ] Mobile menu works
   - [ ] Tables are scrollable on mobile
   - [ ] Forms are usable on mobile

---

## 🔄 Updating the System

To update to a new version:

```bash
cd C:\xampp\htdocs\library-management-system
git pull origin main
```

**After pulling updates:**
1. Check for database migrations in `database/` folder
2. Backup your database first
3. Run any new SQL scripts
4. Clear browser cache
5. Test functionality

---

## 💾 Backup

### Database Backup (Recommended: Daily)

**Method 1: phpMyAdmin**
1. Open phpMyAdmin
2. Select `library_management` database
3. Click **Export**
4. Choose **Quick** export method
5. Format: SQL
6. Click **Go**
7. Save the `.sql` file

**Method 2: Command Line**
```bash
cd C:\xampp\mysql\bin
mysqldump -u root -p library_management > backup.sql
```

### File Backup

Backup these folders:
- `uploads/` - User uploaded files
- `config/` - Configuration files

---

## 📞 Support

If you encounter issues:

1. Check this installation guide
2. Review the troubleshooting section
3. Check the main README.md
4. Review error logs: `C:\xampp\apache\logs\error.log`
5. Create an issue on GitHub

---

## ✅ Installation Complete!

Your Library Management System is now ready to use! 🎉

**Next Steps:**
1. Login as admin
2. Change default passwords
3. Configure system settings
4. Add your library data
5. Start managing your library!

---

**Happy Library Managing! 📚**
