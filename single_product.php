<?php
session_start();

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$product_id) {
    header("Location: all_product.php");
    exit;
}

$product = null;

// Try to load product data
if (file_exists('settings/db_class.php') && file_exists('classes/product_class.php')) {
    try {
        require_once 'settings/db_class.php';
        require_once 'classes/product_class.php';
        
        $product_class = new product_class();
        
        if ($product_class->db_connect()) {
            $product = $product_class->view_single_product($product_id);
        }
    } catch (Exception $e) {
        error_log("Error loading product data: " . $e->getMessage());
    }
}

if (!$product) {
    header("Location: all_product.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css?v=2.0" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link href="css/product.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
            <?php if ($_SESSION['role'] == 1): // Admin users ?>
                <a href="admin/dashboard.php" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fa fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a href="admin/category.php" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fa fa-tags me-1"></i>Category
                </a>
                <a href="admin/brand.php" class="btn btn-sm btn-outline-warning me-2">
                    <i class="fa fa-star me-1"></i>Brand
                </a>
                <a href="admin/product.php" class="btn btn-sm btn-outline-success me-2">
                    <i class="fa fa-plus me-1"></i>Add Product
                </a>
            <?php endif; ?>
            <a href="customer/dashboard.php" class="btn btn-sm btn-outline-info me-2">
                <i class="fa fa-user me-1"></i>My Account
            </a>
            <a href="login/logout.php" class="btn btn-sm btn-outline-danger">
                <i class="fa fa-sign-out-alt me-1"></i>Logout
            </a>
        <?php else: ?>
            <span class="me-2">Menu:</span>
            <a href="login/register.php" class="btn btn-sm btn-outline-primary me-2">
                <i class="fa fa-user-plus me-1"></i>Register
            </a>
            <a href="login/login.php" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-sign-in-alt me-1"></i>Login
            </a>
        <?php endif; ?>
    </div>

    <div class="product-detail-container" style="padding-top: 120px;">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <span>/</span>
            <a href="all_product.php">All Products</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['product_title']); ?></span>
        </div>

        <div class="product-info-section">
            <!-- Product Image -->
            <div class="product-image-wrapper">
                <?php if ($product['product_image']): ?>
                    <img src="<?php echo htmlspecialchars($product['product_image']); ?>"
                         alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                         onerror="this.src='uploads/placeholder.png'">
                <?php else: ?>
                    <img src="uploads/placeholder.png"
                         alt="No image available">
                <?php endif; ?>
            </div>

            <!-- Product Information -->
            <div class="product-details-wrapper">
                    <!-- Product ID (hidden but available for cart) -->
                    <input type="hidden" id="productId" value="<?php echo $product['product_id']; ?>">
                    
                    <!-- Product Title -->
                    <h1><?php echo htmlspecialchars($product['product_title']); ?></h1>

                    <!-- Product Price -->
                    <div class="product-price">$<?php echo number_format($product['product_price'], 2); ?></div>
                    
                    <!-- Product Meta Information -->
                    <div class="product-meta">
                        <p>
                            <strong><i class="fa fa-tag"></i> Category:</strong>
                            <?php echo htmlspecialchars($product['cat_name'] ?? 'No Category'); ?>
                        </p>
                        <p>
                            <strong><i class="fa fa-star"></i> Brand:</strong>
                            <?php echo htmlspecialchars($product['brand_name'] ?? 'No Brand'); ?>
                        </p>
                        <p>
                            <strong><i class="fa fa-barcode"></i> Product ID:</strong>
                            <?php echo $product['product_id']; ?>
                        </p>
                    </div>

                    <!-- Product Description -->
                    <?php if ($product['product_desc']): ?>
                        <div class="description-section">
                            <h3><i class="fa fa-info-circle"></i> Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Product Keywords -->
                    <?php if ($product['product_keywords']): ?>
                        <div class="keywords-section">
                            <h3><i class="fa fa-tags"></i> Keywords</h3>
                            <div>
                                <?php
                                $keywords = explode(',', $product['product_keywords']);
                                foreach ($keywords as $keyword):
                                    $keyword = trim($keyword);
                                    if ($keyword):
                                ?>
                                    <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <button class="btn-add-cart add-to-cart"
                                data-product-id="<?php echo $product['product_id']; ?>">
                            <i class="fa fa-cart-plus"></i> Add to Cart
                        </button>
                        <a href="all_product.php" class="btn-back">
                            <i class="fa fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <div class="related-section">
            <h2><i class="fa fa-th-large"></i> You Might Also Like</h2>

            <div class="related-buttons">
                <a href="all_product.php?brand=<?php echo $product['product_brand']; ?>"
                   class="related-btn related-btn-brand">
                    <i class="fa fa-star"></i> More from <?php echo htmlspecialchars($product['brand_name'] ?? 'this brand'); ?>
                </a>
                <a href="all_product.php?category=<?php echo $product['product_cat']; ?>"
                   class="related-btn related-btn-category">
                    <i class="fa fa-tag"></i> More from <?php echo htmlspecialchars($product['cat_name'] ?? 'this category'); ?>
                </a>
            </div>

            <a href="all_product.php" class="browse-all-btn">
                <i class="fa fa-search"></i> Browse All Products
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Add to cart functionality
            $('.add-to-cart').click(function() {
                const productId = $(this).data('product-id');
                const button = $(this);
                
                // Disable button and show loading
                button.prop('disabled', true);
                const originalHtml = button.html();
                button.html('<i class="fa fa-spinner fa-spin me-2"></i>Adding...');
                
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
                                title: 'Success!',
                                text: response.message || 'Product added to cart successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 2000,
                                showCancelButton: true,
                                cancelButtonText: 'Continue Shopping',
                                confirmButtonText: 'View Cart'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'cart.php';
                                }
                            });
                            
                            // Update cart count if displayed
                            if (response.cart_count !== undefined) {
                                $('.cart-count').text(response.cart_count);
                            }
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to add product to cart.',
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
                            text: 'An error occurred while adding the product to cart.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').click(function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        });
    </script>
</body>
</html>
