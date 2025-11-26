<?php
// CLI helper to create a user with a hashed password.
// Usage: C:\xampp\php\php.exe .\scripts\create_user.php username email password

if (PHP_SAPI !== 'cli') {
    echo "This script is intended to be run from the command line.\n";
    exit(1);
}

require_once __DIR__ . '/../config.php';

$argvCount = $argc ?? 0;
if ($argvCount < 4) {
    echo "Usage: php create_user.php <username> <email> <password>\n";
    exit(1);
}

$username = $argv[1];
$email = $argv[2];
$passwordPlain = $argv[3];

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.\n";
    exit(1);
}

// Check duplicates
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1');
$stmt->execute([':u' => $username, ':e' => $email]);
if ($stmt->fetch()) {
    echo "A user with that username or email already exists.\n";
    exit(1);
}

$hash = password_hash($passwordPlain, PASSWORD_DEFAULT);
$insert = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:u, :e, :p)');
$insert->execute([':u' => $username, ':e' => $email, ':p' => $hash]);

echo "Created user '{$username}' with id " . $pdo->lastInsertId() . "\n";
