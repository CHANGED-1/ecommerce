<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$page_title = 'Manage Brands';
$success = '';
$errors = [];

// Handle Add Brand
if (isset($_POST['add_brand'])) {
    $name = clean_input($_POST['name']);
    $slug = create_slug($name);
    
    if (empty($name)) {
        $errors[] = 'Brand name is required';
    } else {
        // Check if slug exists
        $sql = "SELECT id FROM brands WHERE slug = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $slug);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
            $slug = $slug . '-' . time();
        }
        
        $sql = "INSERT INTO brands (name, slug) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $name, $slug);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Brand added successfully!';
        } else {
            $errors[] = 'Failed to add brand';
        }
    }
}

// Handle Delete Brand
if (isset($_GET['delete'])) {
    $brand_id = intval($_GET['delete']);
    
    // Update products to remove brand
    $sql = "UPDATE products SET brand_id = NULL WHERE brand_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $brand_id);
    mysqli_stmt_execute($stmt);
    
    // Delete brand
    $sql = "DELETE FROM brands WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $brand_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = 'Brand deleted successfully!';
    }
}

// Get all brands with product count
$sql = "SELECT b.*, COUNT(p.id) as product_count 
        FROM brands b 
        LEFT JOIN products p ON b.id = p.brand_id 
        GROUP BY b.id 
        ORDER BY b.name ASC";
$brands = mysqli_query($conn, $sql);

include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Manage Brands</h1>
    </div>
    
    <?php if($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php foreach($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-grid" style="grid-template-columns: 1fr 2fr;">
        <!-- Add Brand Form -->
        <div class="form-card">
            <h2>Add New Brand</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Brand Name *</label>
                    <input type="text" name="name" required placeholder="e.g., Nike, Apple, Samsung">
                </div>
                
                <button type="submit" name="add_brand" class="btn btn-primary btn-block">
                    <i class="fas fa-plus"></i> Add Brand
                </button>
            </form>
        </div>
        
        <!-- Brands List -->
        <div class="table-card">
            <div class="card-header">
                <h2>All Brands</h2>
                <span class="badge badge-info"><?php echo mysqli_num_rows($brands); ?> brands</span>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($brand = mysqli_fetch_assoc($brands)): ?>
                        <tr>
                            <td><strong><?php echo $brand['name']; ?></strong></td>
                            <td><code><?php echo $brand['slug']; ?></code></td>
                            <td><?php echo $brand['product_count']; ?> products</td>
                            <td><?php echo format_date($brand['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../shop.php?brand=<?php echo $brand['id']; ?>" target="_blank" class="btn-icon" title="View Products">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?delete=<?php echo $brand['id']; ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this brand? Products will not be deleted.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>