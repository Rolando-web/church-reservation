<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class Reservation {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    // Create new reservation
    public function create($userId, $reservationDate, $reservationTime, $purpose, $notes = null) {
        try {
            // Validate date is in future
            if (strtotime($reservationDate) < strtotime(date('Y-m-d'))) {
                return ['success' => false, 'message' => 'Reservation date must be in the future'];
            }

            $stmt = $this->conn->prepare("
                INSERT INTO reservations (user_id, reservation_date, reservation_time, purpose, notes, status, payment_status) 
                VALUES (?, ?, ?, ?, ?, 'pending', 'unpaid')
            ");
            $stmt->execute([$userId, $reservationDate, $reservationTime, $purpose, $notes]);

            $reservationId = $this->conn->lastInsertId();

            // Create notification for admin
            $this->createAdminNotification($reservationId, 'new_reservation', 'New reservation request from user');

            return ['success' => true, 'message' => 'Reservation request submitted successfully', 'reservation_id' => $reservationId];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create reservation: ' . $e->getMessage()];
        }
    }

    // Get reservations by user ID
    public function getByUserId($userId, $status = null) {
        try {
            if ($status) {
                $stmt = $this->conn->prepare("
                    SELECT * FROM reservations 
                    WHERE user_id = ? AND status = ? 
                    ORDER BY reservation_date DESC, reservation_time DESC
                ");
                $stmt->execute([$userId, $status]);
            } else {
                $stmt = $this->conn->prepare("
                    SELECT * FROM reservations 
                    WHERE user_id = ? 
                    ORDER BY reservation_date DESC, reservation_time DESC
                ");
                $stmt->execute([$userId]);
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get all reservations (admin)
    public function getAll($status = null, $dateFrom = null, $dateTo = null) {
        try {
            $query = "SELECT r.*, u.name as user_name, u.email as user_email 
                      FROM reservations r 
                      JOIN users u ON r.user_id = u.id 
                      WHERE 1=1";
            $params = [];

            if ($status) {
                $query .= " AND r.status = ?";
                $params[] = $status;
            }

            if ($dateFrom) {
                $query .= " AND r.reservation_date >= ?";
                $params[] = $dateFrom;
            }

            if ($dateTo) {
                $query .= " AND r.reservation_date <= ?";
                $params[] = $dateTo;
            }

            $query .= " ORDER BY r.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get reservation by ID
    public function getById($reservationId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
                FROM reservations r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.id = ?
            ");
            $stmt->execute([$reservationId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    // Update reservation status
    public function updateStatus($reservationId, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE reservations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$status, $reservationId]);

            // Create notification based on status
            if ($status === 'approved') {
                $this->createUserNotification($reservationId, 'approved', 'Your reservation has been approved. You can now proceed with payment.');
            } elseif ($status === 'rejected') {
                $this->createUserNotification($reservationId, 'rejected', 'Your reservation has been rejected.');
            }

            return ['success' => true, 'message' => 'Reservation status updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()];
        }
    }

    // Update payment status
    public function updatePaymentStatus($reservationId, $paymentStatus) {
        try {
            $status = $paymentStatus === 'paid' ? 'paid' : 'approved';
            $stmt = $this->conn->prepare("
                UPDATE reservations 
                SET payment_status = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$paymentStatus, $status, $reservationId]);

            return ['success' => true, 'message' => 'Payment status updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update payment status: ' . $e->getMessage()];
        }
    }

    // Cancel reservation
    public function cancel($reservationId, $userId) {
        try {
            // Check if reservation belongs to user and is pending
            $stmt = $this->conn->prepare("SELECT status FROM reservations WHERE id = ? AND user_id = ?");
            $stmt->execute([$reservationId, $userId]);
            $reservation = $stmt->fetch();

            if (!$reservation) {
                return ['success' => false, 'message' => 'Reservation not found'];
            }

            if ($reservation['status'] !== 'pending') {
                return ['success' => false, 'message' => 'Only pending reservations can be cancelled'];
            }

            $stmt = $this->conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$reservationId]);

            return ['success' => true, 'message' => 'Reservation cancelled successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to cancel reservation: ' . $e->getMessage()];
        }
    }

    // Get pending count
    public function getPendingCount() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'");
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Get statistics
    public function getStats() {
        try {
            $stats = [];
            
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM reservations");
            $stats['total'] = $stmt->fetch()['count'];
            
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'pending'");
            $stats['pending'] = $stmt->fetch()['count'];
            
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'approved' AND payment_status = 'unpaid'");
            $stats['approved'] = $stmt->fetch()['count'];
            
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM reservations WHERE status = 'paid'");
            $stats['paid'] = $stmt->fetch()['count'];
            
            return $stats;
        } catch (PDOException $e) {
            return ['total' => 0, 'pending' => 0, 'approved' => 0, 'paid' => 0];
        }
    }

    // Create notification for admin
    private function createAdminNotification($reservationId, $type, $message) {
        try {
            // Get all admins
            $stmt = $this->conn->query("SELECT id FROM users WHERE role = 'admin'");
            $admins = $stmt->fetchAll();

            $stmt = $this->conn->prepare("
                INSERT INTO notifications (admin_id, type, message, reservation_id, is_read) 
                VALUES (?, ?, ?, ?, FALSE)
            ");

            foreach ($admins as $admin) {
                $stmt->execute([$admin['id'], $type, $message, $reservationId]);
            }
        } catch (PDOException $e) {
            // Silently fail
        }
    }

    // Create notification for user
    private function createUserNotification($reservationId, $type, $message) {
        try {
            $stmt = $this->conn->prepare("SELECT user_id FROM reservations WHERE id = ?");
            $stmt->execute([$reservationId]);
            $reservation = $stmt->fetch();

            if ($reservation) {
                $stmt = $this->conn->prepare("
                    INSERT INTO notifications (admin_id, type, message, reservation_id, is_read) 
                    VALUES (?, ?, ?, ?, FALSE)
                ");
                $stmt->execute([$reservation['user_id'], $type, $message, $reservationId]);
            }
        } catch (PDOException $e) {
            // Silently fail
        }
    }
}
