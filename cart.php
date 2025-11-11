<?php
session_start();

require_once 'controllers/cart_controller.php';
require_once 'controllers/product_controller.php';

// Get user information (guest or logged-in)
$customer_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$ip_address = null;

// Get IP address for guest users
if (!$customer_id) {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
}

// Initialize cart controller
$cartController = new cart_controller();

// Get cart items
$cart_items = $cartController->get_user_cart_ctr($customer_id, $ip_address);

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
    <title>Shopping Cart - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/cart.css" rel="stylesheet">
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
                <h1><i class="fa fa-shopping-cart me-2"></i>Shopping Cart</h1>
                <p class="text-muted">Review your items before checkout</p>
            </div>
        </div>

        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart">
                <i class="fa fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
                <a href="all_product.php" class="btn btn-custom btn-lg mt-3">
                    <i class="fa fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item row align-items-center" data-product-id="<?php echo $item['p_id']; ?>">
                                    <div class="col-md-2">
                                        <img src="<?php echo htmlspecialchars($item['product_image'] ?: 'uploads/placeholder.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                             class="product-image-cart"
                                             onerror="this.src='uploads/placeholder.png'">
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($item['product_title']); ?></h5>
                                        <p class="text-muted small mb-0">
                                            <i class="fa fa-tag me-1"></i><?php echo htmlspecialchars($item['cat_name'] ?? 'No Category'); ?>
                                            <?php if ($item['brand_name']): ?>
                                                | <i class="fa fa-star me-1"></i><?php echo htmlspecialchars($item['brand_name']); ?>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-primary mb-0 mt-1">
                                            <strong>$<?php echo number_format($item['product_price'], 2); ?></strong>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary btn-sm quantity-decrease" type="button">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   class="form-control quantity-input" 
                                                   value="<?php echo $item['qty']; ?>" 
                                                   min="1"
                                                   data-product-id="<?php echo $item['p_id']; ?>">
                                            <button class="btn btn-outline-secondary btn-sm quantity-increase" type="button">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <p class="mb-1"><strong>$<?php echo number_format($item['qty'] * $item['product_price'], 2); ?></strong></p>
                                        <button class="btn btn-sm btn-outline-danger remove-item" data-product-id="<?php echo $item['p_id']; ?>">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-outline-danger" id="emptyCartBtn">
                            <i class="fa fa-trash me-2"></i>Empty Cart
                        </button>
                        <a href="all_product.php" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4 class="mb-4">Order Summary</h4>
                        
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
                            <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                        </div>

                        <?php if (!$customer_id): ?>
                            <div class="alert alert-warning mb-3">
                                <i class="fa fa-info-circle me-2"></i>
                                Please <a href="login/login.php">login</a> or <a href="login/register.php">register</a> to proceed to checkout.
                            </div>
                        <?php endif; ?>

                        <a href="checkout.php" class="btn btn-custom btn-lg w-100 <?php echo !$customer_id ? 'disabled' : ''; ?>" 
                           <?php echo !$customer_id ? 'onclick="return false;"' : ''; ?>>
                            <i class="fa fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/cart.js"></script>
</body>
</html>

