#!/bin/bash

# Gestionale Iliad - Git Hooks Setup
# This script sets up Git hooks for the project

echo "🔧 Setting up Git hooks for Gestionale Iliad..."

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    echo "❌ Error: Not in a Git repository"
    exit 1
fi

# Copy hooks from .githooks to .git/hooks
if [ -d ".githooks" ]; then
    cp .githooks/* .git/hooks/
    chmod +x .git/hooks/*
    echo "✅ Git hooks installed successfully"
    echo ""
    echo "Available hooks:"
    ls -la .git/hooks/ | grep -v sample
else
    echo "❌ Error: .githooks directory not found"
    exit 1
fi

echo ""
echo "🎯 Hooks are now active:"
echo "  - pre-commit: Backend PHP CS Fixer + Frontend Prettier"
echo "  - pre-push: Backend API tests"
echo ""
echo "💡 To disable a hook: chmod -x .git/hooks/hook-name"
echo "💡 To re-enable: chmod +x .git/hooks/hook-name"
