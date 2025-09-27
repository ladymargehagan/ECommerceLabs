<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.',
        'data' => null
    ]);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin privileges required.',
        'data' => null
    ]);
    exit;
}

//Checking if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.',
        'data' => null
    ]);
    exit;
}

//Required files
require_once '../controllers/category_controller.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    //Input validation
    if (!isset($input['cat_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Category ID is required.',
            'data' => null
        ]);
        exit;
    }
    
    if (!is_numeric($input['cat_id']) || $input['cat_id'] <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid category ID.',
            'data' => null
        ]);
        exit;
    }
    
    //Category controller instance
    $categoryController = new CategoryController();
    
    //Deleting category
    $result = $categoryController->delete_category_ctr((int)$input['cat_id']);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while deleting category: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>
