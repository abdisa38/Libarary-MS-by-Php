@echo off
echo ================================
echo Library Management System
echo Copy to XAMPP Script
echo ================================
echo.

:: Check if XAMPP exists in common locations
if exist "C:\xampp\htdocs\" (
    set XAMPP_PATH=C:\xampp\htdocs
    goto :found
)

if exist "D:\xampp\htdocs\" (
    set XAMPP_PATH=D:\xampp\htdocs
    goto :found
)

if exist "C:\Program Files\xampp\htdocs\" (
    set XAMPP_PATH=C:\Program Files\xampp\htdocs
    goto :found
)

echo ERROR: XAMPP htdocs folder not found!
echo.
echo Please install XAMPP first from:
echo https://www.apachefriends.org/download.html
echo.
echo Or manually copy this folder to your XAMPP htdocs directory.
echo.
pause
exit /b

:found
echo Found XAMPP at: %XAMPP_PATH%
echo.
echo Copying project files...
echo.

xcopy "%~dp0*" "%XAMPP_PATH%\Libarary-MS-by-Php\" /E /I /Y /EXCLUDE:%~dp0.gitignore

if %errorlevel% == 0 (
    echo.
    echo ================================
    echo SUCCESS! Project copied successfully!
    echo ================================
    echo.
    echo Next steps:
    echo 1. Open XAMPP Control Panel
    echo 2. Start Apache and MySQL
    echo 3. Import database from database/library.sql
    echo 4. Open browser and go to:
    echo    http://localhost/Libarary-MS-by-Php
    echo.
    echo Login credentials:
    echo Username: admin
    echo Password: admin123
    echo.
) else (
    echo.
    echo ERROR: Failed to copy files!
    echo Please try manual copy (see XAMPP_SETUP.md)
    echo.
)

pause
