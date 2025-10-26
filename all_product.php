<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/product-customer.css" rel="stylesheet">
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
                <a class="nav-link active" href="all_product.php">
                    <i class="fa fa-box me-1"></i>All Products
                </a>
                <a class="nav-link" href="product.php">
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

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fa fa-boxes me-3"></i>All Products
            </h1>
            <p class="lead mb-0">Browse our complete collection of premium products</p>
        </div>
    </div>

    <div class="container">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-tags text-primary me-2"></i>Filter by Category
                    </h6>
                    <select class="form-select" id="categoryFilter">
                        <option value="all">All Categories</option>
                        <!-- Categories will be loaded here -->
                    </select>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-star text-warning me-2"></i>Filter by Brand
                    </h6>
                    <select class="form-select" id="brandFilter">
                        <option value="all">All Brands</option>
                        <!-- Brands will be loaded here -->
                    </select>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-sort text-info me-2"></i>Sort By
                    </h6>
                    <select class="form-select" id="sortFilter">
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                        <option value="price_asc">Price Low-High</option>
                        <option value="price_desc">Price High-Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <button class="btn btn-outline-secondary" id="clearFilters">
                        <i class="fa fa-refresh me-2"></i>Clear All Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="results-info" id="resultsInfo">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fa fa-info-circle text-primary me-2"></i>
                        <span id="resultsCount">0</span> products found
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <div class="pagination-info">
                        Page <span id="currentPage">1</span> of <span id="totalPages">1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="product-grid">
            <div class="row" id="productsContainer">
                <!-- Loading state -->
                <div class="col-12 text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary spinner-large" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading products...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer">
            <nav aria-label="Products pagination">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- Pagination will be generated here -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div class="modal fade" id="productDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="productDetailContent">
                    <!-- Product details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addToCartBtn">
                        <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/all-products.js"></script>
</body>
</html>
