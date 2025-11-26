<?php

require_once "cors.php";
require_once "../config/db.php";

header("Content-Type: application/json");

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
            case 'getAll':
                // Require admin session to access
                if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 3) {
                    echo json_encode(['success' => false, 'message' => 'Not authorized']);
                    exit;
                }

                $stmt = $pdo->query("
                    SELECT 
                        u_id,
                        u_name,
                        u_mail,
                        u_points,
                        u_level_fk,
                        u_role_fk
                    FROM user
                    ORDER BY u_id ASC
                ");

                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    'success' => true,
                    'users' => array_map(fn($u) => [
                        'id'    => $u['u_id'],
                        'name'  => $u['u_name'],
                        'email' => $u['u_mail'],
                        'points'=> $u['u_points'],
                        'level' => $u['u_level_fk'],
                        'role'  => $u['u_role_fk']
                    ], $users)
                ]);
                break;


        case 'logout':
            session_unset();
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Logged out']);
            break;


            case 'get':
                if (!isset($_SESSION['user_role']) || (int)$_SESSION['user_role'] !== 3) {
                    echo json_encode(['success' => false, 'message' => 'Not authorized']);
                    exit;
                }

                if (!isset($_GET['id'])) {
                    echo json_encode(['success' => false, 'message' => 'Missing ID']);
                    exit;
                }

                $id = (int)$_GET['id'];

                try {
                    $stmt = $pdo->prepare("SELECT u_id, u_name, u_mail, u_points, u_level_fk, u_role_fk FROM user WHERE u_id = ?");
                    $stmt->execute([$id]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        echo json_encode(['success' => false, 'message' => 'User not found']);
                        exit;
                    }

                    echo json_encode([
                        'success' => true,
                        'user' => [
                            'id'     => $user['u_id'],
                            'name'   => $user['u_name'],
                            'email'  => $user['u_mail'],
                            'points' => $user['u_points'],
                            'level'  => $user['u_level_fk'],
                            'role'   => $user['u_role_fk']
                        ]
                    ]);

                } catch (PDOException $e) {
                    echo json_encode(['success' => false, 'message' => 'SQL ERROR: ' . $e->getMessage()]);
                }
                break;

                case 'update':
                    if (!isset($_SESSION['user_role']) || (int)$_SESSION['user_role'] !== 3) {
                        echo json_encode(['success' => false, 'message' => 'Not authorized']);
                        exit;
                    }

                    $data = json_decode(file_get_contents('php://input'), true);

                    if (!$data) {
                        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
                        exit;
                    }

                    $id     = (int)$data['id'];
                    $name   = trim($data['name']);
                    $email  = trim($data['email']);
                    $points = (int)$data['points'];
                    $level  = (int)$data['level'];
                    $role   = (int)$data['role'];

                    try {
                        $stmt = $pdo->prepare("
                            UPDATE user
                            SET u_name = ?, u_mail = ?, u_points = ?, u_level_fk = ?, u_role_fk = ?
                            WHERE u_id = ?
                        ");

                        $success = $stmt->execute([$name, $email, $points, $level, $role, $id]);

                        echo json_encode(['success' => $success]);

                    } catch (PDOException $e) {
                        echo json_encode(['success' => false, 'message' => 'SQL ERROR: ' . $e->getMessage()]);
                    }
                    break;

                    case 'update':
                        if (!isset($_SESSION['user_role']) || (int)$_SESSION['user_role'] !== 3) {
                            echo json_encode(['success' => false, 'message' => 'Not authorized']);
                            exit;
                        }

                        $data = json_decode(file_get_contents('php://input'), true);

                        if (!$data) {
                            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
                            exit;
                        }

                        $id     = (int)$data['id'];
                        $name   = trim($data['name']);
                        $email  = trim($data['email']);
                        $points = (int)$data['points'];
                        $level  = (int)$data['level'];
                        $role   = (int)$data['role'];

                        // Password handling
                        $newPassword = isset($data['password']) ? trim($data['password']) : null;
                        $passwordHash = null;

                        if ($newPassword !== null && $newPassword !== "") {
                            if (strlen($newPassword) < 6) {
                                echo json_encode(['success' => false, 'message' => 'Password too short']);
                                exit;
                            }
                            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        }

                        try {
                            if ($passwordHash) {
                                // Update password + other fields
                                $stmt = $pdo->prepare("
                                    UPDATE user 
                                    SET u_name = ?, u_mail = ?, u_points = ?, u_level_fk = ?, u_role_fk = ?, u_password_hash = ?
                                    WHERE u_id = ?
                                ");
                                $success = $stmt->execute([$name, $email, $points, $level, $role, $passwordHash, $id]);

                            } else {
                                // Update fields WITHOUT changing password
                                $stmt = $pdo->prepare("
                                    UPDATE user 
                                    SET u_name = ?, u_mail = ?, u_points = ?, u_level_fk = ?, u_role_fk = ?
                                    WHERE u_id = ?
                                ");
                                $success = $stmt->execute([$name, $email, $points, $level, $role, $id]);
                            }

                            echo json_encode(['success' => $success]);

                        } catch (PDOException $e) {
                            echo json_encode(['success' => false, 'message' => 'SQL ERROR: ' . $e->getMessage()]);
                        }
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
