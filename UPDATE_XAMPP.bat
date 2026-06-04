@echo off
cls
echo ========================================
echo Update XAMPP Installation
echo ========================================
echo.
echo This will copy updated files to XAMPP htdocs
echo.

:: Check for XAMPP
set XAMPP_PATH=

if exist "C:\xampp\htdocs\library-ms\" (
    set XAMPP_PATH=C:\xampp\htdocs\library-ms
    goto :update
)

if exist "D:\xampp\htdocs\library-ms\" (
    set XAMPP_PATH=D:\xampp\htdocs\library-ms
    goto :update
)

if exist "C:\Program Files\xampp\htdocs\library-ms\" (
    set XAMPP_PATH=C:\Program Files\xampp\htdocs\library-ms
    goto :update
)

echo ERROR: Project not found in XAMPP htdocs!
echo Please run INSTALL_TO_XAMPP.bat first.
echo.
pause
exit /b 1

:update
echo Updating files in: %XAMPP_PATH%
echo.

xcopy "%~dp0\*" "%XAMPP_PATH%\" /E /Y /Q /EXCLUDE:%~dp0.gitignore

if %errorlevel% == 0 (
    echo.
    echo ========================================
    echo SUCCESS! Files Updated!
    echo ========================================
    echo.
    echo Changes have been copied to XAMPP.
    echo.
    echo You can now test the updated system at:
    echo http://localhost/library-ms/
    echo.
) else (
    echo.
    echo ERROR: Failed to update files!
    echo.
)

pause
