<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #e74c3c;
        }
        .product-category, .product-brand {
            font-size: 0.9rem;
            color: #7f8c8d;
        }
        .filter-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .btn-add-cart {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-add-cart:hover {
            background: linear-gradient(45deg, #ee5a24, #ff6b6b);
            transform: scale(1.05);
        }
        .pagination {
            justify-content: center;
        }
        .page-link {
            border-radius: 50%;
            margin: 0 2px;
            border: none;
            color: #667eea;
        }
        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
        }
        .search-box {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .search-box:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .filter-dropdown {
            border-radius: 25px;
            border: 2px solid #e9ecef;
        }
        .loading-spinner {
            display: none;
        }
        .no-products {
            text-align: center;
            padding: 3rem;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fa fa-home me-2"></i>Taste of Africa
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="all_product.php">All Products</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/register.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h3><i class="fa fa-filter me-2"></i>Filter Products</h3>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" class="form-control search-box" id="searchInput" placeholder="Search products...">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-select filter-dropdown" id="categoryFilter">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['cat_id']; ?>" 
                                            <?php echo (isset($filter_type) && $filter_type === 'category' && $filter_id == $category['cat_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['cat_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-select filter-dropdown" id="brandFilter">
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id']; ?>"
                                            <?php echo (isset($filter_type) && $filter_type === 'brand' && $filter_id == $brand['brand_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-light w-100" id="clearFilters">
                                <i class="fa fa-times me-1"></i>Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container" style="padding-top: 2rem;">
        <!-- Results Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>
                        <?php if (isset($search_query)): ?>
                            <i class="fa fa-search me-2"></i>Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                        <?php elseif (isset($filter_type) && isset($filter_id)): ?>
                            <i class="fa fa-filter me-2"></i>
                            <?php if ($filter_type === 'category'): ?>
                                Products in <?php echo htmlspecialchars($categories[array_search($filter_id, array_column($categories, 'cat_id'))]['cat_name']); ?>
                            <?php elseif ($filter_type === 'brand'): ?>
                                Products by <?php echo htmlspecialchars($brands[array_search($filter_id, array_column($brands, 'brand_id'))]['brand_name']); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <i class="fa fa-box me-2"></i>All Products
                        <?php endif; ?>
                    </h2>
                    <span class="badge bg-primary fs-6">
                        <?php echo $pagination['total_products']; ?> products found
                    </span>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-spinner text-center py-5" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading products...</p>
        </div>

        <!-- Products Grid -->
        <div class="row" id="productsContainer">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                <img src="../uploads/<?php echo htmlspecialchars($product['product_image'] ?: 'placeholder.png'); ?>" 
                                     class="card-img-top product-image" 
                                     alt="<?php echo htmlspecialchars($product['product_title']); ?>">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-success">ID: <?php echo $product['product_id']; ?></span>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title product-title"><?php echo htmlspecialchars($product['product_title']); ?></h5>
                                <div class="mb-2">
                                    <span class="product-category">
                                        <i class="fa fa-tag me-1"></i><?php echo htmlspecialchars($product['cat_name']); ?>
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <span class="product-brand">
                                        <i class="fa fa-star me-1"></i><?php echo htmlspecialchars($product['brand_name']); ?>
                                    </span>
                                </div>
                                <div class="mt-auto">
                                    <div class="product-price mb-3">
                                        $<?php echo number_format($product['product_price'], 2); ?>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="single_product.php?id=<?php echo $product['product_id']; ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="fa fa-eye me-1"></i>View Details
                                        </a>
                                        <button class="btn btn-add-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                            <i class="fa fa-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-products">
                        <i class="fa fa-box-open fa-3x mb-3"></i>
                        <h4>No products found</h4>
                        <p>Try adjusting your search criteria or filters.</p>
                        <a href="all_product.php" class="btn btn-primary">
                            <i class="fa fa-refresh me-1"></i>View All Products
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Products pagination" class="mt-5">
                <ul class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo isset($search_query) ? '&query=' . urlencode($search_query) : ''; ?><?php echo isset($filter_type) ? '&' . $filter_type . '_id=' . $filter_id : ''; ?>">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($search_query) ? '&query=' . urlencode($search_query) : ''; ?><?php echo isset($filter_type) ? '&' . $filter_type . '_id=' . $filter_id : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo isset($search_query) ? '&query=' . urlencode($search_query) : ''; ?><?php echo isset($filter_type) ? '&' . $filter_type . '_id=' . $filter_id : ''; ?>">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Search functionality
            $('#searchInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    const query = $(this).val().trim();
                    if (query) {
                        window.location.href = 'product_search_result.php?query=' + encodeURIComponent(query);
                    }
                }
            });

            // Category filter
            $('#categoryFilter').on('change', function() {
                const catId = $(this).val();
                if (catId) {
                    window.location.href = 'all_product.php?action=filter_by_category&cat_id=' + catId;
                } else {
                    window.location.href = 'all_product.php';
                }
            });

            // Brand filter
            $('#brandFilter').on('change', function() {
                const brandId = $(this).val();
                if (brandId) {
                    window.location.href = 'all_product.php?action=filter_by_brand&brand_id=' + brandId;
                } else {
                    window.location.href = 'all_product.php';
                }
            });

            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#searchInput').val('');
                $('#categoryFilter').val('');
                $('#brandFilter').val('');
                window.location.href = 'all_product.php';
            });

            // Add to cart placeholder
            window.addToCart = function(productId) {
                alert('Add to cart functionality will be implemented in the next phase. Product ID: ' + productId);
            };
        });
    </script>
</body>
</html>
