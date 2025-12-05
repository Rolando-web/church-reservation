<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Services Table</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Setup Services Table</h1>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/../config/database.php';
                
                try {
                    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    echo '<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">';
                    echo '<p class="text-blue-700 font-semibold">Creating services table...</p>';
                    echo '</div>';
                    
                    // Read SQL file
                    $sql = file_get_contents(__DIR__ . '/create_services_table.sql');
                    
                    // Execute SQL statements
                    $db->exec($sql);
                    
                    echo '<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">';
                    echo '<p class="text-green-700 font-semibold">✓ Services table created successfully!</p>';
                    echo '<p class="text-green-600 mt-2">Sample services have been inserted into the database.</p>';
                    echo '</div>';
                    
                    // Count services
                    $stmt = $db->query("SELECT COUNT(*) as count FROM services");
                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    echo '<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">';
                    echo '<p class="text-green-700 font-semibold">Total services in database: ' . $count . '</p>';
                    echo '</div>';
                    
                    echo '<div class="mt-6 flex gap-4">';
                    echo '<a href="../admin/services.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Go to Services Management</a>';
                    echo '<a href="../user/index.php" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">View Services Page</a>';
                    echo '</div>';
                    
                } catch (PDOException $e) {
                    echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">';
                    echo '<p class="text-red-700 font-semibold">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        echo '<p class="text-red-600 mt-2">The services table already exists. No changes were made.</p>';
                        echo '<div class="mt-4">';
                        echo '<a href="../admin/services.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition inline-block">Go to Services Management</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
            ?>
            
            <div class="mb-6">
                <p class="text-gray-700 mb-4">This script will create the <code class="bg-gray-100 px-2 py-1 rounded">services</code> table in your database with the following structure:</p>
                
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• <strong>id</strong> - Primary key</li>
                        <li>• <strong>name</strong> - Service name</li>
                        <li>• <strong>category</strong> - wedding, baptism, funeral, communion, other</li>
                        <li>• <strong>price</strong> - Service price in PHP</li>
                        <li>• <strong>description</strong> - Service description</li>
                        <li>• <strong>features</strong> - Service features (newline separated)</li>
                        <li>• <strong>image</strong> - Image filename</li>
                        <li>• <strong>created_at</strong> - Timestamp</li>
                        <li>• <strong>updated_at</strong> - Timestamp</li>
                    </ul>
                </div>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <p class="text-blue-700"><strong>Note:</strong> This will also insert 8 sample services including weddings, baptisms, funerals, and communion services.</p>
                </div>
            </div>
            
            <form method="POST">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Create Services Table & Insert Sample Data
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="../admin/dashboard.php" class="text-gray-600 hover:text-gray-800">Back to Dashboard</a>
            </div>
            
            <?php } ?>
        </div>
    </div>
</body>
</html>
