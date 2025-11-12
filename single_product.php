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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/product.css" rel="stylesheet">

    
    
    
    
    
</head>
<body>
    <!-- Top Header Bar -->
    <div>
        <div>
            <div>
                <div>
                    <div>
                        <a href="index.php#about">About Us</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="customer/dashboard.php">My Account</a>
                        <?php endif; ?>
                        <a href="index.php#contact">Contact</a>
                    </div>
                </div>
                <div>
                    <span>Super Value Deals - Save more with coupons</span>
                </div>
                <div>
                    <span>Need help? Call Us: <strong>+1-800-900-122</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav>
        <div>
            <div>
                <a href="index.php">
                    <i></i>
                    <span>Taste of Africa</span>
                </a>
                <div>
                    <form method="GET" action="all_product.php">
                        <input type="text" name="search" placeholder="Search for products...">
                        <button type="submit">Search</button>
                    </form>
                </div>
                <div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] == 1): ?>
                            <a href="admin/dashboard.php" title="Admin Dashboard">
                                <i></i>
                                <span>Admin</span>
                            </a>
                        <?php endif; ?>
                        <a href="customer/dashboard.php" title="My Account">
                            <i></i>
                            <span>Account</span>
                        </a>
                    <?php else: ?>
                        <a href="login/login.php" title="Login">
                            <i></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                    <a href="cart.php" title="Shopping Cart">
                        <i></i>
                        <span>Cart</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Navigation -->
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
            <?php if ($_SESSION['role'] == 1): // Admin users ?>
                <a href="admin/dashboard.php">
                    <i></i>Dashboard
                </a>
                <a href="admin/category.php">
                    <i></i>Category
                </a>
                <a href="admin/brand.php">
                    <i></i>Brand
                </a>
                <a href="admin/product.php">
                    <i></i>Add Product
                </a>
            <?php endif; ?>
            <a href="customer/dashboard.php">
                <i></i>My Account
            </a>
            <a href="login/logout.php">
                <i></i>Logout
            </a>
        <?php else: ?>
            <span>Menu:</span>
            <a href="login/register.php">
                <i></i>Register
            </a>
            <a href="login/login.php">
                <i></i>Login
            </a>
        <?php endif; ?>
    </div>

    <div>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol>
                <li><a href="index.php">Home</a></li>
                <li><a href="all_product.php">All Products</a></li>
                <li aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div>
            <!-- Product Image -->
            <div>
                <div>
                    <div>
                        <?php if ($product['product_image']): ?>
                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                 onerror="this.src='uploads/placeholder.png'">
                        <?php else: ?>
                            <img src="uploads/placeholder.png" 
                                 alt="No image available">
                        <?php endif; ?>
                </div>
            </div>

            <!-- Product Information -->
            <div>
                <div>
                    <!-- Product ID (hidden but available for cart) -->
                    <input type="hidden" id="productId" value="<?php echo $product['product_id']; ?>">
                    
                    <!-- Product Title -->
                    <h1><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    
                    <!-- Product Price -->
                    <div>$<?php echo number_format($product['product_price'], 2); ?></div>
                    
                    <!-- Product Meta Information -->
                    <div>
                        <div>
                            <div>
                                <p>
                                    <strong><i></i>Category:</strong><br>
                                    <span><?php echo htmlspecialchars($product['cat_name'] ?? 'No Category'); ?></span>
                                </p>
                            </div>
                            <div>
                                <p>
                                    <strong><i></i>Brand:</strong><br>
                                    <span><?php echo htmlspecialchars($product['brand_name'] ?? 'No Brand'); ?></span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <div>
                                <p>
                                    <strong><i></i>Product ID:</strong>
                                    <span><?php echo $product['product_id']; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Description -->
                    <?php if ($product['product_desc']): ?>
                        <div>
                            <h4><i></i>Description</h4>
                            <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Product Keywords -->
                    <?php if ($product['product_keywords']): ?>
                        <div>
                            <h5><i></i>Keywords</h5>
                            <div>
                                <?php 
                                $keywords = explode(',', $product['product_keywords']);
                                foreach ($keywords as $keyword): 
                                    $keyword = trim($keyword);
                                    if ($keyword):
                                ?>
                                    <span><?php echo htmlspecialchars($keyword); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div>
                        <button 
                                data-product-id="<?php echo $product['product_id']; ?>">
                            <i></i>Add to Cart
                        </button>
                        <a href="all_product.php">
                            <i></i>Back to Products
                        </a>
                    </div>

                    <!-- Additional Actions -->
                    <div>
                        <div>
                            <div>
                                <a href="all_product.php?category=<?php echo $product['product_cat']; ?>">
                                    <i></i>More from <?php echo htmlspecialchars($product['cat_name'] ?? 'this category'); ?>
                                </a>
                            </div>
                            <div>
                                <a href="all_product.php?brand=<?php echo $product['product_brand']; ?>">
                                    <i></i>More from <?php echo htmlspecialchars($product['brand_name'] ?? 'this brand'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Placeholder) -->
        <div>
            <div>
                <h3><i></i>You Might Also Like</h3>
                <p>Related products will be displayed here in future updates.</p>
                <a href="all_product.php">
                    <i></i>Browse All Products
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
                button.html('<i></i>Adding...');
                
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
