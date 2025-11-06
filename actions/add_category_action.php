<?php
require_once '../settings/db_class.php';
require_once '../classes/category_class.php';
require_once '../controllers/category_controller.php';

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

// Handle image upload after category is created
if ($result['success'] && isset($_FILES['categoryImage']) && $_FILES['categoryImage']['error'] === UPLOAD_ERR_OK) {
    // Get the category ID from the result
    $category_id = $result['category_id'];
    
    // Use the upload_image_ctr method for consistent image handling
    $upload_result = $category_controller->upload_image_ctr($_FILES['categoryImage'], $category_id);
    
    if ($upload_result['success']) {
        $category_image = $upload_result['data'];
        
        // Update the category with the image path
        $update_kwargs = array(
            'cat_id' => $category_id,
            'cat_name' => $category_name,
            'user_id' => $user_id,
            'cat_image' => $category_image
        );
        
        $update_result = $category_controller->update_category_ctr($update_kwargs);
        
        // Update the result to include the image path
        if ($update_result['success']) {
            $result['cat_image'] = $category_image;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($result);
?>
