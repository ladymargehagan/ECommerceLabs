<?php
require_once '../settings/core.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SESSION['role'] != 1) {
    header("Location: ../login/login.php");
    exit;
}

$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Taste of Africa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">

    
    
    
</head>
<body>
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="admin-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
                    <div>
                        <a href="../login/logout.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="container py-3">
            <div class="welcome-card">
                <h3><i class="fas fa-user-circle me-2"></i>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h3>
                <p class="lead">Manage your e-commerce platform from this admin dashboard.</p>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="container">
            <div class="row">
                <!-- Categories Management -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-tags fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Categories</h5>
                            <p class="card-text">Manage product categories, add new ones, edit existing categories, and organize your product catalog.</p>
                            <a href="category.php" class="btn w-100">
                                <i class="fas fa-cog me-1"></i>Manage Categories
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Brands Management -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-tag fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Brands</h5>
                            <p class="card-text">Manage product brands, add new ones, edit existing brands, and organize your brand catalog.</p>
                            <a href="brand.php" class="btn w-100">
                                <i class="fas fa-cog me-1"></i>Manage Brands
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Products Management -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-box fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Products</h5>
                            <p class="card-text">Add, edit, and manage your product inventory. Control pricing, descriptions, and availability.</p>
                            <a href="product.php" class="btn w-100">
                                <i class="fas fa-cog me-1"></i>Manage Products
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Orders Management -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-shopping-cart fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Orders</h5>
                            <p class="card-text">View and manage customer orders, track order status, and handle fulfillment.</p>
                            <button class="btn w-100" disabled>
                                <i class="fas fa-clock me-1"></i>Coming Soon
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Management -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Users</h5>
                            <p class="card-text">Manage user accounts, view customer information, and handle user permissions.</p>
                            <button class="btn w-100" disabled>
                                <i class="fas fa-clock me-1"></i>Coming Soon
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-chart-line fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Analytics</h5>
                            <p class="card-text">View sales reports, customer analytics, and business insights to make data-driven decisions.</p>
                            <button class="btn w-100" disabled>
                                <i class="fas fa-clock me-1"></i>Coming Soon
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-cog fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Settings</h5>
                            <p class="card-text">Configure system settings, manage site preferences, and customize your e-commerce platform.</p>
                            <button class="btn w-100" disabled>
                                <i class="fas fa-clock me-1"></i>Coming Soon
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
