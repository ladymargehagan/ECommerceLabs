<?php
header('Content-Type: application/json');
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
$category_name = trim($_POST['categoryName'] ?? '');

if (empty($category_name)) {
    echo json_encode(array('success' => false, 'message' => 'Category name is required'));
    exit;
}

$category_name = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
$category_controller = new category_controller();

$kwargs = array(
    'cat_name' => $category_name,
    'created_by' => $user_id,
    'cat_image' => ''
);

$result = $category_controller->add_category_ctr($kwargs);

echo json_encode($result);
?>
