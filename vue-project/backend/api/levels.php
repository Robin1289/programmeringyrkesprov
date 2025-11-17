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

// OPTIONS preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once "../config/db.php"; // adjust path if needed

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

try {
    // Get logged-in user info
    $userId = $_SESSION['user_id'] ?? 0;

    $stmt = $pdo->prepare("SELECT u_points, u_level_fk FROM user WHERE u_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $userPoints = $user['u_points'] ?? 0;
    $userLevelId = $user['u_level_fk'] ?? 4; // fallback to Beginner if not set

    // Get current level
    $stmt = $pdo->prepare("SELECT l_id, l_name, l_min_points FROM level WHERE l_id = ? LIMIT 1");
    $stmt->execute([$userLevelId]);
    $currentLevel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentLevel) {
        // fallback to first level if missing
        $stmt->execute([4]);
        $currentLevel = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get next level
    $stmt = $pdo->prepare("SELECT l_id, l_name, l_min_points FROM level WHERE l_id > ? ORDER BY l_id ASC LIMIT 1");
    $stmt->execute([$currentLevel['l_id']]);
    $nextLevel = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "userPoints" => $userPoints,
        "currentLevel" => $currentLevel,
        "nextLevel" => $nextLevel
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
