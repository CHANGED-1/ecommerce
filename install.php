<?php
// install.php - Automated Installation Script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install E-commerce Catalog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .install-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #3b82f6;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
        }
        .success {
            color: #10b981;
        }
        .error {
            color: #ef4444;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="install-box">
        <h1>🛒 E-commerce Catalog Installation</h1>
        
        <div class="step">
            <h3>Step 1: Database Configuration</h3>
            <p>Edit <code>config.php</code> with your database credentials:</p>
            <pre>
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_catalog');
            </pre>
        </div>
        
        <div class="step">
            <h3>Step 2: Create Database</h3>
            <p>1. Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></p>
            <p>2. Create database: <code>ecommerce_catalog</code></p>
            <p>3. Run the SQL script provided in documentation</p>
        </div>
        
        <div class="step">
            <h3>Step 3: Set Permissions</h3>
            <p>Set folder permissions:</p>
            <pre>
chmod 755 uploads/
chmod 755 uploads/products/
chmod 755 uploads/categories/
chmod 755 uploads/brands/
            </pre>
        </div>
        
        <div class="step">
            <h3>Step 4: Test Installation</h3>
            <p><a href="index.php" class="btn">Visit Store</a></p>
            <p><a href="admin/login.php" class="btn">Admin Panel</a></p>
            <p>Default login: <code>admin</code> / <code>admin123</code></p>
        </div>
        
        <div class="step">
            <h3>Step 5: Security</h3>
            <p class="error">⚠️ Delete this install.php file after installation!</p>
            <p>✅ Change default admin password</p>
            <p>✅ Update site URL in config.php</p>
        </div>
    </div>
</body>
</html>