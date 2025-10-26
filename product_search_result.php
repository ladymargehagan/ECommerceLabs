<?php
require_once 'settings/core.php';
require_once 'actions/product_actions.php';

$product_actions = new product_actions();

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($search_query)) {
    header("Location: all_product.php");
    exit;
}

// Search products
$products_result = $product_actions->search_products($search_query);
$products = $products_result['success'] ? $products_result['data'] : array();

// Get categories and brands for filtering
$categories_result = $product_actions->get_categories();
$categories = $categories_result['success'] ? $categories_result['data'] : array();

$brands_result = $product_actions->get_brands();
$brands = $brands_result['success'] ? $brands_result['data'] : array();
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
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .search-box {
            background: #fff;
            border-radius: 25px;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <h2><i class="fa fa-search me-2"></i>Search Results</h2>
                    <div>
                        <a href="all_product.php" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-box me-1"></i>All Products
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-home me-1"></i>Home
                        </a>
                        <a href="login/login.php" class="btn btn-custom">
                            <i class="fa fa-user me-1"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results Info -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="fa fa-search me-2"></i>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h5>
                    <p class="mb-0">Found <?php echo count($products); ?> product(s) matching your search.</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row">
            <div class="col-12">
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="search-box">
                                <form method="GET" action="product_search_result.php">
                                    <div class="input-group">
                                        <input type="text" class="form-control border-0" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search products..." required>
                                        <button class="btn btn-custom" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="categoryFilter" onchange="filterByCategory()">
                                <option value="">Filter by Category</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['cat_id']; ?>"><?php echo htmlspecialchars($category['cat_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="brandFilter" onchange="filterByBrand()">
                                <option value="">Filter by Brand</option>
                                <?php foreach($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Display -->
        <div class="row" id="productsContainer">
            <?php if(empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted">No products match your search criteria. Try different keywords or browse all products.</p>
                    <a href="all_product.php" class="btn btn-custom">
                        <i class="fa fa-box me-1"></i>Browse All Products
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                    <?php 
                    $imageSrc = !empty($product['product_image']) ? $product['product_image'] : 'uploads/placeholder.png';
                    // Highlight search terms in product title
                    $highlighted_title = str_ireplace($search_query, '<span class="search-highlight">' . $search_query . '</span>', htmlspecialchars($product['product_title']));
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="<?php echo htmlspecialchars($imageSrc); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['product_title']); ?>" onerror="this.src='uploads/placeholder.png'">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $highlighted_title; ?></h5>
                                <p class="card-text text-primary fw-bold">$<?php echo number_format($product['product_price'], 2); ?></p>
                                <div class="mt-auto">
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?><br>
                                            <strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?><br>
                                            <strong>Product ID:</strong> <?php echo $product['product_id']; ?>
                                        </small>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <a href="single_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fa fa-eye me-1"></i>View Details
                                        </a>
                                        <button class="btn btn-custom btn-sm flex-fill" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                            <i class="fa fa-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination (if more than 10 results) -->
        <?php if(count($products) > 10): ?>
            <div class="row">
                <div class="col-12">
                    <nav aria-label="Search results pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <span class="page-link">Previous</span>
                            </li>
                            <li class="page-item active">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">Next</span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function filterByCategory() {
            const categoryId = document.getElementById('categoryFilter').value;
            const searchQuery = '<?php echo urlencode($search_query); ?>';
            if (categoryId) {
                window.location.href = 'product_search_result.php?q=' + searchQuery + '&category=' + categoryId;
            } else {
                window.location.href = 'product_search_result.php?q=' + searchQuery;
            }
        }

        function filterByBrand() {
            const brandId = document.getElementById('brandFilter').value;
            const searchQuery = '<?php echo urlencode($search_query); ?>';
            if (brandId) {
                window.location.href = 'product_search_result.php?q=' + searchQuery + '&brand=' + brandId;
            } else {
                window.location.href = 'product_search_result.php?q=' + searchQuery;
            }
        }

        function addToCart(productId) {
            alert('Add to Cart functionality will be implemented in future labs. Product ID: ' + productId);
        }

        // Set selected filters based on URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');
        const brandParam = urlParams.get('brand');
        
        if (categoryParam) {
            document.getElementById('categoryFilter').value = categoryParam;
        }
        if (brandParam) {
            document.getElementById('brandFilter').value = brandParam;
        }
    </script>
</body>
</html>