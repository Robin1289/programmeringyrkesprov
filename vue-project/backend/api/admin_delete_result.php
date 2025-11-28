<?php
header("Content-Type: application/json");
session_start();

require_once "cors.php";
require_once "../config/db.php";

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] != 3) {
    echo json_encode(["success" => false, "message" => "Denied"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);

if (!$id) {
    echo json_encode(["success" => false, "message" => "Missing id"]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM student_quiz WHERE sq_id = :id");
    $stmt->execute(["id" => $id]);

    // Also delete related answers
    $stmt2 = $pdo->prepare("DELETE FROM student_answer WHERE sa_quiz_fk = :quiz");
    $stmt2->execute(["quiz" => $id]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error"]);
}
