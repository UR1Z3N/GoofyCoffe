@echo off
REM Test Runner Script for Windows (Batch)
REM This script runs all unit tests for GoofyCoffe project
REM Usage: run-tests.bat

echo.
echo ========================================
echo   GoofyCoffe Unit Test Runner (Windows)
echo ========================================
echo.

if not exist "tests\TestRunner.php" (
    echo Error: tests\TestRunner.php tidak ditemukan!
    echo Pastikan Anda menjalankan script dari root directory project.
    exit /b 1
)

echo Running all tests...
echo.

php tests\TestRunner.php

pause
