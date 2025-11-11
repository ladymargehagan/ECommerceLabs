<?php
session_start();
header('Content-Type: application/json');

require_once '../controllers/cart_controller.php';

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

// Get product ID
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate product ID
if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

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

// Remove from cart
$result = $cartController->remove_from_cart_ctr($product_id, $customer_id, $ip_address);

// Get updated cart count
if ($result['success']) {
    $cart_count = $cartController->get_cart_count_ctr($customer_id, $ip_address);
    $result['cart_count'] = $cart_count;
}

echo json_encode($result);
?>

