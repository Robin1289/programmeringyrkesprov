<?php

require_once "cors.php";
require_once "../config/db.php";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Check last activity for 30-min timeout
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
}

$_SESSION['last_activity'] = time();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

case 'getUser':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'No user logged in']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'user' => [
                    'id'     => $_SESSION['user_id'],
                    'name'   => $_SESSION['user_name'],
                    'email'  => $_SESSION['user_email'],
                    'points' => $_SESSION['user_points'] ?? 0,
                    'level'  => $_SESSION['user_level'] ?? 1,
                    'role'   => $_SESSION['user_role']   ?? null,
                    'badges' => $_SESSION['user_badges'] ?? []
                ]
            ]);
            break;

        case 'logout':
            session_unset();
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logged out']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
