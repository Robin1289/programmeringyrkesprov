<?php

require_once "../config/db.php";
require_once "cors.php";

session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 3) {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit;
}

try {

    // Count users
    $stmtUsers = $pdo->query("SELECT COUNT(*) AS total FROM user");
    $totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total'];

    // Count quizzes
    $stmtQuizzes = $pdo->query("SELECT COUNT(*) AS total FROM quiz");
    $totalQuizzes = $stmtQuizzes->fetch(PDO::FETCH_ASSOC)['total'];

    // Count student quiz results
    $stmtResults = $pdo->query("SELECT COUNT(*) AS total FROM student_quiz");
    $totalResults = $stmtResults->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        "success" => true,
        "stats" => [
            "users"   => intval($totalUsers),
            "quizzes" => intval($totalQuizzes),
            "results" => intval($totalResults)
        ]
    ]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(["success" => false, "message" => "Server error"]);
}
