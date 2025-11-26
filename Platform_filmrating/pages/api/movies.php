<?php
// GET /pages/api/movies.php
// Parameters:
// - id (optional): return single movie by id
// - page, limit (optional): pagination for list
// - query (optional): search string for title
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT id, title, slug, description, release_date, poster_path, backdrop_path, external_id FROM movies WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Not found']);
            exit;
        }
        echo json_encode(['ok' => true, 'data' => $row], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $limit = max(1, min(100, (int)($_GET['limit'] ?? 20)));
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $limit;
    $query = trim($_GET['query'] ?? '');

    if ($query === '') {
        $stmt = $pdo->prepare('SELECT SQL_CALC_FOUND_ROWS id, title, slug, description, release_date, poster_path FROM movies ORDER BY release_date DESC LIMIT :o, :l');
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare('SELECT SQL_CALC_FOUND_ROWS id, title, slug, description, release_date, poster_path FROM movies WHERE title LIKE :q ORDER BY release_date DESC LIMIT :o, :l');
        $like = "%{$query}%";
        $stmt->bindValue(':q', $like, PDO::PARAM_STR);
        $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = (int)$pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
    echo json_encode(['ok' => true, 'page' => $page, 'limit' => $limit, 'total' => $total, 'data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error']);
}

?>
