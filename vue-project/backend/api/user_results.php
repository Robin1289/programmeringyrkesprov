<?php
require_once "cors.php";
require_once "../config/db.php";

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Not logged in"
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Fetch ALL results for this user
    $stmt = $pdo->prepare("
        SELECT 
            sq.sq_id AS result_id,
            sq.sq_quiz_fk AS quiz_id,
            sq.sq_correct AS score,
            sq.sq_total AS max_score,
            sq.sq_date AS completed_at,
            q.quiz_name
        FROM student_quiz sq
        JOIN quiz q ON q.quiz_id = sq.sq_quiz_fk
        WHERE sq.sq_student_fk = ?
        ORDER BY sq.sq_date DESC
    ");

    $stmt->execute([$userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "results" => $results
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
