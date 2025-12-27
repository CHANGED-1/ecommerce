<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Get product slug
$slug = isset($_GET['slug']) ? clean_input($_GET['slug']) : '';

if (!$slug) {
    redirect('shop.php');
}

// Get product details
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE p.slug = ? AND p.status = 'active'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    redirect('shop.php');
}

$product = mysqli_fetch_assoc($result);
$page_title = $product['name'];

// Increment views
increment_views($conn, $product['id']);

// Get related products
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
        LIMIT 4";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $product['category_id'], $product['id']);
mysqli_stmt_execute($stmt);
$related_products = mysqli_stmt_get_result($stmt);

include 'includes/header.php';
?>

<div class="product-page">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Home</a> / 
            <a href="shop.php">Shop</a> / 
            <a href="category.php?slug=<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a> / 
            <span><?php echo $product['name']; ?></span>
        </div>
        
        <div class="product-details-container">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <?php if($product['image']): ?>
                        <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" id="mainImage">
                    <?php else: ?>
                        <div class="image-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="product-details">
                <h1><?php echo $product['name']; ?></h1>
                
                <div class="product-meta">
                    <div class="product-rating">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span>(<?php echo rand(10, 200); ?> reviews)</span>
                    </div>
                    
                    <span class="product-sku">SKU: <?php echo $product['sku']; ?></span>
                </div>
                
                <div class="product-price-section">
                    <?php if($product['sale_price']): ?>
                        <span class="price-old"><?php echo format_currency($product['price']); ?></span>
                        <span class="price-current"><?php echo format_currency($product['sale_price']); ?></span>
                        <span class="price-save">Save <?php echo calculate_discount($product['price'], $product['sale_price']); ?>%</span>
                    <?php else: ?>
                        <span class="price-current"><?php echo format_currency($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="product-stock">
                    <?php echo get_stock_badge($product['stock_quantity']); ?>
                    <?php if($product['stock_quantity'] > 0 && $product['stock_quantity'] <= LOW_STOCK_THRESHOLD): ?>
                        <p class="stock-warning">Hurry! Only <?php echo $product['stock_quantity']; ?> left in stock!</p>
                    <?php endif; ?>
                </div>
                
                <?php if($product['short_description']): ?>
                    <div class="product-summary">
                        <p><?php echo $product['short_description']; ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="product-info-list">
                    <ul>
                        <li><strong>Category:</strong> <a href="category.php?slug=<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a></li>
                        <?php if($product['brand_name']): ?>
                            <li><strong>Brand:</strong> <?php echo $product['brand_name']; ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <?php if($product['stock_quantity'] > 0): ?>
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn" onclick="decreaseQty()">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                            <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
                        </div>
                        <button class="btn btn-primary btn-lg add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn btn-icon btn-wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="out-of-stock-notice">
                        <p>This product is currently out of stock</p>
                        <button class="btn btn-secondary">Notify When Available</button>
                    </div>
                <?php endif; ?>
                
                <div class="product-extra-info">
                    <div class="info-item">
                        <i class="fas fa-truck"></i>
                        <span>Free shipping on orders over $50</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-undo"></i>
                        <span>30-day return policy</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure payment</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Description Tabs -->
        <div class="product-tabs">
            <div class="tab-headers">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="specifications">Specifications</button>
                <button class="tab-btn" data-tab="reviews">Reviews (<?php echo rand(10, 200); ?>)</button>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="description">
                    <div class="description-content">
                        <?php echo nl2br($product['description']); ?>
                    </div>
                </div>
                
                <div class="tab-pane" id="specifications">
                    <table class="specifications-table">
                        <tr>
                            <th>SKU</th>
                            <td><?php echo $product['sku']; ?></td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td><?php echo $product['category_name']; ?></td>
                        </tr>
                        <?php if($product['brand_name']): ?>
                        <tr>
                            <th>Brand</th>
                            <td><?php echo $product['brand_name']; ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Stock Status</th>
                            <td><?php echo ucfirst(str_replace('_', ' ', $product['stock_status'])); ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="tab-pane" id="reviews">
                    <div class="reviews-section">
                        <h3>Customer Reviews</h3>
                        <p class="no-reviews">No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if(mysqli_num_rows($related_products) > 0): ?>
            <div class="related-products">
                <h2>You May Also Like</h2>
                <div class="products-grid">
                    <?php while($related = mysqli_fetch_assoc($related_products)): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if($related['image']): ?>
                                    <img src="uploads/products/<?php echo $related['image']; ?>" alt="<?php echo $related['name']; ?>">
                                <?php else: ?>
                                    <div class="product-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-overlay">
                                    <a href="product.php?slug=<?php echo $related['slug']; ?>" class="btn btn-primary btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <p class="product-category"><?php echo $related['category_name']; ?></p>
                                <h3 class="product-title">
                                    <a href="product.php?slug=<?php echo $related['slug']; ?>">
                                        <?php echo $related['name']; ?>
                                    </a>
                                </h3>
                                
                                <div class="product-price">
                                    <?php if($related['sale_price']): ?>
                                        <span class="price-old"><?php echo format_currency($related['price']); ?></span>
                                        <span class="price-current"><?php echo format_currency($related['sale_price']); ?></span>
                                    <?php else: ?>
                                        <span class="price-current"><?php echo format_currency($related['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Quantity functions
function increaseQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Product tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabId = this.getAttribute('data-tab');
        
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        
        this.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    });
});

// Add to cart (placeholder)
document.querySelector('.add-to-cart')?.addEventListener('click', function() {
    const qty = document.getElementById('quantity').value;
    alert(`Added ${qty} item(s) to cart! (Cart functionality coming soon)`);
});
</script>

<?php include 'includes/footer.php'; ?>