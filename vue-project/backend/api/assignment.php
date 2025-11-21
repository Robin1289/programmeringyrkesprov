<?php
require_once __DIR__ . "/cors.php";
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
    // 2) Fetch all attempts
    //
    $stmt2 = $pdo->prepare("
        SELECT sq_quiz_fk AS quiz_id, sq_id AS result_id, sq_passed
        FROM student_quiz
        WHERE sq_student_fk = ?
    ");
    $stmt2->execute([$userId]);
    $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $completedMap = [];
    $failedMap = [];

    foreach ($rows as $r) {
        $qid = (int)$r['quiz_id'];

        if ($r['sq_passed'] == 1) {
            if (!isset($completedMap[$qid]))
                $completedMap[$qid] = $r['result_id'];
        } else {
            if (!isset($failedMap[$qid]))
                $failedMap[$qid] = $r['result_id'];
        }
    }

    foreach ($quizzes as &$q) {
    $qid = (int)$q['quiz_id'];

    if (isset($completedMap[$qid])) {
        $q['completed'] = true;
        $q['failed'] = false;
        $q['result_id'] = $completedMap[$qid];
    }
    elseif (isset($failedMap[$qid])) { 
        // User actually attempted AND failed
        $q['completed'] = false;
        $q['failed'] = true;
        $q['result_id'] = $failedMap[$qid];
    }
    else {
        // User has NEVER attempted this quiz
        $q['completed'] = false;
        $q['failed'] = false;
        $q['result_id'] = null;
    }
}


    echo json_encode([
        "success" => true,
        "user_real_level" => $realLevel,
        "quizzes" => $quizzes
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
