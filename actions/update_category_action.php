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
$category_name = trim($_POST['categoryName'] ?? '');

if (empty($category_id) || empty($category_name)) {
    echo json_encode(array('success' => false, 'message' => 'Category ID and name are required'));
    exit;
}

if (!is_numeric($category_id)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid category ID'));
    exit;
}

$category_id = (int)$category_id;
$category_name = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
$category_controller = new category_controller();

$kwargs = array(
    'cat_id' => $category_id,
    'cat_name' => $category_name,
    'user_id' => $user_id
);

$result = $category_controller->update_category_ctr($kwargs);

header('Content-Type: application/json');
echo json_encode($result);
?>
