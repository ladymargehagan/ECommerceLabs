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

// Get current category to preserve existing image if no new image uploaded
$current_category = $category_controller->get_categories_ctr($user_id);
$category_image = '';
if ($current_category['success']) {
    foreach ($current_category['data'] as $cat) {
        if ($cat['cat_id'] == $category_id) {
            $category_image = $cat['cat_image'] ?? '';
            break;
        }
    }
}

// Handle new image upload
if (isset($_FILES['categoryImage']) && $_FILES['categoryImage']['error'] === UPLOAD_ERR_OK) {
    $upload_result = $category_controller->upload_image_ctr($_FILES['categoryImage'], $category_id);
    if ($upload_result['success']) {
        $category_image = $upload_result['data'];
    } else {
        echo json_encode($upload_result);
        exit;
    }
}

$kwargs = array(
    'cat_id' => $category_id,
    'cat_name' => $category_name,
    'user_id' => $user_id,
    'cat_image' => $category_image
);

$result = $category_controller->update_category_ctr($kwargs);

header('Content-Type: application/json');
echo json_encode($result);
?>
