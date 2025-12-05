<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

User::requireLogin();

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';
$serviceId = $_GET['service_id'] ?? '';

if (empty($date) || empty($time)) {
    echo json_encode(['available' => true, 'message' => 'No date/time specified']);
    exit;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    // First, auto-complete past paid reservations
    $db->exec("
        UPDATE reservations 
        SET status = 'completed' 
        WHERE status = 'paid' 
        AND CONCAT(reservation_date, ' ', reservation_time) < NOW()
    ");
    
    // Check if there's an active reservation (pending, approved, or paid) for this date and time
    // We'll consider a 2-hour window to prevent overlapping reservations
    // Also ensure the reservation hasn't already passed (still in the future)
    $stmt = $db->prepare("
        SELECT r.id, r.status, r.reservation_time, u.name as user_name
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        WHERE r.reservation_date = ? 
        AND r.status IN ('pending', 'approved', 'paid')
        AND CONCAT(r.reservation_date, ' ', TIME(r.reservation_time)) >= NOW()
        AND (
            TIME(r.reservation_time) = TIME(?)
            OR ABS(TIME_TO_SEC(TIMEDIFF(TIME(r.reservation_time), TIME(?))) / 3600) < 2
        )
        LIMIT 1
    ");
    $stmt->execute([$date, $time, $time]);
    $existingReservation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingReservation) {
        echo json_encode([
            'available' => false,
            'message' => 'This time slot is already reserved',
            'reserved_by' => $existingReservation['user_name'],
            'status' => $existingReservation['status']
        ]);
    } else {
        echo json_encode([
            'available' => true,
            'message' => 'Time slot is available'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'available' => true,
        'message' => 'Error checking availability',
        'error' => $e->getMessage()
    ]);
}
