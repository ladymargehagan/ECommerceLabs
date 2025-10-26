<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/single_product.css" rel="stylesheet">
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

    <!-- Breadcrumb -->
    <div class="container" style="padding-top: 100px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>
    </div>

    <!-- Product Hero Section -->
    <div class="product-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    <p class="lead">Discover the authentic taste of Africa</p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-back" onclick="history.back()">
                        <i class="fa fa-arrow-left me-1"></i>Back to Products
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details -->
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="text-center">
                    <img src="../uploads/<?php echo htmlspecialchars($product['product_image'] ?: 'placeholder.png'); ?>" 
                         class="img-fluid product-image-large" 
                         alt="<?php echo htmlspecialchars($product['product_title']); ?>">
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="product-info">
                    <!-- Product ID -->
                    <div class="mb-3">
                        <span class="badge bg-primary fs-6">Product ID: <?php echo $product['product_id']; ?></span>
                    </div>

                    <!-- Price -->
                    <div class="product-price">
                        $<?php echo number_format($product['product_price'], 2); ?>
                    </div>

                    <!-- Product Meta -->
                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fa fa-tag meta-icon"></i>
                            <strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name']); ?>
                        </div>
                        <div class="meta-item">
                            <i class="fa fa-star meta-icon"></i>
                            <strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($product['product_desc'])): ?>
                        <div class="product-description">
                            <h5><i class="fa fa-info-circle me-2"></i>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Keywords -->
                    <?php if (!empty($product['product_keywords'])): ?>
                        <div class="keywords-section">
                            <h6><i class="fa fa-tags me-2"></i>Keywords</h6>
                            <?php 
                            $keywords = explode(',', $product['product_keywords']);
                            foreach ($keywords as $keyword): 
                                $keyword = trim($keyword);
                                if (!empty($keyword)):
                            ?>
                                <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Add to Cart -->
                    <div class="mt-4">
                        <button class="btn btn-add-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                            <i class="fa fa-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>

                    <!-- Additional Actions -->
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-outline-primary w-100" onclick="shareProduct()">
                                    <i class="fa fa-share me-1"></i>Share
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100" onclick="addToWishlist(<?php echo $product['product_id']; ?>)">
                                    <i class="fa fa-heart me-1"></i>Wishlist
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Placeholder) -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="text-center mb-4">
                    <i class="fa fa-star me-2"></i>You Might Also Like
                </h3>
                <div class="text-center">
                    <p class="text-muted">Related products will be displayed here in future updates.</p>
                    <a href="all_product.php" class="btn btn-primary">
                        <i class="fa fa-box me-1"></i>View All Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add to cart functionality
            window.addToCart = function(productId) {
                alert('Add to cart functionality will be implemented in the next phase. Product ID: ' + productId);
            };

            // Share product functionality
            window.shareProduct = function() {
                if (navigator.share) {
                    navigator.share({
                        title: '<?php echo addslashes($product['product_title']); ?>',
                        text: 'Check out this amazing product from Taste of Africa!',
                        url: window.location.href
                    });
                } else {
                    // Fallback for browsers that don't support Web Share API
                    const url = window.location.href;
                    navigator.clipboard.writeText(url).then(function() {
                        alert('Product link copied to clipboard!');
                    });
                }
            };

            // Add to wishlist functionality
            window.addToWishlist = function(productId) {
                alert('Wishlist functionality will be implemented in the next phase. Product ID: ' + productId);
            };

            // Image zoom effect
            $('.product-image-large').on('click', function() {
                $(this).toggleClass('zoomed');
            });
        });
    </script>
</body>
</html>
