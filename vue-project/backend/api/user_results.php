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
    $stmt = $pdo->prepare("
        SELECT 
            r.result_id,
            r.r_quiz_fk,
            r.r_points,
            r.r_max_points,
            q.quiz_name
        FROM result r
        JOIN quiz q ON r.r_quiz_fk = q.quiz_id
        WHERE r.result_id = ? AND r.r_user_fk = ?
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

    $correct = intval($res['r_points']);
    $total   = intval($res['r_max_points']);

    echo json_encode([
        "success" => true,
        "result_id" => $res['result_id'],
        "quiz_id" => $res['r_quiz_fk'],
        "quiz_name" => $res['quiz_name'],
        "correct" => $correct,
        "total" => $total
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
