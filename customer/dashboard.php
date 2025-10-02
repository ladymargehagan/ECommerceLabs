<?php
// Fix session permission issues by setting custom session path
$sessionPath = dirname(__DIR__) . '/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0755, true);
}
ini_set('session.save_path', $sessionPath);
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

// Check if user is a regular customer (not admin)
if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
    header('Location: ../admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Dashboard - Flavo Spice Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/index.css" rel="stylesheet">
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
        <div class="main-container">
            <div class="hero-section">
                <h1><i class="fa fa-user me-3"></i>CUSTOMER DASHBOARD</h1>
                <p class="subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                <p style="color: var(--text-light); font-weight: 500;">Manage your spice collection and orders</p>
            </div>

            <!-- Account Information -->
            <div class="user-info mt-4">
                <h4><i class="fa fa-user-circle me-2"></i>Your Account Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($_SESSION['contact']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['country']); ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('F Y', $_SESSION['login_time']); ?></p>
                        <p><strong>Account Type:</strong> <span class="badge" style="background: var(--accent-pink); color: var(--text-dark);">Customer</span></p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-5">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="product-card">
                        <div class="mb-3">
                            <i class="fa fa-shopping-bag fa-3x" style="color: var(--primary-green);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.2rem;">SHOP SPICES</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Browse our collection of premium spice blends</p>
                        <a href="../index.php" class="btn-add-cart w-100">
                            <i class="fa fa-shopping-bag me-1"></i>START SHOPPING
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="product-card">
                        <div class="mb-3">
                            <i class="fa fa-box fa-3x" style="color: var(--accent-coral);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.2rem;">MY ORDERS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Track your spice orders and delivery status</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-box me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="product-card">
                        <div class="mb-3">
                            <i class="fa fa-heart fa-3x" style="color: var(--accent-pink);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.2rem;">WISHLIST</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Save your favorite spice blends for later</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-heart me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="product-card">
                        <div class="mb-3">
                            <i class="fa fa-cog fa-3x" style="color: var(--accent-orange);"></i>
                        </div>
                        <h3 class="product-title" style="font-size: 1.2rem;">SETTINGS</h3>
                        <p style="color: var(--text-light); margin: 15px 0;">Manage your account preferences and details</p>
                        <button class="btn-add-cart w-100" disabled style="opacity: 0.6;">
                            <i class="fa fa-cog me-1"></i>COMING SOON
                        </button>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div style="background: var(--warm-cream); border: 2px solid var(--primary-green); border-radius: 20px; padding: 30px; margin-top: 30px; text-align: center;">
                <i class="fa fa-info-circle fa-2x mb-3" style="color: var(--primary-green);"></i>
                <h4 style="color: var(--text-dark); font-weight: 700;">Welcome to Your Spice Journey!</h4>
                <p style="color: var(--text-light); margin-bottom: 0;">This is your personal dashboard where you can manage your account, track orders, and discover new flavors. More exciting features are coming soon!</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
