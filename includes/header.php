<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-left">
                    <span><i class="fas fa-phone"></i> +256 772 567 890</span>
                    <span><i class="fas fa-envelope"></i> <?php echo ADMIN_EMAIL; ?></span>
                </div>
                <div class="top-bar-right">
                    <a href="admin/login.php"><i class="fas fa-user"></i> Admin</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo SITE_URL; ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <?php echo SITE_NAME; ?>
                </a>
            </div>
            
            <!-- Search Bar -->
            <div class="nav-search">
                <form action="search.php" method="GET">
                    <div class="search-box">
                        <input type="text" name="q" placeholder="Search products..." 
                               value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Navigation Links -->
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li><a href="<?php echo SITE_URL; ?>/shop.php">Shop</a></li>
                <li>
                    <a href="#" class="cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </li>
            </ul>
            
            <div class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
    
    <!-- Categories Bar -->
    <div class="categories-bar">
        <div class="container">
            <div class="categories-menu">
                <?php
                $categories = get_categories($conn);
                foreach ($categories as $cat):
                    $product_count = get_category_product_count($conn, $cat['id']);
                ?>
                    <a href="category.php?slug=<?php echo $cat['slug']; ?>" class="category-item">
                        <?php echo $cat['name']; ?>
                        <span class="count">(<?php echo $product_count; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>