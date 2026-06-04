# Library Management System

A complete, production-ready, responsive Library Management System built with PHP, MySQL, HTML5, CSS3, and JavaScript.

## 🚀 Features

### User Roles
- **Admin**: Full system access and control
- **Librarian**: Manage books, borrowings, and returns
- **Student/User**: View and borrow books

### Authentication System
- ✅ Login with username/email
- ✅ Registration
- ✅ Forgot Password
- ✅ Reset Password
- ✅ Remember Me
- ✅ Session Management
- ✅ Role-Based Access Control
- ✅ Password Hashing (bcrypt)

### Admin Features
- 📊 Comprehensive Dashboard with statistics
- 📚 Book Management (Add, Edit, Delete, Search)
- 📁 Category Management
- ✍️ Author Management
- 🏢 Publisher Management
- 👨‍🎓 Student Management
- 📖 Borrowing Management
- 🔄 Return Management
- 💰 Fine Management (Automatic calculation)
- 📈 Reports and Analytics
- 👥 User Management
- ⚙️ System Settings

### Book Management
- ISBN tracking
- Book cover upload
- Multiple authors and categories
- Shelf location tracking
- Quantity and availability management
- Advanced search and filtering

### Borrowing System
- Real-time availability checking
- Automatic due date calculation
- Duplicate borrow prevention
- Automatic stock management
- Receipt generation

### Return System
- Automatic fine calculation
- Late fee management
- Return history tracking
- Stock restoration

### Search System
- Global search bar
- Search by title, ISBN, author, category
- Real-time search with AJAX
- Advanced filtering

### Reports
- Borrowing reports
- Return reports
- Overdue reports
- Fine reports
- Student reports
- Inventory reports
- Export to PDF, Excel, CSV

### Security Features
- SQL Injection Protection (Prepared Statements)
- XSS Protection
- CSRF Protection
- Session Security
- Input Validation
- File Upload Validation
- Password Hashing

### UI/UX Features
- 🎨 Modern, professional design
- 📱 Fully responsive (Mobile-first)
- 🌓 Dark/Light sidebar
- 📊 Beautiful data tables
- 🔔 Toast notifications
- 🎯 Modal dialogs
- 📄 Pagination
- ♿ Accessibility compliant

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 8+
- **Database**: MySQL
- **Server**: XAMPP

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache Web Server (XAMPP)
- Modern web browser

## 🔧 Installation

### Step 1: Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install XAMPP on your computer
3. Start Apache and MySQL services

### Step 2: Clone/Download Project
```bash
git clone https://github.com/yourusername/Libarary-MS-by-Php.git
cd Libarary-MS-by-Php
```

Or download the ZIP file and extract it to your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\library-management-system\
```

### Step 3: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click on "New" to create a new database
3. Name it: `library_management`
4. Select "utf8mb4_unicode_ci" as collation
5. Click "Create"

### Step 4: Import Database
1. In phpMyAdmin, select the `library_management` database
2. Click on "Import" tab
3. Choose file: `database/library.sql`
4. Click "Go" to import

### Step 5: Configure Database Connection
1. Open `config/database.php`
2. Update the following constants if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your MySQL password
define('DB_NAME', 'library_management');
```

### Step 6: Set Up Uploads Directory
Create the following directories and ensure they're writable:
```
uploads/
uploads/books/
uploads/students/
uploads/users/
```

### Step 7: Access the System
Open your web browser and navigate to:
```
http://localhost/library-management-system/
```

## 👤 Default Login Credentials

### Admin Account
- **Username**: admin
- **Email**: admin@library.com
- **Password**: admin123

### Librarian Account
- **Username**: librarian
- **Email**: librarian@library.com
- **Password**: librarian123

⚠️ **Important**: Change these default passwords after first login!

## 📁 Project Structure

```
library-management-system/
├── admin/                  # Admin dashboard and pages
│   ├── dashboard.php
│   ├── books.php
│   ├── categories.php
│   ├── authors.php
│   ├── publishers.php
│   ├── students.php
│   ├── borrowings.php
│   ├── returns.php
│   ├── users.php
│   └── settings.php
├── librarian/             # Librarian pages
├── student/               # Student pages
├── auth/                  # Authentication pages
│   ├── login.php
│   ├── register.php
│   ├── forgot-password.php
│   ├── reset-password.php
│   └── logout.php
├── assets/                # Static assets
│   ├── css/
│   │   ├── style.css
│   │   ├── auth.css
│   │   └── dashboard.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/                # Configuration files
│   ├── database.php
│   └── constants.php
├── models/                # Data models
│   ├── User.php
│   ├── Book.php
│   ├── Student.php
│   ├── Borrowing.php
│   ├── Category.php
│   ├── Author.php
│   └── Publisher.php
├── includes/              # Shared includes
│   ├── header.php
│   └── footer.php
├── uploads/               # Uploaded files
├── database/              # Database files
│   └── library.sql
├── index.php              # Main entry point
├── .htaccess             # Apache configuration
└── README.md             # This file
```

## 🔒 Security Best Practices

1. **Change Default Passwords**: Update all default passwords immediately
2. **Update Database Credentials**: Use strong database passwords
3. **File Permissions**: Set proper file permissions
   - Directories: 755
   - Files: 644
   - Uploads directory: 755 (writable)
4. **HTTPS**: Use SSL certificate in production
5. **Regular Updates**: Keep PHP and MySQL updated
6. **Backup**: Regular database backups

## 🎯 Usage Guide

### For Admin
1. Login with admin credentials
2. Add categories, authors, and publishers
3. Add books to the system
4. Register students
5. Manage borrowings and returns
6. View reports and analytics
7. Manage system users

### For Librarian
1. Login with librarian credentials
2. Process book borrowings
3. Process book returns
4. Calculate and collect fines
5. Search books and students
6. View reports

### For Students
1. Register an account
2. Login to the system
3. Browse available books
4. View borrowing history
5. Check due dates
6. View fines

## 📊 Database Schema

### Main Tables
- `users` - System users (Admin, Librarian)
- `roles` - User roles
- `students` - Student information
- `books` - Book catalog
- `categories` - Book categories
- `authors` - Author information
- `publishers` - Publisher information
- `borrowings` - Borrowing transactions
- `returns` - Return transactions
- `fines` - Fine records
- `notifications` - System notifications
- `activity_logs` - Activity logging
- `settings` - System settings

### Database Views
- `vw_borrowed_books` - Complete borrowing information
- `vw_overdue_books` - Overdue books view
- `vw_book_inventory` - Book inventory view

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Ensure database exists

### File Upload Issues
- Check folder permissions on `uploads/` directory
- Verify PHP `upload_max_filesize` in `php.ini`
- Check Apache `post_max_size` setting

### Session Issues
- Clear browser cookies
- Check PHP session settings
- Verify session directory is writable

### .htaccess Not Working
- Enable `mod_rewrite` in Apache
- Check `AllowOverride` is set to `All` in Apache config

## 📝 License

This project is open source and available for educational purposes.

## 👨‍💻 Developer

Created with ❤️ for library management

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

## 📧 Support

For support, email support@library.com or create an issue in the repository.

## 🔄 Updates

### Version 1.0.0 (Initial Release)
- Complete authentication system
- Admin, Librarian, and Student roles
- Book management
- Borrowing and return system
- Fine management
- Reporting system
- Responsive design
- Security features

## 📚 Additional Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [XAMPP Documentation](https://www.apachefriends.org/docs/)

---

**Made with PHP, MySQL, and dedication to simplify library management** 📚✨
