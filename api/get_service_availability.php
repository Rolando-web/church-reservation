<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

User::requireLogin();

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    // First, auto-complete reservations that have passed their date
    $db->exec("
        UPDATE reservations 
        SET status = 'completed' 
        WHERE status = 'paid' 
        AND CONCAT(reservation_date, ' ', reservation_time) < NOW()
    ");
    
    // Get all services and check if they have CURRENT active reservations
    // Only consider reservations that are pending, approved, or paid AND haven't passed yet
    $stmt = $db->query("
        SELECT s.id, s.name, s.category,
        (SELECT COUNT(*) FROM reservations r 
         WHERE r.purpose = s.name 
         AND r.reservation_date >= CURDATE()
         AND r.status IN ('pending', 'approved', 'paid')
         AND CONCAT(r.reservation_date, ' ', r.reservation_time) >= NOW()
        ) as active_reservations
        FROM services s
        ORDER BY s.category, s.price
    ");
    
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $availability = [];
    foreach ($services as $service) {
        $availability[$service['id']] = [
            'name' => $service['name'],
            'available' => $service['active_reservations'] == 0,
            'active_reservations' => $service['active_reservations']
        ];
    }
    
    echo json_encode(['success' => true, 'availability' => $availability]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error checking availability', 'error' => $e->getMessage()]);
}
