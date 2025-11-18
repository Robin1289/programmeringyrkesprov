<?php

require_once "cors.php";
require_once "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Not logged in"
    ]);
    exit;
}

// Get the user's stored DB level (4–13)
$user_level_db = $_SESSION['user_level'] ?? 4;

// Convert DB-level (4–13) → Real level (1–10)
$realLevel = max(1, $user_level_db - 3);

// Fetch quizzes the user is ALLOWED to see
try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM quiz
        WHERE quiz_min_level_fk <= ?
        ORDER BY quiz_min_level_fk ASC
    ");
    $stmt->execute([$realLevel]);
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "user_real_level" => $realLevel,
        "quizzes" => $quizzes
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
