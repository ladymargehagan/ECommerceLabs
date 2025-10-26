<?php
require_once 'settings/core.php';
require_once 'controllers/product_controller.php';
require_once 'controllers/category_controller.php';
require_once 'controllers/brand_controller.php';

// Get search parameters
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

if (empty($search_query)) {
    header('Location: product.php');
    exit();
}

// Initialize controllers
$product_controller = new product_controller();
$category_controller = new category_controller();
$brand_controller = new brand_controller();

// Get search results
$search_result = $product_controller->search_products_ctr($search_query);
$all_products = $search_result['success'] ? $search_result['data'] : [];

// Apply additional filters
$filtered_products = $all_products;

if ($category_filter > 0) {
    $filtered_products = array_filter($filtered_products, function($product) use ($category_filter) {
        return $product['product_cat'] == $category_filter;
    });
}

if ($brand_filter > 0) {
    $filtered_products = array_filter($filtered_products, function($product) use ($brand_filter) {
        return $product['product_brand'] == $brand_filter;
    });
}

// Pagination
$limit = 10;
$total_products = count($filtered_products);
$total_pages = ceil($total_products / $limit);
$offset = ($page - 1) * $limit;
$products = array_slice($filtered_products, $offset, $limit);

// Get categories and brands for filters
$categories_result = $category_controller->get_all_categories_ctr();
$categories = $categories_result['success'] ? $categories_result['data'] : [];

$brands_result = $brand_controller->get_all_brands_ctr();
$brands = $brands_result['success'] ? $brands_result['data'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($search_query); ?>" - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fa fa-star text-warning me-2"></i>Taste of Africa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fa fa-home me-1"></i>Home
                </a>
                <a class="nav-link" href="all_product.php">
                    <i class="fa fa-box me-1"></i>All Products
                </a>
                <a class="nav-link active" href="product.php">
                    <i class="fa fa-search me-1"></i>Search Products
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="customer/dashboard.php">
                        <i class="fa fa-user me-1"></i>Dashboard
                    </a>
                    <a class="nav-link" href="login/logout.php">
                        <i class="fa fa-sign-out-alt me-1"></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="login/login.php">
                        <i class="fa fa-sign-in-alt me-1"></i>Login
                    </a>
                    <a class="nav-link" href="login/register.php">
                        <i class="fa fa-user-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="product.php">Search Products</a></li>
                <li class="breadcrumb-item active">Search Results</li>
            </ol>
        </nav>

        <!-- Search Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-2">
                    <i class="fa fa-search me-2"></i>Search Results
                </h1>
                <p class="lead">Results for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Refine by Category:</label>
                <select class="form-select" onchange="filterByCategory(this.value)">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['cat_id']; ?>" 
                                <?php echo $category_filter == $category['cat_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['cat_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Refine by Brand:</label>
                <select class="form-select" onchange="filterByBrand(this.value)">
                    <option value="0">All Brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand['brand_id']; ?>" 
                                <?php echo $brand_filter == $brand['brand_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div>
                    <a href="product.php" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh me-1"></i>New Search
                    </a>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-2"></i>
                    Found <?php echo $total_products; ?> result<?php echo $total_products != 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($search_query); ?>"
                    <?php if ($page > 1): ?>
                        (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="fa fa-search fa-2x mb-3"></i>
                        <h4>No Results Found</h4>
                        <p>No products match your search criteria.</p>
                        <div class="mt-3">
                            <a href="product.php" class="btn btn-primary me-2">
                                <i class="fa fa-search me-1"></i>Try New Search
                            </a>
                            <a href="all_product.php" class="btn btn-outline-secondary">
                                <i class="fa fa-box me-1"></i>View All Products
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-img-top-container" style="height: 200px; overflow: hidden;">
                                <img src="uploads/product/<?php echo htmlspecialchars($product['product_image'] ?: 'placeholder.png'); ?>" 
                                     class="card-img-top" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                                     onerror="this.src='uploads/placeholder.png'">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['product_title']); ?></h5>
                                <p class="card-text text-primary fw-bold fs-5">$<?php echo number_format($product['product_price'], 2); ?></p>
                                
                                <div class="mt-auto">
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fa fa-tag me-1"></i>
                                                <?php echo htmlspecialchars($product['cat_name'] ?: 'No Category'); ?>
                                            </small>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fa fa-star me-1"></i>
                                                <?php echo htmlspecialchars($product['brand_name'] ?: 'No Brand'); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="single_product.php?id=<?php echo $product['product_id']; ?>" 
                                           class="btn btn-primary">
                                            <i class="fa fa-eye me-1"></i>View Details
                                        </a>
                                        <button class="btn btn-outline-success" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                            <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Search results pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $page - 1; ?>&category=<?php echo $category_filter; ?>&brand=<?php echo $brand_filter; ?>">
                                <i class="fa fa-chevron-left"></i> Previous
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&brand=<?php echo $brand_filter; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $page + 1; ?>&category=<?php echo $category_filter; ?>&brand=<?php echo $brand_filter; ?>">
                                Next <i class="fa fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterByCategory(categoryId) {
            const url = new URL(window.location);
            url.searchParams.set('category', categoryId);
            url.searchParams.set('brand', '0');
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function filterByBrand(brandId) {
            const url = new URL(window.location);
            url.searchParams.set('brand', brandId);
            url.searchParams.set('category', '0');
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }

        function addToCart(productId) {
            alert('Add to Cart functionality will be implemented in future labs. Product ID: ' + productId);
        }
    </script>
</body>
</html>