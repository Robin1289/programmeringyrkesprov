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

$userId = $_SESSION['user_id'];
$userLevelDb = $_SESSION['user_level'] ?? 4;

// Convert DB level (4–13) → real level (1–10)
$realLevel = max(1, $userLevelDb - 3);

try {

    //
    // 1) Fetch all quizzes the user is allowed to see
    //
    $stmt = $pdo->prepare("
        SELECT *
        FROM quiz
        WHERE quiz_min_level_fk <= ?
        ORDER BY quiz_min_level_fk ASC
    ");
    $stmt->execute([$realLevel]);
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //
    // 2) Fetch all completed quizzes INCLUDING result id
    //
    $stmt2 = $pdo->prepare("
        SELECT sq_quiz_fk AS quiz_id, sq_id AS result_id
        FROM student_quiz
        WHERE sq_student_fk = ?
    ");

    $stmt2->execute([$userId]);
    $done = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Convert format:
    // quiz_id => result_id
    $completedMap = [];
    foreach ($done as $d) {
        $completedMap[(int)$d['quiz_id']] = (int)$d['result_id'];
    }

    //
    // 3) Mark quizzes as completed + attach result_id
    //
    foreach ($quizzes as &$q) {
        $qid = (int)$q['quiz_id'];

        if (isset($completedMap[$qid])) {
            $q['completed'] = true;
            $q['result_id'] = $completedMap[$qid];
        } else {
            $q['completed'] = false;
            $q['result_id'] = null;
        }
    }

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
