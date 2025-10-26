<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($search_query); ?>" - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <style>
        .search-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
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
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #7f8c8d;
        }
        .search-highlight {
            background: #fff3cd;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
        }
        .refine-search {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
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
                        <a class="nav-link" href="all_product.php">All Products</a>
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

    <!-- Search Hero Section -->
    <div class="search-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fa fa-search me-2"></i>Search Results</h1>
                    <p class="lead">Found <?php echo $pagination['total_products']; ?> results for "<span class="search-highlight"><?php echo htmlspecialchars($search_query); ?></span>"</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light" onclick="history.back()">
                        <i class="fa fa-arrow-left me-1"></i>Back
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Refine Search Section -->
    <div class="container">
        <div class="refine-search">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h5><i class="fa fa-filter me-2"></i>Refine Search</h5>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control search-box" id="searchInput" 
                           value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search products...">
                </div>
                <div class="col-md-2">
                    <select class="form-select filter-dropdown" id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['cat_id']; ?>">
                                <?php echo htmlspecialchars($category['cat_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select filter-dropdown" id="brandFilter">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['brand_id']; ?>">
                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" id="searchBtn">
                        <i class="fa fa-search me-1"></i>Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>
                        <i class="fa fa-list me-2"></i>Search Results
                    </h3>
                    <span class="badge bg-primary fs-6">
                        <?php echo $pagination['total_products']; ?> products found
                    </span>
                </div>
            </div>
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
                                <h5 class="card-title product-title">
                                    <?php 
                                    // Highlight search terms in product title
                                    $highlighted_title = str_ireplace($search_query, '<span class="search-highlight">' . $search_query . '</span>', htmlspecialchars($product['product_title']));
                                    echo $highlighted_title;
                                    ?>
                                </h5>
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
                    <div class="no-results">
                        <i class="fa fa-search fa-3x mb-3"></i>
                        <h4>No products found</h4>
                        <p>We couldn't find any products matching "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</p>
                        <div class="mt-3">
                            <p>Try these suggestions:</p>
                            <ul class="list-unstyled">
                                <li><i class="fa fa-check me-2"></i>Check your spelling</li>
                                <li><i class="fa fa-check me-2"></i>Try different keywords</li>
                                <li><i class="fa fa-check me-2"></i>Use more general terms</li>
                                <li><i class="fa fa-check me-2"></i>Browse by category or brand</li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <a href="all_product.php" class="btn btn-primary me-2">
                                <i class="fa fa-box me-1"></i>View All Products
                            </a>
                            <button class="btn btn-outline-primary" onclick="clearSearch()">
                                <i class="fa fa-refresh me-1"></i>New Search
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Search results pagination" class="mt-5">
                <ul class="pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?query=<?php echo urlencode($search_query); ?>&page=<?php echo $pagination['current_page'] - 1; ?>">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?query=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?query=<?php echo urlencode($search_query); ?>&page=<?php echo $pagination['current_page'] + 1; ?>">
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
                    performSearch();
                }
            });

            $('#searchBtn').on('click', function() {
                performSearch();
            });

            function performSearch() {
                const query = $('#searchInput').val().trim();
                const category = $('#categoryFilter').val();
                const brand = $('#brandFilter').val();
                
                if (query) {
                    let url = 'product_search_result.php?query=' + encodeURIComponent(query);
                    if (category) {
                        url += '&category=' + category;
                    }
                    if (brand) {
                        url += '&brand=' + brand;
                    }
                    window.location.href = url;
                } else {
                    alert('Please enter a search term');
                }
            }

            // Clear search functionality
            window.clearSearch = function() {
                $('#searchInput').val('');
                $('#categoryFilter').val('');
                $('#brandFilter').val('');
                window.location.href = 'all_product.php';
            };

            // Add to cart functionality
            window.addToCart = function(productId) {
                alert('Add to cart functionality will be implemented in the next phase. Product ID: ' + productId);
            };

            // Auto-focus search input
            $('#searchInput').focus();
        });
    </script>
</body>
</html>
