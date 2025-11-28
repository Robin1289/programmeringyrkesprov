<?php
header('Content-Type: application/json');
session_start();

require_once "cors.php";
require_once '../config/db.php';

// --- Only admin (role 3) can access ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if ($_SESSION['user_role'] != 3) {
    echo json_encode(["success" => false, "message" => "Access denied"]);
    exit;
}

try {
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

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error",
        "error" => $e->getMessage()
    ]);
}
