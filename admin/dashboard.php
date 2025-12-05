<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/Notification.php';

User::requireAdmin();

$reservation = new Reservation();
$notification = new Notification();

$stats = $reservation->getStats();
$recentReservations = $reservation->getAll(null, null, null);
$recentReservations = array_slice($recentReservations, 0, 10); // Latest 10

$unreadCount = $notification->getUnreadCount($_SESSION['user_id']);
$notifications = $notification->getByAdminId($_SESSION['user_id'], 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin">
    
    
    
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <div class="">
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-container ml-10">
                <h1 class="text-2xl font-semibold mb-6">Dashboard Overview</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Reservations</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                        </div>
                        <div class="text-4xl">📋</div>
                    </div>
                </div>

                <div class="stat-card bg-white border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Pending</p>
                            <p class="text-3xl font-bold text-orange-500"><?php echo $stats['pending']; ?></p>
                        </div>
                        <div class="text-4xl">⏳</div>
                    </div>
                </div>

                <div class="stat-card bg-white border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Approved</p>
                            <p class="text-3xl font-bold text-green-500"><?php echo $stats['approved']; ?></p>
                        </div>
                        <div class="text-4xl">✅</div>
                    </div>
                </div>

                <div class="stat-card bg-white border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Paid</p>
                            <p class="text-3xl font-bold text-blue-500"><?php echo $stats['paid']; ?></p>
                        </div>
                        <div class="text-4xl">💰</div>
                    </div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Reservations</h2>
                    <a href="reservations.php" class="btn-primary">View All</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentReservations as $r): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#<?php echo $r['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($r['user_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($r['user_email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php echo date('M d, Y', strtotime($r['reservation_date'])); ?><br>
                                        <span class="text-gray-500"><?php echo date('h:i A', strtotime($r['reservation_time'])); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($r['purpose']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-<?php echo $r['status']; ?>">
                                            <?php echo ucfirst($r['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($r['status'] === 'pending'): ?>
                                            <button onclick="updateStatus(<?php echo $r['id']; ?>, 'approved')" class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                                            <button onclick="updateStatus(<?php echo $r['id']; ?>, 'rejected')" class="text-red-600 hover:text-red-900">Reject</button>
                                        <?php elseif ($r['status'] === 'paid'): ?>
                                            <a href="../api/receipt.php?id=<?php echo $r['id']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900">Receipt</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        // Toggle notification dropdown
        document.getElementById('notificationBtn').addEventListener('click', function() {
            document.getElementById('notificationDropdown').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const btn = document.getElementById('notificationBtn');
            const dropdown = document.getElementById('notificationDropdown');
            if (!btn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        async function updateStatus(id, status) {
            if (!confirm(`Are you sure you want to ${status} this reservation?`)) return;

            try {
                const response = await fetch('../api/reservations.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=update_status&reservation_id=${id}&status=${status}`
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error updating status');
            }
        }
    </script>
</body>
</html>
