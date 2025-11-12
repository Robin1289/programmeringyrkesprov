<?php
// CORS & JSON headers
$frontendOrigin = 'http://localhost:5174';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: $frontendOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_level = $_SESSION['user_level'] ?? 1; // fallback to 1

try {
    $stmt = $pdo->prepare("SELECT * FROM quiz WHERE quiz_min_level_fk <= ?");
    $stmt->execute([$user_level]);
    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "quizzes" => $quizzes
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
