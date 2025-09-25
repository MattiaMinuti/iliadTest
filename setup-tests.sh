#!/bin/bash

# Gestionale Iliad PHPUnit Test Setup Script
# This script sets up the PHPUnit testing environment

echo "🧪 Setting up Gestionale Iliad PHPUnit Test Environment"
echo "======================================================"

# Make PHPUnit test runner executable
chmod +x be/tests/run-phpunit-tests.php

# Make pre-commit hook executable
chmod +x .git/hooks/pre-commit

# Make pre-push hook executable
chmod +x .git/hooks/pre-push

echo "✅ PHPUnit test framework setup complete!"
echo ""
echo "📋 Available commands:"
echo "  - Run tests manually: cd be/tests && php run-phpunit-tests.php"
echo "  - Run with PHPUnit directly: cd be && ./vendor/bin/phpunit"
echo "  - Run code quality check: cd be && ./scripts/quality-check.sh"
echo "  - Fix code style: cd be && ./vendor/bin/php-cs-fixer fix"
echo "  - Frontend linting: cd fe && npm run lint"
echo "  - Frontend formatting: cd fe && npm run format"
echo "  - Tests will run automatically before each commit on main branch"
echo "  - Skip tests: git commit --no-verify"
echo ""
echo "🚀 Ready to test with PHPUnit and quality checks!"
