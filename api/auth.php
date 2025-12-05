<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$user = new User();

switch ($action) {
    case 'register':
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = $_POST['phone'] ?? null;

        // Validate
        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            exit;
        }

        $result = $user->register($name, $email, $password, $phone);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: ' . BASE_URL . '/register.php');
            exit;
        }
        break;

    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            exit;
        }

        $result = $user->login($email, $password, $remember_me);
        
        if ($result['success']) {
            if ($result['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/user/index.php');
            }
            exit;
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
        break;

    case 'logout':
        $user->logout();
        header('Location: ' . BASE_URL . '/login.php');
        exit;
        break;

    case 'check_email':
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }

        $exists = $user->emailExists($email);
        
        if ($exists) {
            echo json_encode(['success' => true, 'message' => 'Email found']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email not found']);
        }
        break;

    case 'reset_password':
        $email = $_POST['email'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';

        if (empty($email) || empty($new_password)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: ' . BASE_URL . '/forgot-password.php');
            exit;
        }

        if ($new_password !== $confirm_new_password) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: ' . BASE_URL . '/forgot-password.php');
            exit;
        }

        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            header('Location: ' . BASE_URL . '/forgot-password.php');
            exit;
        }

        $result = $user->resetPassword($email, $new_password);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Password reset successful! Please login with your new password.';
            header('Location: ' . BASE_URL . '/login.php');
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: ' . BASE_URL . '/forgot-password.php');
        }
        exit;
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
