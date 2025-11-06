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
    
    // Process filename
    $originalName = $_FILES['categoryImage']['name'];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    
    // Create directory structure: uploads/u{user_id}/c{category_id}/
    $upload_dir = "../uploads/u{$user_id}/c{$category_id}/";
    
    // Ensure directory exists
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            echo json_encode(array('success' => false, 'message' => 'Failed to create upload directory'));
            exit;
        }
    }
    
    // Generate filename with timestamp
    $timestamp = time();
    $filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
    $file_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['categoryImage']['tmp_name'], $file_path)) {
        $category_image = "uploads/u{$user_id}/c{$category_id}/{$filename}";
        
        // Update the category with the image path
        $update_kwargs = array(
            'cat_id' => $category_id,
            'cat_name' => $category_name,
            'user_id' => $user_id,
            'cat_image' => $category_image
        );
        
        $update_result = $category_controller->update_category_ctr($update_kwargs);
        if ($update_result['success']) {
            // Update the result to include the image path
            $result['cat_image'] = $category_image;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($result);
?>
