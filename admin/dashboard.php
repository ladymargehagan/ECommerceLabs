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
    <title>Admin Dashboard - Flavo Spice Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Header -->
    <div class="nav-header">
        <div class="nav-links">
            <span class="brand">flavo</span>
            <a href="../index.php">HOME</a>
            <a href="#shop">SHOP</a>
            <a href="#story">OUR STORY</a>
            <a href="#contact">CONTACT US</a>
            <a href="../login/logout.php" class="btn btn-custom btn-sm">
                <i class="fa fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>

    <div class="container" style="padding-top: 160px;">
        <!-- Welcome Section -->
        <div class="main-container">
            <div class="hero-section">
                <h1><i class="fa fa-user-shield me-3"></i>ADMIN DASHBOARD</h1>
                <p class="subtitle">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                <p style="color: var(--text-light); font-weight: 500;">Manage your Flavo spice store from this control panel</p>
            </div>

            <!-- Dashboard Cards -->
            <div class="row mt-5">
                <!-- Categories Management -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card h-100">
                        <div class="mb-3">
                            <i class="fa fa-tags fa-3x" style="color: var(--accent-coral);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.3rem;">CATEGORIES</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Manage spice categories, add new blends, and organize your product catalog.</p>
                        <a href="category.php" class="btn-add-cart w-100">
                            <i class="fa fa-cog me-1"></i>MANAGE CATEGORIES
                        </a>
                    </div>
                </div>

                <!-- Products Management -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card h-100">
                        <div class="mb-3">
                            <i class="fa fa-pepper-hot fa-3x" style="color: var(--primary-green);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.3rem;">SPICE BLENDS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Add, edit, and manage your spice inventory. Control pricing and availability.</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-cog me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>

                <!-- Orders Management -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card h-100">
                        <div class="mb-3">
                            <i class="fa fa-shopping-cart fa-3x" style="color: var(--accent-orange);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.3rem;">ORDERS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">View and manage customer orders, track shipments and fulfillment.</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-cog me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>

                <!-- Users Management -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card h-100">
                        <div class="mb-3">
                            <i class="fa fa-users fa-3x" style="color: var(--accent-pink);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.3rem;">CUSTOMERS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Manage customer accounts, view user information and preferences.</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-cog me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>

                <!-- Analytics -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card h-100">
                        <div class="mb-3">
                            <i class="fa fa-chart-bar fa-3x" style="color: var(--secondary-green);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.3rem;">ANALYTICS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">View sales reports, customer insights and business analytics.</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-cog me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>

                <!-- Settings -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="product-card h-100">
                        <div class="mb-3">
                            <i class="fa fa-cogs fa-3x" style="color: var(--text-dark);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.3rem;">SETTINGS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Configure system settings and customize your store preferences.</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-cog me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
