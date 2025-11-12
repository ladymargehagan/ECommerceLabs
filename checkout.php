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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/checkout.css" rel="stylesheet">

    
    
    
    
</head>
<body>
    <!-- Navigation -->
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                <a href="admin/dashboard.php">
                    <i></i>Dashboard
                </a>
                <a href="admin/category.php">
                    <i></i>Category
                </a>
                <a href="admin/brand.php">
                    <i></i>Brand
                </a>
                <a href="admin/product.php">
                    <i></i>Add Product
                </a>
            <?php endif; ?>
            <a href="customer/dashboard.php">
                <i></i>My Account
            </a>
            <a href="login/logout.php">
                <i></i>Logout
            </a>
        <?php else: ?>
            <span>Menu:</span>
            <a href="login/register.php">
                <i></i>Register
            </a>
            <a href="login/login.php">
                <i></i>Login
            </a>
        <?php endif; ?>
    </div>

    <div>
        <div>
            <div>
                <h1><i></i>Checkout</h1>
                <p>Complete your purchase</p>
            </div>
        </div>

        <div>
            <!-- Order Summary -->
            <div>
                <div>
                    <div>
                        <h5><i></i>Order Summary</h5>
                    </div>
                    <div>
                        <?php foreach ($cart_items as $item): ?>
                            <div>
                                <div>
                                    <img src="<?php echo htmlspecialchars($item['product_image'] ?: 'uploads/placeholder.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                         onerror="this.src='uploads/placeholder.png'">
                                </div>
                                <div>
                                    <h6><?php echo htmlspecialchars($item['product_title']); ?></h6>
                                    <p>
                                        <i></i><?php echo htmlspecialchars($item['cat_name'] ?? 'No Category'); ?>
                                        <?php if ($item['brand_name']): ?>
                                            | <i></i><?php echo htmlspecialchars($item['brand_name']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div>
                                    <span>Qty: <?php echo $item['qty']; ?></span>
                                </div>
                                <div>
                                    <strong>$<?php echo number_format($item['qty'] * $item['product_price'], 2); ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div>
                    <div>
                        <h5><i></i>Customer Information</h5>
                    </div>
                    <div>
                        <div>
                            <div>
                                <label>Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" readonly>
                            </div>
                            <div>
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                            </div>
                            <div>
                                <label>Country</label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['country'] ?? ''); ?>" readonly>
                            </div>
                            <div>
                                <label>City</label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['city'] ?? ''); ?>" readonly>
                            </div>
                            <div>
                                <label>Contact</label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['contact'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div>
                    <i></i>
                    <h4>Simulated Payment</h4>
                    <p>This is a demo checkout. Click the button below to simulate payment processing.</p>
                    <button id="simulatePaymentBtn">
                        <i></i>Simulate Payment
                    </button>
                </div>
            </div>

            <!-- Order Total -->
            <div>
                <div>
                    <h4>Order Total</h4>
                    
                    <div>
                        <span>Items (<?php echo $item_count; ?>):</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div>
                        <span>Tax (10%):</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div>
                        <strong>Total:</strong>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </div>

                    <a href="cart.php">
                        <i></i>Back to Cart
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

