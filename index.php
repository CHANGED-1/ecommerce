<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Home';

// Get featured products
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.featured = 1 AND p.status = 'active' 
        ORDER BY p.created_at DESC 
        LIMIT 8";
$featured_products = mysqli_query($conn, $sql);

// Get new arrivals
$sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC 
        LIMIT 8";
$new_products = mysqli_query($conn, $sql);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-slider">
    <div class="hero-slide active">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to <?php echo SITE_NAME; ?></h1>
                <p>Discover amazing products at unbeatable prices</p>
                <a href="shop.php" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
            <?php
            $featured_categories = array_slice(get_categories($conn), 0, 6);
            foreach ($featured_categories as $category):
                $product_count = get_category_product_count($conn, $category['id']);
            ?>
                <a href="category.php?slug=<?php echo $category['slug']; ?>" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3><?php echo $category['name']; ?></h3>
                    <p><?php echo $product_count; ?> Products</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Products</h2>
            <a href="shop.php" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="products-grid">
            <?php while($product = mysqli_fetch_assoc($featured_products)): ?>
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
                        
                        <?php if($product['stock_quantity'] == 0): ?>
                            <span class="product-badge badge-stock">Out of Stock</span>
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
                        
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>(<?php echo rand(10, 100); ?>)</span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">New Arrivals</h2>
            <a href="shop.php?sort=newest" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="products-grid">
            <?php while($product = mysqli_fetch_assoc($new_products)): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if($product['image']): ?>
                            <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <?php else: ?>
                            <div class="product-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <span class="product-badge badge-new">New</span>
                        
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
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-shipping-fast"></i>
                <h3>Free Shipping</h3>
                <p>On orders over $50</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-undo"></i>
                <h3>Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-lock"></i>
                <h3>Secure Payment</h3>
                <p>100% secure transactions</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Dedicated support team</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>