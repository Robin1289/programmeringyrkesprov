<?php
require_once "cors.php";
require_once "../config/db.php";

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$userId = intval($data["user_id"] ?? 0);
$quizId = intval($data["quiz_id"] ?? 0);
$answers = $data["answers"] ?? [];

if ($userId <= 0 || $quizId <= 0) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['quiz_id']) || !isset($data['student_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid payload"]);
    exit;
}

$quiz_id = intval($data['quiz_id']);
$student_id = intval($data['student_id']);
$answers = $data['answers'];

try {
    $pdo->beginTransaction();

    // Count correct answers
    $correctCount = 0;
    $totalQuestions = count($answers);

    foreach ($answers as $ans) {
        $q_id = $ans['q_id'];
        $type = $ans['type'];
        $isCorrect = 0;

        // Fetch correct answer(s)
        $stmt = $pdo->prepare("SELECT * FROM question WHERE q_id = ?");
        $stmt->execute([$q_id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$question) continue;

        if ($type === "single") {
            // Fetch correct answer id
            $stmtA = $pdo->prepare("SELECT a_id FROM answer WHERE a_q_fk = ? AND a_iscorrect = 1");
            $stmtA->execute([$q_id]);
            $correctId = $stmtA->fetchColumn();

            if (in_array($correctId, $ans['answer_ids'])) {
                $isCorrect = 1;
            }
        }

        elseif ($type === "multiple") {
            // Fetch all correct answers
            $stmtA = $pdo->prepare("SELECT a_id FROM answer WHERE a_q_fk = ? AND a_iscorrect = 1");
            $stmtA->execute([$q_id]);
            $correctIds = $stmtA->fetchAll(PDO::FETCH_COLUMN);

            sort($correctIds);
            $given = $ans['answer_ids'];
            sort($given);

            if ($given == $correctIds) {
                $isCorrect = 1;
            }
        }

        elseif ($type === "text") {
            // Teacher will grade this later → always save as incorrect (0)
            $isCorrect = 0;
        }

        elseif ($type === "sort") {
            $correctText = explode(",", $question['q_correct_text']);
            $correctText = array_map("trim", $correctText);

            if ($correctText === $ans['order']) {
                $isCorrect = 1;
            }
        }

        elseif ($type === "match") {
            $stmtM = $pdo->prepare("SELECT mp_id, mp_right_text FROM match_pair WHERE mp_question_fk = ?");
            $stmtM->execute([$q_id]);
            $pairs = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            $allCorrect = true;
            foreach ($pairs as $p) {
                $left = $p['mp_id'];
                $rightCorrect = $p['mp_right_text'];

                $chosenRightId = $ans['matches'][$left] ?? null;

                // Look up chosen text
                $stmtR = $pdo->prepare("SELECT mp_right_text FROM match_pair WHERE mp_id = ?");
                $stmtR->execute([$chosenRightId]);
                $chosenRightText = $stmtR->fetchColumn();

                if ($rightCorrect !== $chosenRightText) {
                    $allCorrect = false;
                }
            }

            $isCorrect = $allCorrect ? 1 : 0;
        }

        // Store student answer
        $stmtSave = $pdo->prepare("
            INSERT INTO student_answer
            (sa_student_fk, sa_quiz_fk, sa_question_fk, sa_is_correct, sa_answer_text)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtSave->execute([
            $student_id,
            $quiz_id,
            $q_id,
            $isCorrect,
            json_encode($ans) // store raw answer for auditing
        ]);

        if ($isCorrect) $correctCount++;
    }

    // Save quiz result
    $stmt = $pdo->prepare("
        INSERT INTO student_quiz
        (sq_student_fk, sq_quiz_fk, sq_score, sq_correct, sq_total, sq_date)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $student_id,
        $quiz_id,
        $correctCount,
        $correctCount,
        $totalQuestions
    ]);

    $resultId = $pdo->lastInsertId();

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "result_id" => $resultId,
        "correct" => $correctCount,
        "total" => $totalQuestions
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

// fetch all quiz questions
$stmt = $pdo->prepare("
    SELECT q.*
    FROM quiz_questions qq
    JOIN question q ON qq.qq_question_fk = q.q_id
    WHERE qq.qq_quiz_fk = ?
");
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll();

$totalPoints = 0;
$earnedPoints = 0;
$resultDetails = [];

foreach ($questions as $q) {
    $qid = $q["q_id"];
    $type = $q["q_type"];
    $correct = $q["q_correct_text"];
    $points = $q["q_points"];
    $totalPoints += $points;

    $userAnswer = $answers[$qid] ?? null;
    $isCorrect = false;

    if ($type === "single" || $type === "text") {
        $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($correct));
    }

    if ($type === "multiple") {
        $correctArr = array_map("trim", explode(",", $correct));
        sort($correctArr);
        $userArr = array_map("trim", explode(",", $userAnswer ?? ""));
        sort($userArr);
        $isCorrect = ($correctArr === $userArr);
    }

    if ($type === "drag") {
        $correctArr = array_map("trim", explode(",", $correct));
        $userArr = $userAnswer;
        $isCorrect = ($correctArr === $userArr);
    }

    if ($type === "match") {
        // match goes via match_pairs table
        $mp = $pdo->prepare("SELECT * FROM match_pairs WHERE mp_question_fk = ?");
        $mp->execute([$qid]);
        $pairs = $mp->fetchAll();

        $ok = true;
        foreach ($pairs as $p) {
            $left = $p["mp_left"];
            if (($answers[$qid][$left] ?? "") !== $p["mp_right"]) {
                $ok = false;
                break;
            }
        }
        $isCorrect = $ok;
    }

    if ($isCorrect) {
        $earnedPoints += $points;
    }

    $resultDetails[] = [
        "question_id" => $qid,
        "isCorrect" => $isCorrect,
        "earned" => $isCorrect ? $points : 0
    ];
}

// store result
$stmt = $pdo->prepare("
    INSERT INTO result (r_user_fk, r_quiz_fk, r_points, r_max_points)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$userId, $quizId, $earnedPoints, $totalPoints]);

echo json_encode([
    "success" => true,
    "earnedPoints" => $earnedPoints,
    "totalPoints" => $totalPoints,
    "details" => $resultDetails
]);
