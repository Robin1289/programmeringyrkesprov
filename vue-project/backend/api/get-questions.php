<?php
require_once "cors.php";
require_once "../config/db.php";

$quizId = intval($_GET["quiz_id"] ?? 0);

if ($quizId <= 0) {
    echo json_encode(["success" => false, "message" => "Missing quiz ID"]);
    exit;
}

try {
    // fetch questions in order
    $stmt = $pdo->prepare("
        SELECT q.*
        FROM quiz_questions qq
        JOIN question q ON qq.qq_question_fk = q.q_id
        WHERE qq.qq_quiz_fk = ?
        ORDER BY qq.qq_order ASC
    ");
    $stmt->execute([$quizId]);
    $questions = $stmt->fetchAll();

    // fetch match pairs
    foreach ($questions as &$q) {
        if ($q["q_type"] === "match") {
            $mp = $pdo->prepare("SELECT * FROM match_pairs WHERE mp_question_fk = ?");
            $mp->execute([$q["q_id"]]);
            $q["match_pairs"] = $mp->fetchAll();
        }

        // drag questions have comma-separated items inside q_correct_text
        if ($q["q_type"] === "drag") {
            $q["drag_items"] = array_map("trim", explode(",", $q["q_correct_text"]));
        }
    }

    echo json_encode([
        "success" => true,
        "questions" => $questions
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Database error"]);
}
