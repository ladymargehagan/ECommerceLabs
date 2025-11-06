<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

// Start output buffering to catch any errors
ob_start();

try {
    require_once '../settings/db_class.php';
    require_once '../classes/brand_class.php';
    require_once '../controllers/brand_controller.php';

    session_start();

    if (!isset($_SESSION['user_id'])) {
        ob_clean();
        echo json_encode(array('success' => false, 'message' => 'User not logged in'));
        exit;
    }

    if ($_SESSION['role'] != 1) {
        ob_clean();
        echo json_encode(array('success' => false, 'message' => 'Access denied. Admin privileges required.'));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_clean();
        echo json_encode(array('success' => false, 'message' => 'Invalid request method'));
        exit;
    }

    $brand_name = trim($_POST['brandName'] ?? '');

    if (empty($brand_name)) {
        ob_clean();
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
        
        // Process filename
        $originalName = $_FILES['brandImage']['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        
        // Create directory structure: uploads/u{user_id}/b{brand_id}/
        $upload_dir = "../uploads/u{$user_id}/b{$brand_id}/";
        
        // Ensure directory exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                ob_clean();
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
            
            // Update the brand with the image path
            $update_kwargs = array(
                'brand_id' => $brand_id,
                'brand_name' => $brand_name,
                'brand_image' => $brand_image
            );
            
            $update_result = $brand_controller->update_brand_ctr($update_kwargs);
            if ($update_result['success']) {
                // Update the result to include the image path
                $result['brand_image'] = $brand_image;
            }
        }
    }

    ob_clean();
    echo json_encode($result);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode(array(
        'success' => false, 
        'message' => 'An error occurred: ' . $e->getMessage()
    ));
} catch (Error $e) {
    ob_clean();
    echo json_encode(array(
        'success' => false, 
        'message' => 'A fatal error occurred: ' . $e->getMessage()
    ));
}
?>
