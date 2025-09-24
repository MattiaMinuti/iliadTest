#!/usr/bin/env php
<?php

/**
 * Test Runner for Gestionale Iliad API
 * 
 * This script runs all API tests and can be used in CI/CD pipelines
 * or as a pre-commit hook.
 */

require_once __DIR__ . '/ApiTests.php';

// Check if backend is running
function checkBackendRunning($url = 'http://localhost:8000') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

// Main execution
echo "🧪 Gestionale Iliad API Test Suite\n";
echo str_repeat("=", 50) . "\n\n";

// Check if backend is running
if (!checkBackendRunning()) {
    echo "❌ Backend is not running at http://localhost:8000\n";
    echo "Please start the backend with: docker-compose up -d\n";
    exit(1);
}

echo "✅ Backend is running\n\n";

// Run tests
$apiTests = new ApiTests();
$success = $apiTests->runAll();

// Exit with appropriate code
if ($success) {
    echo "\n🎉 All tests passed! API is working correctly.\n";
    exit(0);
} else {
    echo "\n💥 Some tests failed! Please check the API.\n";
    exit(1);
}
