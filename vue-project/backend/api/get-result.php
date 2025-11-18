<?php
require_once "cors.php";
require_once "../config/db.php";

// Must be logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Not logged in"
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// GET ALL RESULTS FOR THIS USER


try {

    // Fetch results with quiz name
    $stmt = $pdo->prepare("
        SELECT 
            r.result_id,
            r.r_user_fk,
            r.r_quiz_fk,
            r.r_score,
            r.r_percentage,
            r.r_passed,
            r.r_timestamp,
            q.quiz_name
        FROM result r
        JOIN quiz q ON r.r_quiz_fk = q.quiz_id
        WHERE r.r_user_fk = ?
        ORDER BY r.r_timestamp DESC
    ");

    $stmt->execute([$userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "results" => $results
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
