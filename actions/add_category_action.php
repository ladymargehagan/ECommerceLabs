<?php
session_start();

// content type to JSON
header('Content-Type: application/json');

// Checking if user is logged in
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

// Checking if request method is POST
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
    //JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    //Input validation
    if (!isset($input['cat_name']) || empty(trim($input['cat_name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Category name is required.',
            'data' => null
        ]);
        exit;
    }
    
    //Preparing data for controller
    $kwargs = [
        'cat_name' => trim($input['cat_name']),
        'user_id' => $_SESSION['user_id']
    ];
    
    //Category controller instance
    $categoryController = new CategoryController();
    $result = $categoryController->add_category_ctr($kwargs);
    
    //Returning the result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while adding category: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>
