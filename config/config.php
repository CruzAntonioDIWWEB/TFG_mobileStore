<?php

namespace config;

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Import the DatabaseConfig class
require_once __DIR__ . '/DatabaseConfig.php';

// Create an instance of DatabaseConfig
$db = new DatabaseConfig();

// Get the PDO connection
$pdo = $db->getConnection();

// Base URL and assets URL
define('BASE_URL', '/dashboard/TFG/Public/');
define('ASSETS_URL', '/dashboard/TFG/assets/');

// This return statement is necessary to ensure the PDO instance is available globally
return $pdo;
