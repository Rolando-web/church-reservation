<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

User::requireLogin();

// Database connection
$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

// Get unread notification count
$unread_count = 0;
try {
    $stmt = $db->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
} catch (PDOException $e) {
    // Table might not exist or column might be missing, silently fail
    $unread_count = 0;
}

// Get all services from database
$services = [];
try {
    $stmt = $db->query("SELECT * FROM services ORDER BY category, price");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If table doesn't exist, use empty array
    $services = [];
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Services - Browse & Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .service-card {
            transition: all 0.3s ease;
            background: white;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        .service-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .price-badge {
            background: #002B5C;
            border-radius: 8px;
        }
        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card-features {
            flex: 1;
        }
        body {
            background: linear-gradient(rgba(0, 43, 92, 0.65), rgba(0, 43, 92, 0.75)),
                        url('../assets/images/bunner.png') center/cover no-repeat;
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

    <!-- Hero Section (image background) -->
    <section class="py-[250px] text-white" style="background: linear-gradient(rgba(0,43,92,0.55), rgba(0,43,92,0.55)), url('../assets/images/bunner.png') center/cover no-repeat;">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-4">Available Church Services</h1>
            <p class="text-lg md:text-xl text-blue-100 mb-8">Discover a diverse range of sacred church services and ceremonies designed to nurture your spiritual journey and connect you with our faith community. </p>
            <a href="dashboard.php" class="inline-block px-8 py-3 rounded-lg font-medium transition text-sm text-white" style="background: linear-gradient(135deg, #002B5C 0%, #004080 100%);" onmouseover="this.style.background='linear-gradient(135deg, #004080 0%, #002B5C 100%)'" onmouseout="this.style.background='linear-gradient(135deg, #002B5C 0%, #004080 100%)'">
                View My Bookings
            </a>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-12" style="background: linear-gradient(135deg, rgba(0,43,92,0.65) 0%, rgba(0,61,122,0.65) 100%), url('../assets/images/church.png') center/cover no-repeat;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <!-- Search Bar -->
            <div class="max-w-md mb-6">
                <div class="relative">
                    <input type="text" id="searchInput" onkeyup="searchServices()" placeholder="Search services (e.g., wedding, baptism, funeral...)" class="w-full px-4 py-3 pl-12 rounded-lg border border-white/30 bg-white/10 backdrop-blur-sm text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent shadow-sm">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button onclick="filterServices('all')" class="filter-btn bg-white text-[#002B5C] px-5 py-2 rounded-lg font-medium hover:bg-gray-100 transition text-sm shadow-sm">
                    All Services
                </button>
                <button onclick="filterServices('wedding')" class="filter-btn bg-white/10 backdrop-blur-sm border border-white/30 text-white px-5 py-2 rounded-lg font-medium hover:bg-white/20 transition text-sm">
                    💒 Weddings
                </button>
                <button onclick="filterServices('baptism')" class="filter-btn bg-white/10 backdrop-blur-sm border border-white/30 text-white px-5 py-2 rounded-lg font-medium hover:bg-white/20 transition text-sm">
                    👼 Baptism
                </button>
                <button onclick="filterServices('funeral')" class="filter-btn bg-white/10 backdrop-blur-sm border border-white/30 text-white px-5 py-2 rounded-lg font-medium hover:bg-white/20 transition text-sm">
                    🕊️ Funeral
                </button>
                <button onclick="filterServices('communion')" class="filter-btn bg-white/10 backdrop-blur-sm border border-white/30 text-white px-5 py-2 rounded-lg font-medium hover:bg-white/20 transition text-sm">
                    📿 First Communion
                </button>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <?php 
                $category_icons = [
                    'wedding' => '💒',
                    'baptism' => '👼',
                    'funeral' => '🕊️',
                    'communion' => '📿',
                    'other' => '✝️'
                ];
                
                $category_labels = [
                    'wedding' => 'Wedding',
                    'baptism' => 'Baptism',
                    'funeral' => 'Funeral',
                    'communion' => 'Communion',
                    'other' => 'Other'
                ];
                
                if (empty($services)): ?>
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-600 text-xl">No services available at the moment. Please check back later.</p>
                    </div>
                <?php else: 
                    foreach ($services as $service): 
                        $features = explode("\n", trim($service['features']));
                        $category = strtolower($service['category']);
                        $icon = $category_icons[$category] ?? '✝️';
                        $label = $category_labels[$category] ?? ucfirst($category);
                ?>
                
                <!-- Service Card -->
                <div class="service-card rounded-xl overflow-hidden shadow-sm border border-gray-200 hover:shadow-xl" data-category="<?php echo htmlspecialchars($category); ?>" data-name="<?php echo htmlspecialchars($service['name']); ?>" data-service-id="<?php echo $service['id']; ?>" data-service-name="<?php echo htmlspecialchars($service['name']); ?>">
                    <div class="relative">
                        <img src="../assets/images/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="service-image" onerror="this.src='../assets/images/church.png'">
                        <div class="availability-badge absolute top-3 right-3 bg-[#002B5C] text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg" data-service-id="<?php echo $service['id']; ?>">
                            <span class="availability-text">Checking...</span>
                        </div>
                    </div>
                    <div class="card-content p-5">
                        <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($service['description']); ?></p>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 text-xs mb-2">Starting at</p>
                            <p class="text-2xl font-bold text-[#002B5C]">₱<?php echo number_format($service['price'], 0); ?><span class="text-sm text-gray-500 font-normal">/service</span></p>
                        </div>
                        
                        <button onclick="openModalFromCard(this)" class="book-now-btn block w-full bg-[#002B5C] text-white text-center py-2.5 rounded-lg font-semibold hover:bg-[#004080] transition text-sm" data-service-id="<?php echo $service['id']; ?>">
                            Book Now
                        </button>
                    </div>
                </div>

                <?php endforeach; 
                endif; ?>

            </div>
        </div>
    </section>

    <!-- Booking Modal -->
    <div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 text-white p-6 rounded-t-2xl" style="background: linear-gradient(135deg, #002B5C 0%, #004080 100%);">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-serif font-bold" id="modalTitle">Book Service</h2>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200 text-3xl">&times;</button>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Service Details -->
                <div class="mb-6 p-4 rounded-lg" style="background: linear-gradient(135deg, rgba(0, 43, 92, 0.05) 0%, rgba(0, 61, 122, 0.08) 100%);">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" id="modalServiceName"></h3>
                            <p class="text-gray-600" id="modalServiceDesc"></p>
                        </div>
                        <div class="text-2xl font-bold" style="color: #002B5C;" id="modalPrice"></div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-700" id="modalFeatures"></div>
                </div>

                <!-- Booking Form -->
                <form action="../api/reservations.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="purpose" id="servicePurpose">
                    <input type="hidden" name="amount" id="serviceAmount">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reservation Date</label>
                        <input type="date" id="reservationDate" name="reservation_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" onchange="checkTimeSlotAvailability()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time</label>
                        <input type="time" id="reservationTime" name="reservation_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" onchange="checkTimeSlotAvailability()">
                        <div id="availabilityMessage" class="mt-2 text-sm hidden"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Any special requests or requirements..."></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" onclick="closeModal()" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-6 py-3 text-white rounded-lg font-semibold transition" style="background: linear-gradient(135deg, #002B5C 0%, #004080 100%);" onmouseover="this.style.background='linear-gradient(135deg, #004080 0%, #002B5C 100%)'" onmouseout="this.style.background='linear-gradient(135deg, #002B5C 0%, #004080 100%)'">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   
    <!-- Footer -->
    <footer class="text-white py-12 bg-[#002B5C]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <span class="text-3xl">🕊️</span>
                        <span class="ml-2 text-xl font-serif font-bold">Church Reservation</span>
                    </div>
                    <p class="text-gray-400">
                        Simplifying church bookings for everyone.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#home" class="hover:text-white transition">Home</a></li>
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#about" class="hover:text-white transition">About</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="login.php" class="hover:text-white transition">Login</a></li>
                        <li><a href="register.php" class="hover:text-white transition">Register</a></li>
                        <li><a href="forgot-password.php" class="hover:text-white transition">Forgot Password</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Church Reservation System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Check service availability on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAllServiceAvailability();
        });

        async function checkAllServiceAvailability() {
            try {
                const response = await fetch('../api/get_service_availability.php');
                const data = await response.json();
                
                if (data.success) {
                    // Update each service card
                    document.querySelectorAll('.service-card').forEach(card => {
                        const serviceId = card.dataset.serviceId;
                        const serviceName = card.dataset.serviceName;
                        
                        // Find availability info by matching service name
                        const availInfo = Object.values(data.availability).find(a => a.name === serviceName);
                        
                        const badge = card.querySelector('.availability-badge');
                        const badgeText = badge.querySelector('.availability-text');
                        const bookBtn = card.querySelector('.book-now-btn');
                        
                        if (availInfo && !availInfo.available) {
                            // Service is unavailable
                            badge.classList.remove('bg-[#002B5C]');
                            badge.classList.add('bg-red-500');
                            badgeText.textContent = 'Unavailable';
                            
                            // Disable button
                            bookBtn.disabled = true;
                            bookBtn.classList.remove('bg-[#002B5C]', 'hover:bg-[#004080]');
                            bookBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                            bookBtn.textContent = 'Currently Booked';
                        } else {
                            // Service is available
                            badge.classList.remove('bg-red-500');
                            badge.classList.add('bg-[#002B5C]');
                            badgeText.textContent = 'Available';
                            
                            // Enable button
                            bookBtn.disabled = false;
                            bookBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                            bookBtn.classList.add('bg-[#002B5C]', 'hover:bg-[#004080]');
                            bookBtn.textContent = 'Book Now';
                        }
                    });
                }
            } catch (error) {
                console.error('Error checking availability:', error);
            }
        }

        async function checkTimeSlotAvailability() {
            const dateInput = document.getElementById('reservationDate');
            const timeInput = document.getElementById('reservationTime');
            const messageDiv = document.getElementById('availabilityMessage');
            const submitBtn = document.querySelector('#bookingModal form button[type="submit"]');
            
            if (!dateInput.value || !timeInput.value) {
                messageDiv.classList.add('hidden');
                return;
            }
            
            try {
                const response = await fetch(`../api/check_availability.php?date=${dateInput.value}&time=${timeInput.value}`);
                const data = await response.json();
                
                messageDiv.classList.remove('hidden');
                
                if (data.available) {
                    messageDiv.className = 'mt-2 text-sm text-green-600 font-semibold';
                    messageDiv.innerHTML = '✓ This time slot is available';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    messageDiv.className = 'mt-2 text-sm text-red-600 font-semibold';
                    messageDiv.innerHTML = '✗ This time slot is already reserved. Please choose a different time.';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } catch (error) {
                console.error('Error checking time slot:', error);
            }
        }

        function searchServices() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const cards = document.querySelectorAll('.service-card');
            
            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                const category = card.dataset.category.toLowerCase();
                
                if (title.includes(filter) || description.includes(filter) || category.includes(filter)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function filterServices(category) {
            // Clear search when filtering
            document.getElementById('searchInput').value = '';
            
            const cards = document.querySelectorAll('.service-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update button styles
            buttons.forEach(btn => {
                if (btn !== event.target) {
                    btn.classList.remove('bg-white', 'text-[#002B5C]');
                    btn.classList.add('bg-white/10', 'backdrop-blur-sm', 'border', 'border-white/30', 'text-white');
                }
            });
            event.target.classList.remove('bg-white/10', 'backdrop-blur-sm', 'border', 'border-white/30', 'text-white');
            event.target.classList.add('bg-white', 'text-[#002B5C]');
            
            // Filter cards
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        }

        function openBookingModal(serviceName, serviceDesc, price, features, purpose) {
            document.getElementById('modalTitle').textContent = 'Book ' + serviceName;
            document.getElementById('modalServiceName').textContent = serviceName;
            document.getElementById('modalServiceDesc').textContent = serviceDesc;
            document.getElementById('modalPrice').textContent = price;
            document.getElementById('servicePurpose').value = purpose;
            document.getElementById('serviceAmount').value = price.replace('₱', '').replace(',', '');
            
            // Display features
            const featuresHtml = features.map(f => `<p class="flex items-start"><span class="mr-2" style="color: #002B5C;">✓</span>${f}</p>`).join('');
            document.getElementById('modalFeatures').innerHTML = featuresHtml;
            
            document.getElementById('bookingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Extract data from card and open modal
        function openModalFromCard(button) {
            const card = button.closest('.service-card');
            const serviceName = card.querySelector('h3').textContent.trim();
            const serviceDesc = card.querySelector('.line-clamp-2').textContent.trim();
            const priceElement = card.querySelector('.text-2xl.font-bold');
            // Get only the price number, removing the /service text
            let priceText = priceElement ? priceElement.childNodes[0].textContent.trim() : '₱0';
            // Extract numeric value (remove ₱ and commas)
            const numericPrice = parseFloat(priceText.replace('₱', '').replace(/,/g, ''));
            const displayPrice = '₱' + numericPrice.toLocaleString();
            
            // For now, we'll show basic info since features aren't displayed in cards
            // You can fetch full details via AJAX if needed
            const features = [
                'Professional church venue',
                'Ceremonial arrangements included',
                'Booking confirmation via email'
            ];
            const purpose = serviceName;
            
            // Pass the numeric price for the hidden input and display price for the modal
            document.getElementById('modalTitle').textContent = 'Book ' + serviceName;
            document.getElementById('modalServiceName').textContent = serviceName;
            document.getElementById('modalServiceDesc').textContent = serviceDesc;
            document.getElementById('modalPrice').textContent = displayPrice;
            document.getElementById('servicePurpose').value = purpose;
            document.getElementById('serviceAmount').value = numericPrice;
            
            // Display features
            const featuresHtml = features.map(f => `<p class="flex items-start"><span class="mr-2" style="color: #002B5C;">✓</span>${f}</p>`).join('');
            document.getElementById('modalFeatures').innerHTML = featuresHtml;
            
            document.getElementById('bookingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Profile Modal Functions
        function openProfileModal() {
            // Create modal if it doesn't exist
            if (!document.getElementById('profileModal')) {
                const modalHTML = `
                    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
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
                `;
                document.body.insertAdjacentHTML('beforeend', modalHTML);
                
                // Add form submit handler
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
            }
            
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
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            const modal = document.getElementById('profileModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                document.getElementById('profilePassword').value = '';
            }
        }
    </script>
</body>
</html>
