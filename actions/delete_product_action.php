<?php
header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

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

$product_id = trim($_POST['productId'] ?? '');

if (empty($product_id)) {
    echo json_encode(array('success' => false, 'message' => 'Product ID is required'));
    exit;
}

$product_controller = new product_controller();
$result = $product_controller->delete_product_ctr($product_id);

echo json_encode($result);
?>
