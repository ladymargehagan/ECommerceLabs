<?php
session_start();

require_once '../controllers/customer_controller.php';

header('Content-Type: application/json');
// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.',
        'redirect' => null
    ]);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validate required fields
if (empty($email) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and password are required.',
        'redirect' => null
    ]);
    exit;
}

// Initialize customer controller
$customerController = new CustomerController();

// Prepare kwargs array
$kwargs = [
    'email' => $email,
    'password' => $password
];

// Attempt login
$result = $customerController->login_customer_ctr($kwargs);

if ($result['success']) {
    // Login successful - set session variables
    $customerData = $result['data'];
    
    $_SESSION['user_id'] = $customerData['customer_id'];
    $_SESSION['role'] = $customerData['user_role'];
    $_SESSION['name'] = $customerData['customer_name'];
    $_SESSION['email'] = $customerData['customer_email'];
    $_SESSION['country'] = $customerData['customer_country'];
    $_SESSION['city'] = $customerData['customer_city'];
    $_SESSION['contact'] = $customerData['customer_contact'];
    $_SESSION['image'] = $customerData['customer_image'];
    $_SESSION['login_time'] = time();
    
    // Set session timeout (24 hours)
    $_SESSION['timeout'] = time() + (24 * 60 * 60);
    
    // Merge guest cart into user cart (hybrid cart approach)
    require_once '../controllers/cart_controller.php';
    $cartController = new cart_controller();
    
    // Get IP address
    $ip_address = null;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    
    // Merge guest cart
    $merge_result = $cartController->merge_guest_cart_ctr($ip_address, $customerData['customer_id']);
    $cart_merged = $merge_result['success'];
    $merge_message = $merge_result['message'];
    
    // Determine redirect based on user role
    $redirectUrl = '../index.php'; // Default redirect
    
    // Check if there's a redirect after login (e.g., from checkout)
    if (isset($_SESSION['redirect_after_login'])) {
        $redirectUrl = '../' . $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
    } elseif ($customerData['user_role'] == 1) {
        // Admin user
        $redirectUrl = '../admin/dashboard.php';
    } elseif ($customerData['user_role'] == 2) {
        // Regular customer
        $redirectUrl = '../index.php';
    }
    
    $login_message = 'Login successful! Welcome back, ' . $customerData['customer_name'] . '!';
    if ($cart_merged && strpos($merge_message, 'merged') !== false) {
        $login_message .= ' ' . $merge_message;
    }
    
    echo json_encode([
        'success' => true,
        'message' => $login_message,
        'redirect' => $redirectUrl,
        'cart_merged' => $cart_merged,
        'user_data' => [
            'id' => $customerData['customer_id'],
            'name' => $customerData['customer_name'],
            'email' => $customerData['customer_email'],
            'role' => $customerData['user_role']
        ]
    ]);
} else {
    // Login failed
    echo json_encode([
        'success' => false,
        'message' => $result['message'],
        'redirect' => null
    ]);
}
?>
