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
$product_cat = trim($_POST['productCategory'] ?? '');
$product_brand = trim($_POST['productBrand'] ?? '');
$product_title = trim($_POST['productTitle'] ?? '');
$product_price = trim($_POST['productPrice'] ?? '');
$product_desc = trim($_POST['productDescription'] ?? '');
$product_keywords = trim($_POST['productKeywords'] ?? '');

if (empty($product_id) || empty($product_title) || empty($product_cat) || empty($product_brand) || empty($product_price)) {
    echo json_encode(array('success' => false, 'message' => 'All required fields must be filled'));
    exit;
}

if (!is_numeric($product_price) || $product_price <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Valid product price is required'));
    exit;
}

$product_title = htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8');
$product_desc = htmlspecialchars($product_desc, ENT_QUOTES, 'UTF-8');
$product_keywords = htmlspecialchars($product_keywords, ENT_QUOTES, 'UTF-8');

$product_controller = new product_controller();

// Get current product to preserve existing image if no new image uploaded
$current_product = $product_controller->get_product_by_id_ctr($product_id);
$product_image = $current_product['success'] ? $current_product['data']['product_image'] : '';

$kwargs = array(
    'product_id' => $product_id,
    'product_cat' => $product_cat,
    'product_brand' => $product_brand,
    'product_title' => $product_title,
    'product_price' => $product_price,
    'product_desc' => $product_desc,
    'product_image' => $product_image,
    'product_keywords' => $product_keywords
);

$result = $product_controller->update_product_ctr($kwargs);

echo json_encode($result);
?>
