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
            
        case 'debug_products':
            debugProducts();
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
    
    // Get all products first
    $all_products_result = $product_controller->get_all_products_ctr();
    
    if (!$all_products_result['success']) {
        echo json_encode($all_products_result);
        return;
    }
    
    $all_products = $all_products_result['data'];
    $total = count($all_products);
    
    // Get products for current page
    $products = array_slice($all_products, $offset, $limit);
    
    $pagination = array(
        'current_page' => $page,
        'total_pages' => ceil($total / $limit),
        'total_items' => $total,
        'items_per_page' => $limit
    );
    
    echo json_encode(array(
        'success' => true,
        'data' => array(
            'products' => $products,
            'pagination' => $pagination,
            'total' => $total,
            'page' => $page
        )
    ));
}

function getProductDetail() {
    $product_id = intval($_GET['product_id'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(array('success' => false, 'message' => 'Product ID is required'));
        return;
    }
    
    $product_controller = new product_controller();
    
    // Get all products first
    $all_products_result = $product_controller->get_all_products_ctr();
    
    if (!$all_products_result['success']) {
        echo json_encode($all_products_result);
        return;
    }
    
    $all_products = $all_products_result['data'];
    
    // Find the specific product
    $product = null;
    foreach ($all_products as $p) {
        if ($p['product_id'] == $product_id) {
            $product = $p;
            break;
        }
    }
    
    if ($product) {
        echo json_encode(array('success' => true, 'data' => $product));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Product not found'));
    }
}

function searchProducts() {
    $search_term = trim($_GET['search'] ?? '');
    
    if (empty($search_term)) {
        echo json_encode(array('success' => false, 'message' => 'Search term is required'));
        return;
    }
    
    $product_controller = new product_controller();
    
    // Get all products first
    $all_products_result = $product_controller->get_all_products_ctr();
    
    if (!$all_products_result['success']) {
        echo json_encode($all_products_result);
        return;
    }
    
    $all_products = $all_products_result['data'];
    $search_term_lower = strtolower($search_term);
    
    // Filter products by search term
    $filtered_products = array_filter($all_products, function($product) use ($search_term_lower) {
        return strpos(strtolower($product['product_title']), $search_term_lower) !== false ||
               strpos(strtolower($product['product_desc']), $search_term_lower) !== false ||
               strpos(strtolower($product['product_keywords']), $search_term_lower) !== false ||
               strpos(strtolower($product['cat_name']), $search_term_lower) !== false ||
               strpos(strtolower($product['brand_name']), $search_term_lower) !== false;
    });
    
    echo json_encode(array(
        'success' => true,
        'data' => array(
            'products' => array_values($filtered_products),
            'total' => count($filtered_products),
            'search_term' => $search_term
        )
    ));
}

function filterProducts() {
    $category = $_GET['category'] ?? 'all';
    $brand = $_GET['brand'] ?? 'all';
    $sort = $_GET['sort'] ?? 'name_asc';
    
    $product_controller = new product_controller();
    
    // Get all products first
    $all_products_result = $product_controller->get_all_products_ctr();
    
    if (!$all_products_result['success']) {
        echo json_encode($all_products_result);
        return;
    }
    
    $all_products = $all_products_result['data'];
    
    // Filter by category
    if ($category !== 'all') {
        $all_products = array_filter($all_products, function($product) use ($category) {
            return $product['product_cat'] == $category;
        });
    }
    
    // Filter by brand
    if ($brand !== 'all') {
        $all_products = array_filter($all_products, function($product) use ($brand) {
            return $product['product_brand'] == $brand;
        });
    }
    
    // Sort products
    switch ($sort) {
        case 'name_asc':
            usort($all_products, function($a, $b) {
                return strcmp($a['product_title'], $b['product_title']);
            });
            break;
        case 'name_desc':
            usort($all_products, function($a, $b) {
                return strcmp($b['product_title'], $a['product_title']);
            });
            break;
        case 'price_asc':
            usort($all_products, function($a, $b) {
                return floatval($a['product_price']) - floatval($b['product_price']);
            });
            break;
        case 'price_desc':
            usort($all_products, function($a, $b) {
                return floatval($b['product_price']) - floatval($a['product_price']);
            });
            break;
    }
    
    echo json_encode(array(
        'success' => true,
        'data' => array(
            'products' => array_values($all_products),
            'total' => count($all_products),
            'filters' => array(
                'category' => $category,
                'brand' => $brand,
                'sort' => $sort
            )
        )
    ));
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

function debugProducts() {
    $product_controller = new product_controller();
    $result = $product_controller->get_all_products_ctr();
    
    echo json_encode(array(
        'success' => true,
        'debug' => true,
        'data' => $result,
        'count' => is_array($result['data']) ? count($result['data']) : 'not array'
    ));
}
?>
