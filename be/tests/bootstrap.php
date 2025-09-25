<?php

/**
 * PHPUnit Bootstrap for Gestionale Iliad API Tests.
 *
 * This file sets up the testing environment for PHPUnit
 */

// Set testing environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_DEBUG'] = 'true';

// Set timezone
date_default_timezone_set('UTC');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define test constants
define('TEST_BASE_URL', 'http://localhost:8000');
define('TEST_TIMEOUT', 30);

// Include Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set up any additional test configuration here
// For example, database seeding, mock services, etc.
