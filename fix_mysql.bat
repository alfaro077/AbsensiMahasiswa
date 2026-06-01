@echo off
REM ============================================
REM Fix MySQL 8.0.30 missing component DLL
REM ============================================
echo.
echo Step 1: Go to https://dev.mysql.com/downloads/mysql/8.0.html
echo Step 2: Download mysql-8.0.46-winx64.zip
echo Step 3: Extract lib/plugin/component_reference_cache.dll
echo Step 4: Copy it to C:\laragon\bin\mysql\mysql-8.0.30-winx64\lib\plugin\
echo Step 5: Restart Laragon
echo.
echo After MySQL is running, run:
echo   php artisan migrate
echo.
pause
