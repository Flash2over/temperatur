<?php
// Load environment variables from .env file (for local development)
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            // Only set if not already set by real environment variables
            if (!isset($_ENV[trim($key)])) {
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// Load .env file for local development (Docker will use real env vars)
loadEnv(__DIR__ . '/../.env');

define('API_KEY', $_ENV['API_KEY'] ?? 'defaultkey');
define('DB_DSN', 'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'temperatur') . ';charset=utf8mb4');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');