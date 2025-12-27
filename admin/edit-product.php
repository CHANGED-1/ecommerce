<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$page_title = 'Edit Product';
$errors = [];
$success = '';

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$product_id) {
    redirect('products.php');
}

// Get product data
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    redirect('products.php');
}

$product = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $slug = create_slug($name);
    $description = clean_input($_POST['description']);
    $short_description = clean_input($_POST['short_description']);
    $category_id = intval($_POST['category_id']);
    $brand_id = intval($_POST['brand_id']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $sku = clean_input($_POST['sku']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = clean_input($_POST['status']);
    
    // Validation
    if (empty($name)) $errors[] = 'Product name is required';
    if (empty($category_id)) $errors[] = 'Category is required';
    if ($price <= 0) $errors[] = 'Price must be greater than 0';
    if ($sale_price && $sale_price >= $price) $errors[] = 'Sale price must be less than regular price';
    
    // Check if slug/SKU exists for other products
    $sql = "SELECT id FROM products WHERE (slug = ? OR sku = ?) AND id != ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $slug, $sku, $product_id);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
        $slug = $slug . '-' . time();
    }
    
    // Handle image upload
    $image_filename = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_result = upload_image($_FILES['image'], PRODUCT_UPLOAD_PATH);
        if ($upload_result['success']) {
            // Delete old image
            if ($product['image']) {
                delete_image($product['image'], PRODUCT_UPLOAD_PATH);
            }
            $image_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    // Handle image deletion
    if (isset($_POST['delete_image']) && $product['image']) {
        delete_image($product['image'], PRODUCT_UPLOAD_PATH);
        $image_filename = '';
    }
    
    if (empty($errors)) {
        $sql = "UPDATE products SET name = ?, slug = ?, description = ?, short_description = ?, category_id = ?, brand_id = ?, price = ?, sale_price = ?, sku = ?, stock_quantity = ?, image = ?, featured = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssiiddsisisi", $name, $slug, $description, $short_description, $category_id, $brand_id, $price, $sale_price, $sku, $stock_quantity, $image_filename, $featured, $status, $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            update_stock_status($conn, $product_id);
            $success = 'Product updated successfully!';
            
            // Refresh product data
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
        } else {
            $errors[] = 'Failed to update product';
        }
    }
}

$categories = get_categories($conn);
$brands = get_brands($conn);

include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Edit Product</h1>
        <div>
            <a href="../product.php?slug=<?php echo $product['slug']; ?>" target="_blank" class="btn btn-secondary">
                <i class="fas fa-eye"></i> View Product
            </a>
            <a href="products.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <?php if($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>SKU *</label>
                    <input type="text" name="sku" value="<?php echo $product['sku']; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Short Description</label>
                <textarea name="short_description" rows="2"><?php echo $product['short_description']; ?></textarea>
                <small>Brief description for product cards (max 500 characters)</small>
            </div>
            
            <div class="form-group">
                <label>Full Description</label>
                <textarea name="description" rows="6"><?php echo $product['description']; ?></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id">
                        <option value="">Select Brand</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>" <?php echo $product['brand_id'] == $brand['id'] ? 'selected' : ''; ?>>
                                <?php echo $brand['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-grid-3">
                <div class="form-group">
                    <label>Regular Price * ($)</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Sale Price ($)</label>
                    <input type="number" name="sale_price" step="0.01" min="0" value="<?php echo $product['sale_price']; ?>">
                    <small>Leave empty if no sale</small>
                </div>
                
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Product Image</label>
                <?php if($product['image']): ?>
                    <div class="current-image-preview">
                        <img src="../uploads/products/<?php echo $product['image']; ?>" alt="Current">
                        <label class="checkbox-label">
                            <input type="checkbox" name="delete_image" value="1">
                            <span>Delete this image</span>
                        </label>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
                <small>Max 5MB. Allowed: JPG, PNG, GIF, WebP</small>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="draft" <?php echo $product['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Product Info</label>
                    <div style="padding: 10px; background: var(--light); border-radius: 6px;">
                        <p><strong>Created:</strong> <?php echo format_date($product['created_at']); ?></p>
                        <p><strong>Views:</strong> <?php echo $product['views']; ?></p>
                        <p><strong>Stock Status:</strong> <?php echo get_stock_badge($product['stock_quantity']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="featured" value="1" <?php echo $product['featured'] ? 'checked' : ''; ?>>
                    <span>Feature this product on homepage</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
                <a href="delete-product.php?id=<?php echo $product_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.current-image-preview {
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--light);
    border-radius: 8px;
    display: inline-block;
}

.current-image-preview img {
    max-width: 200px;
    border-radius: 6px;
    display: block;
    margin-bottom: 0.5rem;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}
</style>

<?php include 'includes/admin_footer.php'; ?>