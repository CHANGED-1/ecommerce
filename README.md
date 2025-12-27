# 🛒 E-commerce Product Catalog System

A complete, modern e-commerce product catalog with advanced filtering, search functionality, and full admin management. Perfect for learning e-commerce development fundamentals!

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

## ✨ Features

### 🛍️ Public Store
- **Product Catalog** - Beautiful grid layout with product cards
- **Advanced Search** - Real-time product search with analytics
- **Smart Filtering** - Filter by category, brand, price range, stock status
- **Sorting Options** - Sort by price, name, date, popularity
- **Product Details** - Comprehensive product pages with tabs
- **Category Navigation** - Browse products by category
- **Sale Badges** - Automatic discount percentage calculation
- **Stock Management** - Low stock warnings and out-of-stock indicators
- **Related Products** - Show similar items on product pages
- **Responsive Design** - Works perfectly on all devices
- **Shopping Cart UI** - Visual cart interface (no payment processing)

### 🔐 Admin Panel
- **Secure Login** - Password-protected admin access
- **Dashboard** - Statistics and quick insights
- **Product Management**:
  - ✅ Add new products
  - ✅ Edit existing products
  - ✅ Delete products (with image cleanup)
  - ✅ Bulk view and manage
- **Category Management** - Organize products efficiently
- **Brand Management** - Track product brands
- **Image Upload** - Secure file handling with validation
- **Stock Tracking** - Real-time stock status updates
- **Low Stock Alerts** - Get notified about low inventory
- **Search Analytics** - Track what customers search for
- **Featured Products** - Mark products for homepage display

---

## 📸 Screenshots

### Public Store
```
🏠 Homepage          🛍️ Shop Page         📦 Product Detail
[Hero Banner]       [Filters Sidebar]    [Image Gallery]
[Categories]        [Products Grid]      [Add to Cart]
[Featured Items]    [Pagination]         [Related Products]
```

### Admin Panel
```
📊 Dashboard         ➕ Add Product       📋 Manage Products
[Statistics]        [Product Form]       [Products Table]
[Low Stock Alert]   [Image Upload]       [Quick Actions]
[Popular Searches]  [Categories]         [Search/Filter]
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Apache Server (XAMPP/WAMP/MAMP)
- Web Browser

### Installation (10 Minutes)

#### 1. Download & Extract
```bash
# Clone or download the repository
git clone https://github.com/CHANGED-1/ecommerce.git
```

#### 2. Move to Web Directory
- **XAMPP**: `C:\xampp\htdocs\ecommerce\`
- **WAMP**: `C:\wamp64\www\ecommerce\`
- **MAMP**: `/Applications/MAMP/htdocs/ecommerce/`

#### 3. Create Database
```sql
-- Open phpMyAdmin: http://localhost/phpmyadmin
-- Create database: ecommerce_catalog
-- Run the SQL script from the documentation
```

#### 4. Configure Settings
Edit `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_catalog');
define('SITE_URL', 'http://localhost/ecommerce');
```

#### 5. Set Permissions
```bash
chmod 755 uploads/
chmod 755 uploads/products/
chmod 755 uploads/categories/
chmod 755 uploads/brands/
```

#### 6. Access the Store
- **Public Store**: `http://localhost/ecommerce/`
- **Admin Panel**: `http://localhost/ecommerce/admin/login.php`
  - Username: `admin`
  - Password: `admin123`

---

## 📁 Project Structure

```
ecommerce/
│
├── 📄 config.php                 # Configuration
├── 📄 index.php                  # Homepage
├── 📄 shop.php                   # All products with filters
├── 📄 product.php                # Product detail page
├── 📄 search.php                 # Search results
├── 📄 category.php               # Category products
│
├── 📁 admin/                     # Admin Panel
│   ├── login.php                # Admin login
│   ├── dashboard.php            # Statistics dashboard
│   ├── products.php             # Manage products
│   ├── add-product.php          # Add new product
│   ├── edit-product.php         # Edit product
│   ├── delete-product.php       # Delete product
│   ├── categories.php           # Manage categories
│   ├── brands.php               # Manage brands
│   ├── logout.php               # Logout
│   └── includes/
│       ├── admin_header.php
│       └── admin_footer.php
│
├── 📁 includes/                  # Shared Components
│   ├── header.php               # Public header
│   ├── footer.php               # Public footer
│   └── functions.php            # Helper functions (30+)
│
├── 📁 css/                       # Stylesheets
│   ├── style.css                # Public styles (~800 lines)
│   └── admin.css                # Admin styles (~500 lines)
│
├── 📁 js/                        # JavaScript
│   └── main.js                  # All functionality (~300 lines)
│
├── 📁 uploads/                   # File Storage
│   ├── products/                # Product images
│   ├── categories/              # Category images
│   └── brands/                  # Brand logos
│
└── 📄 README.md                  # This file
```

---

## 🎯 Key Features Explained

### 1. Product Management (CRUD)

**Create - Add Products**
```php
- Product name, description, SKU
- Category and brand selection
- Regular price + optional sale price
- Stock quantity tracking
- Image upload with validation
- Featured flag for homepage
- Status (active/inactive/draft)
```

**Read - View Products**
```php
- Grid and list views
- Filter by category
- Filter by brand
- Price range filter
- Stock availability filter
- Sort by multiple criteria
- Pagination support
```

**Update - Edit Products**
```php
- Modify all product details
- Replace or remove images
- Update stock levels
- Change prices and sale status
- Toggle featured status
```

**Delete - Remove Products**
```php
- Soft or hard delete options
- Automatic image cleanup
- Confirmation required
- Admin audit trail
```

### 2. Advanced Filtering System

```javascript
✓ Category Filter (Radio buttons)
✓ Brand Filter (Radio buttons)
✓ Price Range (Min/Max inputs)
✓ Stock Status (In Stock / All)
✓ Multiple filters combine
✓ Real-time results
✓ Filter persistence in URL
```

### 3. Search Functionality

```php
- Search by product name
- Search by description
- Search by SKU
- Search analytics tracking
- Results count logging
- Popular searches display
```

### 4. Smart Stock Management

```php
- Real-time stock tracking
- Automatic status updates:
  * In Stock (quantity > threshold)
  * Low Stock (quantity ≤ threshold)
  * Out of Stock (quantity = 0)
- Low stock alerts in admin
- Stock warnings on product pages
```

---

## 💡 Usage Guide

### For Store Visitors

#### Browsing Products
1. Visit homepage to see featured products
2. Click "Shop" to view all products
3. Use category menu to filter by type
4. Use sidebar filters to narrow results
5. Click any product for details

#### Searching
1. Use search bar in navigation
2. Enter product name, description, or SKU
3. View results with matching products
4. Searches are logged for analytics

#### Viewing Products
1. Click product card to view details
2. See main image and description
3. Check stock availability
4. View price and sale discounts
5. See related products below

### For Administrators

#### Login to Admin
1. Navigate to `/admin/login.php`
2. Enter credentials (admin/admin123)
3. Access dashboard

#### Adding Products
1. Click "Add Product" in sidebar
2. Fill required fields:
   - Product name *
   - Category *
   - Price *
3. Add optional details:
   - Short description
   - Full description
   - Brand
   - Sale price
   - Stock quantity
   - Image
4. Toggle featured checkbox for homepage
5. Click "Add Product"

#### Managing Products
1. Go to "Products" in sidebar
2. View all products in table
3. Use search to find specific items
4. Actions available:
   - 👁️ View on store
   - ✏️ Edit product
   - 🗑️ Delete product

#### Editing Products
1. Click edit icon on any product
2. Modify any field
3. Upload new image or delete existing
4. Update stock quantities
5. Click "Update Product"

#### Dashboard Insights
- Total products count
- Low stock alerts
- Out of stock items
- Popular search terms
- Recent products added

---

## 🔧 Configuration

### Site Settings (`config.php`)

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce_catalog');

// Site Info
define('SITE_NAME', 'ShopHub');
define('SITE_URL', 'http://localhost/ecommerce');
define('ADMIN_EMAIL', 'admin@shophub.com');

// File Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('PRODUCTS_PER_PAGE', 12);

// Stock Settings
define('LOW_STOCK_THRESHOLD', 10);
```

### Customization Options

**Change Site Name**
```php
// config.php
define('SITE_NAME', 'Your Store Name');
```

**Adjust Low Stock Threshold**
```php
// config.php
define('LOW_STOCK_THRESHOLD', 5); // Alert when stock ≤ 5
```

**Change Items Per Page**
```php
// config.php
define('PRODUCTS_PER_PAGE', 20); // Show 20 products
```

**Modify Color Scheme**
```css
/* css/style.css */
:root {
    --primary: #your-color;
    --secondary: #your-color;
}
```

---

## 📊 Database Schema

### Tables Overview

**admin_users** - Administrator accounts
```sql
id | username | email | password | created_at
```

**categories** - Product categories
```sql
id | name | slug | description | image | display_order | created_at
```

**brands** - Product brands
```sql
id | name | slug | logo | created_at
```

**products** - Main products table
```sql
id | name | slug | description | short_description
category_id | brand_id | price | sale_price | sku
stock_quantity | stock_status | image | featured
status | views | created_at | updated_at
```

**product_images** - Multiple images per product
```sql
id | product_id | image_path | is_primary | display_order | created_at
```

**product_attributes** - Custom product attributes
```sql
id | product_id | attribute_name | attribute_value
```

**search_logs** - Search analytics
```sql
id | search_term | results_count | created_at
```

---

## 🎓 What You'll Learn

Building this project teaches:

✅ **PHP Development**
- Variables and data types
- Functions and includes
- MySQL database operations
- Prepared statements
- File uploads
- Session management
- Form validation
- Security best practices

✅ **MySQL Database**
- Database design
- Table relationships (Foreign keys)
- CRUD operations
- Complex queries with JOINs
- Indexes for performance
- Data normalization

✅ **Frontend Development**
- HTML5 semantic markup
- CSS3 (Grid, Flexbox, Variables)
- Responsive design
- JavaScript DOM manipulation
- Event handling
- AJAX concepts

✅ **E-commerce Concepts**
- Product catalog management
- Category organization
- Search and filtering
- Stock management
- Price handling
- Image management
- Admin dashboards

✅ **Security**
- SQL injection prevention
- XSS protection
- Password hashing
- Input validation
- File upload security
- Session security

---

## 🐛 Troubleshooting

### Common Issues

#### ❌ Database Connection Failed
```
Error: Connection failed: Access denied

Solution:
1. Check MySQL is running in XAMPP/WAMP
2. Verify credentials in config.php
3. Ensure database 'ecommerce_catalog' exists
4. Try 127.0.0.1 instead of localhost
```

#### ❌ Images Not Uploading
```
Error: Failed to move uploaded file

Solution:
1. Check uploads/ folder exists
2. Set permissions: chmod 755 uploads/
3. Verify upload_max_filesize in php.ini:
   upload_max_filesize = 10M
   post_max_size = 10M
4. Restart Apache after php.ini changes
```

#### ❌ Can't Login to Admin
```
Error: Invalid username or password

Solution:
1. Check admin user exists in database:
   SELECT * FROM admin_users;
2. Reset password:
   UPDATE admin_users 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
   WHERE username = 'admin';
   (Password: admin123)
3. Clear browser cookies
```

#### ❌ Styles Not Loading
```
Error: CSS not applied

Solution:
1. Clear browser cache (Ctrl+F5)
2. Check file paths in header.php
3. Verify CSS files exist in css/ folder
4. Check browser console for 404 errors
```

#### ❌ Search Not Working
```
Error: No results or errors

Solution:
1. Check search_logs table exists
2. Verify products have status='active'
3. Check for MySQL errors in error log
4. Test with simple search like 'test'
```

#### ❌ Filters Not Working
```
Error: Filters don't filter products

Solution:
1. Check $_GET parameters in URL
2. Verify category/brand IDs are correct
3. Test each filter individually
4. Check browser console for JavaScript errors
```

---

## 🚀 Future Enhancements

### Phase 1 (Easy)
- [ ] Wishlist functionality
- [ ] Product reviews and ratings
- [ ] Recently viewed products
- [ ] Product comparison
- [ ] Print product details
- [ ] Share on social media
- [ ] Newsletter subscription

### Phase 2 (Intermediate)
- [ ] Multiple product images
- [ ] Product variants (size, color)
- [ ] Inventory management
- [ ] Order management system
- [ ] Customer accounts
- [ ] Email notifications
- [ ] Export products to CSV/Excel
- [ ] Import products from CSV
- [ ] Advanced analytics
- [ ] Sales reports

### Phase 3 (Advanced)
- [ ] **Payment Integration**
  - Stripe
  - PayPal
  - Credit card processing
- [ ] Shopping cart with checkout
- [ ] Order tracking
- [ ] Shipping calculation
- [ ] Tax calculation
- [ ] Coupon/discount system
- [ ] Multi-vendor support
- [ ] RESTful API
- [ ] Mobile app
- [ ] Real-time inventory
- [ ] AI-powered recommendations

---

## 🔐 Security Best Practices

### Implemented
✅ Password hashing with bcrypt
✅ Prepared statements (SQL injection prevention)
✅ Input sanitization (XSS prevention)
✅ File upload validation
✅ Session security
✅ HTTPS ready
✅ Admin authentication

### Recommended for Production
- [ ] Add CSRF tokens
- [ ] Implement rate limiting
- [ ] Add input validation library
- [ ] Use environment variables for config
- [ ] Enable error logging
- [ ] Add SSL certificate
- [ ] Implement backup system
- [ ] Add activity logging
- [ ] Use password reset via email
- [ ] Add two-factor authentication

---

## 📚 Additional Resources

### Documentation
- [PHP Manual](https://www.php.net/manual/) - Official PHP docs
- [MySQL Reference](https://dev.mysql.com/doc/) - MySQL documentation
- [MDN Web Docs](https://developer.mozilla.org/) - HTML/CSS/JS reference

### Tutorials
- [PHP MySQL CRUD](https://www.tutorialrepublic.com/php-tutorial/php-mysql-crud-application.php)
- [E-commerce Basics](https://www.cloudways.com/blog/setup-ecommerce-website/)
- [Security Guide](https://www.php.net/manual/en/security.php)

### Tools
- [phpMyAdmin](https://www.phpmyadmin.net/) - Database management
- [Composer](https://getcomposer.org/) - PHP dependency manager
- [Git](https://git-scm.com/) - Version control

---

## 🤝 Contributing

Contributions welcome! Here's how:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Contribution Ideas
- Bug fixes
- New features
- Documentation improvements
- Performance optimizations
- Security enhancements
- UI/UX improvements
- Test coverage
- Translations

---

## 📄 License

MIT License - Free to use for personal and commercial projects

```
Copyright (c) 2025 Guloba Moses

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software.
```

---

## 👨‍💻 Author

**Guloba Moses**
- Website: [yourwebsite.com](https://yourwebsite.com)
- GitHub: [@CHANGED-1](https://github.com/CHANGED-1)
<!-- - LinkedIn: [Guloba Moses](https://linkedin.com/in/yourprofile) -->
- Email: consult@guloba.com

---

## 🙏 Acknowledgments

- Icons by [Font Awesome](https://fontawesome.com/)
- Design inspiration from modern e-commerce sites
- PHP & MySQL community for excellent documentation
- XAMPP team for the development environment

---

## 📞 Support

Need help?

1. **Documentation** - Read this complete guide
2. **Issues** - Check [existing issues](https://github.com/CHANGED-1/ecommerce/issues)
3. **Discussions** - Join [community discussions](https://github.com/CHANGED-1/ecommerce/discussions)
4. **Email** - Contact: consult@guloba.com

---

## 📈 Project Statistics

- **Lines of Code**: 3,500+
- **Files**: 20+
- **Database Tables**: 7
- **Features**: 40+
- **Functions**: 30+
- **Difficulty**: Beginner to Intermediate
- **Learning Time**: 1-2 weeks
- **Development Time**: Teaching project

---

## 🎉 Show Your Support

If you found this helpful:
- ⭐ **Star** this repository
- 🐛 **Report** bugs
- 💡 **Suggest** features
- 📢 **Share** with others
- 🤝 **Contribute** code
- ☕ **Buy me a coffee**

---

## 🌟 Features at a Glance

| Feature | Public Store | Admin Panel |
|---------|-------------|-------------|
| Product Display | ✅ | ✅ |
| Search | ✅ | ✅ |
| Category Filter | ✅ | ✅ |
| Brand Filter | ✅ | ✅ |
| Price Filter | ✅ | ❌ |
| Add Products | ❌ | ✅ |
| Edit Products | ❌ | ✅ |
| Delete Products | ❌ | ✅ |
| Image Upload | ❌ | ✅ |
| Stock Management | View Only | ✅ |
| Analytics | ❌ | ✅ |
| Featured Products | ✅ | ✅ |
| Sale Prices | ✅ | ✅ |

---

**Built with ❤️ for learning e-commerce development**

⭐ Don't forget to star this repository if it helped you!

---

*Last Updated: December 2025*
*Version: 1.0.0*