<?php
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

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

// Get product ID from POST data
$product_id = trim($_POST['productId'] ?? '');

if (empty($product_id) || !is_numeric($product_id)) {
    echo json_encode(array('success' => false, 'message' => 'Valid product ID is required'));
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success' => false, 'message' => 'No image file uploaded'));
    exit;
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['productImage'];

// Validate file type
$allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
$file_type = mime_content_type($file['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF images are allowed'));
    exit;
}

// Validate file size (5MB max)
$max_size = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $max_size) {
    echo json_encode(array('success' => false, 'message' => 'File size too large. Maximum 5MB allowed'));
    exit;
}

// Sanitize filename
$original_name = $file['name'];
$extension = pathinfo($original_name, PATHINFO_EXTENSION);
$sanitized_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($original_name, PATHINFO_FILENAME));

// Create directory structure: uploads/u{user_id}/p{product_id}/
$upload_dir = "../uploads/u{$user_id}/p{$product_id}/";

// Ensure directory exists
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        echo json_encode(array('success' => false, 'message' => 'Failed to create upload directory'));
        exit;
    }
}

// Generate filename with timestamp
$timestamp = time();
$filename = "image_{$sanitized_name}_{$timestamp}.{$extension}";
$file_path = $upload_dir . $filename;

// Verify the path is within uploads directory (security check)
$real_upload_path = realpath($upload_dir);
$real_uploads_path = realpath('../uploads/');

if (!$real_upload_path || strpos($real_upload_path, $real_uploads_path) !== 0) {
    echo json_encode(array('success' => false, 'message' => 'Invalid upload path. Files must be stored in uploads directory only'));
    exit;
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Store relative path in database
    $product_image = "uploads/u{$user_id}/p{$product_id}/{$filename}";
    
    // Update product with image path
    $product_controller = new product_controller();
    $update_kwargs = array(
        'product_id' => $product_id,
        'product_image' => $product_image
    );
    
    $result = $product_controller->update_product_image_ctr($update_kwargs);
    
    if ($result['success']) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'Image uploaded successfully',
            'image_path' => $product_image
        ));
    } else {
        // If database update fails, remove the uploaded file
        unlink($file_path);
        echo json_encode(array('success' => false, 'message' => 'Failed to update product with image path'));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed to upload image file'));
}

header('Content-Type: application/json');
?>
