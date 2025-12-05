<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

$user = new User();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get_profile' && User::isLoggedIn()) {
        $userData = $user->getUserById($_SESSION['user_id']);
        if ($userData) {
            echo json_encode(['success' => true, 'user' => $userData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile' && User::isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Name and email are required']);
            exit;
        }

        $result = $user->updateUser($userId, $name, $email, $phone);

        if (!empty($password) && $result['success']) {
            $db = Database::getInstance()->getConnection();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
        }

        if ($result['success']) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
        }

        echo json_encode($result);
        exit;
    }

    if (!User::isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }

    switch ($action) {
        case 'update':
            $userId = $_POST['user_id'] ?? '';
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($userId) || empty($name) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }

            $result = $user->updateUser($userId, $name, $email, $phone);

            if (!empty($password) && $result['success']) {
                $db = Database::getInstance()->getConnection();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);
            }

            echo json_encode($result);
            break;

        case 'delete':
            $userId = $_POST['user_id'] ?? '';

            if (empty($userId)) {
                echo json_encode(['success' => false, 'message' => 'User ID is required']);
                exit;
            }

            $result = $user->deleteUser($userId);
            echo json_encode($result);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
