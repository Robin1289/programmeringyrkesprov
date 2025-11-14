<?php
$frontendOrigin = 'http://localhost:5174';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: $frontendOrigin");
header("Access-Control-Allow-Credentials: true");

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
    // Fetch ONLY student users (u_role_fk = 1)
    $stmt = $pdo->query("
        SELECT u_id, u_points
        FROM user
        WHERE u_role_fk = 1
        ORDER BY u_points DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = count($rows);
    $rank = null;

    foreach ($rows as $index => $row) {
        if ((int)$row['u_id'] === (int)$userId) {
            $rank = $index + 1;
            break;
        }
    }

    // Percentage calculation:
    // Higher rank (smaller number) = better percentage
    $percentage = 0;
    if ($rank !== null && $total > 1) {
        $percentage = round((($total - $rank) / ($total - 1)) * 100);
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
