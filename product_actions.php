<?php
// Enable error reporting to see exact errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'settings/db_class.php';
require_once 'classes/product_class.php';
require_once 'controllers/product_controller.php';

// Initialize product controller
$product_controller = new product_controller();

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Get the action from the request
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

try {
    switch ($action) {
        case 'get_all_products':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $result = $product_controller->view_all_products_ctr($limit, $offset);
            echo json_encode($result);
            break;

        case 'search_products':
            $query = isset($_GET['query']) ? trim($_GET['query']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $result = $product_controller->search_products_ctr($query, $limit, $offset);
            echo json_encode($result);
            break;

        case 'filter_by_category':
            $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $result = $product_controller->filter_products_by_category_ctr($category_id, $limit, $offset);
            echo json_encode($result);
            break;

        case 'filter_by_brand':
            $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $result = $product_controller->filter_products_by_brand_ctr($brand_id, $limit, $offset);
            echo json_encode($result);
            break;

        case 'get_single_product':
            $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
            
            $result = $product_controller->view_single_product_ctr($product_id);
            echo json_encode($result);
            break;

        case 'get_products_with_filters':
            $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
            $search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : null;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            $result = $product_controller->get_products_with_filters_ctr($category_id, $brand_id, $search_query, $limit, $offset);
            echo json_encode($result);
            break;

        case 'get_categories':
            try {
                $result = $product_controller->get_categories_ctr();
                echo json_encode($result);
            } catch (Exception $e) {
                echo json_encode(array('success' => false, 'message' => 'Categories error: ' . $e->getMessage()));
            }
            break;

        case 'get_brands':
            try {
                $result = $product_controller->get_brands_ctr();
                echo json_encode($result);
            } catch (Exception $e) {
                echo json_encode(array('success' => false, 'message' => 'Brands error: ' . $e->getMessage()));
            }
            break;

        case 'get_product_count':
            $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
            $search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : null;
            
            if ($category_id && $brand_id && $search_query) {
                $result = $product_controller->get_products_with_filters_ctr($category_id, $brand_id, $search_query, null, 0);
                $count = $result['total_count'];
            } elseif ($category_id && $brand_id) {
                $result = $product_controller->get_products_with_filters_ctr($category_id, $brand_id, null, null, 0);
                $count = $result['total_count'];
            } elseif ($category_id && $search_query) {
                $result = $product_controller->get_products_with_filters_ctr($category_id, null, $search_query, null, 0);
                $count = $result['total_count'];
            } elseif ($brand_id && $search_query) {
                $result = $product_controller->get_products_with_filters_ctr(null, $brand_id, $search_query, null, 0);
                $count = $result['total_count'];
            } elseif ($category_id) {
                $result = $product_controller->filter_products_by_category_ctr($category_id, null, 0);
                $count = $result['total_count'];
            } elseif ($brand_id) {
                $result = $product_controller->filter_products_by_brand_ctr($brand_id, null, 0);
                $count = $result['total_count'];
            } elseif ($search_query) {
                $result = $product_controller->search_products_ctr($search_query, null, 0);
                $count = $result['total_count'];
            } else {
                $result = $product_controller->view_all_products_ctr(null, 0);
                $count = $result['total_count'];
            }
            
            echo json_encode(array('success' => true, 'count' => $count));
            break;

        default:
            echo json_encode(array('success' => false, 'message' => 'Invalid action'));
            break;
    }
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => 'An error occurred: ' . $e->getMessage()));
}
?>
