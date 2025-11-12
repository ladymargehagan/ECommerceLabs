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
    <link href="css/main.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <style>
        .product-image {
            max-height: 500px;
            object-fit: cover;
            width: 100%;
        }
        .product-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
        }
        .product-price {
            font-size: 2em;
            font-weight: bold;
            color: #28a745;
        }
        .product-meta {
            background: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .keywords {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .keyword-badge {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .action-buttons .btn {
            flex: 1;
            min-width: 150px;
        }
    </style>
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

    <div class="container" style="padding-top: 120px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body p-0">
                        <?php if ($product['product_image']): ?>
                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                 onerror="this.src='uploads/placeholder.png'">
                        <?php else: ?>
                            <img src="uploads/placeholder.png" 
                                 class="product-image" 
                                 alt="No image available">
                        <?php endif; ?>
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="product-info">
                    <!-- Product ID (hidden but available for cart) -->
                    <input type="hidden" id="productId" value="<?php echo $product['product_id']; ?>">
                    
                    <!-- Product Title -->
                    <h1 class="mb-3"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    
                    <!-- Product Price -->
                    <div class="product-price mb-4">$<?php echo number_format($product['product_price'], 2); ?></div>
                    
                    <!-- Product Meta Information -->
                    <div class="product-meta">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong><i class="fa fa-tag me-2"></i>Category:</strong><br>
                                    <span class="text-primary"><?php echo htmlspecialchars($product['cat_name'] ?? 'No Category'); ?></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong><i class="fa fa-star me-2"></i>Brand:</strong><br>
                                    <span class="text-warning"><?php echo htmlspecialchars($product['brand_name'] ?? 'No Brand'); ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-0">
                                    <strong><i class="fa fa-barcode me-2"></i>Product ID:</strong>
                                    <span class="badge bg-secondary"><?php echo $product['product_id']; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Description -->
                    <?php if ($product['product_desc']): ?>
                        <div class="mb-4">
                            <h4><i class="fa fa-info-circle me-2"></i>Description</h4>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Product Keywords -->
                    <?php if ($product['product_keywords']): ?>
                        <div class="mb-4">
                            <h5><i class="fa fa-tags me-2"></i>Keywords</h5>
                            <div class="keywords">
                                <?php 
                                $keywords = explode(',', $product['product_keywords']);
                                foreach ($keywords as $keyword): 
                                    $keyword = trim($keyword);
                                    if ($keyword):
                                ?>
                                    <span class="keyword-badge"><?php echo htmlspecialchars($keyword); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="btn btn-custom btn-lg add-to-cart" 
                                data-product-id="<?php echo $product['product_id']; ?>">
                            <i class="fa fa-cart-plus me-2"></i>Add to Cart
                        </button>
                        <a href="all_product.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fa fa-arrow-left me-2"></i>Back to Products
                        </a>
                    </div>

                    <!-- Additional Actions -->
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="all_product.php?category=<?php echo $product['product_cat']; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-tag me-1"></i>More from <?php echo htmlspecialchars($product['cat_name'] ?? 'this category'); ?>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="all_product.php?brand=<?php echo $product['product_brand']; ?>" 
                                   class="btn btn-outline-warning btn-sm">
                                    <i class="fa fa-star me-1"></i>More from <?php echo htmlspecialchars($product['brand_name'] ?? 'this brand'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Placeholder) -->
        <div class="row mt-5">
            <div class="col-12">
                <h3><i class="fa fa-th-large me-2"></i>You Might Also Like</h3>
                <p class="text-muted">Related products will be displayed here in future updates.</p>
                <a href="all_product.php" class="btn btn-outline-primary">
                    <i class="fa fa-search me-1"></i>Browse All Products
                </a>
            </div>
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
