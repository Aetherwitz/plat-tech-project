<?php
// POST /pages/api/ratings.php
// Body (JSON): { movie_id: int, rating: int }
require_once __DIR__ . '/../../config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

$movie_id = (int)($data['movie_id'] ?? 0);
$rating = (int)($data['rating'] ?? 0);
if ($movie_id <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid input']);
    exit;
}

try {
    // Ensure ratings table has unique index (user_id,movie_id)
    $stmt = $pdo->prepare('INSERT INTO ratings (user_id, movie_id, rating) VALUES (:u, :m, :r)
        ON DUPLICATE KEY UPDATE rating = :r2, updated_at = NOW()');
    $stmt->execute([':u' => $_SESSION['user_id'], ':m' => $movie_id, ':r' => $rating, ':r2' => $rating]);

    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error']);
}

?>
