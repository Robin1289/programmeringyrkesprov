<?php
// Allow your current frontend origin
$frontendOrigin = 'http://localhost:5174';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: $frontendOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once "../config/db.php"; // make sure path is correct

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Use $_POST because frontend sends URL-encoded form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate input
if (!$name || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "All fields required"]);
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM user WHERE u_mail = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email already in use"]);
        exit;
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Set starting level and role
    $level = 1;
    $role = 1;

    // Insert new user with level and role
    $stmt = $pdo->prepare("
        INSERT INTO user (u_name, u_mail, u_password_hash, u_level_fk, u_role_fk) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $email, $hashed, $level, $role]);

    // Auto-login after registration
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['last_activity'] = time();

    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $userId,
            "name" => $name,
            "email" => $email,
            "level" => $level,
            "role" => $role
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
