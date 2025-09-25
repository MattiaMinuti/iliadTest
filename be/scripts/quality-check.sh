#!/bin/bash

# Gestionale Iliad Code Quality Check Script
# This script runs all quality checks for the backend

echo "🔍 Gestionale Iliad Code Quality Check"
echo "====================================="

# Change to backend directory
cd "$(dirname "$0")/.."

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo "❌ Composer dependencies not installed"
    echo "Please run: composer install"
    exit 1
fi

echo "✅ Dependencies are installed"
echo ""

# Run PHP CS Fixer
echo "🔧 Running PHP CS Fixer..."
if [ -f "vendor/bin/php-cs-fixer" ]; then
    ./vendor/bin/php-cs-fixer fix --dry-run --diff --verbose
    CS_FIXER_RESULT=$?
    
    if [ $CS_FIXER_RESULT -ne 0 ]; then
        echo ""
        echo "❌ Code style issues found!"
        echo "Run: ./vendor/bin/php-cs-fixer fix"
        exit 1
    fi
    echo "✅ Code style is correct"
else
    echo "⚠️  PHP CS Fixer not available"
fi

echo ""

# Run PHPUnit tests
echo "🧪 Running PHPUnit tests..."
if [ -f "vendor/bin/phpunit" ]; then
    ./vendor/bin/phpunit --configuration=phpunit.xml
    PHPUNIT_RESULT=$?
    
    if [ $PHPUNIT_RESULT -ne 0 ]; then
        echo ""
        echo "❌ Tests failed!"
        exit 1
    fi
    echo "✅ All tests passed"
else
    echo "⚠️  PHPUnit not available"
fi

echo ""
echo "🎉 All quality checks passed!"
echo "Code is ready for commit! 🚀"
