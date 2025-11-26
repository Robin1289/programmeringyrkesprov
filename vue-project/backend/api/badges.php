<?php
require_once "cors.php";
require_once "../config/db.php";

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Not logged in"
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            b.b_id,
            b.b_name,
            b.b_description,
            b.b_image,
            sb.sb_date
        FROM student_badge sb
        JOIN badge b ON b.b_id = sb.sb_badge_fk
        WHERE sb.sb_student_fk = ?
        ORDER BY sb.sb_date DESC
    ");

    $stmt->execute([$userId]);
    $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "badges" => $badges
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
?>
