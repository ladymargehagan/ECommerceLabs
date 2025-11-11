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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/cart.css" rel="stylesheet">

    
    
    
    
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
                <h1><i></i>Shopping Cart</h1>
                <p>Review your items before checkout</p>
            </div>
        </div>

        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div>
                <i></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="all_product.php">
                    <i></i>Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div>
                <!-- Cart Items -->
                <div>
                    <div>
                        <div>
                            <?php foreach ($cart_items as $item): ?>
                                <div data-product-id="<?php echo $item['p_id']; ?>">
                                    <div>
                                        <img src="<?php echo htmlspecialchars($item['product_image'] ?: 'uploads/placeholder.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                             onerror="this.src='uploads/placeholder.png'">
                                    </div>
                                    <div>
                                        <h5><?php echo htmlspecialchars($item['product_title']); ?></h5>
                                        <p>
                                            <i></i><?php echo htmlspecialchars($item['cat_name'] ?? 'No Category'); ?>
                                            <?php if ($item['brand_name']): ?>
                                                | <i></i><?php echo htmlspecialchars($item['brand_name']); ?>
                                            <?php endif; ?>
                                        </p>
                                        <p>
                                            <strong>$<?php echo number_format($item['product_price'], 2); ?></strong>
                                        </p>
                                    </div>
                                    <div>
                                        <div>
                                            <button type="button">
                                                <i></i>
                                            </button>
                                            <input type="number" 
                                                   value="<?php echo $item['qty']; ?>" 
                                                   min="1"
                                                   data-product-id="<?php echo $item['p_id']; ?>">
                                            <button type="button">
                                                <i></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <p><strong>$<?php echo number_format($item['qty'] * $item['product_price'], 2); ?></strong></p>
                                        <button data-product-id="<?php echo $item['p_id']; ?>">
                                            <i></i> Remove
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <button id="emptyCartBtn">
                            <i></i>Empty Cart
                        </button>
                        <a href="all_product.php">
                            <i></i>Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div>
                    <div>
                        <h4>Order Summary</h4>
                        
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

                        <?php if (!$customer_id): ?>
                            <div>
                                <i></i>
                                Please <a href="login/login.php">login</a> or <a href="login/register.php">register</a> to proceed to checkout.
                            </div>
                        <?php endif; ?>

                        <a href="checkout.php"disabled' : ''; ?>" 
                           <?php echo !$customer_id ? 'onclick="return false;"' : ''; ?>>
                            <i></i>Proceed to Checkout
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

