<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Initialize empty arrays
$products = array();
$categories = array();
$brands = array();
$total_count = 0;
$total_pages = 0;
$error_message = '';

// Get current page and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : null;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : null;

$limit = 10;
$offset = ($page - 1) * $limit;

// Try to load data from database
if (file_exists('settings/db_class.php') && file_exists('classes/product_class.php')) {
    try {
        require_once 'settings/db_class.php';
        require_once 'classes/product_class.php';
        
        $product_class = new product_class();
        
        if ($product_class->db_connect()) {
            // Get products based on filters
            if ($category_id || $brand_id || $search_query) {
                $products = $product_class->get_products_with_filters($category_id, $brand_id, $search_query, $limit, $offset);
                $total_count = $product_class->get_filtered_count($category_id, $brand_id, $search_query);
            } else {
                $products = $product_class->view_all_products($limit, $offset);
                $total_count = $product_class->get_products_count();
            }
            
            // Get categories and brands for filters
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
    } catch (Error $e) {
        $error_message = 'Fatal error: ' . $e->getMessage();
        error_log("Fatal error loading product data: " . $e->getMessage());
    }
} else {
    $error_message = 'Required files not found.';
}

// Calculate pagination (avoid division by zero)
$total_pages = $limit > 0 ? ceil($total_count / $limit) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Taste of Africa</title>

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
                        <input type="text" name="search" placeholder="Search for products..." 
                               value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
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
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): // ONLY Admin users ?>
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
        <!-- Header -->
        <div>
            <div>
                <h1Playfair Display', serif; color: var(--forest-green);">All Products</h1>
                <p>Discover our amazing collection of African products</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div>
            <form method="GET" id="filterForm">
                <div>
                    <div>
                        <label for="search">Search Products</label>
                        <input type="text" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search_query); ?>" 
                               placeholder="Search by name, description, or keywords...">
                    </div>
                    <div>
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['cat_id']; ?>" 
                                        <?php echo ($category_id == $category['cat_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="brand">Brand</label>
                        <select id="brand" name="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>" 
                                        <?php echo ($brand_id == $brand['brand_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit">
                                <i></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
                <?php if ($category_id || $brand_id || $search_query): ?>
                    <div>
                        <div>
                            <a href="all_product.php">
                                <i></i>Clear Filters
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Error Message -->
        <?php if ($error_message): ?>
            <div role="alert">
                <i></i><?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Results Summary -->
        <div>
            <div>
                <p>
                    Showing <?php echo count($products); ?> of <?php echo $total_count; ?> products
                    <?php if ($search_query): ?>
                        for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                    <?php endif; ?>
                    <?php if ($category_id): ?>
                        in category "<strong><?php echo htmlspecialchars(array_column($categories, 'cat_name', 'cat_id')[$category_id] ?? 'Unknown'); ?></strong>"
                    <?php endif; ?>
                    <?php if ($brand_id): ?>
                        from brand "<strong><?php echo htmlspecialchars(array_column($brands, 'brand_name', 'brand_id')[$brand_id] ?? 'Unknown'); ?></strong>"
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div>
                <i></i>
                <h3>No Products Found</h3>
                <p>Try adjusting your search criteria or browse all products.</p>
                <a href="all_product.php">View All Products</a>
            </div>
        <?php else: ?>
            <div>
                <?php foreach ($products as $product): ?>
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
                            <span>New</span>
                        </div>
                        <div>
                            <h5><?php echo htmlspecialchars($product['product_title']); ?></h5>
                            <div>
                                <span><?php echo htmlspecialchars($product['cat_name'] ?? 'Category'); ?></span>
                                <?php if ($product['brand_name']): ?>
                                    <span> • By <?php echo htmlspecialchars($product['brand_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                            </div>
                            <div>
                                <span>$<?php echo number_format($product['product_price'], 2); ?></span>
                            </div>
                            <button data-product-id="<?php echo $product['product_id']; ?>">
                                <i></i> Add
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div>
                    <nav aria-label="Products pagination">
                        <ul>
                            <?php if ($page > 1): ?>
                                <li>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                        <i></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <liactive' : ''; ?>">
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                        Next <i></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Add to cart functionality
            $('.add-to-cart-btn, .add-to-cart').click(function() {
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

            // Auto-submit form on filter change
            $('#category, #brand').change(function() {
                $('#filterForm').submit();
            });

            // Search with Enter key
            $('#search').keypress(function(e) {
                if (e.which == 13) {
                    $('#filterForm').submit();
                }
            });
        });
    </script>
</body>
</html>
