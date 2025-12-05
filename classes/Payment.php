<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Database.php';

class Payment {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    // Process payment
    public function process($reservationId, $amount, $paymentMethod, $paymentDetails = []) {
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // Prepare payment details
            $paymentPhone = $paymentDetails['phone'] ?? null;
            $paymentReference = $paymentDetails['reference'] ?? null;
            $cardLast4 = $paymentDetails['card_last4'] ?? null;
            $cardHolder = $paymentDetails['card_holder'] ?? null;

            // Insert payment record
            $stmt = $this->conn->prepare("
                INSERT INTO payments (reservation_id, amount, payment_method, payment_status, payment_phone, payment_reference, card_last4, card_holder) 
                VALUES (?, ?, ?, 'completed', ?, ?, ?, ?)
            ");
            $stmt->execute([$reservationId, $amount, $paymentMethod, $paymentPhone, $paymentReference, $cardLast4, $cardHolder]);

            // Update reservation payment status
            $stmt = $this->conn->prepare("
                UPDATE reservations 
                SET payment_status = 'paid', status = 'paid', updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$reservationId]);

            $this->conn->commit();

            return ['success' => true, 'message' => 'Payment processed successfully'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()];
        }
    }

    // Get payment by reservation ID
    public function getByReservationId($reservationId) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM payments WHERE reservation_id = ? ORDER BY payment_date DESC LIMIT 1");
            $stmt->execute([$reservationId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    // Generate PDF receipt
    public function generateReceipt($reservationId) {
        try {
            // Get reservation details
            $stmt = $this->conn->prepare("
                SELECT r.*, u.name as user_name, u.email as user_email, u.phone as user_phone,
                       p.amount, p.payment_method, p.payment_date, p.payment_phone, p.payment_reference, p.card_last4, p.card_holder
                FROM reservations r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN payments p ON r.id = p.reservation_id
                WHERE r.id = ?
            ");
            $stmt->execute([$reservationId]);
            $data = $stmt->fetch();

            if (!$data) {
                return ['success' => false, 'message' => 'Reservation not found'];
            }

            // Create PDF without TCPDF (simple HTML-based approach)
            return $this->generateSimpleReceipt($data);

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to generate receipt: ' . $e->getMessage()];
        }
    }

    // Generate simple HTML receipt (alternative to PDF)
    private function generateSimpleReceipt($data) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Receipt - Reservation #' . $data['id'] . '</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #002B5C; padding-bottom: 20px; margin-bottom: 20px; }
                .header h1 { color: #002B5C; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                .receipt-info { margin: 20px 0; }
                .receipt-info table { width: 100%; border-collapse: collapse; }
                .receipt-info td { padding: 10px; border-bottom: 1px solid #ddd; }
                .receipt-info td:first-child { font-weight: bold; width: 200px; }
                .footer { margin-top: 40px; text-align: center; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
                .amount { font-size: 24px; color: #06D6A0; font-weight: bold; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🕊️ Church Reservation System</h1>
                <p>Official Payment Receipt</p>
                <p>123 Church Street, City, Country | Phone: (123) 456-7890 | Email: info@church.com</p>
            </div>
            
            <div class="receipt-info">
                <h2 style="color: #002B5C;">Receipt Details</h2>
                <table>
                    <tr>
                        <td>Receipt Number:</td>
                        <td>REC-' . str_pad($data['id'], 6, '0', STR_PAD_LEFT) . '</td>
                    </tr>
                    <tr>
                        <td>Reservation ID:</td>
                        <td>' . $data['id'] . '</td>
                    </tr>
                    <tr>
                        <td>Customer Name:</td>
                        <td>' . htmlspecialchars($data['user_name']) . '</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>' . htmlspecialchars($data['user_email']) . '</td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td>' . htmlspecialchars($data['user_phone'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td>Reservation Date:</td>
                        <td>' . date('F d, Y', strtotime($data['reservation_date'])) . '</td>
                    </tr>
                    <tr>
                        <td>Reservation Time:</td>
                        <td>' . date('h:i A', strtotime($data['reservation_time'])) . '</td>
                    </tr>
                    <tr>
                        <td>Purpose:</td>
                        <td>' . htmlspecialchars($data['purpose']) . '</td>
                    </tr>
                    <tr>
                        <td>Payment Method:</td>
                        <td>' . ucfirst(str_replace('_', ' ', $data['payment_method'])) . '</td>
                    </tr>';
            
            // Add payment details based on method
            if (($data['payment_method'] === 'paymaya' || $data['payment_method'] === 'gcash') && !empty($data['payment_phone'])) {
                $html .= '
                    <tr>
                        <td>Phone Number:</td>
                        <td>' . htmlspecialchars($data['payment_phone']) . '</td>
                    </tr>';
            }
            
            if (($data['payment_method'] === 'paymaya' || $data['payment_method'] === 'gcash') && !empty($data['payment_reference'])) {
                $html .= '
                    <tr>
                        <td>Reference Number:</td>
                        <td>' . htmlspecialchars($data['payment_reference']) . '</td>
                    </tr>';
            }
            
            if ($data['payment_method'] === 'credit_card' && !empty($data['card_last4'])) {
                $html .= '
                    <tr>
                        <td>Card Number:</td>
                        <td>**** **** **** ' . htmlspecialchars($data['card_last4']) . '</td>
                    </tr>';
            }
            
            if ($data['payment_method'] === 'credit_card' && !empty($data['card_holder'])) {
                $html .= '
                    <tr>
                        <td>Card Holder:</td>
                        <td>' . htmlspecialchars($data['card_holder']) . '</td>
                    </tr>';
            }
            
            $html .= '
                    <tr>
                        <td>Payment Date:</td>
                        <td>' . date('F d, Y h:i A', strtotime($data['payment_date'])) . '</td>
                    </tr>
                    <tr>
                        <td>Amount Paid:</td>
                        <td class="amount">₱' . number_format($data['amount'], 2) . '</td>
                    </tr>
                </table>
            </div>
            
            <div class="footer">
                <p><strong>Thank you for your reservation!</strong></p>
                <p>This is an official receipt. Please keep for your records.</p>
                <p>Generated on: ' . date('F d, Y h:i A') . '</p>
            </div>
            
            <div class="no-print" style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" style="padding: 10px 30px; background: #002B5C; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    Print Receipt
                </button>
                <button onclick="window.close()" style="padding: 10px 30px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                    Close
                </button>
            </div>
        </body>
        </html>';

        return ['success' => true, 'html' => $html];
    }

    // Get all payments (admin)
    public function getAll() {
        try {
            $stmt = $this->conn->query("
                SELECT p.*, r.purpose, u.name as user_name 
                FROM payments p
                JOIN reservations r ON p.reservation_id = r.id
                JOIN users u ON r.user_id = u.id
                ORDER BY p.payment_date DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
