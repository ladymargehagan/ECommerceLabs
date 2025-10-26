<?php
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
$result = $product_controller->get_all_products_ctr();

header('Content-Type: application/json');
echo json_encode($result);
?>
