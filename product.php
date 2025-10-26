<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/product.css" rel="stylesheet">
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
                <a class="nav-link active" href="product.php">
                    <i class="fa fa-box me-1"></i>Products
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
                <i class="fa fa-box me-3"></i>Discover Our Products
            </h1>
            <p class="lead mb-0">Explore our curated collection of premium products</p>
        </div>
    </div>

    <div class="container">
        <!-- Enhanced Search Section -->
        <div class="search-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="search-container position-relative">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input border-start-0" id="searchInput" 
                                   placeholder="Search for products, brands, or categories..." autocomplete="off">
                        </div>
                        <!-- Search Suggestions Dropdown -->
                        <div id="searchSuggestions" class="search-suggestions dropdown-menu show" style="display: none;">
                            <!-- Suggestions will be populated here -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-custom" id="searchBtn">
                        <i class="fa fa-search me-2"></i>Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-tags text-primary me-2"></i>Categories
                    </h6>
                    <div id="categoryFilters">
                        <button class="btn filter-btn active" data-category="all">
                            <i class="fa fa-th-large me-2"></i>All Categories
                        </button>
                        <!-- Categories will be loaded here -->
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-star text-warning me-2"></i>Brands
                    </h6>
                    <div id="brandFilters">
                        <button class="btn filter-btn active" data-brand="all">
                            <i class="fa fa-star me-2"></i>All Brands
                        </button>
                        <!-- Brands will be loaded here -->
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-dollar-sign text-success me-2"></i>Price Range
                    </h6>
                    <div class="price-range-container">
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" id="priceRangeMin" 
                                       placeholder="Min" min="0" step="0.01">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" id="priceRangeMax" 
                                       placeholder="Max" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="mt-2">
                            <div id="priceRangeSlider"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-sort text-info me-2"></i>Sort & Filter
                    </h6>
                    <div class="sort-container">
                        <select class="form-select form-select-sm" id="sortSelect">
                            <option value="name_asc">Name A-Z</option>
                            <option value="name_desc">Name Z-A</option>
                            <option value="price_asc">Price Low-High</option>
                            <option value="price_desc">Price High-Low</option>
                            <option value="newest">Newest First</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm mt-2 w-100" id="clearFilters">
                            <i class="fa fa-refresh me-2"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Dynamic Dropdown Filters -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" 
                                id="categoryDropdown" data-bs-toggle="dropdown">
                            <i class="fa fa-tags me-2"></i>Filter by Category
                        </button>
                        <ul class="dropdown-menu w-100" id="categoryDropdownMenu">
                            <li><a class="dropdown-item" href="#" data-value="all">All Categories</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dropdown">
                        <button class="btn btn-outline-warning dropdown-toggle w-100" type="button" 
                                id="brandDropdown" data-bs-toggle="dropdown">
                            <i class="fa fa-star me-2"></i>Filter by Brand
                        </button>
                        <ul class="dropdown-menu w-100" id="brandDropdownMenu">
                            <li><a class="dropdown-item" href="#" data-value="all">All Brands</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="results-info results-info-hidden" id="resultsInfo">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fa fa-info-circle text-primary me-2"></i>
                        <span id="resultsCount">0</span> products found
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-outline-secondary" id="clearFilters">
                        <i class="fa fa-times me-2"></i>Clear All Filters
                    </button>
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
        <nav aria-label="Products pagination" id="paginationContainer" class="pagination-container-hidden">
            <ul class="pagination" id="pagination">
                <!-- Pagination will be generated here -->
            </ul>
        </nav>
    </div>

    <!-- Product Detail Modal -->
    <div class="modal fade" id="productDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-box me-2"></i>Product Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="productDetailContent">
                    <!-- Product details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-custom" id="addToCartBtn">
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
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/product-customer.js"></script>
    <!-- Additional initialization script -->
    <script>
        // Initialize any page-specific functionality
        $(document).ready(function() {
            // Any additional initialization can go here
            console.log('Product page initialized with enhanced JavaScript');
        });
    </script>
</body>
</html>
