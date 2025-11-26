<?php
// Database configuration - update these values for your environment
// For XAMPP default MySQL, DB_HOST is usually '127.0.0.1' and DB_USER is 'root' with empty password
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'platform_filmrating');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create PDO instance
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    // In production, don't expose details — log them instead. This is helpful for local development.
    exit('Database connection failed: ' . $e->getMessage());
}

// Usage: include __DIR__ . '/config.php'; then use $pdo for queries.
