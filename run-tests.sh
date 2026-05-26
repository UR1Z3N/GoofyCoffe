#!/bin/bash
# Test Runner Script for Linux/Mac
# This script runs all unit tests for GoofyCoffe project
# Usage: ./run-tests.sh

echo ""
echo "========================================"
echo "   GoofyCoffe Unit Test Runner (Linux)"
echo "========================================"
echo ""

if [ ! -f "tests/TestRunner.php" ]; then
    echo "Error: tests/TestRunner.php not found!"
    echo "Make sure you run this script from project root directory."
    exit 1
fi

echo "Running all tests..."
echo ""

php tests/TestRunner.php

echo ""
echo "Tests completed!"
