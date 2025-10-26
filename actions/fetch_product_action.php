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

$product_controller = new product_controller();

// Check if requesting a specific product
if (isset($_GET['productId']) && !empty($_GET['productId'])) {
    $product_id = trim($_GET['productId']);
    $result = $product_controller->get_product_by_id_ctr($product_id);
} else {
    // Return all products
    $result = $product_controller->get_all_products_ctr();
}

echo json_encode($result);
?>
