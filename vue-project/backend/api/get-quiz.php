<?php
require_once "cors.php";
require_once "../config/db.php";

header("Content-Type: application/json");

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Invalid quiz ID"]);
    exit;
}

$quiz_id = intval($_GET['id']);

try {

    // 1. FETCH QUIZ

    $stmt = $pdo->prepare("SELECT quiz_id, quiz_name, quiz_description FROM quiz WHERE quiz_id = ?");
    $stmt->execute([$quiz_id]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        echo json_encode(["success" => false, "message" => "Quiz not found"]);
        exit;
    }


    // 2. FETCH QUESTIONS IN ORDER FROM quiz_questions

    $stmt = $pdo->prepare("
        SELECT q.q_id, q.q_name, q.q_type, q.q_points, q.q_correct_text
        FROM quiz_questions qq
        JOIN question q ON q.q_id = qq.qq_question_fk
        WHERE qq.qq_quiz_fk = ?
        ORDER BY qq.qq_order ASC
    ");
    $stmt->execute([$quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        echo json_encode(["success" => false, "message" => "No questions found"]);
        exit;
    }


    // ---------------------------------------------------------
    // 3. FOR EACH QUESTION → FETCH ANSWERS / MATCH PAIRS
    // ---------------------------------------------------------
    foreach ($questions as &$q) {

        // MULTIPLE, SINGLE, SORT, TEXT

        $stmtA = $pdo->prepare("
            SELECT a_id, a_name, a_iscorrect
            FROM answer
            WHERE a_q_fk = ?
            ORDER BY a_id ASC
        ");
        $stmtA->execute([$q['q_id']]);
       $answers = $stmtA->fetchAll(PDO::FETCH_ASSOC);

            // shuffle answers
            shuffle($answers);

            $q['answers'] = $answers;


        // MATCH QUESTIONS

        if ($q['q_type'] === "match") {
            $stmtM = $pdo->prepare("
                SELECT mp_id, mp_left_text, mp_right_text
                FROM match_pair
                WHERE mp_question_fk = ?
                ORDER BY mp_id ASC
            ");
            $stmtM->execute([$q['q_id']]);
            $q['match_pairs'] = $stmtM->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $q['match_pairs'] = [];
        }
    }

    // 4. RETURN DATA

    echo json_encode([
        "success" => true,
        "quiz" => $quiz,
        "questions" => $questions
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
