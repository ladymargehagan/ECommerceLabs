<?php
require_once 'settings/core.php';
require_once 'controllers/product_controller.php';

// Set JSON header
header('Content-Type: application/json');

// Get action parameter
$action = $_GET['action'] ?? '';

// SUPER SIMPLE - Just return what's in the database
if ($action === 'get_products_paginated') {
    $product_controller = new product_controller();
    $result = $product_controller->get_all_products_ctr();
    
    if ($result['success']) {
        $products = $result['data'];
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        $paginated_products = array_slice($products, $offset, $limit);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'products' => $paginated_products,
                'pagination' => array(
                    'current_page' => $page,
                    'total_pages' => ceil(count($products) / $limit),
                    'total_items' => count($products),
                    'items_per_page' => $limit
                ),
                'total' => count($products),
                'page' => $page
            )
        ));
    } else {
        echo json_encode($result);
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Invalid action: ' . $action));
}
?>
