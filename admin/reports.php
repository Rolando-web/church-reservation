<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';

User::requireAdmin();

$reservation = new Reservation();

// Get filter parameters
$status = $_GET['status'] ?? null;
$dateFrom = $_GET['date_from'] ?? null;
$dateTo = $_GET['date_to'] ?? null;

// Get filtered reservations for preview
$reservations = $reservation->getAll($status, $dateFrom, $dateTo);

// Calculate totals
$totalReservations = count($reservations);
$totalRevenue = array_sum(array_column($reservations, 'amount'));
$paidCount = count(array_filter($reservations, fn($r) => $r['payment_status'] === 'paid'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            main { margin-left: 0 !important; }
            .report-container { max-width: 1200px; margin: 0 auto; }
        }
        .print-only { display: none; }
    </style>
</head>
<body class="admin">
</head>
<body>
    <div class="no-print">
        <?php include __DIR__ . '/components/navbar.php'; ?>
    </div>

    <div>
        <div class="no-print">
            <?php include __DIR__ . '/components/sidebar.php'; ?>
        </div>

        <main class="admin-main">
            <div class="admin-container ml-10">
                <div class="no-print flex justify-between items-center mb-4">
                    <h1 class="text-lg font-semibold">Generate Reports</h1>
                    <a href="reservations.php" class="text-sm text-gray-600 hover:text-gray-800 transition">← Back to Reservations</a>
                </div>

                <!-- Filter Form -->
                <div class="no-print bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Report Filters</h2>
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="<?php echo $dateFrom; ?>" class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="<?php echo $dateTo; ?>" class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full btn-primary">Apply Filters</button>
                        </div>
                    </form>
                </div>

                <!-- Summary Stats -->
                <div class="no-print grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Reservations</p>
                                <p class="text-3xl font-bold text-gray-900"><?php echo $totalReservations; ?></p>
                            </div>
                            <div class="text-4xl">📋</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Paid Reservations</p>
                                <p class="text-3xl font-bold text-green-600"><?php echo $paidCount; ?></p>
                            </div>
                            <div class="text-4xl">✅</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Revenue</p>
                                <p class="text-3xl font-bold text-blue-600">₱<?php echo number_format($totalRevenue, 2); ?></p>
                            </div>
                            <div class="text-4xl">💰</div>
                        </div>
                    </div>
                </div>

                <!-- Export Buttons -->
                <div class="no-print bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Export Report</h2>
                    <div class="flex gap-4">
                        <button onclick="window.print()" class="btn-primary flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print / PDF
                        </button>
                        <button onclick="exportToCSV()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export to CSV
                        </button>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden report-container">
                    <!-- Print Header -->
                    <div class="print-only p-6 border-b">
                        <div class="text-center">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">🕊️ Church Reservation System</h1>
                            <h2 class="text-xl text-gray-700 mb-4">Reservation Report</h2>
                            <p class="text-sm text-gray-600">
                                Generated on: <?php echo date('F d, Y h:i A'); ?>
                                <?php if ($dateFrom || $dateTo): ?>
                                    <br>Period: 
                                    <?php echo $dateFrom ? date('M d, Y', strtotime($dateFrom)) : 'Beginning'; ?> - 
                                    <?php echo $dateTo ? date('M d, Y', strtotime($dateTo)) : 'Present'; ?>
                                <?php endif; ?>
                                <?php if ($status): ?>
                                    <br>Status Filter: <?php echo ucfirst($status); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-6 text-center">
                            <div>
                                <p class="text-sm text-gray-600">Total Reservations</p>
                                <p class="text-2xl font-bold"><?php echo $totalReservations; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Paid Reservations</p>
                                <p class="text-2xl font-bold text-green-600"><?php echo $paidCount; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Revenue</p>
                                <p class="text-2xl font-bold text-blue-600">₱<?php echo number_format($totalRevenue, 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table id="reportTable" class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Date & Time</th>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Purpose</th>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Payment</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($reservations as $r): ?>
                                        <tr>
                                            <td class="px-2 py-2 whitespace-nowrap">#<?php echo $r['id']; ?></td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($r['user_name']); ?></div>
                                                <div class="text-gray-500"><?php echo htmlspecialchars($r['user_email']); ?></div>
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <?php echo date('M d, Y', strtotime($r['reservation_date'])); ?><br>
                                                <span class="text-gray-500"><?php echo date('h:i A', strtotime($r['reservation_time'])); ?></span>
                                            </td>
                                            <td class="px-2 py-2 text-sm"><?php echo htmlspecialchars($r['purpose']); ?></td>
                                            <td class="px-2 py-2 whitespace-nowrap font-semibold text-green-600">₱<?php echo number_format($r['amount'], 2); ?></td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <span class="status-badge status-<?php echo $r['status']; ?>">
                                                    <?php echo ucfirst($r['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <span class="status-badge status-<?php echo $r['payment_status']; ?>">
                                                    <?php echo ucfirst($r['payment_status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-2 py-2 text-right font-bold text-gray-900">TOTAL:</td>
                                        <td class="px-2 py-2 font-bold text-green-600">₱<?php echo number_format($totalRevenue, 2); ?></td>
                                        <td colspan="2" class="px-2 py-2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        function exportToCSV() {
            const table = document.getElementById('reportTable');
            let csv = [];
            
            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Get data rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    // Clean up the text content
                    let text = td.textContent.trim().replace(/\n/g, ' ').replace(/,/g, ';');
                    row.push(`"${text}"`);
                });
                csv.push(row.join(','));
            });
            
            // Create download
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `reservation-report-${new Date().getTime()}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
