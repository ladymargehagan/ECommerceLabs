<?php
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

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

$brand_id = trim($_POST['brandId'] ?? '');

if (empty($brand_id)) {
    echo json_encode(array('success' => false, 'message' => 'Brand ID is required'));
    exit;
}

$brand_controller = new brand_controller();
$result = $brand_controller->delete_brand_ctr($brand_id);

header('Content-Type: application/json');
echo json_encode($result);
?>
