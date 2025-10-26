<?php
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

class product_actions {
    private $product_controller;
    
    public function __construct() {
        $this->product_controller = new product_controller();
    }
    
    // Get all products for customer display
    public function get_all_products() {
        return $this->product_controller->get_all_products_ctr();
    }
    
    // Search products by query for customer search
    public function search_products($query) {
        return $this->product_controller->search_products_ctr($query);
    }
    
    // Filter products by category for customer filtering
    public function filter_products_by_category($cat_id) {
        return $this->product_controller->filter_products_by_category_ctr($cat_id);
    }
    
    // Filter products by brand for customer filtering
    public function filter_products_by_brand($brand_id) {
        return $this->product_controller->filter_products_by_brand_ctr($brand_id);
    }
    
    // Get single product by ID for customer view
    public function get_single_product($id) {
        return $this->product_controller->get_product_by_id_ctr($id);
    }
    
    // Get categories for customer filtering dropdowns
    public function get_categories() {
        return $this->product_controller->get_categories_ctr();
    }
    
    // Get brands for customer filtering dropdowns
    public function get_brands() {
        return $this->product_controller->get_brands_ctr();
    }
}
?>