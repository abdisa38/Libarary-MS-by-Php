@echo off
cls
echo ========================================
echo Fix Registration Auto-Login
echo ========================================
echo.

:: Find XAMPP
set XAMPP_PATH=

if exist "C:\xampp\htdocs\library-ms\auth\register.php" (
    set XAMPP_PATH=C:\xampp\htdocs\library-ms
    goto :fix
)

if exist "D:\xampp\htdocs\library-ms\auth\register.php" (
    set XAMPP_PATH=D:\xampp\htdocs\library-ms
    goto :fix
)

echo ERROR: XAMPP installation not found!
echo Please run INSTALL_TO_XAMPP.bat first.
pause
exit /b 1

:fix
echo Found XAMPP installation at: %XAMPP_PATH%
echo.
echo Fixing registration file...

:: Copy the fixed register.php
copy /Y "%~dp0COPY_THIS_register.php" "%XAMPP_PATH%\auth\register.php" >nul

if %errorlevel% == 0 (
    echo.
    echo ========================================
    echo SUCCESS! Registration Fixed!
    echo ========================================
    echo.
    echo Registration will now auto-login users
    echo and redirect them to their dashboard.
    echo.
    echo Test it now at:
    echo http://localhost/library-ms/auth/register.php
    echo.
) else (
    echo.
    echo ERROR: Failed to copy file!
    echo Try running as Administrator.
    echo.
)

pause
