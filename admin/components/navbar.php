<!-- Clean Admin Navigation Component -->
<header class="admin-navbar">
    <!-- Align brand to left with slight margin (ml-1) to match sidebar edge -->
    <div class="flex items-center justify-between ml-1 pr-6" style="color:#fff;">
        <div class="brand ml-1">
            <img src="../assets/images/church.png" alt="logo">
            <div>
                <div style="font-size:15px; letter-spacing:.5px;">Church Reservation</div>
                <div style="font-size:11px; opacity:.7;">Admin Panel</div>
            </div>
        </div>
        <div class="flex items-center gap-5">
            <a href="#" title="Notifications" class="text-gray-300 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h11z"/>
                </svg>
            </a>
            <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../api/auth.php?action=logout" class="border p-2 px-4 bg-red-500 text-white rounded-md text-sm">Logout</a>
        </div>
    </div>
    
</header>
