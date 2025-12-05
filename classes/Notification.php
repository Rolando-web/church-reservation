<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class Notification {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    // Create notification
    public function create($adminId, $type, $message, $reservationId = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notifications (admin_id, type, message, reservation_id, is_read) 
                VALUES (?, ?, ?, ?, FALSE)
            ");
            $stmt->execute([$adminId, $type, $message, $reservationId]);

            return ['success' => true, 'message' => 'Notification created'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create notification: ' . $e->getMessage()];
        }
    }

    // Get notifications by admin ID
    public function getByAdminId($adminId, $limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT n.*, r.purpose, u.name as user_name 
                FROM notifications n
                LEFT JOIN reservations r ON n.reservation_id = r.id
                LEFT JOIN users u ON r.user_id = u.id
                WHERE n.admin_id = ?
                ORDER BY n.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$adminId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get unread count
    public function getUnreadCount($adminId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE admin_id = ? AND is_read = FALSE
            ");
            $stmt->execute([$adminId]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Mark as read
    public function markAsRead($notificationId) {
        try {
            $stmt = $this->conn->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ?");
            $stmt->execute([$notificationId]);

            return ['success' => true, 'message' => 'Notification marked as read'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to mark as read: ' . $e->getMessage()];
        }
    }

    // Mark all as read
    public function markAllAsRead($adminId) {
        try {
            $stmt = $this->conn->prepare("UPDATE notifications SET is_read = TRUE WHERE admin_id = ?");
            $stmt->execute([$adminId]);

            return ['success' => true, 'message' => 'All notifications marked as read'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to mark all as read: ' . $e->getMessage()];
        }
    }

    // Delete notification
    public function delete($notificationId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id = ?");
            $stmt->execute([$notificationId]);

            return ['success' => true, 'message' => 'Notification deleted'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete notification: ' . $e->getMessage()];
        }
    }
}
