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

if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin privileges required.',
        'data' => null
    ]);
    exit;
}

//Required files
require_once '../controllers/category_controller.php';

try {
    //Category controller instance
    $categoryController = new CategoryController();
    
    //Fetching all categories
    $result = $categoryController->get_all_categories_ctr();

    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching categories: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>
