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

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/index.css" rel="stylesheet">
    <style>
        .admin-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .dashboard-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .crud-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        
        .crud-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .crud-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .crud-card .card-body {
            padding: 1.5rem;
        }
        
        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .action-btn.disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .action-btn.disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .admin-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <div class="container">
            <!-- Admin Navigation -->
            <div class="admin-nav">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2><i class="fa fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
                        <p class="mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="../index.php" class="btn btn-outline-primary me-2">
                            <i class="fa fa-home me-1"></i>Home
                        </a>
                        <a href="../login/logout.php" class="btn btn-outline-danger">
                            <i class="fa fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="row">
                    <div class="col-md-8">
                        <h1><i class="fa fa-cogs me-2"></i>System Management</h1>
                        <p class="lead">Manage your e-commerce platform with ease. All CRUD operations are available below.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="stats-card">
                            <i class="fa fa-user-shield stats-icon text-primary"></i>
                            <h4>Administrator</h4>
                            <p class="text-muted">Full Access</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CRUD Operations -->
            <div class="row">
                <!-- Category Management -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-tags me-2"></i>Category Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Manage product categories for your store. Add, edit, or remove categories to organize your products.</p>
                            <div class="d-grid gap-2">
                                <a href="category.php" class="action-btn">
                                    <i class="fa fa-edit me-2"></i>Manage Categories
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Management -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-box me-2"></i>Product Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Add, edit, or remove products from your inventory. Manage product details, pricing, and availability.</p>
                            <div class="d-grid gap-2">
                                <a href="#" class="action-btn disabled">
                                    <i class="fa fa-tools me-2"></i>Coming Soon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Management -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-users me-2"></i>Customer Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">View and manage customer accounts. Handle customer information, orders, and support requests.</p>
                            <div class="d-grid gap-2">
                                <a href="#" class="action-btn disabled">
                                    <i class="fa fa-tools me-2"></i>Coming Soon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Management -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-shopping-cart me-2"></i>Order Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Track and manage customer orders. Update order status, process refunds, and handle shipping.</p>
                            <div class="d-grid gap-2">
                                <a href="#" class="action-btn disabled">
                                    <i class="fa fa-tools me-2"></i>Coming Soon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Management -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-warehouse me-2"></i>Inventory Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Monitor stock levels, set up low stock alerts, and manage inventory across multiple locations.</p>
                            <div class="d-grid gap-2">
                                <a href="#" class="action-btn disabled">
                                    <i class="fa fa-tools me-2"></i>Coming Soon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports & Analytics -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Reports & Analytics</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">View sales reports, customer analytics, and business insights to make informed decisions.</p>
                            <div class="d-grid gap-2">
                                <a href="#" class="action-btn disabled">
                                    <i class="fa fa-tools me-2"></i>Coming Soon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="crud-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="category.php" class="btn btn-outline-primary w-100">
                                        <i class="fa fa-plus me-2"></i>Add Category
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="#" class="btn btn-outline-secondary w-100 disabled">
                                        <i class="fa fa-plus me-2"></i>Add Product
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="#" class="btn btn-outline-info w-100 disabled">
                                        <i class="fa fa-eye me-2"></i>View Orders
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="#" class="btn btn-outline-warning w-100 disabled">
                                        <i class="fa fa-chart-line me-2"></i>View Reports
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Add smooth animations
            $('.crud-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
            
            // Disable click events on disabled buttons
            $('.action-btn.disabled, .btn.disabled').on('click', function(e) {
                e.preventDefault();
                return false;
            });
        });
    </script>
</body>
</html>
