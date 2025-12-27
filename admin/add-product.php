<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$page_title = 'Add Product';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $slug = create_slug($name);
    $description = clean_input($_POST['description']);
    $short_description = clean_input($_POST['short_description']);
    $category_id = intval($_POST['category_id']);
    $brand_id = intval($_POST['brand_id']);
    $price = floatval($_POST['price']);
    $sale_price = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $sku = !empty($_POST['sku']) ? clean_input($_POST['sku']) : generate_sku();
    $stock_quantity = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = clean_input($_POST['status']);
    
    // Validation
    if (empty($name)) $errors[] = 'Product name is required';
    if (empty($category_id)) $errors[] = 'Category is required';
    if ($price <= 0) $errors[] = 'Price must be greater than 0';
    if ($sale_price && $sale_price >= $price) $errors[] = 'Sale price must be less than regular price';
    
    // Check if slug/SKU exists
    $sql = "SELECT id FROM products WHERE slug = ? OR sku = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $slug, $sku);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
        $slug = $slug . '-' . time();
        $sku = $sku . '-' . rand(100, 999);
    }
    
    // Handle image upload
    $image_filename = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_result = upload_image($_FILES['image'], PRODUCT_UPLOAD_PATH);
        if ($upload_result['success']) {
            $image_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        $sql = "INSERT INTO products (name, slug, description, short_description, category_id, brand_id, price, sale_price, sku, stock_quantity, image, featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssiiддsisis", $name, $slug, $description, $short_description, $category_id, $brand_id, $price, $sale_price, $sku, $stock_quantity, $image_filename, $featured, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $product_id = mysqli_insert_id($conn);
            update_stock_status($conn, $product_id);
            $success = 'Product added successfully!';
            // Clear form
            $name = $description = $short_description = '';
            $category_id = $brand_id = 0;
            $price = $sale_price = $stock_quantity = 0;
            $featured = 0;
        } else {
            $errors[] = 'Failed to add product';
        }
    }
}

$categories = get_categories($conn);
$brands = get_brands($conn);

include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Add New Product</h1>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
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
                    <input type="text" name="name" value="<?php echo $name ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" name="sku" value="<?php echo $sku ?? ''; ?>" placeholder="Auto-generated if empty">
                </div>
            </div>
            
            <div class="form-group">
                <label>Short Description</label>
                <textarea name="short_description" rows="2"><?php echo $short_description ?? ''; ?></textarea>
                <small>Brief description for product cards (max 500 characters)</small>
            </div>
            
            <div class="form-group">
                <label>Full Description</label>
                <textarea name="description" rows="6"><?php echo $description ?? ''; ?></textarea>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Brand</label>
                    <select name="brand_id">
                        <option value="">Select Brand</option>
                        <?php foreach($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-grid-3">
                <div class="form-group">
                    <label>Regular Price * ($)</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?php echo $price ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Sale Price ($)</label>
                    <input type="number" name="sale_price" step="0.01" min="0" value="<?php echo $sale_price ?? ''; ?>">
                    <small>Leave empty if no sale</small>
                </div>
                
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" value="<?php echo $stock_quantity ?? 0; ?>" required>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" accept="image/*">
                    <small>Max 5MB. Allowed: JPG, PNG, GIF, WebP</small>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="featured" value="1">
                    <span>Feature this product on homepage</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Add Product
                </button>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>