<?php
require_once __DIR__ . '/config/database.php';

// Redirect to landing page
header('Location: ' . BASE_URL . '/landing.php');
exit;
