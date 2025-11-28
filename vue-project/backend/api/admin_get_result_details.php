<?php

require_once "cors.php";
require_once '../config/db.php';

header("Content-Type: application/json");
session_start();

// Only admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != 3) {
    echo json_encode(["success" => false, "message" => "Access denied"]);
    exit;
}

if (!isset($_GET["id"])) {
    echo json_encode(["success" => false, "message" => "Missing id"]);
    exit;
}

$id = intval($_GET["id"]);

try {

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
        WHERE sa.sa_quiz_fk = :quiz AND sa.sa_student_fk = :student
    ";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute(["quiz" => $quiz_id, "student" => $student_id]);

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

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error",
        "error" => $e->getMessage()
    ]);
}
