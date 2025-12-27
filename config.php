<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Moses@19');
define('DB_NAME', 'ecommerce_catalog');

// Site Configuration
define('SITE_NAME', 'ShopHub');
define('SITE_URL', 'http://localhost:8001');
define('ADMIN_EMAIL', 'admin@shophub.com');

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('PRODUCT_UPLOAD_PATH', UPLOAD_PATH . 'products/');
define('CATEGORY_UPLOAD_PATH', UPLOAD_PATH . 'categories/');
define('BRAND_UPLOAD_PATH', UPLOAD_PATH . 'brands/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// Stock Settings
define('LOW_STOCK_THRESHOLD', 10);

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('UTC');
?>