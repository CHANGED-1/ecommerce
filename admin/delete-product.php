<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$product_id) {
    redirect('products.php');
}

// Get product
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    redirect('products.php');
}

$product = mysqli_fetch_assoc($result);

// Delete image
if ($product['image']) {
    delete_image($product['image'], PRODUCT_UPLOAD_PATH);
}

// Delete product
$sql = "DELETE FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);

$_SESSION['success'] = 'Product deleted successfully!';
redirect('products.php');
?>