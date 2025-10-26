<?php
require_once 'settings/core.php';

// Get search parameters from URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

if (empty($search_query)) {
    header('Location: product.php');
    exit();
}
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

    <!-- Breadcrumb -->
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="product.php">Search Products</a></li>
                <li class="breadcrumb-item active">Search Results</li>
            </ol>
        </nav>
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fa fa-search me-3"></i>Search Results
            </h1>
            <p class="lead mb-0">Results for "<span id="searchQuery"><?php echo htmlspecialchars($search_query); ?></span>"</p>
        </div>
    </div>

    <div class="container">
        <!-- Refine Search Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-tags text-primary me-2"></i>Refine by Category
                    </h6>
                    <select class="form-select" id="categoryFilter">
                        <option value="all">All Categories</option>
                        <!-- Categories will be loaded here -->
                    </select>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-star text-warning me-2"></i>Refine by Brand
                    </h6>
                    <select class="form-select" id="brandFilter">
                        <option value="all">All Brands</option>
                        <!-- Brands will be loaded here -->
                    </select>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fa fa-sort text-info me-2"></i>Sort Results
                    </h6>
                    <select class="form-select" id="sortFilter">
                        <option value="relevance">Most Relevant</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                        <option value="price_asc">Price Low-High</option>
                        <option value="price_desc">Price High-Low</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <button class="btn btn-outline-secondary" id="clearFilters">
                        <i class="fa fa-refresh me-2"></i>Clear Filters
                    </button>
                    <button class="btn btn-outline-primary ms-2" id="newSearch">
                        <i class="fa fa-search me-2"></i>New Search
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
                        <span id="resultsCount">0</span> results found for "<span id="currentSearchQuery"><?php echo htmlspecialchars($search_query); ?></span>"
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <div class="pagination-info">
                        Page <span id="currentPage">1</span> of <span id="totalPages">1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results Grid -->
        <div class="product-grid">
            <div class="row" id="searchResultsContainer">
                <!-- Loading state -->
                <div class="col-12 text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary spinner-large" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Searching products...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer">
            <nav aria-label="Search results pagination">
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
    <script>
        // Pass search parameters to JavaScript
        const SEARCH_QUERY = '<?php echo addslashes($search_query); ?>';
        const CATEGORY_FILTER = <?php echo $category_filter; ?>;
        const BRAND_FILTER = <?php echo $brand_filter; ?>;
    </script>
    <script src="js/search-results.js"></script>
</body>
</html>
