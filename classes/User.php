<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class User {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    // Register new user
    public function register($name, $email, $password, $phone = null) {
        try {
            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $hashedPassword, $phone]);

            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    // Login user
    public function login($email, $password, $rememberMe = false) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }

            $user = $stmt->fetch();

            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Set remember me cookie (optional)
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            }

            return [
                'success' => true,
                'message' => 'Login successful',
                'role' => $user['role']
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    // Logout user
    public function logout() {
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        return ['success' => true, 'message' => 'Logout successful'];
    }

    // Check if email exists (for forgot password)
    public function emailExists($email) {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Reset password
    public function resetPassword($email, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);

            return ['success' => true, 'message' => 'Password reset successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Password reset failed: ' . $e->getMessage()];
        }
    }

    // Get user by ID
    public function getUserById($userId) {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, email, phone, role, created_at FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    // Get all users (admin only)
    public function getAllUsers() {
        try {
            $stmt = $this->conn->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Update user
    public function updateUser($userId, $name, $email, $phone) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $userId]);

            return ['success' => true, 'message' => 'User updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    // Delete user
    public function deleteUser($userId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $stmt->execute([$userId]);

            return ['success' => true, 'message' => 'User deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()];
        }
    }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Check if user is admin
    public static function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    // Require login
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    // Require admin
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }
}
