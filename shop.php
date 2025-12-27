<?php
require_once 'config.php';
require_once 'includes/functions.php';

$page_title = 'Shop';

// Get filter parameters
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$brand_filter = isset($_GET['brand']) ? intval($_GET['brand']) : 0;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$sort = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'newest';
$stock_filter = isset($_GET['stock']) ? clean_input($_GET['stock']) : '';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * PRODUCTS_PER_PAGE;

// Build filters
$filters = [
    'category' => $category_filter,
    'brand' => $brand_filter,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'sort' => $sort,
    'stock' => $stock_filter
];

// Get products
$query_data = build_product_query($filters);
$sql = $query_data['sql'] . " LIMIT " . PRODUCTS_PER_PAGE . " OFFSET " . $offset;

if (!empty($query_data['params'])) {
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($query_data['types'])) {
        mysqli_stmt_bind_param($stmt, $query_data['types'], ...$query_data['params']);
    }
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);
} else {
    $products = mysqli_query($conn, $sql);
}

// Get total count for pagination
$count_sql = str_replace("SELECT p.*, c.name as category_name, b.name as brand_name", "SELECT COUNT(*) as total", $query_data['sql']);
if (!empty($query_data['params'])) {
    $count_stmt = mysqli_prepare($conn, $count_sql);
    if (!empty($query_data['types'])) {
        mysqli_stmt_bind_param($count_stmt, $query_data['types'], ...$query_data['params']);
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_products = mysqli_fetch_assoc($count_result)['total'];
} else {
    $count_result = mysqli_query($conn, $count_sql);
    $total_products = mysqli_fetch_assoc($count_result)['total'];
}

$total_pages = ceil($total_products / PRODUCTS_PER_PAGE);

// Get price range for filter
$price_range = get_price_range($conn);

include 'includes/header.php';
?>

<div class="shop-page">
    <div class="container">
        <div class="shop-layout">
            <!-- Sidebar Filters -->
            <aside class="shop-sidebar">
                <div class="filter-section">
                    <h3>Filters</h3>
                    
                    <form method="GET" action="shop.php" id="filterForm">
                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h4>Category</h4>
                            <div class="filter-options">
                                <?php
                                $categories = get_categories($conn);
                                foreach ($categories as $cat):
                                    $count = get_category_product_count($conn, $cat['id']);
                                ?>
                                    <label class="filter-option">
                                        <input type="radio" name="category" value="<?php echo $cat['id']; ?>" 
                                               <?php echo $category_filter == $cat['id'] ? 'checked' : ''; ?>>
                                        <span><?php echo $cat['name']; ?> (<?php echo $count; ?>)</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Brand Filter -->
                        <div class="filter-group">
                            <h4>Brand</h4>
                            <div class="filter-options">
                                <?php
                                $brands = get_brands($conn);
                                foreach ($brands as $brand):
                                ?>
                                    <label class="filter-option">
                                        <input type="radio" name="brand" value="<?php echo $brand['id']; ?>"
                                               <?php echo $brand_filter == $brand['id'] ? 'checked' : ''; ?>>
                                        <span><?php echo $brand['name']; ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Price Filter -->
                        <div class="filter-group">
                            <h4>Price Range</h4>
                            <div class="price-filter">
                                <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price; ?>" min="0" step="0.01">
                                <span>to</span>
                                <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price; ?>" min="0" step="0.01">
                            </div>
                            <p class="price-range-info">
                                Range: <?php echo format_currency($price_range['min_price']); ?> - 
                                <?php echo format_currency($price_range['max_price']); ?>
                            </p>
                        </div>
                        
                        <!-- Stock Filter -->
                        <div class="filter-group">
                            <h4>Availability</h4>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="radio" name="stock" value="in_stock"
                                           <?php echo $stock_filter == 'in_stock' ? 'checked' : ''; ?>>
                                    <span>In Stock Only</span>
                                </label>
                                <label class="filter-option">
                                    <input type="radio" name="stock" value=""
                                           <?php echo $stock_filter == '' ? 'checked' : ''; ?>>
                                    <span>All Products</span>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                        <a href="shop.php" class="btn btn-secondary btn-block">Clear All</a>
                    </form>
                </div>
            </aside>
            
            <!-- Products Area -->
            <div class="shop-content">
                <!-- Toolbar -->
                <div class="shop-toolbar">
                    <div class="toolbar-info">
                        <p>Showing <?php echo mysqli_num_rows($products); ?> of <?php echo $total_products; ?> products</p>
                    </div>
                    
                    <div class="toolbar-sort">
                        <form method="GET" action="shop.php" id="sortForm">
                            <?php if($category_filter): ?><input type="hidden" name="category" value="<?php echo $category_filter; ?>"><?php endif; ?>
                            <?php if($brand_filter): ?><input type="hidden" name="brand" value="<?php echo $brand_filter; ?>"><?php endif; ?>
                            <?php if($min_price): ?><input type="hidden" name="min_price" value="<?php echo $min_price; ?>"><?php endif; ?>
                            <?php if($max_price): ?><input type="hidden" name="max_price" value="<?php echo $max_price; ?>"><?php endif; ?>
                            
                            <label>Sort by:</label>
                            <select name="sort" onchange="this.form.submit()">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                                <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                            </select>
                        </form>
                    </div>
                </div>
                
                <!-- Products Grid -->
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
                                    
                                    <?php if($product['stock_quantity'] > 0 && $product['stock_quantity'] <= LOW_STOCK_THRESHOLD): ?>
                                        <p class="stock-warning">Only <?php echo $product['stock_quantity']; ?> left!</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if($page > 1): ?>
                                <a href="?page=<?php echo $page-1; ?>&<?php echo http_build_query(array_filter($filters)); ?>" class="page-link">&laquo; Previous</a>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($filters)); ?>" 
                                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if($page < $total_pages): ?>
                                <a href="?page=<?php echo $page+1; ?>&<?php echo http_build_query(array_filter($filters)); ?>" class="page-link">Next &raquo;</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h3>No Products Found</h3>
                        <p>Try adjusting your filters or <a href="shop.php">browse all products</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>