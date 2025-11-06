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

$product_cat = trim($_POST['productCategory'] ?? '');
$product_brand = trim($_POST['productBrand'] ?? '');
$product_title = trim($_POST['productTitle'] ?? '');
$product_price = trim($_POST['productPrice'] ?? '');
$product_desc = trim($_POST['productDescription'] ?? '');
$product_keywords = trim($_POST['productKeywords'] ?? '');

if (empty($product_title)) {
    echo json_encode(array('success' => false, 'message' => 'Product title is required'));
    exit;
}

if (empty($product_cat)) {
    echo json_encode(array('success' => false, 'message' => 'Product category is required'));
    exit;
}

if (empty($product_brand)) {
    echo json_encode(array('success' => false, 'message' => 'Product brand is required'));
    exit;
}

if (empty($product_price) || !is_numeric($product_price) || $product_price <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Valid product price is required'));
    exit;
}

$product_title = htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8');
$product_desc = htmlspecialchars($product_desc, ENT_QUOTES, 'UTF-8');
$product_keywords = htmlspecialchars($product_keywords, ENT_QUOTES, 'UTF-8');

$product_controller = new product_controller();

$kwargs = array(
    'product_cat' => $product_cat,
    'product_brand' => $product_brand,
    'product_title' => $product_title,
    'product_price' => $product_price,
    'product_desc' => $product_desc,
    'product_image' => '',
    'product_keywords' => $product_keywords
);

$result = $product_controller->add_product_ctr($kwargs);

// Handle image upload after product is created
if ($result['success'] && isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
    // Get the product ID from the result
    $product_id = $result['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Use the same pattern as user registration
    $originalName = $_FILES['productImage']['name'];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    
    // Create directory structure: product/{product_id}/
    $upload_dir = "../product/{$product_id}/";
    
    // Ensure directory exists (product/ folder should exist on server)
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
    if (move_uploaded_file($_FILES['productImage']['tmp_name'], $file_path)) {
        $product_image = "product/{$product_id}/{$filename}";
        
        // Update the product with the image path
        $update_kwargs = array(
            'product_id' => $product_id,
            'product_cat' => $product_cat,
            'product_brand' => $product_brand,
            'product_title' => $product_title,
            'product_price' => $product_price,
            'product_desc' => $product_desc,
            'product_image' => $product_image,
            'product_keywords' => $product_keywords
        );
        
        $product_controller->update_product_ctr($update_kwargs);
    }
}

header('Content-Type: application/json');
echo json_encode($result);
?>
