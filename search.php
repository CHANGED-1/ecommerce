<?php
require_once 'config.php';
require_once 'includes/functions.php';

$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$page_title = 'Search Results';

if (!$search_query) {
    redirect('shop.php');
}

// Search products
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
        ORDER BY p.created_at DESC";

$search_term = '%' . $search_query . '%';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);
$results_count = mysqli_num_rows($results);

// Log search
log_search($conn, $search_query, $results_count);

include 'includes/header.php';
?>

<div class="search-results-page">
    <div class="container">
        <div class="search-header">
            <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
            <p><?php echo $results_count; ?> product(s) found</p>
        </div>
        
        <?php if($results_count > 0): ?>
            <div class="products-grid">
                <?php while($product = mysqli_fetch_assoc($results)): ?>
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
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h2>No Products Found</h2>
                <p>We couldn't find any products matching "<?php echo htmlspecialchars($search_query); ?>"</p>
                <p>Try:</p>
                <ul>
                    <li>Checking your spelling</li>
                    <li>Using more general terms</li>
                    <li>Browsing our <a href="shop.php">product catalog</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>