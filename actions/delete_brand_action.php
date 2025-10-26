<?php
require_once '../settings/db_class.php';
require_once '../classes/brand_class.php';
require_once '../controllers/brand_controller.php';

session_start();

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

if (!is_numeric($brand_id)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid brand ID'));
    exit;
}

$brand_id = (int)$brand_id;

try {
    $brand_controller = new brand_controller();
    $result = $brand_controller->delete_brand_ctr($brand_id);
} catch (Exception $e) {
    error_log("Delete brand error: " . $e->getMessage());
    $result = array('success' => false, 'message' => 'An error occurred while deleting the brand');
}

header('Content-Type: application/json');
echo json_encode($result);
?>
