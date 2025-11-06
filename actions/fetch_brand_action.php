<?php
require_once '../settings/db_class.php';
require_once '../classes/brand_class.php';
require_once '../controllers/brand_controller.php';

// Set JSON header first
header('Content-Type: application/json');

// Start session
session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'User not logged in', 'data' => array()));
    exit;
}

// Check admin role
if ($_SESSION['role'] != 1) {
    echo json_encode(array('success' => false, 'message' => 'Access denied. Admin privileges required.', 'data' => array()));
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    $brand_controller = new brand_controller();
    $result = $brand_controller->get_brands_by_user_ctr($user_id);
    
    // Ensure result has the expected structure
    if (!isset($result['success'])) {
        $result = array('success' => false, 'message' => 'Unexpected response format', 'data' => array());
    }
    
    // Ensure data is always an array
    if (!isset($result['data']) || !is_array($result['data'])) {
        $result['data'] = array();
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    error_log("Fetch brands error: " . $e->getMessage());
    echo json_encode(array(
        'success' => false, 
        'message' => 'An error occurred while fetching brands: ' . $e->getMessage(),
        'data' => array()
    ));
} catch (Error $e) {
    error_log("Fetch brands fatal error: " . $e->getMessage());
    echo json_encode(array(
        'success' => false, 
        'message' => 'A fatal error occurred while fetching brands',
        'data' => array()
    ));
}
?>
