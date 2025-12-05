<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';

User::requireAdmin();

header('Content-Type: application/json');

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'create':
            $name = $_POST['name'] ?? '';
            $category = $_POST['category'] ?? '';
            $price = $_POST['price'] ?? 0;
            $description = $_POST['description'] ?? '';
            $features = $_POST['features'] ?? '';
            $image = 'church.png'; // Default image

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../assets/images/';
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'service_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $image = $new_filename;
                    }
                }
            }

            if (empty($name) || empty($category) || empty($description)) {
                echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
                exit;
            }

            $stmt = $db->prepare("INSERT INTO services (name, category, price, description, features, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $price, $description, $features, $image]);

            echo json_encode(['success' => true, 'message' => 'Service created successfully']);
            header('Location: ../admin/services.php');
            break;

        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $category = $_POST['category'] ?? '';
            $price = $_POST['price'] ?? 0;
            $description = $_POST['description'] ?? '';
            $features = $_POST['features'] ?? '';
            
            if (empty($id) || empty($name) || empty($category) || empty($description)) {
                echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
                exit;
            }

            // Get current image
            $stmt = $db->prepare("SELECT image FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $current_service = $stmt->fetch(PDO::FETCH_ASSOC);
            $image = $current_service['image'] ?? 'church.png';

            // Handle file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../assets/images/';
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'service_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        // Delete old image if it's not the default
                        if ($image !== 'church.png' && file_exists($upload_dir . $image)) {
                            unlink($upload_dir . $image);
                        }
                        $image = $new_filename;
                    }
                }
            }

            $stmt = $db->prepare("UPDATE services SET name = ?, category = ?, price = ?, description = ?, features = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $category, $price, $description, $features, $image, $id]);

            echo json_encode(['success' => true, 'message' => 'Service updated successfully']);
            header('Location: ../admin/services.php');
            break;

        case 'delete':
            $id = $_POST['id'] ?? 0;

            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Service ID is required']);
                exit;
            }

            $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => 'Service deleted successfully']);
            break;

        case 'list':
            $stmt = $db->query("SELECT * FROM services ORDER BY created_at DESC");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'services' => $services]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
