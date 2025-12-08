<?php
require_once "cors.php";
require_once "../config/db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['quiz_id']) || !isset($data['student_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid payload"]);
    exit;
}

$quiz_id    = intval($data['quiz_id']);
$student_id = intval($data['student_id']);
$answers    = $data['answers'] ?? [];

try {
    $pdo->beginTransaction();

    // Load quiz xp and min level
    $stmt = $pdo->prepare("
        SELECT quiz_min_level_fk, COALESCE(quiz_xp, 50) AS quiz_xp
        FROM quiz
        WHERE quiz_id = ?
        LIMIT 1
    ");
    $stmt->execute([$quiz_id]);
    $quizRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$quizRow) {
        throw new Exception("Quiz not found");
    }

    $quizMinLevel = intval($quizRow['quiz_min_level_fk']);
    $quizBaseXp   = intval($quizRow['quiz_xp']);

    // Load user points and level
    $stmt = $pdo->prepare("
        SELECT u_points, u_level_fk
        FROM user
        WHERE u_id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $stmt->execute([$student_id]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$userRow) {
        throw new Exception("User not found");
    }

    $currentPoints = intval($userRow['u_points']);
    $currentLevel  = intval($userRow['u_level_fk']);

    // Previous attempts for replay scaling
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS attempts, MAX(sq_correct) AS best_correct
        FROM student_quiz
        WHERE sq_student_fk = ? AND sq_quiz_fk = ?
    ");
    $stmt->execute([$student_id, $quiz_id]);
    $prevRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $prevAttempts   = intval($prevRow['attempts'] ?? 0);
    $bestCorrectOld = intval($prevRow['best_correct'] ?? 0);

    $correctCount   = 0;
    $totalQuestions = count($answers);

    // Grade answers
    foreach ($answers as $ans) {
        $q_id   = intval($ans['q_id']);
        $type   = $ans['type'] ?? '';
        $isCorrect = 0;

        // Fetch question
        $stmt = $pdo->prepare("SELECT * FROM question WHERE q_id = ?");
        $stmt->execute([$q_id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$question) {
            continue;
        }

        // Single choice
        if ($type === "single") {
            $stmtA = $pdo->prepare("
                SELECT a_id
                FROM answer
                WHERE a_q_fk = ? AND a_iscorrect = 1
                LIMIT 1
            ");
            $stmtA->execute([$q_id]);
            $correctId = $stmtA->fetchColumn();

            $given = $ans['answer_ids'] ?? [];
            if ($correctId && in_array($correctId, $given)) {
                $isCorrect = 1;
            }
        }

        // Multiple choice
        elseif ($type === "multiple") {
            $stmtA = $pdo->prepare("
                SELECT a_id
                FROM answer
                WHERE a_q_fk = ? AND a_iscorrect = 1
            ");
            $stmtA->execute([$q_id]);
            $correctIds = $stmtA->fetchAll(PDO::FETCH_COLUMN);

            $given = $ans['answer_ids'] ?? [];
            sort($given);
            sort($correctIds);

            if ($given === $correctIds) {
                $isCorrect = 1;
            }
        }

        // Text answer
        elseif ($type === "text") {
            $userText = strtolower(trim($ans['text'] ?? ''));
            $keyword  = strtolower(trim($question['q_correct_text'] ?? ''));

            if ($keyword !== '' && str_contains($userText, $keyword)) {
                $isCorrect = 1;
            }
        }

        // Sort question
        elseif ($type === "sort") {
            $correctArr = array_map("trim", explode(",", $question['q_correct_text'] ?? ''));
            $userOrder  = $ans['order'] ?? [];
            if ($correctArr && $userOrder && $correctArr === $userOrder) {
                $isCorrect = 1;
            }
        }

        // Match question
        elseif ($type === "match") {
            $stmtM = $pdo->prepare("
                SELECT mp_id, mp_right_text
                FROM match_pair
                WHERE mp_question_fk = ?
            ");
            $stmtM->execute([$q_id]);
            $pairs = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            $ok = true;
            $userMatches = $ans['matches'] ?? [];

            foreach ($pairs as $pair) {
                $leftId      = intval($pair['mp_id']);
                $correctText = $pair['mp_right_text'];

                $chosenRightId = $userMatches[$leftId] ?? null;
                if (!$chosenRightId) {
                    $ok = false;
                    break;
                }

                $stmtR = $pdo->prepare("
                    SELECT mp_right_text
                    FROM match_pair
                    WHERE mp_id = ?
                    LIMIT 1
                ");
                $stmtR->execute([$chosenRightId]);
                $chosenText = $stmtR->fetchColumn();

                if ($chosenText !== $correctText) {
                    $ok = false;
                    break;
                }
            }

            $isCorrect = $ok ? 1 : 0;
        }

        // Save per-question answer
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

    // Score and pass
    $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
    $passed     = $percentage >= 70 ? 1 : 0;

    // XP ratio with 11→10 rule and 120% for perfect
    if ($totalQuestions > 0) {
        $denom = $totalQuestions >= 11 ? $totalQuestions - 1 : $totalQuestions;
        if ($denom < 1) {
            $denom = $totalQuestions;
        }
        $ratio = $denom > 0 ? min($correctCount / $denom, 1.0) : 0.0;
        if ($correctCount === $totalQuestions && $totalQuestions >= 2) {
            $ratio = 1.2;
        }
    } else {
        $ratio = 0.0;
    }

    // XP scaling for overlevel quizzes
    $levelDiff = $currentLevel - $quizMinLevel;
    if ($levelDiff <= 0) {
        $levelMult = 1.0;
    } elseif ($levelDiff == 1) {
        $levelMult = 0.75;
    } elseif ($levelDiff == 2) {
        $levelMult = 0.5;
    } else {
        $levelMult = 0.25;
    }

    $xpBase = $quizBaseXp * $ratio * $levelMult;

    // Replay scaling if no improvement
    $replayFactor = 1.0;
    if ($prevAttempts > 0 && $correctCount <= $bestCorrectOld) {
        $replayFactor = max(0.1, 1.0 - 0.2 * $prevAttempts);
    }

    $xpGain = (int)round($xpBase * $replayFactor);
    if ($xpGain < 0) {
        $xpGain = 0;
    }

    $pointsBefore = $currentPoints;
    $pointsTemp   = $currentPoints + $xpGain;
    $newLevel     = $currentLevel;
    $newPoints    = $pointsTemp;
    $levelsGained = 0;

    // Level up loop
    while (true) {
        $stmt = $pdo->prepare("
            SELECT l_id, l_min_points
            FROM level
            WHERE l_id > ?
            ORDER BY l_id ASC
            LIMIT 1
        ");
        $stmt->execute([$newLevel]);
        $next = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$next) {
            break;
        }

        $cost = intval($next['l_min_points']);
        if ($newPoints < $cost) {
            break;
        }

        $newPoints -= $cost;
        $newLevel   = intval($next['l_id']);
        $levelsGained++;
    }

    // Update user
    $stmt = $pdo->prepare("
        UPDATE user
        SET u_points = ?, u_level_fk = ?
        WHERE u_id = ?
    ");
    $stmt->execute([$newPoints, $newLevel, $student_id]);

    // Save quiz attempt
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

    require_once "award_badges.php";
    $newBadges = awardBadges($pdo, $student_id);


    // Level name and next requirement for progress bar
    $stmt = $pdo->prepare("SELECT l_name FROM level WHERE l_id = ? LIMIT 1");
    $stmt->execute([$newLevel]);
    $newLevelName = $stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT l_min_points
        FROM level
        WHERE l_id > ?
        ORDER BY l_id ASC
        LIMIT 1
    ");
    $stmt->execute([$newLevel]);
    $nextRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextCost = $nextRow ? intval($nextRow['l_min_points']) : null;

    $pdo->commit();

    echo json_encode([
        "success"           => true,
        "result_id"         => $resultId,
        "correct"           => $correctCount,
        "total"             => $totalQuestions,
        "percentage"        => $percentage,
        "passed"            => $passed,
        "xp_gained"         => $xpGain,
        "levels_gained"     => $levelsGained,
        "new_level_id"      => $newLevel,
        "new_level_name"    => $newLevelName,
        "points_before"     => $pointsBefore,
        "points_after"      => $newPoints,
        "next_level_points" => $nextCost
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
