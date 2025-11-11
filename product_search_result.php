<?php
session_start();

// Initialize empty arrays
$products = array();
$categories = array();
$brands = array();
$total_count = 0;
$total_pages = 0;

// Get search parameters
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 10;
$offset = ($page - 1) * $limit;

// Redirect if no search query
if (empty($search_query)) {
    header("Location: all_product.php");
    exit;
}

// Try to load data from database
if (file_exists('settings/db_class.php') && file_exists('classes/product_class.php')) {
    try {
        require_once 'settings/db_class.php';
        require_once 'classes/product_class.php';
        
        $product_class = new product_class();
        
        if ($product_class->db_connect()) {
            // Get search results
            $products = $product_class->get_products_with_filters($category_id, $brand_id, $search_query, $limit, $offset);
            $total_count = $product_class->get_filtered_count($category_id, $brand_id, $search_query);
            
            // Get categories and brands for filters
            $categories = $product_class->get_categories();
            $brands = $product_class->get_brands();
            
            // Ensure we have arrays
            if (!is_array($products)) $products = array();
            if (!is_array($categories)) $categories = array();
            if (!is_array($brands)) $brands = array();
        }
    } catch (Exception $e) {
        error_log("Error loading search data: " . $e->getMessage());
    }
}

// Calculate pagination
$total_pages = ceil($total_count / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($search_query); ?>" - Taste of Africa</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/product.css" rel="stylesheet">

    
    
    
    
</head>
<body>
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
                <li aria-current="page">Search Results</li>
            </ol>
        </nav>

        <!-- Header -->
        <div>
            <div>
                <h1><i></i>Search Results</h1>
                <p>
                    Results for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                    <?php if ($category_id): ?>
                        in category "<strong><?php echo htmlspecialchars(array_column($categories, 'cat_name', 'cat_id')[$category_id] ?? 'Unknown'); ?></strong>"
                    <?php endif; ?>
                    <?php if ($brand_id): ?>
                        from brand "<strong><?php echo htmlspecialchars(array_column($brands, 'brand_name', 'brand_id')[$brand_id] ?? 'Unknown'); ?></strong>"
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Refine Search Section -->
        <div>
            <h5><i></i>Refine Your Search</h5>
            <form method="GET" id="refineForm">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                <div>
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
                                <i></i>Refine Search
                            </button>
                        </div>
                    </div>
                </div>
                <?php if ($category_id || $brand_id): ?>
                    <div>
                        <div>
                            <a href="product_search_result.php?q=<?php echo urlencode($search_query); ?>">
                                <i></i>Clear Filters
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Results Summary -->
        <div>
            <div>
                <p>
                    Showing <?php echo count($products); ?> of <?php echo $total_count; ?> results
                    <?php if ($total_count > 0): ?>
                        (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Search Results -->
        <?php if (empty($products)): ?>
            <div>
                <i></i>
                <h3>No Results Found</h3>
                <p>We couldn't find any products matching your search criteria.</p>
                <div>
                    <a href="all_product.php">Browse All Products</a>
                    <button onclick="history.back()">Go Back</button>
                </div>
            </div>
        <?php else: ?>
            <div id="productsContainer">
                <?php foreach ($products as $product): ?>
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
                                <div>
                                    <span>ID: <?php echo $product['product_id']; ?></span>
                                </div>
                            </div>
                            <div>
                                <h5>
                                    <?php 
                                    $title = htmlspecialchars($product['product_title']);
                                    echo str_ireplace($search_query, '<span>' . htmlspecialchars($search_query) . '</span>', $title);
                                    ?>
                                </h5>
                                <div>$<?php echo number_format($product['product_price'], 2); ?></div>
                                <div>
                                    <i></i><?php echo htmlspecialchars($product['cat_name'] ?? 'No Category'); ?>
                                </div>
                                <div>
                                    <i></i><?php echo htmlspecialchars($product['brand_name'] ?? 'No Brand'); ?>
                                </div>
                                <?php if ($product['product_desc']): ?>
                                    <p>
                                        <?php 
                                        $desc = htmlspecialchars(substr($product['product_desc'], 0, 100));
                                        if (strlen($product['product_desc']) > 100) $desc .= '...';
                                        echo str_ireplace($search_query, '<span>' . htmlspecialchars($search_query) . '</span>', $desc);
                                        ?>
                                    </p>
                                <?php endif; ?>
                                <div>
                                    <div>
                                        <a href="single_product.php?id=<?php echo $product['product_id']; ?>">
                                            <i></i>View Details
                                        </a>
                                        <button 
                                                data-product-id="<?php echo $product['product_id']; ?>">
                                            <i></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div>
                    <nav aria-label="Search results pagination">
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

        <!-- Search Suggestions -->
        <div>
            <div>
                <h4><i></i>Search Tips</h4>
                <div>
                    <div>
                        <ul>
                            <li><i></i>Try different keywords</li>
                            <li><i></i>Check your spelling</li>
                            <li><i></i>Use more general terms</li>
                        </ul>
                    </div>
                    <div>
                        <ul>
                            <li><i></i>Browse by category</li>
                            <li><i></i>Filter by brand</li>
                            <li><i></i>View all products</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Add to cart functionality (placeholder)
            $('.add-to-cart').click(function() {
                const productId = $(this).data('product-id');
                
                Swal.fire({
                    title: 'Add to Cart',
                    text: 'Product ID: ' + productId + ' - This feature will be implemented soon!',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });

            // Auto-submit form on filter change
            $('#category, #brand').change(function() {
                $('#refineForm').submit();
            });
        });
    </script>
</body>
</html>
