<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';

header('Content-Type: application/json');

User::requireLogin();

$action = $_POST['action'] ?? '';
$reservation = new Reservation();

switch ($action) {
    case 'create':
        $userId = $_SESSION['user_id'];
        $reservationDate = $_POST['reservation_date'] ?? '';
        $reservationTime = $_POST['reservation_time'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $notes = $_POST['notes'] ?? null;
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : null;

        // If purpose is "Other", use other_purpose field
        if ($purpose === 'Other' && !empty($_POST['other_purpose'])) {
            $purpose = $_POST['other_purpose'];
        }

        if (empty($reservationDate) || empty($reservationTime) || empty($purpose)) {
            $_SESSION['error'] = 'All required fields must be filled';
            header('Location: ' . BASE_URL . '/user/request-reservation.php');
            exit;
        }

        $result = $reservation->create($userId, $reservationDate, $reservationTime, $purpose, $notes, $amount);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Reservation request submitted successfully! Please wait for admin approval.';
            header('Location: ' . BASE_URL . '/user/dashboard.php');
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: ' . BASE_URL . '/user/request-reservation.php');
        }
        exit;

    case 'cancel':
        $reservationId = $_POST['reservation_id'] ?? '';
        $userId = $_SESSION['user_id'];

        if (empty($reservationId)) {
            echo json_encode(['success' => false, 'message' => 'Reservation ID is required']);
            exit;
        }

        $result = $reservation->cancel($reservationId, $userId);
        echo json_encode($result);
        break;

    case 'update_status':
        User::requireAdmin();
        
        $reservationId = $_POST['reservation_id'] ?? '';
        $status = $_POST['status'] ?? '';

        if (empty($reservationId) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Reservation ID and status are required']);
            exit;
        }

        $result = $reservation->updateStatus($reservationId, $status);
        
        // Create notification for user
        if ($result['success']) {
            try {
                $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                
                // Get reservation details
                $stmt = $db->prepare("SELECT user_id, purpose, reservation_date FROM reservations WHERE id = ?");
                $stmt->execute([$reservationId]);
                $resData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($resData) {
                    // Create notification message based on status
                    if ($status === 'approved') {
                        $title = "Reservation Approved! 🎉";
                        $message = "Your reservation for " . htmlspecialchars($resData['purpose']) . " on " . date('M d, Y', strtotime($resData['reservation_date'])) . " has been approved. Please proceed with the payment.";
                    } elseif ($status === 'rejected') {
                        $title = "Reservation Rejected";
                        $message = "Unfortunately, your reservation for " . htmlspecialchars($resData['purpose']) . " on " . date('M d, Y', strtotime($resData['reservation_date'])) . " has been rejected. Please contact us for more information.";
                    } else {
                        $title = "Reservation Status Updated";
                        $message = "Your reservation status has been updated to: " . ucfirst($status);
                    }
                    
                    // Insert notification
                    $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, reservation_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$resData['user_id'], $status, $title, $message, $reservationId]);
                }
            } catch (Exception $e) {
                // Log error but don't fail the status update
                error_log("Failed to create notification: " . $e->getMessage());
            }
        }
        
        echo json_encode($result);
        break;

    case 'reschedule':
        User::requireAdmin();
        
        $reservationId = $_POST['reservation_id'] ?? '';
        $newDate = $_POST['new_date'] ?? '';
        $newTime = $_POST['new_time'] ?? '';

        if (empty($reservationId) || empty($newDate) || empty($newTime)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            
            // Get old reservation details
            $stmt = $db->prepare("SELECT user_id, purpose, reservation_date, reservation_time FROM reservations WHERE id = ?");
            $stmt->execute([$reservationId]);
            $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$oldData) {
                echo json_encode(['success' => false, 'message' => 'Reservation not found']);
                exit;
            }
            
            // Update reservation
            $stmt = $db->prepare("UPDATE reservations SET reservation_date = ?, reservation_time = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$newDate, $newTime, $reservationId]);
            
            // Create notification for user
            $title = "Reservation Rescheduled 📅";
            $message = "Your reservation for " . htmlspecialchars($oldData['purpose']) . " has been rescheduled from " . 
                       date('M d, Y', strtotime($oldData['reservation_date'])) . " at " . date('h:i A', strtotime($oldData['reservation_time'])) . 
                       " to " . date('M d, Y', strtotime($newDate)) . " at " . date('h:i A', strtotime($newTime)) . ".";
            
            $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, reservation_id) VALUES (?, 'reschedule', ?, ?, ?)");
            $stmt->execute([$oldData['user_id'], $title, $message, $reservationId]);
            
            echo json_encode(['success' => true, 'message' => 'Reservation rescheduled successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error rescheduling reservation: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
