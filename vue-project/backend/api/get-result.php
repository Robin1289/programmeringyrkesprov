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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing result ID"
    ]);
    exit;
}

$resultId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

try {

    // Fetch from student_quiz
    $stmt = $pdo->prepare("
        SELECT 
            sq.sq_id AS result_id,
            sq.sq_quiz_fk AS quiz_id,
            sq.sq_correct AS correct,
            sq.sq_total AS total,
            sq.sq_date AS completed_at,
            q.quiz_name
        FROM student_quiz sq
        JOIN quiz q ON q.quiz_id = sq.sq_quiz_fk
        WHERE sq.sq_id = ? AND sq.sq_student_fk = ?
        LIMIT 1
    ");

    $stmt->execute([$resultId, $userId]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$res) {
        echo json_encode([
            "success" => false,
            "message" => "Result not found"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "result" => [
            "result_id" => $res["result_id"],
            "quiz_id" => $res["quiz_id"],
            "quiz_name" => $res["quiz_name"],
            "correct" => intval($res["correct"]),
            "total" => intval($res["total"]),
            "completed_at" => $res["completed_at"]
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
?>
