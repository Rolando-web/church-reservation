<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Payment.php';
require_once __DIR__ . '/../classes/Reservation.php';

header('Content-Type: application/json');

User::requireLogin();

$action = $_POST['action'] ?? '';
$payment = new Payment();
$reservation = new Reservation();

switch ($action) {
    case 'process':
        $reservationId = $_POST['reservation_id'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? '';

        if (empty($reservationId) || empty($amount) || empty($paymentMethod)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: ' . BASE_URL . '/user/dashboard.php');
            exit;
        }

        // Verify reservation belongs to user
        $reservationData = $reservation->getById($reservationId);
        if (!$reservationData || $reservationData['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Invalid reservation';
            header('Location: ' . BASE_URL . '/user/dashboard.php');
            exit;
        }

        // Collect payment details based on method
        $paymentDetails = [];
        
        if ($paymentMethod === 'paymaya') {
            $paymentDetails['phone'] = $_POST['paymaya_phone'] ?? '';
            $paymentDetails['reference'] = $_POST['paymaya_reference'] ?? '';
        } elseif ($paymentMethod === 'gcash') {
            $paymentDetails['phone'] = $_POST['gcash_phone'] ?? '';
            $paymentDetails['reference'] = $_POST['gcash_reference'] ?? '';
        } elseif ($paymentMethod === 'credit_card') {
            $cardNumber = $_POST['card_number'] ?? '';
            // Store only last 4 digits for security
            $paymentDetails['card_last4'] = substr(str_replace(' ', '', $cardNumber), -4);
            $paymentDetails['card_holder'] = $_POST['card_name'] ?? '';
        }

        $result = $payment->process($reservationId, $amount, $paymentMethod, $paymentDetails);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Payment successful! You can now download your receipt.';
            header('Location: ' . BASE_URL . '/user/dashboard.php');
        } else {
            $_SESSION['error'] = $result['message'];
            header('Location: ' . BASE_URL . '/user/payment.php?id=' . $reservationId);
        }
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
