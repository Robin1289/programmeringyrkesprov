<?php

require_once "cors.php";
require_once '../config/db.php';

// -------------------------------
// SESSION
// -------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true
    ]);
}

// -------------------------------
// READ JSON INPUT (FIXED)
// -------------------------------
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// Debug (optional):
// file_put_contents("debug_login.txt", "RAW: $raw\n", FILE_APPEND);

$identifier = trim($input['email'] ?? '');   // email OR username
$password   = $input['password'] ?? '';

if ($identifier === '' || $password === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter both email/username and password'
    ]);
    exit;
}

// -------------------------------
// LOGIN LOGIC
// -------------------------------
try {
    // username OR email
    $stmt = $pdo->prepare("
        SELECT *
        FROM user
        WHERE u_mail = ? OR u_name = ?
        LIMIT 1
    ");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['u_password_hash'])) {

        $_SESSION['user_id'] = $user['u_id'];
        $_SESSION['user_name'] = $user['u_name'];
        $_SESSION['user_email'] = $user['u_mail'];
        $_SESSION['user_level'] = $user['u_level_fk']; 
        $_SESSION['user_role'] = $user['u_role_fk'];    
        $_SESSION['last_activity'] = time();


        echo json_encode([
            'success' => true,
            'message' => "Welcome back, {$user['u_name']}!",
            'user' => [
                'id'        => $user['u_id'],
                'name'      => $user['u_name'],
                'email'     => $user['u_mail'],
                'points'    => $user['u_points'],
                'level'     => $user['u_level_fk'],
                'role'      => $user['u_role_fk']
            ]
        ]);
        exit;
    }

    // Invalid login
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email/username or password.'
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Database error. Try again later.'
    ]);
}
?>
