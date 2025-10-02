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
    <title>Customer Dashboard - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/index.css" rel="stylesheet">
</head>
<body>
    <div class="container" style="padding-top: 120px;">
        <!-- Customer Navigation -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="fa fa-user me-2"></i>Customer Dashboard</h4>
                    <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                </div>
                <div>
                    <a href="../index.php" class="btn btn-outline-primary me-2">
                        <i class="fa fa-home me-1"></i>Home
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-danger">
                        <i class="fa fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fa fa-user-circle me-2"></i>Your Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($_SESSION['contact']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['country']); ?></p>
                                <p><strong>Member Since:</strong> <?php echo date('F Y', $_SESSION['login_time']); ?></p>
                                <p><strong>Account Type:</strong> <span class="badge bg-info">Customer</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fa fa-shopping-cart me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="../index.php" class="btn btn-primary">
                                <i class="fa fa-shopping-bag me-2"></i>Start Shopping
                            </a>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fa fa-box me-2"></i>My Orders
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <i class="fa fa-heart me-2"></i>Wishlist
                            </a>
                            <a href="#" class="btn btn-outline-warning">
                                <i class="fa fa-cog me-2"></i>Account Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fa fa-clock me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            Welcome to your customer dashboard! This is where you can manage your account, view orders, and access your shopping features.
                        </div>
                        <p class="text-muted">More features coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
