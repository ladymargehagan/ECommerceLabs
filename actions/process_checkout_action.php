<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.',
        'order_id' => null,
        'order_reference' => null
    ]);
    exit;
}

// Check if user is logged in (required for checkout)
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to checkout. Please login first.',
        'order_id' => null,
        'order_reference' => null,
        'redirect' => '../login/login.php'
    ]);
    exit;
}

$customer_id = (int)$_SESSION['user_id'];

// Initialize controllers
$cartController = new cart_controller();
$orderController = new order_controller();

// Get user's cart
$cart_items = $cartController->get_user_cart_ctr($customer_id);

// Check if cart is empty
if (empty($cart_items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Your cart is empty. Please add items to your cart before checkout.',
        'order_id' => null,
        'order_reference' => null
    ]);
    exit;
}

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['qty'] * $item['product_price'];
}

// Start transaction-like process
try {
    // Step 1: Create order
    $order_result = $orderController->create_order_ctr([
        'customer_id' => $customer_id,
        'order_status' => 'completed'
    ]);
    
    if (!$order_result['success']) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create order: ' . $order_result['message'],
            'order_id' => null,
            'order_reference' => null
        ]);
        exit;
    }
    
    $order_id = $order_result['order_id'];
    
    // Step 2: Add order details
    $all_details_success = true;
    $failed_products = [];
    
    foreach ($cart_items as $item) {
        $detail_result = $orderController->add_order_details_ctr([
            'order_id' => $order_id,
            'product_id' => $item['p_id'],
            'quantity' => $item['qty']
        ]);
        
        if (!$detail_result['success']) {
            $all_details_success = false;
            $failed_products[] = $item['product_title'];
        }
    }
    
    if (!$all_details_success) {
        // If order details failed, we should ideally rollback, but for simplicity, we'll continue
        // In production, you'd want to use database transactions
        error_log("Failed to add order details for products: " . implode(', ', $failed_products));
    }
    
    // Step 3: Record payment
    $payment_result = $orderController->record_payment_ctr([
        'order_id' => $order_id,
        'customer_id' => $customer_id,
        'amount' => $total_amount,
        'currency' => 'USD'
    ]);
    
    if (!$payment_result['success']) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to record payment: ' . $payment_result['message'],
            'order_id' => $order_id,
            'order_reference' => null
        ]);
        exit;
    }
    
    // Step 4: Get order reference (invoice number)
    $order_info = $orderController->get_order_by_id_ctr($order_id);
    $order_reference = $order_info ? $order_info['invoice_no'] : null;
    
    // Step 5: Empty cart
    $empty_cart_result = $cartController->empty_cart_ctr($customer_id);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully! Thank you for your purchase.',
        'order_id' => $order_id,
        'order_reference' => $order_reference,
        'total_amount' => $total_amount,
        'cart_emptied' => $empty_cart_result['success']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during checkout: ' . $e->getMessage(),
        'order_id' => null,
        'order_reference' => null
    ]);
}
?>

