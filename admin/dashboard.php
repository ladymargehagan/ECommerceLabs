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
    <div>
        <!-- Header -->
        <div>
            <div>
                <div>
                    <h2><i></i>Admin Dashboard</h2>
                    <div>
                        <a href="../login/logout.php">
                            <i></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Section -->
        <div>
            <div>
                <div>
                    <h3><i></i>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h3>
                    <p>Manage your e-commerce platform from this admin dashboard.</p>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div>
            <!-- Categories Management -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Categories</h5>
                        <p>Manage product categories, add new ones, edit existing categories, and organize your product catalog.</p>
                        <a href="category.php">
                            <i></i>Manage Categories
                        </a>
                    </div>
                </div>
            </div>

            <!-- Brands Management -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Brands</h5>
                        <p>Manage product brands, add new ones, edit existing brands, and organize your brand catalog.</p>
                        <a href="brand.php">
                            <i></i>Manage Brands
                        </a>
                    </div>
                </div>
            </div>

            <!-- Products Management -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Products</h5>
                        <p>Add, edit, and manage your product inventory. Control pricing, descriptions, and availability.</p>
                        <a href="product.php">
                            <i></i>Manage Products
                        </a>
                    </div>
                </div>
            </div>

            <!-- Orders Management -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Orders</h5>
                        <p>View and manage customer orders, track order status, and handle fulfillment.</p>
                        <button disabled>
                            <i></i>Coming Soon
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Management -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Users</h5>
                        <p>Manage user accounts, view customer information, and handle user permissions.</p>
                        <button disabled>
                            <i></i>Coming Soon
                        </button>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Analytics</h5>
                        <p>View sales reports, customer analytics, and business insights to make data-driven decisions.</p>
                        <button disabled>
                            <i></i>Coming Soon
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div>
                <div>
                    <div>
                        <div>
                            <i></i>
                        </div>
                        <h5>Settings</h5>
                        <p>Configure system settings, manage site preferences, and customize your e-commerce platform.</p>
                        <button disabled>
                            <i></i>Coming Soon
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
