<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$page_title = 'Manage Categories';
$success = '';
$errors = [];

// Handle Add Category
if (isset($_POST['add_category'])) {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $slug = create_slug($name);
    
    if (empty($name)) {
        $errors[] = 'Category name is required';
    } else {
        // Check if slug exists
        $sql = "SELECT id FROM categories WHERE slug = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $slug);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
            $slug = $slug . '-' . time();
        }
        
        $sql = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $slug, $description);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Category added successfully!';
        } else {
            $errors[] = 'Failed to add category';
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $cat_id = intval($_GET['delete']);
    
    // Update products to remove category
    $sql = "UPDATE products SET category_id = NULL WHERE category_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $cat_id);
    mysqli_stmt_execute($stmt);
    
    // Delete category
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $cat_id);
    if (mysqli_stmt_execute($stmt)) {
        $success = 'Category deleted successfully!';
    }
}

// Get all categories
$categories = get_categories($conn);

include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Manage Categories</h1>
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
        <!-- Add Category Form -->
        <div class="form-card">
            <h2>Add New Category</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                
                <button type="submit" name="add_category" class="btn btn-primary btn-block">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </form>
        </div>
        
        <!-- Categories List -->
        <div class="table-card">
            <div class="card-header">
                <h2>All Categories</h2>
                <span class="badge badge-info"><?php echo count($categories); ?> categories</span>
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
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td>
                                <strong><?php echo $cat['name']; ?></strong>
                                <?php if($cat['description']): ?>
                                    <br><small><?php echo truncate_text($cat['description'], 50); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code><?php echo $cat['slug']; ?></code></td>
                            <td><?php echo get_category_product_count($conn, $cat['id']); ?> products</td>
                            <td><?php echo format_date($cat['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../category.php?slug=<?php echo $cat['slug']; ?>" target="_blank" class="btn-icon" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?delete=<?php echo $cat['id']; ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this category? Products will not be deleted.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>