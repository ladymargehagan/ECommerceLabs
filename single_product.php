<?php
require_once 'settings/core.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: all_product.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Taste of Africa</title>
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

    <!-- Breadcrumb -->
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                <li class="breadcrumb-item active" id="breadcrumbProduct">Product Details</li>
            </ol>
        </nav>
    </div>

    <div class="container">
        <!-- Product Details Section -->
        <div class="product-details-section">
            <div class="row" id="productDetailsContainer">
                <!-- Loading state -->
                <div class="col-12 text-center py-5">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary spinner-large" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading product details...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <div class="related-products-section mt-5">
            <h3 class="mb-4">
                <i class="fa fa-heart text-danger me-2"></i>Related Products
            </h3>
            <div class="row" id="relatedProductsContainer">
                <!-- Related products will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Add to Cart Modal -->
    <div class="modal fade" id="addToCartModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add to Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <img id="cartProductImage" src="" alt="Product" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h6 id="cartProductTitle"></h6>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" value="1" min="1" max="99">
                            </div>
                            <div class="mb-3">
                                <strong>Price: $<span id="cartProductPrice"></span></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAddToCart">
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
        // Pass product ID to JavaScript
        const PRODUCT_ID = <?php echo $product_id; ?>;
    </script>
    <script src="js/single-product.js"></script>
</body>
</html>
