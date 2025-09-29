#!/usr/bin/env php
<?php

/**
 * PHPUnit Test Runner for Gestionale Iliad API.
 *
 * This script runs PHPUnit tests and can be used in CI/CD pipelines
 * or as a pre-commit hook.
 */

// Check if backend is running (try both localhost and custom domain)
function checkBackendRunning($urls = ['http://localhost:8000', 'http://iliadApi'])
{
    foreach ($urls as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return true;
        }
    }
    
    return false;
}

// Main execution
echo "🧪 Gestionale Iliad PHPUnit Test Suite\n";
echo str_repeat('=', 50) . "\n\n";

// Check if backend is running
if (! checkBackendRunning()) {
    echo "❌ Backend is not running at http://localhost:8000 or http://iliadApi\n";
    echo "Please start the backend with: docker-compose up -d\n";
    exit(1);
}

echo "✅ Backend is running\n\n";

// Check if PHPUnit is available
if (! file_exists(__DIR__ . '/../vendor/bin/phpunit')) {
    echo "❌ PHPUnit is not installed\n";
    echo "Please run: composer install\n";
    exit(1);
}

echo "✅ PHPUnit is available\n\n";

// Run PHPUnit tests
$phpunitPath = __DIR__ . '/../vendor/bin/phpunit';
$configPath = __DIR__ . '/../phpunit.xml';

$command = "{$phpunitPath} --configuration={$configPath} --colors=always";

echo "🚀 Running PHPUnit tests...\n";
echo "Command: {$command}\n\n";

// Execute PHPUnit
$output = [];
$returnCode = 0;
exec($command . ' 2>&1', $output, $returnCode);

// Display output
foreach ($output as $line) {
    echo $line . "\n";
}

echo "\n" . str_repeat('=', 50) . "\n";

if ($returnCode === 0) {
    echo "🎉 All PHPUnit tests passed!\n";
    exit(0);
} else {
    echo "💥 Some PHPUnit tests failed!\n";
    exit(1);
}
