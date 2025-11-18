<?php

require_once "cors.php";
require_once "../config/db.php"; // adjust path if needed

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Get user_id from query
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid user ID"
    ]);
    exit;
}

try {
    // Fetch user quiz results joined with quiz info
    $stmt = $pdo->prepare("
        SELECT 
            r.r_id,
            r.r_user_fk,
            r.r_score AS score,
            q.quiz_id,
            q.quiz_name,
            q.quiz_category,
            q.quiz_min_level_fk,
            q.quiz_description,
            q.quiz_iscompleted,
            q.quiz_points AS total_points,
            r.r_completed_at AS completed_at
        FROM result r
        JOIN quiz q ON r.r_quiz_fk = q.quiz_id
        WHERE r.r_user_fk = :user_id
        ORDER BY r.r_completed_at DESC
    ");

    $stmt->execute(['user_id' => $user_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "results" => $results
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
