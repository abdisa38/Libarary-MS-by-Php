# XAMPP Setup Guide

## Problem: Project Not in XAMPP htdocs

Your project is currently located at:
```
C:\Users\SPARK COMPUTERS MART\Videos\Library MS by php\Libarary-MS-by-Php
```

But XAMPP looks for projects in:
```
C:\xampp\htdocs\
```

## Solution 1: Copy Project to XAMPP htdocs (EASIEST)

### Steps:

1. **Open File Explorer** (Press Windows + E)

2. **Navigate to:**
   ```
   C:\Users\SPARK COMPUTERS MART\Videos\Library MS by php\
   ```

3. **Copy the folder:**
   - Right-click on `Libarary-MS-by-Php` folder
   - Click **Copy** (or press Ctrl+C)

4. **Navigate to XAMPP:**
   ```
   C:\xampp\htdocs\
   ```
   
   If XAMPP is not in C:\ drive, check:
   - `D:\xampp\htdocs\`
   - `C:\Program Files\xampp\htdocs\`

5. **Paste the folder:**
   - Right-click in the htdocs folder
   - Click **Paste** (or press Ctrl+V)

6. **Open in Browser:**
   ```
   http://localhost/Libarary-MS-by-Php
   ```

---

## Solution 2: Create Virtual Host (ADVANCED)

If you want to keep the project in its current location:

### Step 1: Edit Apache Config

1. **Open httpd-vhosts.conf:**
   ```
   C:\xampp\apache\conf\extra\httpd-vhosts.conf
   ```

2. **Add this at the end:**
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/Users/SPARK COMPUTERS MART/Videos/Library MS by php/Libarary-MS-by-Php"
       ServerName library.local
       <Directory "C:/Users/SPARK COMPUTERS MART/Videos/Library MS by php/Libarary-MS-by-Php">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

### Step 2: Edit Hosts File

1. **Open Notepad as Administrator:**
   - Press Windows key
   - Type "Notepad"
   - Right-click → "Run as administrator"

2. **Open hosts file:**
   - Click File → Open
   - Navigate to: `C:\Windows\System32\drivers\etc\`
   - Change filter to "All Files (*.*)"
   - Open the file named `hosts`

3. **Add this line at the end:**
   ```
   127.0.0.1 library.local
   ```

4. **Save and close**

### Step 3: Restart Apache

1. Open XAMPP Control Panel
2. Stop Apache
3. Start Apache

### Step 4: Access System

```
http://library.local
```

---

## Solution 3: Quick Test (TEMPORARY)

If XAMPP is not installed yet:

### Install XAMPP:

1. **Download XAMPP:**
   - Visit: https://www.apachefriends.org/download.html
   - Download for Windows
   - Choose PHP 8.x version

2. **Install:**
   - Run installer
   - Install to `C:\xampp`
   - Complete installation

3. **Start Services:**
   - Open XAMPP Control Panel
   - Click **Start** for Apache
   - Click **Start** for MySQL

4. **Follow Solution 1** to copy project

---

## Verify XAMPP Installation

### Check if XAMPP is Running:

1. Open browser
2. Go to: `http://localhost`
3. You should see XAMPP welcome page

If you see the welcome page, XAMPP is working!

---

## Quick Commands to Find XAMPP

Open Command Prompt and try:

```cmd
dir C:\xampp
dir D:\xampp
dir "C:\Program Files\xampp"
```

One of these should show the XAMPP folder.

---

## After Copying Project:

1. **Import Database:**
   - Go to: http://localhost/phpmyadmin
   - Create database: `library_management`
   - Import: `database/library.sql`

2. **Access System:**
   - Go to: http://localhost/Libarary-MS-by-Php

3. **Login:**
   - Username: `admin`
   - Password: `admin123`

---

## Need Help?

If you still get 404 error:

1. **Verify XAMPP is running:**
   - Apache should show "Running" in XAMPP Control Panel

2. **Check folder location:**
   - Make sure folder is in `C:\xampp\htdocs\`

3. **Check folder name in URL:**
   - Use exact folder name (case-sensitive on some systems)
   - If folder is `Libarary-MS-by-Php`, URL is: `http://localhost/Libarary-MS-by-Php`

4. **Clear browser cache:**
   - Press Ctrl+Shift+Delete
   - Clear cached files
   - Try again

---

## Success Indicators:

✅ XAMPP Control Panel shows Apache running (green)
✅ http://localhost shows XAMPP welcome page
✅ Project folder exists in htdocs
✅ http://localhost/Libarary-MS-by-Php shows login page

---

**Once you see the login page, your system is working!** 🎉
