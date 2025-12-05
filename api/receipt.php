<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Payment.php';
require_once __DIR__ . '/../classes/Reservation.php';

User::requireLogin();

$reservationId = $_GET['id'] ?? null;

if (!$reservationId) {
    die('Invalid reservation ID');
}

$reservationObj = new Reservation();
$reservation = $reservationObj->getById($reservationId);

if (!$reservation) {
    die('Reservation not found');
}

if (!User::isAdmin() && $reservation['user_id'] != $_SESSION['user_id']) {
    die('Access denied');
}

if ($reservation['status'] !== 'paid') {
    die('Receipt not available - payment not completed');
}

$payment = new Payment();
$result = $payment->generateReceipt($reservationId);

if ($result['success']) {
    echo $result['html'];
} else {
    die($result['message']);
}
