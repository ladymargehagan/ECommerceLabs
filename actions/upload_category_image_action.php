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

// Check if category ID is provided
$category_id = trim($_POST['category_id'] ?? '');
if (empty($category_id)) {
    echo json_encode(array('success' => false, 'message' => 'Category ID is required'));
    exit;
}

// Check if file is uploaded
if (!isset($_FILES['categoryImage']) || $_FILES['categoryImage']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success' => false, 'message' => 'No file uploaded or upload error'));
    exit;
}

$file = $_FILES['categoryImage'];
$user_id = $_SESSION['user_id'];

// Validate file size (5MB limit)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(array('success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'));
    exit;
}

// Check file type
$allowed_types = array('image/jpeg', 'image/png', 'image/gif');
$file_type = mime_content_type($file['tmp_name']);
if (!in_array($file_type, $allowed_types)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'));
    exit;
}

// Process filename
$originalName = $file['name'];
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));

// Create directory structure: uploads/u{user_id}/c{category_id}/
$upload_dir = "../uploads/u{$user_id}/c{$category_id}/";

// Create directory if it doesn't exist
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate filename with timestamp
$timestamp = time();
$filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    $category_image = "uploads/u{$user_id}/c{$category_id}/{$filename}";
    
    // Update the category with the image path in database
    $category_controller = new category_controller();
    
    // Get current category data
    $current_category = $category_controller->get_category_by_id_ctr($category_id);
    if (!$current_category['success']) {
        echo json_encode(array('success' => false, 'message' => 'Category not found'));
        exit;
    }
    
    $category_data = $current_category['data'];
    
    // Update category with new image path
    $update_kwargs = array(
        'cat_id' => $category_id,
        'cat_name' => $category_data['cat_name'],
        'cat_image' => $category_image
    );
    
    $result = $category_controller->update_category_ctr($update_kwargs);
    
    if ($result['success']) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'Category image uploaded successfully',
            'image_path' => $category_image
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to update category with image path'));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed to move uploaded file'));
}
?>
