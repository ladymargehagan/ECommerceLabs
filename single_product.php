<?php
require_once 'settings/core.php';
require_once 'actions/product_actions.php';

$product_actions = new product_actions();

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: all_product.php");
    exit;
}

// Get single product
$product_result = $product_actions->get_single_product($product_id);

if (!$product_result['success']) {
    header("Location: all_product.php");
    exit;
}

$product = $product_result['data'];
$imageSrc = !empty($product['product_image']) ? $product['product_image'] : 'uploads/placeholder.png';
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
    <style>
        .product-image {
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .product-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
        }
        .price-display {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <h2><i class="fa fa-box me-2"></i>Product Details</h2>
                    <div>
                        <a href="all_product.php" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-1"></i>Back to Products
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

        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="all_product.php">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Product Details -->
        <div class="row">
            <div class="col-md-6">
                <div class="text-center">
                    <img src="<?php echo htmlspecialchars($imageSrc); ?>" class="img-fluid product-image" alt="<?php echo htmlspecialchars($product['product_title']); ?>" onerror="this.src='uploads/placeholder.png'">
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="mb-3"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    
                    <div class="price-display mb-4">
                        $<?php echo number_format($product['product_price'], 2); ?>
                    </div>

                    <div class="mb-4">
                        <h5>Product Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Product ID:</strong></td>
                                <td><?php echo $product['product_id']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td><?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Brand:</strong></td>
                                <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <?php if(!empty($product['product_desc'])): ?>
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($product['product_keywords'])): ?>
                        <div class="mb-4">
                            <h5>Keywords</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $keywords = explode(',', $product['product_keywords']);
                                foreach($keywords as $keyword): 
                                    $keyword = trim($keyword);
                                    if(!empty($keyword)):
                                ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($keyword); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button class="btn btn-custom btn-lg" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                            <i class="fa fa-cart-plus me-2"></i>Add to Cart
                        </button>
                        <button class="btn btn-outline-primary btn-lg" onclick="addToWishlist(<?php echo $product['product_id']; ?>)">
                            <i class="fa fa-heart me-2"></i>Add to Wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (placeholder) -->
        <div class="row mt-5">
            <div class="col-12">
                <h3>Related Products</h3>
                <p class="text-muted">Related products functionality will be implemented in future labs.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function addToCart(productId) {
            alert('Add to Cart functionality will be implemented in future labs. Product ID: ' + productId);
        }

        function addToWishlist(productId) {
            alert('Add to Wishlist functionality will be implemented in future labs. Product ID: ' + productId);
        }
    </script>
</body>
</html>