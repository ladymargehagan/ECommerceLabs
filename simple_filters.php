<?php
// Simple product actions without authentication requirements
require_once 'settings/db_cred.php';
require_once 'classes/category_class.php';
require_once 'classes/brand_class.php';

// Set JSON header
header('Content-Type: application/json');

// Get action parameter
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_categories':
            getCategoriesSimple();
            break;
            
        case 'get_brands':
            getBrandsSimple();
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Invalid action'));
            break;
    }
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => 'Server error: ' . $e->getMessage()));
}

function getCategoriesSimple() {
    try {
        $category_class = new category_class();
        $result = $category_class->get_categories_by_user(0); // Use 0 for no user filter
        
        echo json_encode(array('success' => true, 'data' => $result));
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Error getting categories: ' . $e->getMessage()));
    }
}

function getBrandsSimple() {
    try {
        $brand_class = new brand_class();
        $result = $brand_class->get_all_brands();
        
        echo json_encode(array('success' => true, 'data' => $result));
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Error getting brands: ' . $e->getMessage()));
    }
}
?>
