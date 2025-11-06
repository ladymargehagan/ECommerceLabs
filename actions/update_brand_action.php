<?php
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

$brand_id = trim($_POST['brandId'] ?? '');
$brand_name = trim($_POST['brandName'] ?? '');

if (empty($brand_id) || empty($brand_name)) {
    echo json_encode(array('success' => false, 'message' => 'Brand ID and name are required'));
    exit;
}

$brand_name = htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8');
$brand_controller = new brand_controller();

// Get current brand to preserve existing image if no new image uploaded
$current_brand = $brand_controller->get_brand_by_id_ctr($brand_id);
$brand_image = $current_brand['success'] ? $current_brand['data']['brand_image'] : '';

// Handle new image upload
if (isset($_FILES['brandImage']) && $_FILES['brandImage']['error'] === UPLOAD_ERR_OK) {
    $user_id = $_SESSION['user_id'];
    
    // Process filename
    $originalName = $_FILES['brandImage']['name'];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    
    // Create directory structure: uploads/u{user_id}/b{brand_id}/
    $upload_dir = "../uploads/u{$user_id}/b{$brand_id}/";
    
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
    if (move_uploaded_file($_FILES['brandImage']['tmp_name'], $file_path)) {
        $brand_image = "uploads/u{$user_id}/b{$brand_id}/{$filename}";
    }
}

$kwargs = array(
    'brand_id' => $brand_id,
    'brand_name' => $brand_name,
    'brand_image' => $brand_image
);

$result = $brand_controller->update_brand_ctr($kwargs);

header('Content-Type: application/json');
echo json_encode($result);
?>
