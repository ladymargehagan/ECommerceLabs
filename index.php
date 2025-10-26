<?php
// Start session to check if user is logged in
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>
<body>
    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
            <?php if ($_SESSION['role'] == 1): // Admin users ?>
                <a href="admin/dashboard.php" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fa fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a href="admin/category.php" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fa fa-tags me-1"></i>Category
                </a>
                <a href="admin/brand.php" class="btn btn-sm btn-outline-warning me-2">
                    <i class="fa fa-star me-1"></i>Brand
                </a>
                <a href="admin/product.php" class="btn btn-sm btn-outline-success me-2">
                    <i class="fa fa-plus me-1"></i>Add Product
                </a>
            <?php endif; ?>
            <a href="customer/dashboard.php" class="btn btn-sm btn-outline-info me-2">
                <i class="fa fa-user me-1"></i>My Account
            </a>
            <a href="login/logout.php" class="btn btn-sm btn-outline-danger">
                <i class="fa fa-sign-out-alt me-1"></i>Logout
            </a>
        <?php else: ?>
            <span class="me-2">Menu:</span>
            <a href="login/register.php" class="btn btn-sm btn-outline-primary me-2">
                <i class="fa fa-user-plus me-1"></i>Register
            </a>
            <a href="login/login.php" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fa fa-sign-in-alt me-1"></i>Login
            </a>
        <?php endif; ?>
        
        <!-- All Products Link -->
        <a href="all_product.php" class="btn btn-sm btn-outline-primary me-2">
            <i class="fa fa-box me-1"></i>All Products
        </a>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-section" style="background: #f8f9fa; padding: 20px 0; margin-top: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center mb-4"><i class="fa fa-search me-2"></i>Search Products</h4>
                    <form method="GET" action="product_search_result.php" id="searchForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="q" id="searchQuery" 
                                           placeholder="Search by product name, description, or keywords..." 
                                           value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                                    <button class="btn btn-custom" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="category" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <!-- Categories will be loaded via JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="brand" id="brandFilter">
                                    <option value="">All Brands</option>
                                    <!-- Brands will be loaded via JavaScript -->
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top: 20px;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Logged in user content -->
            <div class="welcome-card text-center">
                <h1><i class="fa fa-home me-2"></i>Welcome to Taste of Africa!</h1>
                <p class="lead">You have successfully logged in to your account.</p>
                
                <div class="user-info">
                    <h4><i class="fa fa-user me-2"></i>Your Account Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['country']); ?></p>
                            <p><strong>Role:</strong> 
                                <?php 
                                if ($_SESSION['role'] == 1) {
                                    echo '<span class="badge bg-warning">Administrator</span>';
                                } else {
                                    echo '<span class="badge bg-info">Customer</span>';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="all_product.php" class="btn btn-custom btn-lg me-3">
                        <i class="fa fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                    <a href="customer/dashboard.php" class="btn btn-outline-light btn-lg">
                        <i class="fa fa-user-edit me-2"></i>My Account
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Guest user content -->
            <div class="text-center">
                <h1><i class="fa fa-home me-2"></i>Welcome to Taste of Africa</h1>
                <p class="lead text-muted">Discover the authentic flavors of Africa</p>
                <p class="text-muted">Please login or register to access your account and start shopping.</p>
                
                <div class="mt-4">
                    <a href="login/login.php" class="btn btn-custom btn-lg me-3">
                        <i class="fa fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="login/register.php" class="btn btn-outline-primary btn-lg">
                        <i class="fa fa-user-plus me-2"></i>Register
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/product-search.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load categories and brands dynamically
            loadCategoriesAndBrands();
            
            // Show welcome message if just logged in
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('login') === 'success') {
                console.log('Login successful!');
            }

            // Search form validation
            $('#searchForm').submit(function(e) {
                const query = $('#searchQuery').val().trim();
                if (query.length === 0) {
                    e.preventDefault();
                    alert('Please enter a search term.');
                    $('#searchQuery').focus();
                }
            });
        });

        function loadCategoriesAndBrands() {
            // Load categories
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_categories' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">All Categories</option>';
                        response.data.forEach(function(category) {
                            options += '<option value="' + category.cat_id + '">' + category.cat_name + '</option>';
                        });
                        $('#categoryFilter').html(options);
                    }
                }
            });

            // Load brands
            $.ajax({
                url: 'product_actions.php',
                method: 'GET',
                data: { action: 'get_brands' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let options = '<option value="">All Brands</option>';
                        response.data.forEach(function(brand) {
                            options += '<option value="' + brand.brand_id + '">' + brand.brand_name + '</option>';
                        });
                        $('#brandFilter').html(options);
                    }
                }
            });
        }
    </script>
</body>
</html>
