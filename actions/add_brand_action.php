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

$brand_name = trim($_POST['brandName'] ?? '');

if (empty($brand_name)) {
    echo json_encode(array('success' => false, 'message' => 'Brand name is required'));
    exit;
}

$brand_name = htmlspecialchars($brand_name, ENT_QUOTES, 'UTF-8');
$brand_controller = new brand_controller();

$kwargs = array(
    'brand_name' => $brand_name,
    'brand_image' => ''
);

$result = $brand_controller->add_brand_ctr($kwargs);

// Handle image upload after brand is created
if ($result['success'] && isset($_FILES['brandImage']) && $_FILES['brandImage']['error'] === UPLOAD_ERR_OK) {
    // Get the brand ID from the result
    $brand_id = $result['brand_id'];
    $user_id = $_SESSION['user_id'];
    
    // Use the upload_image_ctr method from brand_controller
    $upload_result = $brand_controller->upload_image_ctr($_FILES['brandImage'], $brand_id);
    
    if ($upload_result['success']) {
        $brand_image = $upload_result['data'];
        
        // Update the brand with the image path
        $update_kwargs = array(
            'brand_id' => $brand_id,
            'brand_name' => $brand_name,
            'brand_image' => $brand_image
        );
        
        $brand_controller->update_brand_ctr($update_kwargs);
    }
}

header('Content-Type: application/json');
echo json_encode($result);
?>
