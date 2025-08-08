@echo off
title ESP32 RFID Bridge
echo ESP32 RFID Bridge - Starting...
echo.

REM Check if PHP is available
php --version >nul 2>&1
if errorlevel 1 (
    echo Error: PHP is not installed or not in PATH
    echo Please install PHP and add it to your system PATH
    pause
    exit /b 1
)

REM Set default values
set SERIAL_PORT=COM7
set LARAVEL_URL=http://localhost:8000

REM Check for command line arguments
if not "%1"=="" set SERIAL_PORT=%1
if not "%2"=="" set LARAVEL_URL=%2

echo Configuration:
echo   Serial Port: %SERIAL_PORT%
echo   Laravel URL: %LARAVEL_URL%
echo.

REM Check if Laravel is running
echo Checking Laravel application...
curl -s --max-time 3 %LARAVEL_URL% >nul 2>&1
if errorlevel 1 (
    echo Warning: Cannot connect to Laravel at %LARAVEL_URL%
    echo Make sure Laravel development server is running:
    echo   php artisan serve
    echo.
    pause
)

echo Starting ESP32 Bridge...
echo Press Ctrl+C to stop the bridge
echo.

REM Run the PHP bridge
php esp32_bridge.php %SERIAL_PORT% %LARAVEL_URL%

echo.
echo Bridge stopped.
pause
