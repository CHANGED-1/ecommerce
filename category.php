<?php
require_once 'config.php';
require_once 'includes/functions.php';

$category_slug = isset($_GET['slug']) ? clean_input($_GET['slug']) : '';

if (!$category_slug) {
    redirect('shop.php');
}

// Get category
$sql = "SELECT * FROM categories WHERE slug = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $category_slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    redirect('shop.php');
}

$category = mysqli_fetch_assoc($result);
$page_title = $category['name'];

// Get category products
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.category_id = ? AND p.status = 'active'
        ORDER BY p.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $category['id']);
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);

include 'includes/header.php';
?>

<div class="category-page">
    <div class="container">
        <div class="category-header">
            <h1><?php echo $category['name']; ?></h1>
            <?php if($category['description']): ?>
                <p><?php echo $category['description']; ?></p>
            <?php endif; ?>
            <p class="product-count"><?php echo mysqli_num_rows($products); ?> products</p>
        </div>
        
        <?php if(mysqli_num_rows($products) > 0): ?>
            <div class="products-grid">
                <?php while($product = mysqli_fetch_assoc($products)): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if($product['image']): ?>
                                <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                            <?php else: ?>
                                <div class="product-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($product['sale_price']): ?>
                                <span class="product-badge badge-sale">
                                    -<?php echo calculate_discount($product['price'], $product['sale_price']); ?>%
                                </span>
                            <?php endif; ?>
                            
                            <div class="product-overlay">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>" class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <p class="product-category"><?php echo $product['category_name']; ?></p>
                            <h3 class="product-title">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                    <?php echo $product['name']; ?>
                                </a>
                            </h3>
                            
                            <div class="product-price">
                                <?php if($product['sale_price']): ?>
                                    <span class="price-old"><?php echo format_currency($product['price']); ?></span>
                                    <span class="price-current"><?php echo format_currency($product['sale_price']); ?></span>
                                <?php else: ?>
                                    <span class="price-current"><?php echo format_currency($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                <i class="fas fa-box-open"></i>
                <h3>No Products in This Category</h3>
                <p><a href="shop.php">Browse all products</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>