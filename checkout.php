<?php
session_start();

// Check if user is logged in (required for checkout)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login/login.php");
    exit;
}

require_once 'controllers/cart_controller.php';

$customer_id = (int)$_SESSION['user_id'];

// Initialize cart controller
$cartController = new cart_controller();

// Get cart items
$cart_items = $cartController->get_user_cart_ctr($customer_id);

// Check if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

// Calculate totals
$subtotal = 0;
$item_count = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['qty'] * $item['product_price'];
    $item_count += $item['qty'];
}
$tax = $subtotal * 0.1; // 10% tax (example)
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css?v=2.0" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <style>
        .checkout-item {
            border-bottom: 1px solid #e9ecef;
            padding: 15px 0;
        }
        .checkout-item:last-child {
            border-bottom: none;
        }
        .product-image-checkout {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .checkout-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            position: sticky;
            top: 120px;
        }
        .payment-section {
            background: #fff;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
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
            <a href="login/login.php" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-sign-in-alt me-1"></i>Login
            </a>
        <?php endif; ?>
    </div>

    <div class="container" style="padding-top: 120px;">
        <div class="row mb-4">
            <div class="col-12">
                <h1><i class="fa fa-credit-card me-2"></i>Checkout</h1>
                <p class="text-muted">Complete your purchase</p>
            </div>
        </div>

        <div class="row">
            <!-- Order Summary -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-shopping-cart me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="checkout-item row align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['product_image'] ?: 'uploads/placeholder.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                         class="product-image-checkout"
                                         onerror="this.src='uploads/placeholder.png'">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['product_title']); ?></h6>
                                    <p class="text-muted small mb-0">
                                        <i class="fa fa-tag me-1"></i><?php echo htmlspecialchars($item['cat_name'] ?? 'No Category'); ?>
                                        <?php if ($item['brand_name']): ?>
                                            | <i class="fa fa-star me-1"></i><?php echo htmlspecialchars($item['brand_name']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-secondary">Qty: <?php echo $item['qty']; ?></span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong>$<?php echo number_format($item['qty'] * $item['product_price'], 2); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-user me-2"></i>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['country'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['city'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Contact</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['contact'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="payment-section">
                    <i class="fa fa-credit-card fa-3x text-primary mb-3"></i>
                    <h4>Simulated Payment</h4>
                    <p class="text-muted">This is a demo checkout. Click the button below to simulate payment processing.</p>
                    <button class="btn btn-custom btn-lg" id="simulatePaymentBtn">
                        <i class="fa fa-check-circle me-2"></i>Simulate Payment
                    </button>
                </div>
            </div>

            <!-- Order Total -->
            <div class="col-lg-4">
                <div class="checkout-summary">
                    <h4 class="mb-4">Order Total</h4>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Items (<?php echo $item_count; ?>):</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (10%):</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total:</strong>
                        <strong class="text-primary fs-4">$<?php echo number_format($total, 2); ?></strong>
                    </div>

                    <a href="cart.php" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fa fa-arrow-left me-2"></i>Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/checkout.js"></script>
</body>
</html>

