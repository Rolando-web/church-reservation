<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

User::requireAdmin();

// Database connection
$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

// Get all services
$services = [];
$table_exists = true;
try {
    $stmt = $db->query("SELECT * FROM services ORDER BY created_at DESC");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table doesn't exist yet
    $table_exists = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Church Reservation System</title>
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
            <div class="admin-container z-0 ml-10">
                <?php if (!$table_exists): ?>
                    <!-- Setup Required Notice -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-lg shadow-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-lg font-medium text-yellow-800">Services Table Not Found</h3>
                                <p class="mt-2 text-sm text-yellow-700">The services table needs to be created in your database before you can manage services.</p>
                                <div class="mt-4">
                                    <a href="../database/setup_services.php" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Setup Services Table Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h1 class="text-lg font-semibold mb-1">Available Services</h1>
                        <p class="text-sm text-gray-500">Manage church services and packages</p>
                    </div>
                    <button onclick="openAddModal()" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New Service
                    </button>
                </div>

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($services as $service): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition flex flex-col">
                            <div class="relative">
                                <img src="../assets/images/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="w-full h-48 object-cover" onerror="this.src='../assets/images/church.png'">
                                <div class="absolute top-3 right-3 bg-[#002B5C] text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                                    Available
                                </div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($service['name']); ?></h3>
                                <p class="text-gray-500 text-xs mb-3 line-clamp-2"><?php echo htmlspecialchars($service['description']); ?></p>
                                
                                <div class="mb-4 flex-1">
                                    <p class="text-gray-600 text-xs mb-1">Starting at</p>
                                    <p class="text-2xl font-bold text-[#002B5C]">₱<?php echo number_format($service['price'], 0); ?><span class="text-sm text-gray-500 font-normal">/service</span></p>
                                </div>
                                
                                <div class="flex gap-2 mt-auto">
                                    <button type="button" onclick='editService(<?php echo json_encode($service); ?>)' class="flex-1 bg-[#002B5C] text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition flex items-center justify-center cursor-pointer text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button type="button" onclick="deleteService(<?php echo $service['id']; ?>)" class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center justify-center cursor-pointer text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($services)): ?>
                        <div class="col-span-3 text-center py-12">
                            <p class="text-white text-xl">No services added yet. Click "Add New Service" to get started.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div id="serviceModal" class="hidden fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-[#002B5C] to-blue-900 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold" id="modalTitle">Add New Service</h2>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200 text-3xl leading-none">&times;</button>
                </div>
            </div>
            
            <form id="serviceForm" action="../api/services.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="serviceId">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                    <input type="text" name="name" id="serviceName" required class="w-full px-4 py-2 border text-black border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" id="serviceCategory" required class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="wedding">Wedding</option>
                        <option value="baptism">Baptism</option>
                        <option value="funeral">Funeral</option>
                        <option value="communion">Communion</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (₱) *</label>
                    <input type="number" name="price" id="servicePrice" required min="0" step="0.01" class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" id="serviceDescription" required rows="3" class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Features (one per line) *</label>
                    <textarea name="features" id="serviceFeatures" required rows="5" placeholder="Church venue for 3 hours&#10;Priest officiation&#10;Basic floral arrangement" class="text-black  w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    <p class="text-sm text-gray-500 mt-1">Enter each feature on a new line</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Image</label>
                    <input type="file" name="image" id="serviceImageFile" accept="image/*">
                    <p class="text-sm text-gray-500 mt-1">Upload an image or leave empty to use default church image</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeModal()" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#002B5C] text-white rounded-lg font-semibold hover:bg-blue-900 transition">
                        Save Service
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Service';
            document.getElementById('formAction').value = 'create';
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceId').value = '';
            document.getElementById('serviceModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function editService(service) {
            console.log('Edit clicked:', service);
            document.getElementById('modalTitle').textContent = 'Edit Service';
            document.getElementById('formAction').value = 'update';
            document.getElementById('serviceId').value = service.id;
            document.getElementById('serviceName').value = service.name;
            document.getElementById('serviceCategory').value = service.category;
            document.getElementById('servicePrice').value = service.price;
            document.getElementById('serviceDescription').value = service.description;
            document.getElementById('serviceFeatures').value = service.features;
            document.getElementById('serviceModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('serviceModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        async function deleteService(id) {
            if (!confirm('Are you sure you want to delete this service?')) return;

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                const response = await fetch('../api/services.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to delete service');
                }
            } catch (error) {
                alert('Error deleting service');
            }
        }

        // Close modal on outside click
        document.getElementById('serviceModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
