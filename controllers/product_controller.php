<?php
require_once '../classes/product_class.php';

class product_controller extends product_class
{
    public function add_product_ctr($kwargs)
    {
        $product_cat = $kwargs['product_cat'];
        $product_brand = $kwargs['product_brand'];
        $product_title = $kwargs['product_title'];
        $product_price = $kwargs['product_price'];
        $product_desc = $kwargs['product_desc'];
        $product_image = $kwargs['product_image'];
        $product_keywords = $kwargs['product_keywords'];
        
        if (empty($product_title)) {
            return array('success' => false, 'message' => 'Product title is required');
        }
        
        if (empty($product_cat)) {
            return array('success' => false, 'message' => 'Product category is required');
        }
        
        if (empty($product_brand)) {
            return array('success' => false, 'message' => 'Product brand is required');
        }
        
        if (empty($product_price) || !is_numeric($product_price) || $product_price <= 0) {
            return array('success' => false, 'message' => 'Valid product price is required');
        }
        
        // Check if product title already exists
        if ($this->product_title_exists($product_title)) {
            return array('success' => false, 'message' => 'Product title already exists');
        }
        
        $result = $this->add_product($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);
        
        if ($result) {
            return array('success' => true, 'message' => 'Product added successfully', 'product_id' => $result);
        } else {
            return array('success' => false, 'message' => 'Failed to add product');
        }
    }
    
    public function get_all_products_ctr()
    {
        $products = $this->get_all_products();
        return array('success' => true, 'data' => $products);
    }
    
    public function get_product_by_id_ctr($product_id)
    {
        $product = $this->get_product_by_id($product_id);
        
        if ($product) {
            return array('success' => true, 'data' => $product);
        } else {
            return array('success' => false, 'message' => 'Product not found');
        }
    }
    
    public function update_product_ctr($kwargs)
    {
        $product_id = $kwargs['product_id'];
        $product_cat = $kwargs['product_cat'];
        $product_brand = $kwargs['product_brand'];
        $product_title = $kwargs['product_title'];
        $product_price = $kwargs['product_price'];
        $product_desc = $kwargs['product_desc'];
        $product_image = $kwargs['product_image'];
        $product_keywords = $kwargs['product_keywords'];
        
        if (empty($product_title)) {
            return array('success' => false, 'message' => 'Product title is required');
        }
        
        if (empty($product_cat)) {
            return array('success' => false, 'message' => 'Product category is required');
        }
        
        if (empty($product_brand)) {
            return array('success' => false, 'message' => 'Product brand is required');
        }
        
        if (empty($product_price) || !is_numeric($product_price) || $product_price <= 0) {
            return array('success' => false, 'message' => 'Valid product price is required');
        }
        
        // Check if product exists
        $product = $this->get_product_by_id($product_id);
        if (!$product) {
            return array('success' => false, 'message' => 'Product not found');
        }
        
        // Check if new title already exists (excluding current product)
        if ($this->product_title_exists($product_title, $product_id)) {
            return array('success' => false, 'message' => 'Product title already exists');
        }
        
        $result = $this->update_product($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);
        
        if ($result) {
            return array('success' => true, 'message' => 'Product updated successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to update product');
        }
    }
    
    public function delete_product_ctr($product_id)
    {
        // Check if product exists
        $product = $this->get_product_by_id($product_id);
        if (!$product) {
            return array('success' => false, 'message' => 'Product not found');
        }
        
        $result = $this->delete_product($product_id);
        
        if ($result) {
            return array('success' => true, 'message' => 'Product deleted successfully');
        } else {
            return array('success' => false, 'message' => 'Cannot delete product. It may be in cart or orders.');
        }
    }

    public function get_categories_ctr()
    {
        $categories = $this->get_categories();
        return array('success' => true, 'data' => $categories);
    }

    public function get_brands_ctr()
    {
        $brands = $this->get_brands();
        return array('success' => true, 'data' => $brands);
    }

    public function get_all_products_count_ctr()
    {
        $count = $this->get_all_products_count();
        return array('success' => true, 'data' => $count);
    }
    
    public function get_products_paginated_ctr($limit, $offset)
    {
        $products = $this->get_products_paginated($limit, $offset);
        return array('success' => true, 'data' => $products);
    }
    
    public function filter_products_ctr($category, $brand, $sort)
    {
        $products = $this->filter_products($category, $brand, $sort);
        return array('success' => true, 'data' => $products);
    }

    // ===== CUSTOMER-FACING CONTROLLER METHODS =====
    
    /**
     * View all products for customer display
     * @return array Response with products data
     */
    public function view_all_products_ctr()
    {
        $products = $this->view_all_products();
        return array('success' => true, 'data' => $products);
    }

    /**
     * Search products with enhanced functionality
     * @param string $query Search term
     * @return array Response with search results
     */
    public function search_products_ctr($query)
    {
        if (empty(trim($query))) {
            return array('success' => false, 'message' => 'Search query is required');
        }
        
        $products = $this->search_products($query);
        return array('success' => true, 'data' => $products, 'query' => $query);
    }

    /**
     * Filter products by category
     * @param int $cat_id Category ID
     * @return array Response with filtered products
     */
    public function filter_products_by_category_ctr($cat_id)
    {
        if (!is_numeric($cat_id) || $cat_id <= 0) {
            return array('success' => false, 'message' => 'Valid category ID is required');
        }
        
        $products = $this->filter_products_by_category($cat_id);
        return array('success' => true, 'data' => $products, 'category_id' => $cat_id);
    }

    /**
     * Filter products by brand
     * @param int $brand_id Brand ID
     * @return array Response with filtered products
     */
    public function filter_products_by_brand_ctr($brand_id)
    {
        if (!is_numeric($brand_id) || $brand_id <= 0) {
            return array('success' => false, 'message' => 'Valid brand ID is required');
        }
        
        $products = $this->filter_products_by_brand($brand_id);
        return array('success' => true, 'data' => $products, 'brand_id' => $brand_id);
    }

    /**
     * View single product with full details
     * @param int $id Product ID
     * @return array Response with product details
     */
    public function view_single_product_ctr($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return array('success' => false, 'message' => 'Valid product ID is required');
        }
        
        $product = $this->view_single_product($id);
        
        if ($product) {
            return array('success' => true, 'data' => $product);
        } else {
            return array('success' => false, 'message' => 'Product not found');
        }
    }

    // ===== MARKETING & DISPLAY CONTROLLER METHODS =====

    /**
     * Get featured products for homepage
     * @param int $limit Number of products to return
     * @return array Response with featured products
     */
    public function get_featured_products_ctr($limit = 8)
    {
        $limit = (int)$limit;
        if ($limit <= 0 || $limit > 50) {
            $limit = 8; // Default limit
        }
        
        $products = $this->get_featured_products($limit);
        return array('success' => true, 'data' => $products, 'limit' => $limit);
    }

    /**
     * Get products by price range
     * @param float $min_price Minimum price
     * @param float $max_price Maximum price
     * @return array Response with products in price range
     */
    public function get_products_by_price_range_ctr($min_price, $max_price)
    {
        $min_price = (float)$min_price;
        $max_price = (float)$max_price;
        
        if ($min_price < 0 || $max_price < 0) {
            return array('success' => false, 'message' => 'Prices cannot be negative');
        }
        
        if ($min_price > $max_price) {
            return array('success' => false, 'message' => 'Minimum price cannot be greater than maximum price');
        }
        
        $products = $this->get_products_by_price_range($min_price, $max_price);
        return array('success' => true, 'data' => $products, 'min_price' => $min_price, 'max_price' => $max_price);
    }

    /**
     * Get related products for cross-selling
     * @param int $product_id Current product ID
     * @param int $category_id Category ID
     * @param int $limit Number of related products
     * @return array Response with related products
     */
    public function get_related_products_ctr($product_id, $category_id, $limit = 4)
    {
        $product_id = (int)$product_id;
        $category_id = (int)$category_id;
        $limit = (int)$limit;
        
        if ($product_id <= 0 || $category_id <= 0) {
            return array('success' => false, 'message' => 'Valid product and category IDs are required');
        }
        
        if ($limit <= 0 || $limit > 20) {
            $limit = 4; // Default limit
        }
        
        $products = $this->get_related_products($product_id, $category_id, $limit);
        return array('success' => true, 'data' => $products, 'product_id' => $product_id, 'category_id' => $category_id, 'limit' => $limit);
    }

    // ===== ADVANCED FILTERING CONTROLLER METHODS =====

    /**
     * Get products with pagination for customer display
     * @param int $limit Products per page
     * @param int $offset Offset for pagination
     * @param string $sort Sort order
     * @return array Response with paginated products
     */
    public function get_products_paginated_customer_ctr($limit, $offset, $sort = 'name_asc')
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        if ($limit <= 0 || $limit > 100) {
            $limit = 12; // Default limit
        }
        
        if ($offset < 0) {
            $offset = 0;
        }
        
        $valid_sorts = ['name_asc', 'name_desc', 'price_asc', 'price_desc', 'newest'];
        if (!in_array($sort, $valid_sorts)) {
            $sort = 'name_asc';
        }
        
        $products = $this->get_products_paginated_customer($limit, $offset, $sort);
        return array('success' => true, 'data' => $products, 'limit' => $limit, 'offset' => $offset, 'sort' => $sort);
    }

    /**
     * Get total products count for pagination
     * @return array Response with total count
     */
    public function get_total_products_count_ctr()
    {
        $count = $this->get_total_products_count();
        return array('success' => true, 'data' => $count);
    }

    /**
     * Get products by multiple categories
     * @param array $category_ids Array of category IDs
     * @return array Response with products from specified categories
     */
    public function get_products_by_categories_ctr($category_ids)
    {
        if (empty($category_ids) || !is_array($category_ids)) {
            return array('success' => false, 'message' => 'Category IDs array is required');
        }
        
        // Validate all category IDs are numeric
        $valid_ids = array();
        foreach ($category_ids as $id) {
            if (is_numeric($id) && $id > 0) {
                $valid_ids[] = (int)$id;
            }
        }
        
        if (empty($valid_ids)) {
            return array('success' => false, 'message' => 'Valid category IDs are required');
        }
        
        $products = $this->get_products_by_categories($valid_ids);
        return array('success' => true, 'data' => $products, 'category_ids' => $valid_ids);
    }

    /**
     * Get products by multiple brands
     * @param array $brand_ids Array of brand IDs
     * @return array Response with products from specified brands
     */
    public function get_products_by_brands_ctr($brand_ids)
    {
        if (empty($brand_ids) || !is_array($brand_ids)) {
            return array('success' => false, 'message' => 'Brand IDs array is required');
        }
        
        // Validate all brand IDs are numeric
        $valid_ids = array();
        foreach ($brand_ids as $id) {
            if (is_numeric($id) && $id > 0) {
                $valid_ids[] = (int)$id;
            }
        }
        
        if (empty($valid_ids)) {
            return array('success' => false, 'message' => 'Valid brand IDs are required');
        }
        
        $products = $this->get_products_by_brands($valid_ids);
        return array('success' => true, 'data' => $products, 'brand_ids' => $valid_ids);
    }

    // ===== ANALYTICS & STATISTICS CONTROLLER METHODS =====

    /**
     * Get product statistics for dashboard
     * @return array Response with product statistics
     */
    public function get_product_statistics_ctr()
    {
        $stats = $this->get_product_statistics();
        return array('success' => true, 'data' => $stats);
    }

    // ===== UTILITY CONTROLLER METHODS =====

    /**
     * Check if product title exists (helper method)
     * @param string $title Product title
     * @param int $exclude_id Product ID to exclude (for updates)
     * @return bool True if title exists
     */
    private function product_title_exists($title, $exclude_id = null)
    {
        $title = $this->db->real_escape_string($title);
        $sql = "SELECT product_id FROM products WHERE product_title = '$title'";
        
        if ($exclude_id) {
            $exclude_id = (int)$exclude_id;
            $sql .= " AND product_id != $exclude_id";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }

    /**
     * Validate product data
     * @param array $data Product data
     * @return array Validation result
     */
    private function validate_product_data($data)
    {
        $errors = array();
        
        if (empty($data['product_title'])) {
            $errors[] = 'Product title is required';
        }
        
        if (empty($data['product_cat']) || !is_numeric($data['product_cat'])) {
            $errors[] = 'Valid product category is required';
        }
        
        if (empty($data['product_brand']) || !is_numeric($data['product_brand'])) {
            $errors[] = 'Valid product brand is required';
        }
        
        if (empty($data['product_price']) || !is_numeric($data['product_price']) || $data['product_price'] <= 0) {
            $errors[] = 'Valid product price is required';
        }
        
        return $errors;
    }

    /**
     * Get products with advanced filtering and sorting
     * @param array $filters Filter parameters
     * @return array Response with filtered products
     */
    public function get_products_advanced_ctr($filters)
    {
        $category = isset($filters['category']) ? $filters['category'] : 'all';
        $brand = isset($filters['brand']) ? $filters['brand'] : 'all';
        $sort = isset($filters['sort']) ? $filters['sort'] : 'name_asc';
        $min_price = isset($filters['min_price']) ? (float)$filters['min_price'] : 0;
        $max_price = isset($filters['max_price']) ? (float)$filters['max_price'] : 999999;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 12;
        $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
        
        // Validate parameters
        if ($min_price < 0 || $max_price < 0) {
            return array('success' => false, 'message' => 'Prices cannot be negative');
        }
        
        if ($min_price > $max_price) {
            return array('success' => false, 'message' => 'Minimum price cannot be greater than maximum price');
        }
        
        if ($limit <= 0 || $limit > 100) {
            $limit = 12;
        }
        
        if ($offset < 0) {
            $offset = 0;
        }
        
        // Get products with all filters applied
        $products = $this->filter_products($category, $brand, $sort);
        
        // Apply price range filter
        if ($min_price > 0 || $max_price < 999999) {
            $products = array_filter($products, function($product) use ($min_price, $max_price) {
                return $product['product_price'] >= $min_price && $product['product_price'] <= $max_price;
            });
        }
        
        // Apply pagination
        $total_count = count($products);
        $products = array_slice($products, $offset, $limit);
        
        return array(
            'success' => true, 
            'data' => $products, 
            'total_count' => $total_count,
            'filters' => $filters
        );
    }

}
?>
