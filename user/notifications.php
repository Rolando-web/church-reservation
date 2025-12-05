<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

User::requireLogin();

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';

$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

if (isset($_GET['action']) && $_GET['action'] === 'mark_read' && isset($_GET['id'])) {
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    header('Location: notifications.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    header('Location: notifications.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    header('Location: notifications.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Church Reservation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 43, 92, 0.75), rgba(0, 43, 92, 0.85)),
                        url('../assets/images/church.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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

    <section class="py-12 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-serif font-bold text-primary mb-2">Notifications</h1>
                        <p class="text-gray-600">Stay updated on your reservation status</p>
                    </div>
                    <?php if ($unread_count > 0): ?>
                        <a href="?action=mark_all_read" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition">
                            Mark All as Read
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($notifications)): ?>
                    <div class="text-center py-12">
                        <svg class="mx-auto w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Notifications</h3>
                        <p class="text-gray-500">You're all caught up! Check back later for updates.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="border rounded-lg p-4 <?php echo $notification['is_read'] ? 'bg-white' : 'bg-blue-50 border-blue-200'; ?> hover:shadow-md transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 flex-1">
                                        <div class="flex-shrink-0 mt-1">
                                            <?php if ($notification['type'] === 'approved'): ?>
                                                <div class="bg-green-100 p-2 rounded-full">
                                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            <?php elseif ($notification['type'] === 'rejected'): ?>
                                                <div class="bg-red-100 p-2 rounded-full">
                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            <?php else: ?>
                                                <div class="bg-blue-100 p-2 rounded-full">
                                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 mb-1">
                                                <?php echo htmlspecialchars($notification['title']); ?>
                                                <?php if (!$notification['is_read']): ?>
                                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                                <?php endif; ?>
                                            </h3>
                                            <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span><?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?></span>
                                                <?php if ($notification['reservation_id']): ?>
                                                    <a href="dashboard.php?reservation_id=<?php echo $notification['reservation_id']; ?>" class="text-primary hover:underline">
                                                        View Reservation
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        <?php if (!$notification['is_read']): ?>
                                            <a href="?action=mark_read&id=<?php echo $notification['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Mark as read">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                        <a href="?action=delete&id=<?php echo $notification['id']; ?>" class="text-red-600 hover:text-red-800" title="Delete" onclick="return confirm('Are you sure you want to delete this notification?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; 2025 Church Reservation System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
