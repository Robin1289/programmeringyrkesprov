<?php
require_once "cors.php";
header('Content-Type: application/json');
session_start();

require_once "../config/db.php";

/* Admin only */
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != 3) {
    echo json_encode(["success" => false, "message" => "Access denied"]);
    exit;
}

$action = $_GET["action"] ?? "";

try {

    switch ($action) {

        case "get-stats":
            // Count users
            $stmtUsers = $pdo->query("SELECT COUNT(*) AS total FROM user");
            $totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['total'];

            // Count quizzes
            $stmtQuizzes = $pdo->query("SELECT COUNT(*) AS total FROM quiz");
            $totalQuizzes = $stmtQuizzes->fetch(PDO::FETCH_ASSOC)['total'];

            // Count student quiz results
            $stmtResults = $pdo->query("SELECT COUNT(*) AS total FROM student_quiz");
            $totalResults = $stmtResults->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode([
                "success" => true,
                "stats" => [
                    "users"   => intval($totalUsers),
                    "quizzes" => intval($totalQuizzes),
                    "results" => intval($totalResults)
                ]
            ]);
            break;

        case "get-results":
                // Query: join student_quiz + user + quiz
                $sql = "
                    SELECT 
                        sq.sq_id,
                        sq.sq_student_fk AS student_id,
                        sq.sq_quiz_fk AS quiz_id,
                        sq.sq_correct,
                        sq.sq_total,
                        sq.sq_score,
                        sq.sq_date,
                        sq.sq_passed,

                        u.u_name AS student_name,
                        u.u_mail AS student_email,

                        q.quiz_name,
                        q.quiz_description,
                        q.quiz_category,
                        q.quiz_min_level_fk

                    FROM student_quiz sq
                    INNER JOIN user u ON sq.sq_student_fk = u.u_id
                    INNER JOIN quiz q ON sq.sq_quiz_fk = q.quiz_id
                    ORDER BY sq.sq_date DESC
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "results" => $results
                ]);
            break;

        case "get-result-details": 
            if (!isset($_GET["id"])) {
            echo json_encode(["success" => false, "message" => "Missing id"]);
            exit;

        }
            $id = intval($_GET["id"]);
            /* Fetch quiz attempt */
            $sql = "
                SELECT 
                    sq.*, 
                    u.u_name AS student_name, 
                    u.u_mail AS student_email,
                    q.quiz_name, 
                    q.quiz_category
                FROM student_quiz sq
                INNER JOIN user u ON sq.sq_student_fk = u.u_id
                INNER JOIN quiz q ON sq.sq_quiz_fk = q.quiz_id
                WHERE sq.sq_id = :id
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["id" => $id]);
            $details = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$details) {
                echo json_encode(["success" => false, "message" => "Not found"]);
                exit;

            }

            $quiz_id = $details["sq_quiz_fk"];
            $student_id = $details["sq_student_fk"];

            /*  Fetch all answers */
            $sql2 = "
                SELECT 
                    sa.*,
                    q.q_name,
                    q.q_type,
                    q.q_correct_text,
                    q.q_points
                FROM student_answer sa
                INNER JOIN question q ON sa.sa_question_fk = q.q_id
                WHERE sa.sa_quiz_fk = :quiz_id
                AND sa.sa_student_fk = :student_id

            ";

                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute([
                    "quiz_id" => $details["sq_quiz_fk"],
                    "student_id" => $details["sq_student_fk"]
                ]);

            $formattedAnswers = [];

            while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {

                $raw = $row["sa_answer_text"];
                $type = $row["q_type"];
                $formattedStudent = "";
                $correctAnswer = $row["q_correct_text"];

                /* Decode JSON if needed*/
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $raw = $decoded; // Replace raw with decoded structure
                }

                /* Handle different question types  */

                if ($type === "text") {
                    $formattedStudent = is_array($raw) && isset($raw["text"])
                        ? $raw["text"]
                        : $row["sa_answer_text"];
                }

                else if ($type === "single" || $type === "multiple") {
                    $ids = [];

                    if (is_array($raw) && isset($raw["answer_ids"])) {
                        $ids = $raw["answer_ids"];
                    } elseif (is_array($raw)) {
                        $ids = $raw;
                    } elseif (preg_match('/^\[(.*?)\]$/', $row["sa_answer_text"])) {
                        $ids = json_decode($row["sa_answer_text"], true);
                    }

                    if (!empty($ids)) {
                        $placeholders = implode(",", array_fill(0, count($ids), "?"));
                        $stmtA = $pdo->prepare("SELECT a_name FROM answer WHERE a_id IN ($placeholders)");
                        $stmtA->execute($ids);
                        $names = $stmtA->fetchAll(PDO::FETCH_COLUMN);
                        $formattedStudent = implode(", ", $names);
                    }
                }

                else if ($type === "sort") {
                    if (is_array($raw) && isset($raw["order"])) {
                        $formattedStudent = implode(" → ", $raw["order"]);
                    } else {
                        $formattedStudent = $row["sa_answer_text"];
                    }
                }

                else if ($type === "match") {
                    if (is_array($raw) && isset($raw["matches"])) {
                        $pairs = [];
                        foreach ($raw["matches"] as $left => $right) {
                            $pairs[] = "$left → $right";
                        }
                        $formattedStudent = implode(", ", $pairs);
                    } else {
                        $formattedStudent = $row["sa_answer_text"];
                    }
                }

                $formattedAnswers[] = [
                    "q_id" => $row["sa_question_fk"],
                    "q_name" => $row["q_name"],
                    "correct_answer" => $correctAnswer,
                    "student_answer" => $formattedStudent,
                    "correct" => boolval($row["sa_is_correct"]),
                    "type" => $row["q_type"]
                ];
            }

            $details["answers"] = $formattedAnswers;

            echo json_encode([
                "success" => true,
                "details" => $details
            ]);
            break;
        case "get-levels":

            $levelStmt = $pdo->query("
                SELECT DISTINCT u_level_fk
                FROM user
                WHERE u_level_fk IS NOT NULL
                ORDER BY u_level_fk ASC
            ");

            $levelsUsed = $levelStmt->fetchAll(PDO::FETCH_COLUMN);

            // No students?
            if (count($levelsUsed) === 0) {
                echo json_encode([
                    "success" => true,
                    "levels" => [],
                    "message" => "No student levels found"
                ]);
            }

            $minLevelId = (int)min($levelsUsed);
            $maxLevelId = (int)max($levelsUsed);

            // 🔥 Count students per DB level
            $userCountsStmt = $pdo->prepare("
                SELECT u_level_fk AS level_id, COUNT(*) AS count
                FROM user
                WHERE u_role_fk = 1
                AND u_level_fk BETWEEN ? AND ?
                GROUP BY u_level_fk
                ORDER BY u_level_fk ASC
            ");

            $userCountsStmt->execute([$minLevelId, $maxLevelId]);
            $userCounts = $userCountsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // 🔥 Fetch level meta sorted by DB-ID
            $levelNamesStmt = $pdo->prepare("
                SELECT l_id, l_name
                FROM level
                WHERE l_id BETWEEN ? AND ?
                ORDER BY l_id ASC
            ");

            $levelNamesStmt->execute([$minLevelId, $maxLevelId]);
            $levelNames = $levelNamesStmt->fetchAll(PDO::FETCH_ASSOC);

            // 🔥 Build level list with correct display_level mapping
            $levels = [];
            $displayLevel = 1;

            foreach ($levelNames as $lvl) {

                // Skip DB levels that have *no students at all*
                // Comment this out if you want to show empty levels.
                if (!isset($userCounts[$lvl['l_id']])) {
                    // continue;  // uncomment to hide empty levels
                }

                $levels[] = [
                    "db_level_id"   => (int)$lvl['l_id'],                       // 4–13
                    "display_level" => $displayLevel,                           // 1–10
                    "level_name"    => $lvl['l_name'],
                    "users"         => (int)($userCounts[$lvl['l_id']] ?? 0)
                ];

                $displayLevel++;
            }

            echo json_encode([
                "success"   => true,
                "levels"    => $levels,
                "minLevel"  => $minLevelId,
                "maxLevel"  => $maxLevelId
            ]);
            break;

        case "delete-result":
            $data = json_decode(file_get_contents("php://input"), true);
            $id = intval($data["id"] ?? 0);

            if (!$id) {
                echo json_encode(["success" => false, "message" => "Missing id"]);

            }

            try {
                $stmt = $pdo->prepare("DELETE FROM student_answer WHERE sa_quiz_fk = :id");
                $stmt->execute(["id" => $id]);

                // Also delete related answers
                $stmt2 = $pdo->prepare("DELETE FROM student_quiz WHERE sq_id = :id");
                $stmt2->execute(["quiz" => $id]);

                echo json_encode(["success" => true]);
            } catch (Exception $e) {
                echo json_encode(["success" => false, "message" => "Server error"]);
            }
            break;

        default:
    echo json_encode([
        "success" => false,
        "message" => "Invalid action"
    ]);
    exit;


    }

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}