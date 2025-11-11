<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

// CORS (justera till din frontend URL)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Hantera preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$user = new User($pdo);

// Starta session om inte igång
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'register':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validering
            if (!$name || !$email || !$password) {
                echo json_encode(['success' => false, 'message' => 'Alla fält måste fyllas i']);
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Ogiltig e-postadress']);
                exit;
            }

            if (strlen($password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Lösenord måste vara minst 8 tecken']);
                exit;
            }

            $result = $user->register($name, $email, $password);
            echo json_encode($result);
            break;

        case 'login':
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                echo json_encode(['success' => false, 'message' => 'Alla fält måste fyllas i']);
                exit;
            }

            $result = $user->login($email, $password);

            if ($result['success']) {
                // Regenerera session för säkerhet
                session_regenerate_id(true);
            }

            echo json_encode($result);
            break;

        case 'logout':
            $user->logout();
            echo json_encode(['success' => true, 'message' => 'Utloggad']);
            break;

        case 'getUser':
            $userId = $_GET['id'] ?? $_SESSION['user_id'] ?? null;

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Ingen användare angiven']);
                exit;
            }

            $userData = $user->getUserById($userId);
            if ($userData) {
                echo json_encode(['success' => true, 'user' => $userData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Användare hittades inte']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ogiltig action']);
            break;
    }
} catch (Exception $e) {
    // Logga internt, skicka generiskt fel till frontend
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Serverfel. Försök igen senare.']);
}
?>
