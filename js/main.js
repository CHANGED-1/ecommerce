// =============================================
// MAIN JAVASCRIPT FOR E-COMMERCE CATALOG
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // =============================================
    // MOBILE NAVIGATION TOGGLE
    // =============================================
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
    }
    
    // =============================================
    // SEARCH BOX AUTO-FOCUS
    // =============================================
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        document.addEventListener('keydown', function(e) {
            // Focus search on '/' key
            if (e.key === '/' && e.target.tagName !== 'INPUT') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }
    
    // =============================================
    // PRODUCT QUANTITY CONTROLS
    // =============================================
    window.increaseQty = function() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.getAttribute('max'));
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    };
    
    window.decreaseQty = function() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    };
    
    // =============================================
    // ADD TO CART (UI ONLY)
    // =============================================
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const quantity = document.getElementById('quantity') ? document.getElementById('quantity').value : 1;
            
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                const currentCount = parseInt(cartCount.textContent);
                cartCount.textContent = currentCount + parseInt(quantity);
            }
            
            // Show notification
            showNotification('Product added to cart!', 'success');
        });
    });
    
    // =============================================
    // WISHLIST TOGGLE
    // =============================================
    const wishlistButtons = document.querySelectorAll('.btn-wishlist');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                showNotification('Added to wishlist!', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                showNotification('Removed from wishlist', 'info');
            }
        });
    });
    
    // =============================================
    // PRODUCT TABS
    // =============================================
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // =============================================
    // FILTER FORM AUTO-SUBMIT
    // =============================================
    const filterInputs = document.querySelectorAll('#filterForm input[type="radio"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit filter form on change (optional)
            // document.getElementById('filterForm').submit();
        });
    });
    
    // =============================================
    // IMAGE PREVIEW FOR FILE INPUTS
    // =============================================
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Create preview (you can customize this)
                    console.log('Image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // =============================================
    // SMOOTH SCROLL
    // =============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // =============================================
    // NOTIFICATION SYSTEM
    // =============================================
    window.showNotification = function(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        // Add to body
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    };
    
    // Add notification styles if not in CSS
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: -400px;
                background: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 1rem;
                z-index: 10000;
                transition: right 0.3s ease;
            }
            .notification.show {
                right: 20px;
            }
            .notification-success { border-left: 4px solid #10b981; }
            .notification-error { border-left: 4px solid #ef4444; }
            .notification-info { border-left: 4px solid #3b82f6; }
            .notification i {
                font-size: 1.5rem;
            }
            .notification-success i { color: #10b981; }
            .notification-error i { color: #ef4444; }
            .notification-info i { color: #3b82f6; }
        `;
        document.head.appendChild(style);
    }
    
    // =============================================
    // ADMIN PRODUCT SEARCH
    // =============================================
    const searchProducts = document.getElementById('searchProducts');
    if (searchProducts) {
        searchProducts.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // =============================================
    // CONFIRM BEFORE DELETE
    // =============================================
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.hasAttribute('data-confirmed')) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this item?')) {
                    this.setAttribute('data-confirmed', 'true');
                    this.click();
                }
            }
        });
    });
    
    // =============================================
    // FORM VALIDATION
    // =============================================
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger)';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });
    });
    
    // =============================================
    // PRICE VALIDATION
    // =============================================
    const priceInputs = document.querySelectorAll('input[name="price"], input[name="sale_price"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
                showNotification('Price cannot be negative', 'error');
            }
        });
    });
    
    // =============================================
    // STOCK VALIDATION
    // =============================================
    const stockInput = document.querySelector('input[name="stock_quantity"]');
    if (stockInput) {
        stockInput.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (value < 0) {
                this.value = 0;
                showNotification('Stock cannot be negative', 'error');
            }
        });
    }
});

// =============================================
// UTILITY FUNCTIONS
// =============================================

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Slugify text
function slugify(text) {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    });
}