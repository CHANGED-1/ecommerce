<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$page_title = 'Manage Products';

// Get all products
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        ORDER BY p.created_at DESC";
$products = mysqli_query($conn, $sql);

include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>All Products</h1>
        <a href="add-product.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
    
    <div class="table-card">
        <div class="table-header">
            <div class="search-box">
                <input type="text" id="searchProducts" placeholder="Search products...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($product = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <?php if($product['image']): ?>
                                    <img src="../uploads/products/<?php echo $product['image']; ?>" alt="">
                                <?php else: ?>
                                    <div class="img-placeholder"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo $product['name']; ?></strong>
                                    <small>SKU: <?php echo $product['sku']; ?></small>
                                    <?php if($product['featured']): ?>
                                        <span class="badge badge-warning">Featured</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $product['category_name']; ?></td>
                        <td>
                            <div class="price-cell">
                                <?php if($product['sale_price']): ?>
                                    <span class="price-old"><?php echo format_currency($product['price']); ?></span>
                                    <span class="price-sale"><?php echo format_currency($product['sale_price']); ?></span>
                                <?php else: ?>
                                    <?php echo format_currency($product['price']); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo get_stock_badge($product['stock_quantity']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $product['status']; ?>">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $product['views']; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="../product.php?slug=<?php echo $product['slug']; ?>" target="_blank" class="btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete-product.php?id=<?php echo $product['id']; ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this product?')">
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

<?php include 'includes/admin_footer.php'; ?>