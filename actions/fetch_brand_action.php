<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

// Start output buffering to catch any errors
ob_start();

try {
    require_once '../settings/db_class.php';
    require_once '../classes/brand_class.php';
    require_once '../controllers/brand_controller.php';

    session_start();

    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode(array('success' => false, 'message' => 'User not logged in'));
        exit;
    }

    if ($_SESSION['role'] != 1) {
        ob_clean();
        echo json_encode(array('success' => false, 'message' => 'Access denied. Admin privileges required.'));
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $brand_controller = new brand_controller();
    $result = $brand_controller->get_brands_by_user_ctr($user_id);
    
    ob_clean();
    echo json_encode($result);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode(array(
        'success' => false, 
        'message' => 'An error occurred: ' . $e->getMessage(),
        'data' => array()
    ));
} catch (Error $e) {
    ob_clean();
    echo json_encode(array(
        'success' => false, 
        'message' => 'A fatal error occurred: ' . $e->getMessage(),
        'data' => array()
    ));
}
?>
