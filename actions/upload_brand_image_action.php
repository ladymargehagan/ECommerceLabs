<?php
header('Content-Type: application/json');
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

// Check if brand ID is provided
$brand_id = trim($_POST['brand_id'] ?? '');
if (empty($brand_id)) {
    echo json_encode(array('success' => false, 'message' => 'Brand ID is required'));
    exit;
}

// Check if file is uploaded
if (!isset($_FILES['brandImage']) || $_FILES['brandImage']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success' => false, 'message' => 'No file uploaded or upload error'));
    exit;
}

$file = $_FILES['brandImage'];
$user_id = $_SESSION['user_id'];

// Validate file size (5MB limit)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(array('success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'));
    exit;
}

// Check file type using both MIME type and file extension
$allowed_types = array('image/jpeg', 'image/png', 'image/gif');
$allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

$file_type = mime_content_type($file['tmp_name']);
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($file_type, $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'));
    exit;
}

// Process filename
$originalName = $file['name'];
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));

// Create directory structure: uploads/u{user_id}/b{brand_id}/
$upload_dir = "../uploads/u{$user_id}/b{$brand_id}/";

// Create directory if it doesn't exist with secure permissions
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(array('success' => false, 'message' => 'Failed to create upload directory'));
        exit;
    }
}

// Generate filename with timestamp
$timestamp = time();
$filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    $brand_image = "uploads/u{$user_id}/b{$brand_id}/{$filename}";
    
    // Update the brand with the image path in database
    $brand_controller = new brand_controller();
    
    // Get current brand data
    $current_brand = $brand_controller->get_brand_by_id_ctr($brand_id);
    if (!$current_brand['success']) {
        echo json_encode(array('success' => false, 'message' => 'Brand not found'));
        exit;
    }
    
    $brand_data = $current_brand['data'];
    
    // Update brand with new image path
    $update_kwargs = array(
        'brand_id' => $brand_id,
        'brand_name' => $brand_data['brand_name'],
        'brand_image' => $brand_image
    );
    
    $result = $brand_controller->update_brand_ctr($update_kwargs);
    
    if ($result['success']) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'Brand image uploaded successfully',
            'image_path' => $brand_image
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to update brand with image path'));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed to move uploaded file'));
}
?>
