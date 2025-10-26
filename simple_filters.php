<?php
// Simple product actions without authentication requirements
require_once 'settings/db_cred.php';
require_once 'classes/category_class.php';
require_once 'classes/brand_class.php';
require_once 'classes/product_class.php';

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
            
        case 'search_products':
            searchProductsSimple();
            break;
            
        case 'get_products_paginated':
            getProductsPaginatedSimple();
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

function searchProductsSimple() {
    try {
        $search_term = trim($_GET['search'] ?? '');
        
        if (empty($search_term)) {
            echo json_encode(array('success' => false, 'message' => 'Search term is required'));
            return;
        }
        
        $product_class = new product_class();
        $products = $product_class->search_products($search_term);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $products,
                'total' => count($products),
                'search_term' => $search_term
            )
        ));
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Error searching products: ' . $e->getMessage()));
    }
}

function getProductsPaginatedSimple() {
    try {
        $product_class = new product_class();
        $all_products = $product_class->get_all_products();
        
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        $paginated_products = array_slice($all_products, $offset, $limit);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $paginated_products,
                'pagination' => array(
                    'current_page' => $page,
                    'total_pages' => ceil(count($all_products) / $limit),
                    'total_items' => count($all_products),
                    'items_per_page' => $limit
                ),
                'total' => count($all_products),
                'page' => $page
            )
        ));
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Error getting products: ' . $e->getMessage()));
    }
}
?>
