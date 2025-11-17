<?php

$allowedOrigins = [
    "http://localhost:5173",
    "http://localhost:5174"
];

$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($requestOrigin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $requestOrigin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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
    // Fetch user
    $stmt = $pdo->prepare("SELECT u_points, u_level_fk FROM user WHERE u_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit;
    }

    $points = (int)$user['u_points'];
    $dbLevel = (int)$user['u_level_fk']; // 4–13
    $leveledUp = false;

    while (true) {
        $realLevel = $dbLevel - 3;

        // Get next level row
        $stmt = $pdo->prepare("
            SELECT l_id, l_min_points
            FROM level
            WHERE l_id = ?
            LIMIT 1
        ");
        $stmt->execute([$dbLevel + 1]);
        $next = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$next) break; // No more levels

        $required = (int)$next['l_min_points'];

        if ($points >= $required) {
            // Level up!
            $leveledUp = true;
            $dbLevel++;

            // Deduct required points
            $points -= $required;

        } else {
            break; // Not enough points
        }
    }

    // If leveled up, save to DB
    if ($leveledUp) {
        $update = $pdo->prepare("
            UPDATE user 
            SET u_level_fk = ?, u_points = ?
            WHERE u_id = ?
        ");
        $update->execute([$dbLevel, $points, $userId]);
    }

    echo json_encode([
        "success" => true,
        "leveledUp" => $leveledUp,
        "new_level_fk" => $dbLevel,
        "remaining_points" => $points
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
