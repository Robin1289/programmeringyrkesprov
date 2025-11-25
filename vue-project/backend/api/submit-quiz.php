<?php
require_once "cors.php";
require_once "../config/db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['quiz_id']) || !isset($data['student_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid payload"]);
    exit;
}

$quiz_id = intval($data['quiz_id']);
$student_id = intval($data['student_id']);
$answers = $data['answers'] ?? [];

// Check last attempt
$stmt = $pdo->prepare("
    SELECT sq_passed 
    FROM student_quiz
    WHERE sq_student_fk=? AND sq_quiz_fk=?
    ORDER BY sq_date DESC
    LIMIT 1
");
$stmt->execute([$student_id, $quiz_id]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

// If last attempt was PASSED → block retry
if ($attempt && intval($attempt['sq_passed']) === 1) {
    echo json_encode([
        "success" => false,
        "message" => "Du har redan klarat detta test!"
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $correctCount = 0;
    $totalQuestions = count($answers);

    foreach ($answers as $ans) {
        $q_id = $ans['q_id'];
        $type = $ans['type'];
        $isCorrect = 0;

        // fetch question
        $stmt = $pdo->prepare("SELECT * FROM question WHERE q_id=?");
        $stmt->execute([$q_id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$question) continue;

        /* SINGLE*/
        if ($type === "single") {
            $stmtA = $pdo->prepare("SELECT a_id FROM answer WHERE a_q_fk=? AND a_iscorrect=1");
            $stmtA->execute([$q_id]);
            $correctId = $stmtA->fetchColumn();

            if (in_array($correctId, $ans['answer_ids'])) {
                $isCorrect = 1;
            }
        }

        /* MULTIPLE */
        elseif ($type === "multiple") {
            $stmtA = $pdo->prepare("SELECT a_id FROM answer WHERE a_q_fk=? AND a_iscorrect=1");
            $stmtA->execute([$q_id]);
            $correctIds = $stmtA->fetchAll(PDO::FETCH_COLUMN);

            $given = $ans['answer_ids'] ?? [];
            sort($given);
            sort($correctIds);

            if ($given == $correctIds) {
                $isCorrect = 1;
            }
        }

        /* TEXT – keyword match */
        elseif ($type === "text") {
            $userText = strtolower(trim($ans['text'] ?? ''));
            $keyword = strtolower(trim($question['q_correct_text'] ?? ''));

            if ($keyword !== '' && str_contains($userText, $keyword)) {
                $isCorrect = 1;
            } else {
                $isCorrect = 0;
            }
        }

        /*  SORT */
        elseif ($type === "sort") {
            $correctArr = array_map("trim", explode(",", $question['q_correct_text']));
            if ($correctArr === $ans['order']) $isCorrect = 1;
        }

        /*  MATCH */
        elseif ($type === "match") {
            $stmtM = $pdo->prepare("SELECT mp_id, mp_right_text FROM match_pair WHERE mp_question_fk=?");
            $stmtM->execute([$q_id]);
            $pairs = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            $ok = true;
            foreach ($pairs as $pair) {
                $left = $pair['mp_id'];
                $correct = $pair['mp_right_text'];

                $chosenRightId = $ans['matches'][$left] ?? null;

                $stmtR = $pdo->prepare("SELECT mp_right_text FROM match_pair WHERE mp_id=?");
                $stmtR->execute([$chosenRightId]);
                $text = $stmtR->fetchColumn();

                if ($text !== $correct) $ok = false;
            }
            $isCorrect = $ok ? 1 : 0;
        }

        // save answer
        $stmt = $pdo->prepare("
            INSERT INTO student_answer
            (sa_student_fk, sa_quiz_fk, sa_question_fk, sa_is_correct, sa_answer_text)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $student_id,
            $quiz_id,
            $q_id,
            $isCorrect,
            json_encode($ans)
        ]);

        $correctCount += $isCorrect;
    }

    /*  SUMMARY + PASS/FAIL */
    $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
    $passed = $percentage >= 70 ? 1 : 0;

    $stmt = $pdo->prepare("
        INSERT INTO student_quiz
        (sq_student_fk, sq_quiz_fk, sq_score, sq_correct, sq_total, sq_date, sq_passed)
        VALUES (?, ?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([
        $student_id,
        $quiz_id,
        $correctCount,
        $correctCount,
        $totalQuestions,
        $passed
    ]);

    $resultId = $pdo->lastInsertId();
    $pdo->commit();

    echo json_encode([
        "success" => true,
        "result_id" => $resultId
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
