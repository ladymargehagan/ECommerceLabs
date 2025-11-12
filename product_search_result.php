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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css?v=2.0" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.3s ease;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .product-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #28a745;
        }
        .product-category, .product-brand {
            font-size: 0.9em;
            color: #6c757d;
        }
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
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
                <li class="breadcrumb-item active" aria-current="page">Search Results</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1><i class="fa fa-search me-2"></i>Search Results</h1>
                <p class="text-muted">
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
        <div class="filter-section">
            <h5><i class="fa fa-filter me-2"></i>Refine Your Search</h5>
            <form method="GET" id="refineForm">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['cat_id']; ?>" 
                                        <?php echo ($category_id == $category['cat_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="brand" class="form-label">Brand</label>
                        <select class="form-control" id="brand" name="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>" 
                                        <?php echo ($brand_id == $brand['brand_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-custom">
                                <i class="fa fa-search me-1"></i>Refine Search
                            </button>
                        </div>
                    </div>
                </div>
                <?php if ($category_id || $brand_id): ?>
                    <div class="row mt-2">
                        <div class="col-12">
                            <a href="product_search_result.php?q=<?php echo urlencode($search_query); ?>" 
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-times me-1"></i>Clear Filters
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="row mb-3">
            <div class="col-12">
                <p class="text-muted">
                    Showing <?php echo count($products); ?> of <?php echo $total_count; ?> results
                    <?php if ($total_count > 0): ?>
                        (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Search Results -->
        <?php if (empty($products)): ?>
            <div class="no-products">
                <i class="fa fa-search fa-3x mb-3"></i>
                <h3>No Results Found</h3>
                <p>We couldn't find any products matching your search criteria.</p>
                <div class="mt-3">
                    <a href="all_product.php" class="btn btn-custom me-2">Browse All Products</a>
                    <button class="btn btn-outline-primary" onclick="history.back()">Go Back</button>
                </div>
            </div>
        <?php else: ?>
            <div class="row" id="productsContainer">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                <?php if ($product['product_image']): ?>
                                    <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                         onerror="this.src='uploads/placeholder.png'">
                                <?php else: ?>
                                    <img src="uploads/placeholder.png" 
                                         class="card-img-top product-image" 
                                         alt="No image available">
                                <?php endif; ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-primary">ID: <?php echo $product['product_id']; ?></span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <?php 
                                    $title = htmlspecialchars($product['product_title']);
                                    echo str_ireplace($search_query, '<span class="search-highlight">' . htmlspecialchars($search_query) . '</span>', $title);
                                    ?>
                                </h5>
                                <div class="product-price mb-2">$<?php echo number_format($product['product_price'], 2); ?></div>
                                <div class="product-category mb-1">
                                    <i class="fa fa-tag me-1"></i><?php echo htmlspecialchars($product['cat_name'] ?? 'No Category'); ?>
                                </div>
                                <div class="product-brand mb-3">
                                    <i class="fa fa-star me-1"></i><?php echo htmlspecialchars($product['brand_name'] ?? 'No Brand'); ?>
                                </div>
                                <?php if ($product['product_desc']): ?>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php 
                                        $desc = htmlspecialchars(substr($product['product_desc'], 0, 100));
                                        if (strlen($product['product_desc']) > 100) $desc .= '...';
                                        echo str_ireplace($search_query, '<span class="search-highlight">' . htmlspecialchars($search_query) . '</span>', $desc);
                                        ?>
                                    </p>
                                <?php endif; ?>
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="single_product.php?id=<?php echo $product['product_id']; ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="fa fa-eye me-1"></i>View Details
                                        </a>
                                        <button class="btn btn-custom add-to-cart" 
                                                data-product-id="<?php echo $product['product_id']; ?>">
                                            <i class="fa fa-cart-plus me-1"></i>Add to Cart
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
                <div class="pagination-wrapper">
                    <nav aria-label="Search results pagination">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                        <i class="fa fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                        Next <i class="fa fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Search Suggestions -->
        <div class="row mt-5">
            <div class="col-12">
                <h4><i class="fa fa-lightbulb me-2"></i>Search Tips</h4>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fa fa-check text-success me-2"></i>Try different keywords</li>
                            <li><i class="fa fa-check text-success me-2"></i>Check your spelling</li>
                            <li><i class="fa fa-check text-success me-2"></i>Use more general terms</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fa fa-check text-success me-2"></i>Browse by category</li>
                            <li><i class="fa fa-check text-success me-2"></i>Filter by brand</li>
                            <li><i class="fa fa-check text-success me-2"></i>View all products</li>
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
