<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'church_reservation');

// Timezone
date_default_timezone_set('Asia/Manila');

// Base URL
define('BASE_URL', 'http://localhost/church-reservation');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
