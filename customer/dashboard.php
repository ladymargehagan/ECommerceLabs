<?php
// Start session (using default session path to match index.php)
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

// Allow both admin and regular customers to view their account
// Admin users can still access this page to see their account info
// If you want to redirect admins to admin dashboard, uncomment below:
// if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
//     header('Location: ../admin/dashboard.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Dashboard - Taste of Africa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teachers:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <div>
        <!-- Customer Navigation -->
        <div>
            <div>
                <div>
                    <h4><i></i>My Account</h4>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                </div>
                <div>
                    <a href="../index.php">
                        <i></i>Home
                    </a>
                    <a href="../login/logout.php">
                        <i></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div>
            <div>
                <div>
                    <div>
                        <h5><i></i>Your Account Information</h5>
                    </div>
                    <div>
                        <div>
                            <div>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                                <?php if (isset($_SESSION['contact'])): ?>
                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($_SESSION['contact']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['country']); ?></p>
                                <?php if (isset($_SESSION['login_time'])): ?>
                                    <p><strong>Member Since:</strong> <?php echo date('F Y', $_SESSION['login_time']); ?></p>
                                <?php endif; ?>
                                <p><strong>Role:</strong> 
                                    <?php 
                                    if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
                                        echo '<span>Administrator</span>';
                                    } else {
                                        echo '<span>Customer</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div>
            <div>
                <div>
                    <div>
                        <h5><i></i>Quick Actions</h5>
                    </div>
                    <div>
                        <div>
                            <a href="../index.php">
                                <i></i>Start Shopping
                            </a>
                            <a href="#">
                                <i></i>My Orders
                            </a>
                            <a href="#">
                                <i></i>Wishlist
                            </a>
                            <a href="#">
                                <i></i>Account Settings
                            </a>
                        </div>
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
