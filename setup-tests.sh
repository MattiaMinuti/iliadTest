#!/bin/bash

# Gestionale Iliad Test Setup Script
# This script sets up the testing environment

echo "🧪 Setting up Gestionale Iliad Test Environment"
echo "=============================================="

# Make test runner executable
chmod +x be/tests/run-tests.php

# Make pre-commit hook executable
chmod +x .git/hooks/pre-commit

echo "✅ Test framework setup complete!"
echo ""
echo "📋 Available commands:"
echo "  - Run tests manually: cd be/tests && php run-tests.php"
echo "  - Tests will run automatically before each commit"
echo "  - Skip tests: git commit --no-verify"
echo ""
echo "🚀 Ready to test!"
