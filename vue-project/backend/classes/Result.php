<?php
require_once __DIR__ . '/../db.php'; // ett steg upp från api/
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM quiz");
    $quizzes = $stmt->fetchAll();
    echo json_encode($quizzes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
