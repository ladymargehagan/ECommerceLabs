<?php
require_once 'settings/core.php';
require_once 'controllers/product_controller.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: all_product.php');
    exit();
}

// Initialize controller
$product_controller = new product_controller();

// Get product details
$product_result = $product_controller->view_single_product_ctr($product_id);

if (!$product_result['success']) {
    header('Location: all_product.php');
    exit();
}

$product = $product_result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Taste of Africa</title>
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

    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <div class="product-image-container">
                    <img src="uploads/product/<?php echo htmlspecialchars($product['product_image'] ?: 'placeholder.png'); ?>" 
                         class="img-fluid rounded shadow" 
                         alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                         onerror="this.src='uploads/placeholder.png'">
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="product-title mb-3"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    
                    <div class="product-price mb-4">
                        <span class="h2 text-primary fw-bold">$<?php echo number_format($product['product_price'], 2); ?></span>
                    </div>

                    <div class="product-meta mb-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="meta-item">
                                    <strong>Product ID:</strong><br>
                                    <span class="text-muted"><?php echo $product['product_id']; ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="meta-item">
                                    <strong>Category:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($product['cat_name'] ?: 'No Category'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="meta-item">
                                    <strong>Brand:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($product['brand_name'] ?: 'No Brand'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($product['product_desc']): ?>
                        <div class="product-description mb-4">
                            <h5>Description</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($product['product_keywords']): ?>
                        <div class="product-keywords mb-4">
                            <h5>Keywords</h5>
                            <div class="keywords-container">
                                <?php 
                                $keywords = explode(',', $product['product_keywords']);
                                foreach ($keywords as $keyword): 
                                    $keyword = trim($keyword);
                                    if ($keyword):
                                ?>
                                    <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($keyword); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="product-actions">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                            <a href="all_product.php" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i>Back to Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Optional) -->
        <div class="row mt-5">
            <div class="col-12">
                <h3>Related Products</h3>
                <p class="text-muted">Check out other products in the same category</p>
                <div class="row">
                    <div class="col-12 text-center">
                        <a href="all_product.php?category=<?php echo $product['product_cat']; ?>" class="btn btn-outline-primary">
                            <i class="fa fa-tag me-2"></i>View More <?php echo htmlspecialchars($product['cat_name'] ?: 'Products'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId) {
            alert('Add to Cart functionality will be implemented in future labs. Product ID: ' + productId);
        }
    </script>
</body>
</html>