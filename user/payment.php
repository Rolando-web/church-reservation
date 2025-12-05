<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';

User::requireLogin();

// Get reservation ID
$reservationId = $_GET['id'] ?? null;

if (!$reservationId) {
    header('Location: dashboard.php');
    exit;
}

$reservationObj = new Reservation();
$reservation = $reservationObj->getById($reservationId);

// Verify reservation exists and belongs to user and is approved
if (!$reservation || $reservation['user_id'] != $_SESSION['user_id'] || $reservation['status'] !== 'approved') {
    $_SESSION['error'] = 'Invalid reservation or payment not allowed';
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-[#002B5C]">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="flex items-center">
                        <span class="text-2xl">🕊️</span>
                        <span class="ml-2 text-xl font-serif font-bold text-primary">Church Reservation</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-gray-700 hover:text-primary">← Back to Dashboard</a>
                    <a href="../api/auth.php?action=logout" class="btn-secondary">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="auth-card">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-serif font-bold text-primary">Complete Your Payment</h1>
                <p class="mt-2 text-gray-600">Reservation #<?php echo $reservation['id']; ?></p>
            </div>

            <!-- Reservation Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Reservation Details</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Purpose:</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($reservation['purpose']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-semibold"><?php echo date('F d, Y', strtotime($reservation['reservation_date'])); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Time:</span>
                        <span class="font-semibold"><?php echo date('h:i A', strtotime($reservation['reservation_time'])); ?></span>
                    </div>
                    <div class="flex justify-between border-t pt-3 mt-3">
                        <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                        <span class="text-2xl font-bold text-green-600">₱<?php echo number_format($reservation['amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <form id="paymentForm" method="POST" action="../api/payment.php" class="space-y-6">
                <input type="hidden" name="action" value="process">
                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                <input type="hidden" name="amount" value="<?php echo $reservation['amount']; ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Payment Method</label>
                    
                    <div class="space-y-3">
                        <!-- Cash Payment -->
                        <label class="payment-method-card flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary transition">
                            <input type="radio" name="payment_method" value="cash" required class="mr-4 w-5 h-5">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">💵 Cash Payment</div>
                                <div class="text-sm text-gray-600">Pay directly at the church office</div>
                            </div>
                        </label>

                        <!-- PayMaya -->
                        <label class="payment-method-card flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary transition">
                            <input type="radio" name="payment_method" value="paymaya" required class="mr-4 w-5 h-5">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4 text-white font-bold text-xl">P</div>
                                <div>
                                    <div class="font-semibold text-gray-900">PayMaya</div>
                                    <div class="text-sm text-gray-600">Pay using your PayMaya account</div>
                                </div>
                            </div>
                        </label>

                        <!-- GCash -->
                        <label class="payment-method-card flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary transition">
                            <input type="radio" name="payment_method" value="gcash" required class="mr-4 w-5 h-5">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4 text-white font-bold text-xl">G</div>
                                <div>
                                    <div class="font-semibold text-gray-900">GCash</div>
                                    <div class="text-sm text-gray-600">Pay using your GCash wallet</div>
                                </div>
                            </div>
                        </label>

                        <!-- Credit Card -->
                        <label class="payment-method-card flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary transition">
                            <input type="radio" name="payment_method" value="credit_card" required class="mr-4 w-5 h-5">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4 text-white font-bold text-2xl">💳</div>
                                <div>
                                    <div class="font-semibold text-gray-900">Credit Card</div>
                                    <div class="text-sm text-gray-600">Visa, Mastercard, or JCB</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div id="cashInstructions" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hidden">
                    <h3 class="font-semibold text-yellow-900 mb-2">Cash Payment Instructions:</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>1. Visit the church office during business hours</li>
                        <li>2. Present your Reservation ID: #<?php echo $reservation['id']; ?></li>
                        <li>3. Make the payment of ₱<?php echo number_format($reservation['amount'], 2); ?></li>
                        <li>4. Receive your official receipt</li>
                    </ul>
                </div>

                <!-- PayMaya Payment Details -->
                <div id="payMayaInstructions" class="bg-green-50 border border-green-200 rounded-lg p-4 hidden">
                    <h3 class="font-semibold text-green-900 mb-4">PayMaya Payment Details:</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PayMaya Phone Number *</label>
                            <input type="tel" id="paymayaPhone" name="paymaya_phone" placeholder="09XX XXX XXXX" pattern="[0-9]{11}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Enter your 11-digit PayMaya mobile number</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number *</label>
                            <input type="text" id="paymayaRef" name="paymaya_reference" placeholder="REF-XXXXXXXXXXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Transaction reference number from PayMaya</p>
                        </div>
                    </div>
                </div>

                <!-- GCash Payment Details -->
                <div id="gcashInstructions" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                    <h3 class="font-semibold text-blue-900 mb-4">GCash Payment Details:</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">GCash Phone Number *</label>
                            <input type="tel" id="gcashPhone" name="gcash_phone" placeholder="09XX XXX XXXX" pattern="[0-9]{11}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Enter your 11-digit GCash mobile number</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number *</label>
                            <input type="text" id="gcashRef" name="gcash_reference" placeholder="REF-XXXXXXXXXXXX" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Transaction reference number from GCash</p>
                        </div>
                    </div>
                </div>

                <!-- Credit Card Payment Details -->
                <div id="creditCardInstructions" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hidden">
                    <h3 class="font-semibold text-purple-900 mb-4">Credit Card Details:</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number *</label>
                            <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name *</label>
                            <input type="text" id="cardName" name="card_name" placeholder="JOHN DOE" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent uppercase">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                                <input type="text" id="cardExpiry" name="card_expiry" placeholder="MM/YY" maxlength="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CVV *</label>
                                <input type="text" id="cardCvv" name="card_cvv" placeholder="123" maxlength="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 text-xs text-gray-600 bg-white p-3 rounded">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Your card information is secure and encrypted</span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="btn-primary flex-1">
                        Confirm Payment
                    </button>
                    <a href="dashboard.php" class="btn-secondary flex-1 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        // Show instructions based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cashInstructions = document.getElementById('cashInstructions');
                const payMayaInstructions = document.getElementById('payMayaInstructions');
                const gcashInstructions = document.getElementById('gcashInstructions');
                const creditCardInstructions = document.getElementById('creditCardInstructions');
                
                // Hide all instructions and clear required
                cashInstructions.classList.add('hidden');
                payMayaInstructions.classList.add('hidden');
                gcashInstructions.classList.add('hidden');
                creditCardInstructions.classList.add('hidden');
                
                // Clear all required attributes
                document.querySelectorAll('#payMayaInstructions input, #gcashInstructions input, #creditCardInstructions input').forEach(input => {
                    input.removeAttribute('required');
                });
                
                // Show relevant instructions and set required fields
                if (this.value === 'cash') {
                    cashInstructions.classList.remove('hidden');
                } else if (this.value === 'paymaya') {
                    payMayaInstructions.classList.remove('hidden');
                    document.getElementById('paymayaPhone').setAttribute('required', 'required');
                    document.getElementById('paymayaRef').setAttribute('required', 'required');
                } else if (this.value === 'gcash') {
                    gcashInstructions.classList.remove('hidden');
                    document.getElementById('gcashPhone').setAttribute('required', 'required');
                    document.getElementById('gcashRef').setAttribute('required', 'required');
                } else if (this.value === 'credit_card') {
                    creditCardInstructions.classList.remove('hidden');
                    document.getElementById('cardNumber').setAttribute('required', 'required');
                    document.getElementById('cardName').setAttribute('required', 'required');
                    document.getElementById('cardExpiry').setAttribute('required', 'required');
                    document.getElementById('cardCvv').setAttribute('required', 'required');
                }
            });
        });

        // Format card number with spaces
        document.getElementById('cardNumber')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date
        document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Only allow numbers in CVV
        document.getElementById('cardCvv')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Only allow numbers in phone fields
        document.getElementById('paymayaPhone')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        document.getElementById('gcashPhone')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Form validation before submit
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
            
            if (paymentMethod === 'paymaya') {
                const phone = document.getElementById('paymayaPhone').value;
                if (phone.length !== 11) {
                    e.preventDefault();
                    alert('Please enter a valid 11-digit PayMaya phone number');
                    return false;
                }
            } else if (paymentMethod === 'gcash') {
                const phone = document.getElementById('gcashPhone').value;
                if (phone.length !== 11) {
                    e.preventDefault();
                    alert('Please enter a valid 11-digit GCash phone number');
                    return false;
                }
            } else if (paymentMethod === 'credit_card') {
                const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
                if (cardNumber.length < 13 || cardNumber.length > 19) {
                    e.preventDefault();
                    alert('Please enter a valid card number');
                    return false;
                }
                const cvv = document.getElementById('cardCvv').value;
                if (cvv.length < 3 || cvv.length > 4) {
                    e.preventDefault();
                    alert('Please enter a valid CVV');
                    return false;
                }
            }
        });
    </script>
</body>
</html>
