<?php
require_once '../controllers/product_controller.php';

// Handle all product operations as specified in lab requirements
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (empty($action)) {
    // Default to view all products if no action specified
    $action = 'view_all_products';
}

$product_controller = new product_controller();

switch ($action) {
    case 'view_all_products':
        $result = $product_controller->view_all_products_ctr();
        if ($result['success']) {
            $products = $result['data'];
            $categories = $product_controller->get_categories_ctr()['data'];
            $brands = $product_controller->get_brands_ctr()['data'];
            include 'all_product.php';
        } else {
            echo "Error: " . $result['message'];
        }
        break;
        
    case 'search_products':
        $query = $_GET['query'] ?? $_POST['query'] ?? '';
        $result = $product_controller->search_products_ctr($query);
        if ($result['success']) {
            $products = $result['data'];
            $search_query = $result['query'];
            $categories = $product_controller->get_categories_ctr()['data'];
            $brands = $product_controller->get_brands_ctr()['data'];
            include 'product_search_result.php';
        } else {
            echo "Error: " . $result['message'];
        }
        break;
        
    case 'filter_by_category':
        $cat_id = $_GET['cat_id'] ?? $_POST['cat_id'] ?? '';
        $result = $product_controller->filter_products_by_category_ctr($cat_id);
        if ($result['success']) {
            $products = $result['data'];
            $filter_type = $result['filter_type'];
            $filter_id = $result['filter_id'];
            $categories = $product_controller->get_categories_ctr()['data'];
            $brands = $product_controller->get_brands_ctr()['data'];
            include 'all_product.php';
        } else {
            echo "Error: " . $result['message'];
        }
        break;
        
    case 'filter_by_brand':
        $brand_id = $_GET['brand_id'] ?? $_POST['brand_id'] ?? '';
        $result = $product_controller->filter_products_by_brand_ctr($brand_id);
        if ($result['success']) {
            $products = $result['data'];
            $filter_type = $result['filter_type'];
            $filter_id = $result['filter_id'];
            $categories = $product_controller->get_categories_ctr()['data'];
            $brands = $product_controller->get_brands_ctr()['data'];
            include 'all_product.php';
        } else {
            echo "Error: " . $result['message'];
        }
        break;
        
    case 'view_single_product':
        $product_id = $_GET['id'] ?? $_POST['product_id'] ?? '';
        $result = $product_controller->view_single_product_ctr($product_id);
        if ($result['success']) {
            $product = $result['data'];
            include 'single_product.php';
        } else {
            echo "Error: " . $result['message'];
        }
        break;
        
    default:
        echo "Invalid action";
        break;
}
?>