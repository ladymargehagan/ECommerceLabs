<?php
require_once '../settings/core.php';

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

if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success' => false, 'message' => 'No file uploaded or upload error'));
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['productId'] ?? 'temp_' . time();

// Handle image upload following the same pattern as user registration
$originalName = $_FILES['productImage']['name'];
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));

// Create directory structure: uploads/u{user_id}/p{product_id}/
$upload_dir = "../uploads/u{$user_id}/p{$product_id}/";

// Ensure directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate filename with timestamp for efficient searching (binary search friendly)
$timestamp = time();
$filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($_FILES['productImage']['tmp_name'], $file_path)) {
    // Return relative path from uploads folder
    $relative_path = "uploads/u{$user_id}/p{$product_id}/{$filename}";
    
    echo json_encode(array(
        'success' => true, 
        'message' => 'Image uploaded successfully',
        'data' => array(
            'file_path' => $relative_path,
            'filename' => $filename,
            'timestamp' => $timestamp
        )
    ));
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed to upload image'));
}

header('Content-Type: application/json');
?>
