<?php
session_start();

// Initialize empty arrays
$products = array();
$categories = array();
$brands = array();
$total_count = 0;
$error_message = '';

// Get cart count for navigation
$cart_count = 0;
if (file_exists('controllers/cart_controller.php')) {
    try {
        require_once 'controllers/cart_controller.php';
        $cartController = new cart_controller();
        
        $customer_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $ip_address = null;
        
        if (!$customer_id) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_address = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip_address = $_SERVER['REMOTE_ADDR'];
            }
        }
        
        $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
    } catch (Exception $e) {
        // Silently fail, cart count will be 0
    }
}

// Get featured products (first 8 products)
$limit = 8;
$offset = 0;

// Try to load data from database
if (file_exists('settings/db_class.php') && file_exists('classes/product_class.php')) {
    try {
        require_once 'settings/db_class.php';
        require_once 'classes/product_class.php';
        
        $product_class = new product_class();
        
        if ($product_class->db_connect()) {
            // Get featured products (first 8)
            $products = $product_class->view_all_products($limit, $offset);
            $total_count = $product_class->get_products_count();
            
            // Get categories and brands
            $categories = $product_class->get_categories();
            $brands = $product_class->get_brands();
            
            // Ensure we have arrays
            if (!is_array($products)) $products = array();
            if (!is_array($categories)) $categories = array();
            if (!is_array($brands)) $brands = array();
        } else {
            $error_message = 'Failed to connect to database.';
        }
    } catch (Exception $e) {
        $error_message = 'Error loading product data: ' . $e->getMessage();
        error_log("Error loading product data: " . $e->getMessage());
    }
} else {
    $error_message = 'Required files not found.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taste of Africa - Authentic African Groceries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/homepage.css" rel="stylesheet">
</head>
<body>
    <!-- Top Header Bar -->
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <a href="#about">About Us</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="customer/dashboard.php">My Account</a>
                        <?php endif; ?>
                        <a href="#contact">Contact</a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <span class="promo-text">Super Value Deals - Save more with coupons</span>
                </div>
                <div class="col-md-4 text-end">
                    <span>Need help? Call Us: <strong>+1-800-900-122</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <div class="container">
            <div class="nav-container">
                <!-- Logo -->
                <a href="index.php" class="logo-section">
                    <i class="fas fa-seedling"></i>
                    <span>Taste of Africa</span>
                </a>

                <!-- Search Bar -->
                <div class="search-section">
                    <form method="GET" action="all_product.php">
                        <input type="text" name="search" placeholder="Search for products..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="nav-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] == 1): ?>
                            <a href="admin/dashboard.php" class="nav-action-item" title="Admin Dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Admin</span>
                            </a>
                        <?php endif; ?>
                        <a href="customer/dashboard.php" class="nav-action-item" title="My Account">
                            <i class="fas fa-user"></i>
                            <span>Account</span>
                        </a>
                    <?php else: ?>
                        <a href="login/login.php" class="nav-action-item" title="Login">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                    
                    <a href="cart.php" class="nav-action-item cart-badge" title="Shopping Cart">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Cart</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 20px;">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1>Fresh African Produce<br>Big Discount</h1>
                <p class="subtitle">Save up to 50% off on your first order</p>
                <div class="hero-cta">
                    <a href="all_product.php" class="btn btn-custom btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="#categories" class="btn btn-outline-primary btn-lg">
                        Browse Categories
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <img src="uploads/placeholder.png" alt="Fresh African Produce" 
                     onerror="this.src='uploads/placeholder.png'">
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="featured-categories" id="categories">
            <h2 class="section-title">Featured Categories</h2>
            
            <div class="category-filters">
                <button class="category-filter-btn active" data-category="all">All</button>
                <?php 
                $displayed_categories = array_slice($categories, 0, 6);
                foreach ($displayed_categories as $category): 
                ?>
                    <button class="category-filter-btn" data-category="<?php echo $category['cat_id']; ?>">
                        <?php echo htmlspecialchars($category['cat_name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="categories-grid">
                <?php 
                $category_icons = [
                    'Fruits' => 'fa-apple-alt',
                    'Vegetables' => 'fa-carrot',
                    'Spices' => 'fa-pepper-hot',
                    'Grains' => 'fa-wheat-awn',
                    'Beverages' => 'fa-wine-bottle',
                    'Meat' => 'fa-drumstick-bite',
                    'Dairy' => 'fa-cheese',
                    'Snacks' => 'fa-cookie'
                ];
                
                foreach ($categories as $category): 
                    $cat_name = $category['cat_name'];
                    $icon = 'fa-tag'; // default
                    foreach ($category_icons as $key => $icon_class) {
                        if (stripos($cat_name, $key) !== false) {
                            $icon = $icon_class;
                            break;
                        }
                    }
                    
                    // Get product count for this category
                    if ($product_class) {
                        $item_count = $product_class->get_category_count($category['cat_id']);
                    } else {
                        $item_count = 0;
                    }
                ?>
                    <a href="all_product.php?category=<?php echo $category['cat_id']; ?>" class="category-card">
                        <i class="fas <?php echo $icon; ?>"></i>
                        <h5><?php echo htmlspecialchars($cat_name); ?></h5>
                        <span class="item-count"><?php echo $item_count; ?> items</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Promotional Banners -->
        <section class="promo-banners">
            <div class="promo-banner">
                <div>
                    <h4>Everyday Fresh & Clean with Our Products</h4>
                    <a href="all_product.php" class="btn btn-custom">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <img src="uploads/placeholder.png" alt="Fresh Products" class="promo-banner-image">
            </div>
            
            <div class="promo-banner">
                <div>
                    <h4>Make your Breakfast Healthy and Easy</h4>
                    <a href="all_product.php" class="btn btn-custom">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <img src="uploads/placeholder.png" alt="Healthy Breakfast" class="promo-banner-image">
            </div>
            
            <div class="promo-banner">
                <div>
                    <h4>The best Organic Products Online</h4>
                    <a href="all_product.php" class="btn btn-custom">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <img src="uploads/placeholder.png" alt="Organic Products" class="promo-banner-image">
            </div>
        </section>

        <!-- Popular Products -->
        <section class="popular-products">
            <h2 class="section-title">Popular Products</h2>
            
            <div class="product-filters">
                <button class="product-filter-btn active" data-filter="all">All</button>
                <button class="product-filter-btn" data-filter="fruits">Fresh Fruits</button>
                <button class="product-filter-btn" data-filter="vegetables">Vegetables</button>
                <button class="product-filter-btn" data-filter="grains">Grains</button>
                <button class="product-filter-btn" data-filter="spices">Spices</button>
            </div>

            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fa fa-box fa-3x mb-3"></i>
                    <h3>No Products Available</h3>
                    <p>Products will be displayed here once added to the system.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card-modern">
                            <div class="product-image-wrapper">
                                <?php if ($product['product_image']): ?>
                                    <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                         class="product-image-modern" 
                                         alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                         onerror="this.src='uploads/placeholder.png'">
                                <?php else: ?>
                                    <img src="uploads/placeholder.png" 
                                         class="product-image-modern" 
                                         alt="No image available">
                                <?php endif; ?>
                                <span class="product-badge">New</span>
                            </div>
                            <div class="product-card-body">
                                <h5 class="product-title-modern"><?php echo htmlspecialchars($product['product_title']); ?></h5>
                                <div class="product-meta-modern">
                                    <span><?php echo htmlspecialchars($product['cat_name'] ?? 'Category'); ?></span>
                                    <?php if ($product['brand_name']): ?>
                                        <span> • By <?php echo htmlspecialchars($product['brand_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <div class="product-price-modern">
                                    <span class="current-price">$<?php echo number_format($product['product_price'], 2); ?></span>
                                </div>
                                <button class="add-to-cart-btn" data-product-id="<?php echo $product['product_id']; ?>">
                                    <i class="fas fa-shopping-cart"></i> Add
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-5">
                    <a href="all_product.php" class="btn btn-custom btn-lg">
                        View All Products <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Footer -->
    <footer style="background: var(--white); border-top: 1px solid var(--border-color); padding: 40px 0; margin-top: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 style="font-family: 'Playfair Display', serif; color: var(--forest-green); margin-bottom: 16px;">Taste of Africa</h5>
                    <p style="color: var(--text-medium);">Bringing authentic African flavors to your doorstep. Fresh, organic, and locally sourced products.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 style="font-weight: 600; margin-bottom: 16px;">Quick Links</h6>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="index.php" style="color: var(--text-medium);">Home</a></li>
                        <li style="margin-bottom: 8px;"><a href="all_product.php" style="color: var(--text-medium);">Products</a></li>
                        <li style="margin-bottom: 8px;"><a href="#about" style="color: var(--text-medium);">About</a></li>
                        <li style="margin-bottom: 8px;"><a href="#contact" style="color: var(--text-medium);">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 style="font-weight: 600; margin-bottom: 16px;">Customer Service</h6>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><a href="cart.php" style="color: var(--text-medium);">Shopping Cart</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li style="margin-bottom: 8px;"><a href="customer/dashboard.php" style="color: var(--text-medium);">My Account</a></li>
                        <?php else: ?>
                            <li style="margin-bottom: 8px;"><a href="login/login.php" style="color: var(--text-medium);">Login</a></li>
                            <li style="margin-bottom: 8px;"><a href="login/register.php" style="color: var(--text-medium);">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 style="font-weight: 600; margin-bottom: 16px;">Follow Us</h6>
                    <div style="display: flex; gap: 12px;">
                        <a href="#" style="color: var(--forest-green); font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--forest-green); font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: var(--forest-green); font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr style="border-color: var(--border-color); margin: 32px 0;">
            <div class="text-center" style="color: var(--text-medium);">
                <p>&copy; <?php echo date('Y'); ?> Taste of Africa. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Category filter buttons
            $('.category-filter-btn').click(function() {
                $('.category-filter-btn').removeClass('active');
                $(this).addClass('active');
                const categoryId = $(this).data('category');
                if (categoryId === 'all') {
                    window.location.href = 'all_product.php';
                } else {
                    window.location.href = 'all_product.php?category=' + categoryId;
                }
            });

            // Product filter buttons
            $('.product-filter-btn').click(function() {
                $('.product-filter-btn').removeClass('active');
                $(this).addClass('active');
                // Filter logic can be added here
            });

            // Add to cart functionality
            $('.add-to-cart-btn').click(function() {
                const productId = $(this).data('product-id');
                const button = $(this);
                
                button.prop('disabled', true);
                const originalHtml = button.html();
                button.html('<i class="fas fa-spinner fa-spin"></i> Adding...');
                
                $.ajax({
                    url: 'actions/add_to_cart_action.php',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        button.prop('disabled', false);
                        button.html(originalHtml);
                        
                        if (response.success) {
                            Swal.fire({
                                title: 'Added to Cart!',
                                text: response.message || 'Product added successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 2000,
                                showCancelButton: true,
                                cancelButtonText: 'Continue',
                                confirmButtonText: 'View Cart'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'cart.php';
                                }
                            });
                            
                            // Update cart count
                            if (response.cart_count !== undefined) {
                                $('.cart-count').text(response.cart_count);
                                if (response.cart_count > 0 && $('.cart-count').length === 0) {
                                    $('.cart-badge').append('<span class="cart-count">' + response.cart_count + '</span>');
                                }
                            }
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to add product.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        button.prop('disabled', false);
                        button.html(originalHtml);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
