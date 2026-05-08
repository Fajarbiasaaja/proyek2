@echo off
REM Politech Database Setup Script
REM Jalankan script ini setelah MySQL running di XAMPP

cls
echo.
echo ================================================
echo   POLITECH DATABASE SETUP SCRIPT
echo ================================================
echo.
echo Membuat database MySQL "politech"...
echo.

REM Create database
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS politech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [OK] Database "politech" berhasil dibuat!
    echo.
    echo Jalankan migration...
    echo.
    
    REM Run migration
    cd /d "%~dp0.."
    php artisan migrate
    
    if %ERRORLEVEL% EQU 0 (
        echo.
        echo [OK] Migration berhasil dijalankan!
        echo.
        echo Database siap digunakan!
    ) else (
        echo.
        echo [ERROR] Migration gagal!
        pause
    )
) else (
    echo.
    echo [ERROR] Gagal membuat database!
    echo Pastikan MySQL sudah berjalan di XAMPP Control Panel
    echo.
    pause
)
