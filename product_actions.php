<?php
require_once 'settings/core.php';
require_once 'controllers/product_controller.php';
require_once 'controllers/category_controller.php';
require_once 'controllers/brand_controller.php';

// Set JSON header
header('Content-Type: application/json');

// Get action parameter
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_products':
            getProducts();
            break;
            
        case 'get_products_paginated':
            getProductsPaginated();
            break;
            
        case 'get_product_detail':
            getProductDetail();
            break;
            
        case 'search_products':
            searchProducts();
            break;
            
        case 'filter_products':
            filterProducts();
            break;
            
        case 'get_categories':
            getCategories();
            break;
            
        case 'get_brands':
            getBrands();
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Invalid action'));
            break;
    }
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => 'Server error: ' . $e->getMessage()));
}

function getProducts() {
    $page = intval($_GET['page'] ?? 1);
    $limit = 12; // Products per page
    $offset = ($page - 1) * $limit;
    
    $product_controller = new product_controller();
    
    // Get total count
    $total_result = $product_controller->get_all_products_count_ctr();
    if (!$total_result['success']) {
        echo json_encode($total_result);
        return;
    }
    
    $total = $total_result['data'];
    
    // Get products for current page
    $result = $product_controller->get_products_paginated_ctr($limit, $offset);
    
    if ($result['success']) {
        $pagination = array(
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_items' => $total,
            'items_per_page' => $limit
        );
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $result['data'],
                'pagination' => $pagination,
                'total' => $total,
                'page' => $page
            )
        ));
    } else {
        echo json_encode($result);
    }
}

function getProductsPaginated() {
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;
    
    $product_controller = new product_controller();
    
    // Get total count
    $total_result = $product_controller->get_all_products_count_ctr();
    if (!$total_result['success']) {
        echo json_encode($total_result);
        return;
    }
    
    $total = $total_result['data'];
    
    // Get products for current page
    $result = $product_controller->get_products_paginated_ctr($limit, $offset);
    
    if ($result['success']) {
        $pagination = array(
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_items' => $total,
            'items_per_page' => $limit
        );
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $result['data'],
                'pagination' => $pagination,
                'total' => $total,
                'page' => $page
            )
        ));
    } else {
        echo json_encode($result);
    }
}

function getProductDetail() {
    $product_id = intval($_GET['product_id'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(array('success' => false, 'message' => 'Product ID is required'));
        return;
    }
    
    $product_controller = new product_controller();
    $result = $product_controller->get_product_by_id_ctr($product_id);
    
    echo json_encode($result);
}

function searchProducts() {
    $search_term = trim($_GET['search'] ?? '');
    
    if (empty($search_term)) {
        echo json_encode(array('success' => false, 'message' => 'Search term is required'));
        return;
    }
    
    $product_controller = new product_controller();
    $result = $product_controller->search_products_ctr($search_term);
    
    if ($result['success']) {
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $result['data'],
                'total' => count($result['data']),
                'search_term' => $search_term
            )
        ));
    } else {
        echo json_encode($result);
    }
}

function filterProducts() {
    $category = $_GET['category'] ?? 'all';
    $brand = $_GET['brand'] ?? 'all';
    $sort = $_GET['sort'] ?? 'name_asc';
    
    $product_controller = new product_controller();
    $result = $product_controller->filter_products_ctr($category, $brand, $sort);
    
    if ($result['success']) {
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $result['data'],
                'total' => count($result['data']),
                'filters' => array(
                    'category' => $category,
                    'brand' => $brand,
                    'sort' => $sort
                )
            )
        ));
    } else {
        echo json_encode($result);
    }
}

function getCategories() {
    $category_controller = new category_controller();
    $user_id = $_SESSION['user_id'] ?? 0;
    $result = $category_controller->get_categories_ctr($user_id);
    
    echo json_encode($result);
}

function getBrands() {
    $brand_controller = new brand_controller();
    $result = $brand_controller->get_all_brands_ctr();
    
    echo json_encode($result);
}
?>
