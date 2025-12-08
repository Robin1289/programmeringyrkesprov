<?php
require_once "cors.php";
require_once "../config/db.php";

header("Content-Type: application/json");

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Invalid quiz ID"]);
    exit;
}

$quiz_id = (int) $_GET['id'];

try {

    // FETCH QUIZ
    $stmt = $pdo->prepare("
        SELECT quiz_id, quiz_name, quiz_description
        FROM quiz
        WHERE quiz_id = ?
        LIMIT 1
    ");
    $stmt->execute([$quiz_id]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        echo json_encode(["success" => false, "message" => "Quiz not found"]);
        exit;
    }

    // FETCH QUESTIONS
    $stmt = $pdo->prepare("
        SELECT 
            q.q_id,
            q.q_name,
            q.q_type,
            q.q_points,
            q.q_correct_text
        FROM quiz_questions qq
        INNER JOIN question q 
            ON q.q_id = qq.qq_question_fk
        WHERE qq.qq_quiz_fk = ?
        ORDER BY qq.qq_order ASC
    ");

    $stmt->execute([$quiz_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        echo json_encode(["success" => false, "message" => "No questions found"]);
        exit;
    }

    // ATTACH ANSWERS / MATCH PAIRS
    foreach ($questions as &$q) {

        $q['answers'] = [];
        $q['match_pairs'] = [];

        // Load answers (for single, multiple, sort)
        $stmtA = $pdo->prepare("
            SELECT 
                a_id,
                a_name,
                a_iscorrect
            FROM answer
            WHERE a_q_fk = ?
            ORDER BY a_id ASC
        ");

        $stmtA->execute([$q['q_id']]);
        $answers = $stmtA->fetchAll(PDO::FETCH_ASSOC);

        if ($answers) {

            // Shuffle only for radio & checkbox questions
            if (in_array($q['q_type'], ['single', 'multiple'])) {
                shuffle($answers);
            }

            $q['answers'] = $answers;
        }

        // Load match pairs
        if ($q['q_type'] === 'match') {

            $stmtM = $pdo->prepare("
                SELECT 
                    mp_id,
                    mp_left_text,
                    mp_right_text
                FROM match_pair
                WHERE mp_question_fk = ?
                ORDER BY mp_id ASC
            ");

            $stmtM->execute([$q['q_id']]);

            $q['match_pairs'] = $stmtM->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    echo json_encode([
        "success"   => true,
        "quiz"      => $quiz,
        "questions" => $questions
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
