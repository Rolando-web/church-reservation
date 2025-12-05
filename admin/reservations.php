<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';

User::requireAdmin();

$reservation = new Reservation();

// Get filters
$status = $_GET['status'] ?? null;
$dateFrom = $_GET['date_from'] ?? null;
$dateTo = $_GET['date_to'] ?? null;

$reservations = $reservation->getAll($status, $dateFrom, $dateTo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin">
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <div>
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-container ml-10">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-lg font-semibold">Manage Reservations</h1>
                    <a href="reports.php" class="btn btn-ghost flex items-center gap-2 bg-red-300 text-red-700 hover:bg-red-100 font-medium transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate Reports
                    </a>
                </div>

            <!-- Filter Bar -->
            <div class="card mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Status</label>
                        <select name="status" class="text-black  w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Date From</label>
                        <input type="date" name="date_from" value="<?php echo $dateFrom; ?>" class="text-black  w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Date To</label>
                        <input type="date" name="date_to" value="<?php echo $dateTo; ?>" class="text-black  w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full text-sm py-2">Apply Filters</button>
                    </div>
                </form>
            </div>

            <!-- Reservations Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($reservations as $r): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-lg transition flex flex-col">
                        <div class="flex justify-between items-start mb-3">
                            <span class="status-badge status-<?php echo $r['status']; ?>">
                                <?php echo ucfirst($r['status']); ?>
                            </span>
                            <span class="text-xs text-gray-500">#<?php echo $r['id']; ?></span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($r['purpose']); ?></h3>
                        
                        <div class="space-y-2 text-sm mb-4 flex-1">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="font-medium"><?php echo htmlspecialchars($r['user_name']); ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span><?php echo date('M d, Y', strtotime($r['reservation_date'])); ?> at <?php echo date('h:i A', strtotime($r['reservation_time'])); ?></span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-purple-600">₱<?php echo number_format($r['amount'], 2); ?></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
                            <span class="status-badge status-<?php echo $r['payment_status']; ?> text-xs">
                                <?php echo ucfirst($r['payment_status']); ?>
                            </span>
                            <div class="flex gap-2">
                                <?php if ($r['status'] === 'pending'): ?>
                                    <button onclick="updateStatus(<?php echo $r['id']; ?>, 'approved')" class="text-xs bg-green-50 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-100 font-medium transition">✓ Approve</button>
                                    <button onclick="updateStatus(<?php echo $r['id']; ?>, 'rejected')" class="text-xs bg-red-50 text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-100 font-medium transition">✗ Reject</button>
                                <?php elseif ($r['status'] === 'approved' || $r['status'] === 'paid'): ?>
                                    <button onclick="openRescheduleModal(<?php echo $r['id']; ?>, '<?php echo $r['reservation_date']; ?>', '<?php echo $r['reservation_time']; ?>')" class="text-xs bg-purple-50 text-purple-700 px-3 py-1.5 rounded-lg hover:bg-purple-100 font-medium transition">🔄 Reschedule</button>
                                <?php endif; ?>
                                <?php if ($r['status'] === 'paid'): ?>
                                    <a href="../api/receipt.php?id=<?php echo $r['id']; ?>" target="_blank" class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-100 font-medium transition">📄 Receipt</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            </div>
        </main>
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-[#002B5C] to-[#003d7a] text-white p-6 rounded-t-xl">
                <h3 class="text-2xl font-bold">Reschedule Reservation</h3>
            </div>
            <form id="rescheduleForm" class="p-6 space-y-4">
                <input type="hidden" id="rescheduleReservationId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Date</label>
                    <input type="date" id="rescheduleDate" class="w-full px-4 py-2 border text-black border-black rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Time</label>
                    <input type="time" id="rescheduleTime" class="w-full px-4 py-2 border text-black border-black rounded-lg focus:ring-2 focus:ring-[#002B5C] focus:border-transparent" required>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeRescheduleModal()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#002B5C] text-white rounded-lg hover:bg-[#003d7a] font-semibold transition">Reschedule</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        function openRescheduleModal(id, currentDate, currentTime) {
            document.getElementById('rescheduleReservationId').value = id;
            document.getElementById('rescheduleDate').value = currentDate;
            document.getElementById('rescheduleTime').value = currentTime;
            document.getElementById('rescheduleModal').classList.remove('hidden');
        }

        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').classList.add('hidden');
        }

        document.getElementById('rescheduleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('rescheduleReservationId').value;
            const date = document.getElementById('rescheduleDate').value;
            const time = document.getElementById('rescheduleTime').value;
            
            try {
                const response = await fetch('../api/reservations.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=reschedule&reservation_id=${id}&new_date=${date}&new_time=${time}`
                });

                const result = await response.json();
                if (result.success) {
                    alert('Reservation rescheduled successfully!');
                    location.reload();
                } else {
                    alert(result.message || 'Error rescheduling reservation');
                }
            } catch (error) {
                alert('Error rescheduling reservation');
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
