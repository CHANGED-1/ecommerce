<?php
// Helper Functions

// Sanitize input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Create URL slug
function create_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Check if admin is logged in
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Format currency
function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

// Format date
function format_date($date) {
    return date('M j, Y', strtotime($date));
}

// Truncate text
function truncate_text($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Generate SKU
function generate_sku() {
    return 'SKU-' . strtoupper(uniqid());
}

// Upload image
function upload_image($file, $upload_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_name = $file['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if ($file_size > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    if (!in_array($file_ext, ALLOWED_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP'];
    }
    
    $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return ['success' => true, 'filename' => $new_filename];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// Delete image
function delete_image($filename, $upload_dir) {
    $file_path = $upload_dir . $filename;
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

// Calculate discount percentage
function calculate_discount($regular_price, $sale_price) {
    if ($sale_price >= $regular_price) return 0;
    return round((($regular_price - $sale_price) / $regular_price) * 100);
}

// Get stock badge
function get_stock_badge($quantity) {
    if ($quantity == 0) {
        return '<span class="badge badge-danger">Out of Stock</span>';
    } elseif ($quantity <= LOW_STOCK_THRESHOLD) {
        return '<span class="badge badge-warning">Low Stock (' . $quantity . ')</span>';
    } else {
        return '<span class="badge badge-success">In Stock (' . $quantity . ')</span>';
    }
}

// Update stock status
function update_stock_status($conn, $product_id) {
    $sql = "SELECT stock_quantity FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    if ($product) {
        $quantity = $product['stock_quantity'];
        if ($quantity == 0) {
            $status = 'out_of_stock';
        } elseif ($quantity <= LOW_STOCK_THRESHOLD) {
            $status = 'low_stock';
        } else {
            $status = 'in_stock';
        }
        
        $sql = "UPDATE products SET stock_status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $product_id);
        mysqli_stmt_execute($stmt);
    }
}

// Get all categories
function get_categories($conn) {
    $sql = "SELECT * FROM categories ORDER BY display_order ASC, name ASC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get all brands
function get_brands($conn) {
    $sql = "SELECT * FROM brands ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get product count
function get_product_count($conn, $conditions = "") {
    $sql = "SELECT COUNT(*) as count FROM products";
    if ($conditions) {
        $sql .= " WHERE " . $conditions;
    }
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Get category product count
function get_category_product_count($conn, $category_id) {
    $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ? AND status = 'active'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Log search term
function log_search($conn, $search_term, $results_count) {
    $sql = "INSERT INTO search_logs (search_term, results_count) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $search_term, $results_count);
    mysqli_stmt_execute($stmt);
}

// Increment product views
function increment_views($conn, $product_id) {
    $sql = "UPDATE products SET views = views + 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
}

// Get price range
function get_price_range($conn) {
    $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Build product query with filters
function build_product_query($filters = []) {
    $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN brands b ON p.brand_id = b.id 
            WHERE p.status = 'active'";
    
    $params = [];
    $types = '';
    
    if (!empty($filters['category'])) {
        $sql .= " AND p.category_id = ?";
        $params[] = $filters['category'];
        $types .= 'i';
    }
    
    if (!empty($filters['brand'])) {
        $sql .= " AND p.brand_id = ?";
        $params[] = $filters['brand'];
        $types .= 'i';
    }
    
    if (!empty($filters['search'])) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $search_term = '%' . $filters['search'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'ss';
    }
    
    if (isset($filters['min_price'])) {
        $sql .= " AND p.price >= ?";
        $params[] = $filters['min_price'];
        $types .= 'd';
    }
    
    if (isset($filters['max_price'])) {
        $sql .= " AND p.price <= ?";
        $params[] = $filters['max_price'];
        $types .= 'd';
    }
    
    if (!empty($filters['stock'])) {
        if ($filters['stock'] == 'in_stock') {
            $sql .= " AND p.stock_quantity > 0";
        } elseif ($filters['stock'] == 'out_of_stock') {
            $sql .= " AND p.stock_quantity = 0";
        }
    }
    
    // Sorting
    $sort = $filters['sort'] ?? 'newest';
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'name_asc':
            $sql .= " ORDER BY p.name ASC";
            break;
        case 'popular':
            $sql .= " ORDER BY p.views DESC";
            break;
        default:
            $sql .= " ORDER BY p.created_at DESC";
    }
    
    return ['sql' => $sql, 'params' => $params, 'types' => $types];
}
?>