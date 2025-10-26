<?php
session_start();

$product_id = $_GET['id'] ?? '';
$product = null;

try {
    require_once 'classes/product_class.php';
    $product_class = new product_class();
    $product = $product_class->view_single_product($product_id);
} catch (Exception $e) {
    $product = null;
}

if (!$product) {
    echo "Product not found";
    exit;
}
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
    <link href="css/single_product.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa fa-home me-2"></i>Taste of Africa
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="all_product.php">All Products</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
                    <a class="nav-link" href="login/logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login/register.php">Register</a>
                    <a class="nav-link" href="login/login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 100px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">Products</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <img src="uploads/<?php echo htmlspecialchars($product['product_image'] ?: 'placeholder.png'); ?>" 
                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['product_title']); ?>">
            </div>

            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Product ID (hidden for add-to-cart) -->
                        <input type="hidden" id="productId" value="<?php echo $product['product_id']; ?>">
                        
                        <h1 class="card-title"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                        
                        <div class="mb-3">
                            <span class="badge bg-primary">Product ID: <?php echo $product['product_id']; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <h4 class="text-danger">$<?php echo number_format($product['product_price'], 2); ?></h4>
                        </div>
                        
                        <div class="mb-3">
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name']); ?></p>
                            <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?></p>
                        </div>
                        
                        <?php if (!empty($product['product_desc'])): ?>
                            <div class="mb-3">
                                <h5>Description</h5>
                                <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($product['product_keywords'])): ?>
                            <div class="mb-3">
                                <h5>Keywords</h5>
                                <p><?php echo htmlspecialchars($product['product_keywords']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid">
                            <button class="btn btn-success btn-lg" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                <i class="fa fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId) {
            alert('Add to cart functionality will be implemented in the next phase. Product ID: ' + productId);
        }
    </script>
</body>
</html>