<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'User not logged in'));
    exit;
}

if ($_SESSION['role'] != 1) {
    echo json_encode(array('success' => false, 'message' => 'Access denied. Admin privileges required.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'message' => 'Invalid request method'));
    exit;
}

$user_id = $_SESSION['user_id'];
$category_id = trim($_POST['categoryId'] ?? '');

if (empty($category_id)) {
    echo json_encode(array('success' => false, 'message' => 'Category ID is required'));
    exit;
}

if (!is_numeric($category_id)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid category ID'));
    exit;
}

$category_id = (int)$category_id;

try {
    $category_controller = new category_controller();
    $result = $category_controller->delete_category_ctr($category_id, $user_id);
} catch (Exception $e) {
    error_log("Delete category error: " . $e->getMessage());
    $result = array('success' => false, 'message' => 'An error occurred while deleting the category');
}

header('Content-Type: application/json');
echo json_encode($result);
?>
