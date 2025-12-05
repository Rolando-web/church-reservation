<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';

User::requireLogin();

$reservation = new Reservation();
$reservations = $reservation->getByUserId($_SESSION['user_id']);

// Get unread notification count
$unread_count = 0;
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $stmt = $db->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
} catch (PDOException $e) {
    // Table might not exist or column might be missing, silently fail
    $unread_count = 0;
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';

// Get filter if set
$filterStatus = $_GET['status'] ?? 'all';
if ($filterStatus !== 'all') {
    $reservations = array_filter($reservations, function($r) use ($filterStatus) {
        return $r['status'] === $filterStatus;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 43, 92, 0.75), rgba(0, 43, 92, 0.85)),
                        url('../assets/images/login.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
    </style>
</head>
<body>
   <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🕊️</span>
                    <span class="text-xl font-serif font-bold text-primary">Church Services</span>
                </div>
                <div class="flex items-center space-x-4">
                     <a href="index.php" class="text-gray-600 hover:text-primary transition font-medium text-sm">
                        Browse Services
                    </a>
                    <a href="dashboard.php" class="text-gray-600 hover:text-primary transition font-medium text-sm">
                        My Reservations
                    </a>

                    
                    <!-- User Profile (Clickable) -->
                    <button onclick="openProfileModal()" class="flex items-center space-x-2 px-3 py-1.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700"><?php echo htmlspecialchars($user_name); ?></span>
                    </button>
                    
                    <!-- Notification Bell -->
                    <a href="notifications.php" class="relative text-gray-700 hover:text-primary transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <?php if ($unread_count > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    
                    <a href="../api/auth.php?action=logout" class="text-sm text-gray-600 hover:text-primary transition font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="px-4 py-6 sm:px-0">

            <!-- Filter Bar -->
            <div class="bg-primary rounded-lg shadow-lg p-6 mb-6">
                <div class="flex flex-wrap gap-3">
                    <a href="?status=all" class="px-6 py-2 rounded-lg font-semibold transition <?php echo $filterStatus === 'all' ? 'bg-white text-[#002B5C]' : 'border border text-white hover:bg-white/30'; ?>">All</a>
                    <a href="?status=pending" class="px-6 py-2 rounded-lg font-semibold transition <?php echo $filterStatus === 'pending' ? 'bg-white text-[#002B5C]' : 'border border text-white hover:bg-white/30'; ?>">Pending</a>
                    <a href="?status=approved" class="px-6 py-2 rounded-lg font-semibold transition <?php echo $filterStatus === 'approved' ? 'bg-white text-[#002B5C]' : 'border border text-white hover:bg-white/30'; ?>">Approved</a>
                    <a href="?status=paid" class="px-6 py-2 rounded-lg font-semibold transition <?php echo $filterStatus === 'paid' ? 'bg-white text-[#002B5C]' : 'border border text-white hover:bg-white/30'; ?>">Paid</a>
                    <a href="?status=rejected" class="px-6 py-2 rounded-lg font-semibold transition <?php echo $filterStatus === 'rejected' ? 'bg-white text-[#002B5C]' : 'border border text-white hover:bg-white/30'; ?>">Rejected</a>
                    <a href="?status=cancelled" class="px-6 py-2 rounded-lg font-semibold transition <?php echo $filterStatus === 'cancelled' ? 'bg-white text-[#002B5C]' : 'border border text-white hover:bg-white/30'; ?>">Cancelled</a>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Reservations Grid -->
            <?php if (empty($reservations)): ?>
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Reservations Found</h3>
                    <p class="text-gray-500 mb-4">You haven't made any reservations yet.</p>
                    <a href="request-reservation.php" class="btn-primary inline-block">Make Your First Reservation</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($reservations as $r): ?>
                        <div class="reservation-card bg-white rounded-lg shadow-lg p-6 flex flex-col">
                            <div class="flex justify-between items-start mb-4">
                                <span class="status-badge status-<?php echo $r['status']; ?>">
                                    <?php echo ucfirst($r['status']); ?>
                                </span>
                                <span class="text-sm text-gray-500">#<?php echo $r['id']; ?></span>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($r['purpose']); ?>
                            </h3>

                            <div class="space-y-2 text-sm text-gray-600 mb-4 flex-1">
                                <div class="flex items-center">
                                    <span class="font-semibold w-20">Date:</span>
                                    <span><?php echo date('F d, Y', strtotime($r['reservation_date'])); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-semibold w-20">Time:</span>
                                    <span><?php echo date('h:i A', strtotime($r['reservation_time'])); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-semibold w-20">Amount:</span>
                                    <span class="text-green-600 font-semibold">₱<?php echo number_format($r['amount'], 2); ?></span>
                                </div>
                                
                                <?php if ($r['notes']): ?>
                                    <p class="text-sm text-gray-500 mt-2 italic">
                                        "<?php echo htmlspecialchars(substr($r['notes'], 0, 100)); ?>..."
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2 mt-auto">
                                <?php if ($r['status'] === 'approved' && $r['payment_status'] === 'unpaid'): ?>
                                    <a href="payment.php?id=<?php echo $r['id']; ?>" class="btn-primary block text-center">
                                        💳 Pay Now
                                    </a>
                                <?php endif; ?>

                                <?php if ($r['status'] === 'paid'): ?>
                                    <a href="../api/receipt.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-success block text-center">
                                        📄 Download Receipt
                                    </a>
                                <?php endif; ?>

                                <?php if ($r['status'] === 'pending'): ?>
                                    <button onclick="cancelReservation(<?php echo $r['id']; ?>)" class="btn-danger block w-full text-center">
                                        ❌ Cancel
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Profile Edit Modal -->
    <div id="profileModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-[#002B5C] to-[#003d7a] text-white p-6 rounded-t-xl">
                <h3 class="text-2xl font-bold">Edit Profile</h3>
            </div>
            <form id="profileForm" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="profileName" value="<?php echo htmlspecialchars($user_name); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="profileEmail" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="profilePhone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="profilePassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent" placeholder="Enter new password">
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeProfileModal()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#002B5C] text-white rounded-lg hover:bg-[#003d7a] font-semibold transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        // Profile Modal Functions
        function openProfileModal() {
            // Load current user data
            fetch('../api/users.php?action=get_profile')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('profileName').value = data.user.name;
                        document.getElementById('profileEmail').value = data.user.email;
                        document.getElementById('profilePhone').value = data.user.phone || '';
                    }
                });
            document.getElementById('profileModal').classList.remove('hidden');
        }

        function closeProfileModal() {
            document.getElementById('profileModal').classList.add('hidden');
            document.getElementById('profilePassword').value = '';
        }

        // Handle profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update_profile');
            
            try {
                const response = await fetch('../api/users.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert(result.message || 'Error updating profile');
                }
            } catch (error) {
                alert('Error updating profile');
            }
        });

        async function cancelReservation(id) {
            if (!confirm('Are you sure you want to cancel this reservation?')) return;

            try {
                const response = await fetch('../api/reservations.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=cancel&reservation_id=${id}`
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error cancelling reservation');
            }
        }
    </script>
</body>
</html>
