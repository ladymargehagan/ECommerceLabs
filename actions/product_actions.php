<?php
require_once '../controllers/product_controller.php';

// Handle AJAX requests for product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_controller = new product_controller();
    
    switch ($action) {
        case 'view_all_products':
            $page = intval($_POST['page'] ?? 1);
            $per_page = intval($_POST['per_page'] ?? 10);
            $result = $product_controller->view_all_products_ctr($page, $per_page);
            echo json_encode($result);
            break;
            
        case 'search_products':
            $query = trim($_POST['query'] ?? '');
            $page = intval($_POST['page'] ?? 1);
            $per_page = intval($_POST['per_page'] ?? 10);
            $result = $product_controller->search_products_ctr($query, $page, $per_page);
            echo json_encode($result);
            break;
            
        case 'filter_by_category':
            $cat_id = intval($_POST['cat_id'] ?? 0);
            $page = intval($_POST['page'] ?? 1);
            $per_page = intval($_POST['per_page'] ?? 10);
            $result = $product_controller->filter_products_by_category_ctr($cat_id, $page, $per_page);
            echo json_encode($result);
            break;
            
        case 'filter_by_brand':
            $brand_id = intval($_POST['brand_id'] ?? 0);
            $page = intval($_POST['page'] ?? 1);
            $per_page = intval($_POST['per_page'] ?? 10);
            $result = $product_controller->filter_products_by_brand_ctr($brand_id, $page, $per_page);
            echo json_encode($result);
            break;
            
        case 'view_single_product':
            $product_id = intval($_POST['product_id'] ?? 0);
            $result = $product_controller->view_single_product_ctr($product_id);
            echo json_encode($result);
            break;
            
        case 'get_categories':
            $result = $product_controller->get_categories_ctr();
            echo json_encode($result);
            break;
            
        case 'get_brands':
            $result = $product_controller->get_brands_ctr();
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Invalid action'));
            break;
    }
    exit;
}

// Handle GET requests for direct page access
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $product_controller = new product_controller();
    
    switch ($action) {
        case 'view_all_products':
            $page = intval($_GET['page'] ?? 1);
            $per_page = intval($_GET['per_page'] ?? 10);
            $result = $product_controller->view_all_products_ctr($page, $per_page);
            
            // Return data for direct page access
            if ($result['success']) {
                $products = $result['data'];
                $pagination = $result['pagination'];
                $categories = $product_controller->get_categories_ctr()['data'];
                $brands = $product_controller->get_brands_ctr()['data'];
                
                // Include the view file
                include '../views/all_product.php';
            } else {
                echo "Error: " . $result['message'];
            }
            break;
            
        case 'search_products':
            $query = trim($_GET['query'] ?? '');
            $page = intval($_GET['page'] ?? 1);
            $per_page = intval($_GET['per_page'] ?? 10);
            $result = $product_controller->search_products_ctr($query, $page, $per_page);
            
            if ($result['success']) {
                $products = $result['data'];
                $pagination = $result['pagination'];
                $search_query = $result['query'];
                $categories = $product_controller->get_categories_ctr()['data'];
                $brands = $product_controller->get_brands_ctr()['data'];
                
                // Include the search results view file
                include '../views/product_search_result.php';
            } else {
                echo "Error: " . $result['message'];
            }
            break;
            
        case 'filter_by_category':
            $cat_id = intval($_GET['cat_id'] ?? 0);
            $page = intval($_GET['page'] ?? 1);
            $per_page = intval($_GET['per_page'] ?? 10);
            $result = $product_controller->filter_products_by_category_ctr($cat_id, $page, $per_page);
            
            if ($result['success']) {
                $products = $result['data'];
                $pagination = $result['pagination'];
                $filter_type = $result['filter_type'];
                $filter_id = $result['filter_id'];
                $categories = $product_controller->get_categories_ctr()['data'];
                $brands = $product_controller->get_brands_ctr()['data'];
                
                // Include the filtered products view file
                include '../views/all_product.php';
            } else {
                echo "Error: " . $result['message'];
            }
            break;
            
        case 'filter_by_brand':
            $brand_id = intval($_GET['brand_id'] ?? 0);
            $page = intval($_GET['page'] ?? 1);
            $per_page = intval($_GET['per_page'] ?? 10);
            $result = $product_controller->filter_products_by_brand_ctr($brand_id, $page, $per_page);
            
            if ($result['success']) {
                $products = $result['data'];
                $pagination = $result['pagination'];
                $filter_type = $result['filter_type'];
                $filter_id = $result['filter_id'];
                $categories = $product_controller->get_categories_ctr()['data'];
                $brands = $product_controller->get_brands_ctr()['data'];
                
                // Include the filtered products view file
                include '../views/all_product.php';
            } else {
                echo "Error: " . $result['message'];
            }
            break;
            
        case 'view_single_product':
            $product_id = intval($_GET['product_id'] ?? 0);
            $result = $product_controller->view_single_product_ctr($product_id);
            
            if ($result['success']) {
                $product = $result['data'];
                
                // Include the single product view file
                include '../views/single_product.php';
            } else {
                echo "Error: " . $result['message'];
            }
            break;
            
        default:
            echo "Invalid action";
            break;
    }
    exit;
}

// If no valid action is provided, redirect to home
header("Location: ../index.php");
exit;
?>
