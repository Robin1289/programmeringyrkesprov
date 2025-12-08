<?php
require_once "cors.php";
require_once "../config/db.php";

function awardBadges(PDO $pdo, int $userId) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM badge
        WHERE b_id NOT IN (
            SELECT sb_badge_fk FROM student_badge WHERE sb_student_fk = ?
        )
    ");
    $stmt->execute([$userId]);
    $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT u_points, u_level_fk FROM user WHERE u_id=?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $userPoints = intval($user["u_points"]);
    $userLevel = intval($user["u_level_fk"]);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_quiz WHERE sq_student_fk=?");
    $stmt->execute([$userId]);
    $quizCount = intval($stmt->fetchColumn());

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM student_answer sa
        JOIN question q ON q.q_id = sa.sa_question_fk
        WHERE sa.sa_student_fk=? AND sa.sa_is_correct=1 AND q.q_type='text'
    ");
    $stmt->execute([$userId]);
    $textCorrect = intval($stmt->fetchColumn());

    $newBadges = [];

    foreach ($badges as $b) {

        $cond = $b["b_condition"];
        $earned = false;

        if ($cond === "FIRST_QUIZ") {
            $earned = $quizCount >= 1;
        }

        elseif (str_starts_with($cond, "TOTAL_XP")) {
            $need = intval(explode(":", $cond)[1]);
            $earned = $userPoints >= $need;
        }

        elseif (str_starts_with($cond, "LEVEL_REACHED")) {
            $need = intval(explode(":", $cond)[1]);
            $earned = $userLevel >= $need;
        }

        elseif (str_starts_with($cond, "QUIZ_COUNT")) {
            $need = intval(explode(":", $cond)[1]);
            $earned = $quizCount >= $need;
        }

        elseif ($cond === "PERFECT_QUIZ") {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM student_quiz
                WHERE sq_student_fk=? AND sq_correct=sq_total
            ");
            $stmt->execute([$userId]);
            $earned = $stmt->fetchColumn() > 0;
        }

        elseif (str_starts_with($cond, "TEXT_STREAK")) {
            $need = intval(explode(":", $cond)[1]);
            $earned = $textCorrect >= $need;
        }

        elseif (str_starts_with($cond, "QUIZ_TIME")) {
            $limit = intval(explode(":", $cond)[1]);
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM student_quiz
                WHERE sq_student_fk=? AND TIMESTAMPDIFF(SECOND, sq_start_time, sq_date) < ?
            ");
            $stmt->execute([$userId, $limit]);
            $earned = $stmt->fetchColumn() > 0;
        }

        if ($earned) {
            $stmt = $pdo->prepare("
                INSERT INTO student_badge (sb_student_fk, sb_badge_fk, sb_date)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$userId, $b["b_id"]]);

            $newBadges[] = $b;
        }
    }

    return $newBadges;
}
