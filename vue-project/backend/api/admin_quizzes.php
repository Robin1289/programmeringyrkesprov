<?php
header('Content-Type: application/json');
session_start();

require_once "cors.php";
require_once "../config/db.php";

/* Admin only */
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != 3) {
    echo json_encode(["success" => false, "message" => "Access denied"]);
    exit;
}

$action = $_GET["action"] ?? "";

try {

    switch ($action) {

        /* ----------------------------------------
           GET ALL QUIZZES (ADMIN LIST)
        -----------------------------------------*/
        case "list":

            $stmt = $pdo->prepare("
                SELECT 
                    q.quiz_id,
                    q.quiz_name,
                    q.quiz_description,
                    q.quiz_category,
                    q.quiz_min_level_fk,
                    COUNT(qq.qq_id) AS question_count
                FROM quiz q
                LEFT JOIN quiz_questions qq 
                    ON q.quiz_id = qq.qq_quiz_fk
                GROUP BY q.quiz_id
                ORDER BY q.quiz_id DESC
            ");

            $stmt->execute();

            echo json_encode([
                "success" => true,
                "quizzes" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;


            /* GET SINGLE QUIZ + QUESTIONS */
            case "get":

            $quizId = intval($_GET["quiz_id"] ?? 0);
            if (!$quizId) throw new Exception("Missing quiz_id");

            $stmt = $pdo->prepare("
                SELECT *
                FROM quiz
                WHERE quiz_id = :id
            ");
            $stmt->execute(["id" => $quizId]);
            $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$quiz) throw new Exception("Quiz not found");

            $stmt = $pdo->prepare("
                SELECT 
                    q.q_id,
                    q.q_name,
                    q.q_type,
                    q.q_points,
                    q.q_correct_text,
                    qq.qq_order
                FROM quiz_questions qq
                JOIN question q 
                    ON qq.qq_question_fk = q.q_id
                WHERE qq.qq_quiz_fk = :id
                ORDER BY qq.qq_order
            ");
            $stmt->execute(["id" => $quizId]);

            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($questions as &$q) {

                // Multiple & sort use answers table
                if ($q["q_type"] === "multiple" || $q["q_type"] === "sort") {

                    $stmt2 = $pdo->prepare("
                        SELECT a_id, a_name, a_iscorrect
                        FROM answer
                        WHERE a_q_fk = :qid
                        ORDER BY a_id
                    ");
                    $stmt2->execute(["qid" => $q["q_id"]]);
                    $q["answers"] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                } else {
                    $q["answers"] = [];
                }

                // Match uses match_pair table
                if ($q["q_type"] === "match") {

                    $stmt3 = $pdo->prepare("
                        SELECT mp_id, mp_left_text, mp_right_text
                        FROM match_pair
                        WHERE mp_question_fk = :qid
                        ORDER BY mp_id
                    ");
                    $stmt3->execute(["qid" => $q["q_id"]]);
                    $q["match_pairs"] = $stmt3->fetchAll(PDO::FETCH_ASSOC);

                } else {
                    $q["match_pairs"] = [];
                }

            }

            echo json_encode([
                "success" => true,
                "quiz" => $quiz,
                "questions" => $questions
            ]);
            break;



            /* ----------------------------------------
            SAVE MULTIPLE CHOICE ANSWERS
            -----------------------------------------*/
            case "save_answers":

                $data = json_decode(file_get_contents("php://input"), true);

                if (!isset($data["q_id"], $data["answers"]))
                    throw new Exception("Missing data");

                $qid = intval($data["q_id"]);
                if ($qid <= 0)
                    throw new Exception("Invalid q_id");

                $answers = $data["answers"];

                // Clear old answers
                $pdo->prepare("
                    DELETE FROM answer WHERE a_q_fk = :qid
                ")->execute(["qid" => $qid]);

                // Insert answers
                $stmt = $pdo->prepare("
                    INSERT INTO answer
                        (a_q_fk, a_name, a_iscorrect)
                    VALUES
                        (:qid, :name, :correct)
                ");

                foreach ($answers as $a) {

                    $stmt->execute([
                        "qid"     => $qid,
                        "name"    => $a["a_name"] ?? "",
                        "correct" => !empty($a["a_iscorrect"]) ? 1 : 0
                    ]);

                }

                echo json_encode(["success" => true]);
                break;




        /* ----------------------------------------
           CREATE QUIZ
        -----------------------------------------*/
        case "create":

            $data = json_decode(file_get_contents("php://input"), true);

            $stmt = $pdo->prepare("
                INSERT INTO quiz
                  (quiz_name, quiz_description, quiz_category, quiz_min_level_fk)
                VALUES
                  (:name, :desc, :cat, :lvl)
            ");

            $stmt->execute([
                "name" => $data["quiz_name"],
                "desc" => $data["quiz_description"],
                "cat"  => $data["quiz_category"] ?? null,
                "lvl"  => $data["quiz_min_level_fk"] ?? 1
            ]);

            echo json_encode([
                "success" => true,
                "quiz_id" => $pdo->lastInsertId()
            ]);
            break;



        /* ----------------------------------------
           UPDATE QUIZ
        -----------------------------------------*/
        case "update":

            $data = json_decode(file_get_contents("php://input"), true);

            $stmt = $pdo->prepare("
                UPDATE quiz
                SET
                    quiz_name = :name,
                    quiz_description = :desc,
                    quiz_category = :cat,
                    quiz_min_level_fk = :lvl
                WHERE quiz_id = :id
            ");

            $stmt->execute([
                "id"   => $data["quiz_id"],
                "name" => $data["quiz_name"],
                "desc" => $data["quiz_description"],
                "cat"  => $data["quiz_category"] ?? null,
                "lvl"  => $data["quiz_min_level_fk"]
            ]);

            echo json_encode(["success" => true]);
            break;



        /* ----------------------------------------
           DELETE QUIZ
        -----------------------------------------*/
        case "delete":

            $quizId = intval($_GET["quiz_id"] ?? 0);
            if (!$quizId) throw new Exception("Missing quiz_id");

            $stmt = $pdo->prepare("DELETE FROM quiz WHERE quiz_id = :id");
            $stmt->execute(["id" => $quizId]);

            echo json_encode(["success" => true]);
            break;



        /* ----------------------------------------
           ADD QUESTION TO QUIZ
        -----------------------------------------*/
        case "attach_question":

            $data = json_decode(file_get_contents("php://input"), true);

            $stmt = $pdo->prepare("
                INSERT INTO quiz_questions
                 (qq_quiz_fk, qq_question_fk, qq_order)
                VALUES
                 (:quiz, :question, :order)
            ");

            $stmt->execute([
                "quiz"     => $data["quiz_id"],
                "question"=> $data["question_id"],
                "order"    => $data["order"] ?? null
            ]);

            echo json_encode(["success" => true]);
            break;



        /* ----------------------------------------
           REMOVE QUESTION FROM QUIZ
        -----------------------------------------*/
        case "detach_question":

            $data = json_decode(file_get_contents("php://input"), true);

            $stmt = $pdo->prepare("
                DELETE FROM quiz_questions
                WHERE qq_quiz_fk = :quiz
                  AND qq_question_fk = :question
            ");

            $stmt->execute([
                "quiz"     => $data["quiz_id"],
                "question"=> $data["question_id"]
            ]);

            echo json_encode(["success" => true]);
            break;

            case "create_question":

            $data = json_decode(file_get_contents("php://input"), true);

            $stmt = $pdo->prepare("
                INSERT INTO question
                    (q_name, q_type, q_correct_text, q_points)
                VALUES
                    (:name, :type, :correct, :points)
            ");

            $stmt->execute([
                "name"    => $data["q_name"],
                "type"    => $data["q_type"],
                "correct" => $data["q_correct_text"],
                "points"  => $data["q_points"] ?? 1
            ]);

            echo json_encode([
                "success" => true,
                "q_id" => $pdo->lastInsertId()
            ]);
            break;


            
            case "update_question":

            $data = json_decode(file_get_contents("php://input"), true);

            $stmt = $pdo->prepare("
                UPDATE question SET
                    q_name = :name,
                    q_type = :type,
                    q_correct_text = :correct,
                    q_points = :points
                WHERE q_id = :id
            ");

            $stmt->execute([
                "id"      => $data["q_id"],
                "name"    => $data["q_name"],
                "type"    => $data["q_type"],
                "correct" => $data["q_correct_text"],
                "points"  => $data["q_points"]
            ]);

            echo json_encode(["success" => true]);
            break;

            case "delete_question":

            $id = intval($_GET["q_id"] ?? 0);

            if (!$id) throw new Exception("Missing q_id.");

            // Remove from quizzes first
            $pdo->prepare("
                DELETE FROM quiz_questions WHERE qq_question_fk = :id
            ")->execute(["id" => $id]);

            // Delete question
            $pdo->prepare("
                DELETE FROM question WHERE q_id = :id
            ")->execute(["id" => $id]);

            echo json_encode(["success" => true]);
            break;
            
            case "save_match_pairs":

            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data["q_id"], $data["pairs"]))
                throw new Exception("Missing data");

            $qid = intval($data["q_id"]);

            $pdo->prepare("
                DELETE FROM match_pair 
                WHERE mp_question_fk = :qid
            ")->execute(["qid"=>$qid]);

            $stmt = $pdo->prepare("
                INSERT INTO match_pair
                    (mp_question_fk, mp_left_text, mp_right_text)
                VALUES
                    (:qid, :l, :r)
            ");

            foreach ($data["pairs"] as $p) {
                $stmt->execute([
                    "qid" => $qid,
                    "l" => $p["mp_left_text"],
                    "r" => $p["mp_right_text"]
                ]);
            }

            echo json_encode(["success"=>true]);
            break;






        default:
            throw new Exception("Invalid action");

    }

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
