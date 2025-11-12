<?php
// Allow your current frontend origin (change to 5174 if needed)
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

require_once '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Please enter both email and password']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE u_mail = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['u_password_hash'])) {
        $_SESSION['user_id'] = $user['u_id'];
        $_SESSION['user_name'] = $user['u_name'];
        $_SESSION['user_email'] = $user['u_mail'];
        $_SESSION['last_activity'] = time();

        echo json_encode([
            'success' => true,
            'message' => "Welcome back, {$user['u_name']}! You are now logged in.",
            'user' => [
                'id' => $user['u_id'],
                'name' => $user['u_name'],
                'email' => $user['u_mail'],
                'points' => $user['u_points'],
                'level' => $user['u_level_fk'],
                'role' => $user['u_role_fk']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Try again later.']);
}
?>
