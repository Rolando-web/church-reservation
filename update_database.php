<?php
/**
 * Database Update Script
 * Run this file ONCE in your browser: http://localhost/church-reservation/update_database.php
 * This will update the notifications table to support user notifications
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>Church Reservation - Database Update</h2>";
echo "<p>Updating notifications table...</p>";

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop old notifications table
    echo "<p>1. Dropping old notifications table...</p>";
    $db->exec("DROP TABLE IF EXISTS notifications");
    echo "<p style='color: green;'>✓ Old table dropped</p>";
    
    // Create new notifications table
    echo "<p>2. Creating new notifications table...</p>";
    $sql = "CREATE TABLE notifications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        reservation_id INT,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_is_read (is_read),
        INDEX idx_created_at (created_at)
    )";
    $db->exec($sql);
    echo "<p style='color: green;'>✓ New notifications table created successfully!</p>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Database Update Complete!</h3>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Delete this file (update_database.php) for security</li>";
    echo "<li>Go to <a href='user/index.php'>Browse Services</a></li>";
    echo "<li>The notification bell should now work properly!</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please run the SQL manually in phpMyAdmin:</p>";
    echo "<pre>";
    echo "DROP TABLE IF EXISTS notifications;\n\n";
    echo "CREATE TABLE notifications (\n";
    echo "    id INT PRIMARY KEY AUTO_INCREMENT,\n";
    echo "    user_id INT NOT NULL,\n";
    echo "    type VARCHAR(50) NOT NULL,\n";
    echo "    title VARCHAR(255) NOT NULL,\n";
    echo "    message TEXT NOT NULL,\n";
    echo "    reservation_id INT,\n";
    echo "    is_read BOOLEAN DEFAULT FALSE,\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,\n";
    echo "    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,\n";
    echo "    INDEX idx_user_id (user_id),\n";
    echo "    INDEX idx_is_read (is_read),\n";
    echo "    INDEX idx_created_at (created_at)\n";
    echo ");";
    echo "</pre>";
}
?>
