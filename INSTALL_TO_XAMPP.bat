@echo off
cls
echo ========================================
echo Library Management System Installer
echo ========================================
echo.
echo This will copy the project to XAMPP htdocs
echo.

:: Check for XAMPP in common locations
set XAMPP_FOUND=0
set XAMPP_PATH=

if exist "C:\xampp\htdocs\" (
    set XAMPP_PATH=C:\xampp\htdocs
    set XAMPP_FOUND=1
    goto :install
)

if exist "D:\xampp\htdocs\" (
    set XAMPP_PATH=D:\xampp\htdocs
    set XAMPP_FOUND=1
    goto :install
)

if exist "C:\Program Files\xampp\htdocs\" (
    set XAMPP_PATH=C:\Program Files\xampp\htdocs
    set XAMPP_FOUND=1
    goto :install
)

:: XAMPP not found
echo ERROR: XAMPP htdocs folder not found!
echo.
echo Please install XAMPP first from:
echo https://www.apachefriends.org/download.html
echo.
echo Or manually copy this folder to your XAMPP htdocs directory.
echo.
pause
exit /b 1

:install
echo Found XAMPP at: %XAMPP_PATH%
echo.
echo Creating project directory...

:: Create the directory
if not exist "%XAMPP_PATH%\library-ms\" mkdir "%XAMPP_PATH%\library-ms"

echo Copying files... (this may take a moment)
echo.

:: Copy all files using xcopy
xcopy "%~dp0\*" "%XAMPP_PATH%\library-ms\" /E /I /Y /Q

if %errorlevel% == 0 (
    echo.
    echo ========================================
    echo SUCCESS! Installation Complete!
    echo ========================================
    echo.
    echo Project installed to:
    echo %XAMPP_PATH%\library-ms\
    echo.
    echo NEXT STEPS:
    echo.
    echo 1. Make sure XAMPP Apache and MySQL are running
    echo 2. Import database: http://localhost/phpmyadmin
    echo    - Create database: library_management
    echo    - Import file: database/library.sql
    echo.
    echo 3. Access your project at:
    echo    http://localhost/library-ms/auto_login.php
    echo.
    echo 4. Or regular login:
    echo    http://localhost/library-ms/auth/login.php
    echo.
    pause
) else (
    echo.
    echo ERROR: Failed to copy files!
    echo.
    echo Try running as Administrator:
    echo - Right-click this file
    echo - Select "Run as administrator"
    echo.
    pause
)
