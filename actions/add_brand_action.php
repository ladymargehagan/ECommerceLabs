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

$user_id = $_SESSION['user_id'];
$brand_name = trim($_POST['brandName'] ?? '');

if (empty($brand_name)) {
    echo json_encode(array('success' => false, 'message' => 'Brand name is required'));
    exit;
}

$brand_name = htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8');
$brand_controller = new brand_controller();

$kwargs = array(
    'brand_name' => $brand_name,
    'created_by' => $user_id
);

$result = $brand_controller->add_brand_ctr($kwargs);

header('Content-Type: application/json');
echo json_encode($result);
?>
