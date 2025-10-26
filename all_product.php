<?php
session_start();
require_once 'controllers/product_controller.php';

$product_controller = new product_controller();
$result = $product_controller->view_all_products_ctr();
$products = $result['success'] ? $result['data'] : array();
$categories = $product_controller->get_categories_ctr()['data'];
$brands = $product_controller->get_brands_ctr()['data'];
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
    <link href="css/product_display.css" rel="stylesheet">
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
                <a class="nav-link active" href="all_product.php">All Products</a>
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
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="fa fa-box me-2"></i>All Products</h2>
                
                <!-- Search Box -->
                <form class="d-flex mb-3" method="GET" action="actions/product_actions.php">
                    <input type="hidden" name="action" value="search_products">
                    <input class="form-control me-2" type="search" name="query" placeholder="Search products..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
                
                <!-- Category and Brand Filters -->
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-select" onchange="filterByCategory(this.value)">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['cat_id']; ?>">
                                    <?php echo htmlspecialchars($category['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" onchange="filterByBrand(this.value)">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>">
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100">
                            <img src="uploads/<?php echo htmlspecialchars($product['product_image'] ?: 'placeholder.png'); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['product_title']); ?></h5>
                                <p class="text-muted">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name']); ?><br>
                                    <strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?>
                                </p>
                                <div class="mt-auto">
                                    <div class="product-price mb-3">$<?php echo number_format($product['product_price'], 2); ?></div>
                                    <div class="d-grid gap-2">
                                        <a href="actions/product_actions.php?action=view_single_product&id=<?php echo $product['product_id']; ?>" 
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
                <div class="col-12 text-center py-5">
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your search criteria or filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterByCategory(catId) {
            if (catId) {
                window.location.href = 'actions/product_actions.php?action=filter_by_category&cat_id=' + catId;
            } else {
                window.location.href = 'actions/product_actions.php?action=view_all_products';
            }
        }

        function filterByBrand(brandId) {
            if (brandId) {
                window.location.href = 'actions/product_actions.php?action=filter_by_brand&brand_id=' + brandId;
            } else {
                window.location.href = 'actions/product_actions.php?action=view_all_products';
            }
        }

        function addToCart(productId) {
            alert('Add to cart functionality will be implemented in the next phase. Product ID: ' + productId);
        }
    </script>
</body>
</html>