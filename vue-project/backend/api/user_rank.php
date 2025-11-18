<?php

require_once "cors.php";
require_once "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Fetch ONLY students + convert to real levels
    $stmt = $pdo->query("
        SELECT 
            u_id,
            u_level_fk,
            u_points,
            (u_level_fk - 3) AS real_level
        FROM user
        WHERE u_role_fk = 1
        ORDER BY real_level DESC, u_points DESC
    ");

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = count($rows);

    $rank = null;

    foreach ($rows as $index => $row) {
        if ((int)$row['u_id'] === (int)$userId) {
            $rank = $index + 1; // ranks start at 1
            break;
        }
    }

    // New TOP % calculation (you requested: "Top 100% etc.")
    $percentage = 100;
    if ($rank !== null && $total > 0) {
        $percentage = round((1 - (($rank - 1) / $total)) * 100);
    }

    echo json_encode([
        "success" => true,
        "rank" => $rank,
        "total" => $total,
        "percentage" => $percentage
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
