<?php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.',
        'data' => null
    ]);
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin privileges required.',
        'data' => null
    ]);
    exit;
}

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
    $input = json_decode(file_get_contents('php://input'), true);
    
    //Input validation
    if (!isset($input['cat_id']) || !isset($input['cat_name'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Category ID and name are required.',
            'data' => null
        ]);
        exit;
    }
    
    if (empty(trim($input['cat_name']))) {
        echo json_encode([
            'success' => false,
            'message' => 'Category name is required.',
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
    
    //Preparing data for controller
    $kwargs = [
        'cat_id' => (int)$input['cat_id'],
        'cat_name' => trim($input['cat_name'])
    ];
    
    //Category controller instance
    $categoryController = new CategoryController();
    
    //Updating category
    $result = $categoryController->update_category_ctr($kwargs);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating category: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>
