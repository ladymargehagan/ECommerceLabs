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

$user_id = $_SESSION['user_id'];
$brand_controller = new brand_controller();
$result = $brand_controller->get_brands_by_category_for_user_ctr($user_id);

header('Content-Type: application/json');
echo json_encode($result);
?>
