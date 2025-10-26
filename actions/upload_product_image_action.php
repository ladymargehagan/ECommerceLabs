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

// Check if product ID is provided
$product_id = trim($_POST['product_id'] ?? '');
if (empty($product_id)) {
    echo json_encode(array('success' => false, 'message' => 'Product ID is required'));
    exit;
}

// Check if file is uploaded
if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success' => false, 'message' => 'No file uploaded or upload error'));
    exit;
}

$file = $_FILES['productImage'];
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

// Construct file path - MUST be inside uploads/ folder only
$upload_dir = "../uploads/u{$user_id}/p{$product_id}/";

// Verify upload directory is inside uploads/ folder
$real_upload_dir = realpath($upload_dir);
$real_uploads_dir = realpath("../uploads/");

if (!$real_upload_dir || strpos($real_upload_dir, $real_uploads_dir) !== 0) {
    echo json_encode(array('success' => false, 'message' => 'Invalid upload directory. Must be inside uploads/ folder.'));
    exit;
}

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
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    $product_image = "uploads/u{$user_id}/p{$product_id}/{$filename}";
    
    // Update the product with the image path in database
    $product_controller = new product_controller();
    
    // Get current product data
    $current_product = $product_controller->get_product_by_id_ctr($product_id);
    if (!$current_product['success']) {
        echo json_encode(array('success' => false, 'message' => 'Product not found'));
        exit;
    }
    
    $product_data = $current_product['data'];
    
    // Update product with new image path
    $update_kwargs = array(
        'product_id' => $product_id,
        'product_cat' => $product_data['product_cat'],
        'product_brand' => $product_data['product_brand'],
        'product_title' => $product_data['product_title'],
        'product_price' => $product_data['product_price'],
        'product_desc' => $product_data['product_desc'],
        'product_image' => $product_image,
        'product_keywords' => $product_data['product_keywords']
    );
    
    $result = $product_controller->update_product_ctr($update_kwargs);
    
    if ($result['success']) {
        echo json_encode(array(
            'success' => true, 
            'message' => 'Product image uploaded successfully',
            'image_path' => $product_image
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to update product with image path'));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed to move uploaded file'));
}

header('Content-Type: application/json');
?>
