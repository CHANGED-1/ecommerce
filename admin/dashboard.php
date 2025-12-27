<?php
require_once '../config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$page_title = 'Dashboard';

// Get statistics
$total_products = get_product_count($conn, "status = 'active'");
$out_of_stock = get_product_count($conn, "stock_quantity = 0");
$low_stock = get_product_count($conn, "stock_quantity > 0 AND stock_quantity <= " . LOW_STOCK_THRESHOLD);

// Get category count
$cat_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM categories");
$total_categories = mysqli_fetch_assoc($cat_result)['count'];

// Get recent products
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC LIMIT 5";
$recent_products = mysqli_query($conn, $sql);

// Get low stock products
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.stock_quantity <= " . LOW_STOCK_THRESHOLD . " AND p.stock_quantity > 0
        ORDER BY p.stock_quantity ASC LIMIT 5";
$low_stock_products = mysqli_query($conn, $sql);

// Get popular searches
$sql = "SELECT search_term, SUM(results_count) as total_results, COUNT(*) as search_count 
        FROM search_logs 
        GROUP BY search_term 
        ORDER BY search_count DESC 
        LIMIT 5";
$popular_searches = mysqli_query($conn, $sql);

include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo $_SESSION['admin_username']; ?>!</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_products; ?></h3>
                <p>Total Products</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-green">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_categories; ?></h3>
                <p>Categories</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $low_stock; ?></h3>
                <p>Low Stock Items</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-red">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $out_of_stock; ?></h3>
                <p>Out of Stock</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Recent Products -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Recent Products</h2>
                <a href="products.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = mysqli_fetch_assoc($recent_products)): ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <?php if($product['image']): ?>
                                        <img src="../uploads/products/<?php echo $product['image']; ?>" alt="">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo $product['name']; ?></strong>
                                        <small><?php echo $product['category_name']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo format_currency($product['price']); ?></td>
                            <td><?php echo get_stock_badge($product['stock_quantity']); ?></td>
                            <td>
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-icon">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Low Stock Alert -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Low Stock Alert</h2>
            </div>
            <?php if(mysqli_num_rows($low_stock_products) > 0): ?>
                <div class="alert-list">
                    <?php while($product = mysqli_fetch_assoc($low_stock_products)): ?>
                        <div class="alert-item">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="alert-content">
                                <strong><?php echo $product['name']; ?></strong>
                                <p>Only <?php echo $product['stock_quantity']; ?> left in stock</p>
                            </div>
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-secondary">
                                Update
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No low stock items</p>
            <?php endif; ?>
        </div>
        
        <!-- Popular Searches -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Popular Searches</h2>
            </div>
            <?php if(mysqli_num_rows($popular_searches) > 0): ?>
                <div class="search-list">
                    <?php while($search = mysqli_fetch_assoc($popular_searches)): ?>
                        <div class="search-item">
                            <span class="search-term"><?php echo $search['search_term']; ?></span>
                            <span class="search-count"><?php echo $search['search_count']; ?> searches</span>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No search data yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>