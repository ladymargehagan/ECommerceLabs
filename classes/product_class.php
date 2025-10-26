<?php
require_once '../settings/db_class.php';

class product_class extends db_connection
{
    public function add_product($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords)
    {
        // Check if product title already exists
        $check_sql = "SELECT product_id FROM products WHERE product_title = '$product_title'";
        if ($this->db_fetch_one($check_sql)) {
            return false;
        }

        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                VALUES ('$product_cat', '$product_brand', '$product_title', '$product_price', '$product_desc', '$product_image', '$product_keywords')";
        $result = $this->db_write_query($sql);
        
        if ($result) {
            // Return the ID of the inserted product
            return $this->db->insert_id;
        }
        
        return false;
    }

    public function get_all_products()
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY c.cat_name ASC, b.brand_name ASC, p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_product_by_id($product_id)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = '$product_id'";
        $result = $this->db_fetch_one($sql);
        
        return $result;
    }

    public function update_product($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords)
    {
        // Check if product exists
        $check_sql = "SELECT product_id FROM products WHERE product_id = '$product_id'";
        $product_exists = $this->db_fetch_one($check_sql);
        
        if (!$product_exists) {
            return false;
        }

        // Check if new title already exists (excluding current product)
        $check_name_sql = "SELECT product_id FROM products WHERE product_title = '$product_title' AND product_id != '$product_id'";
        if ($this->db_fetch_one($check_name_sql)) {
            return false;
        }

        $sql = "UPDATE products SET 
                product_cat = '$product_cat', 
                product_brand = '$product_brand', 
                product_title = '$product_title', 
                product_price = '$product_price', 
                product_desc = '$product_desc', 
                product_image = '$product_image', 
                product_keywords = '$product_keywords' 
                WHERE product_id = '$product_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function delete_product($product_id)
    {
        // Check if product exists
        $check_sql = "SELECT product_id FROM products WHERE product_id = '$product_id'";
        $product_exists = $this->db_fetch_one($check_sql);
        
        if (!$product_exists) {
            return false;
        }

        // Check if product is used in cart or orders
        $check_cart_sql = "SELECT p_id FROM cart WHERE p_id = '$product_id'";
        $check_orders_sql = "SELECT product_id FROM orderdetails WHERE product_id = '$product_id'";
        
        if ($this->db_fetch_one($check_cart_sql) || $this->db_fetch_one($check_orders_sql)) {
            return false;
        }

        $sql = "DELETE FROM products WHERE product_id = '$product_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function get_categories()
    {
        $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_brands()
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_all_products_count()
    {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['total'] : 0;
    }
    
    public function get_products_paginated($limit, $offset)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_title ASC
                LIMIT $limit OFFSET $offset";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }
    
    public function filter_products($category, $brand, $sort)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE 1=1";
        
        // Add category filter
        if ($category !== 'all' && is_numeric($category)) {
            $sql .= " AND p.product_cat = '$category'";
        }
        
        // Add brand filter
        if ($brand !== 'all' && is_numeric($brand)) {
            $sql .= " AND p.product_brand = '$brand'";
        }
        
        // Add sorting
        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY p.product_title ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY p.product_title DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY p.product_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.product_price DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY p.product_id DESC";
                break;
            default:
                $sql .= " ORDER BY p.product_title ASC";
                break;
        }
        
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    // ===== CUSTOMER-FACING METHODS =====
    
    /**
     * View all products with full details for customer display
     * @return array Array of products with category and brand information
     */
    public function view_all_products()
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id IS NOT NULL
                ORDER BY p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Search products by query string
     * @param string $query Search term
     * @return array Array of matching products
     */
    public function search_products($query)
    {
        $query = $this->db->real_escape_string($query);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE (p.product_title LIKE '%$query%' 
                OR p.product_desc LIKE '%$query%' 
                OR p.product_keywords LIKE '%$query%'
                OR c.cat_name LIKE '%$query%'
                OR b.brand_name LIKE '%$query%')
                AND p.product_id IS NOT NULL
                ORDER BY 
                    CASE 
                        WHEN p.product_title LIKE '%$query%' THEN 1
                        WHEN c.cat_name LIKE '%$query%' THEN 2
                        WHEN b.brand_name LIKE '%$query%' THEN 3
                        ELSE 4
                    END,
                    p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Filter products by category ID
     * @param int $cat_id Category ID
     * @return array Array of products in the specified category
     */
    public function filter_products_by_category($cat_id)
    {
        $cat_id = $this->db->real_escape_string($cat_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = '$cat_id'
                AND p.product_id IS NOT NULL
                ORDER BY p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Filter products by brand ID
     * @param int $brand_id Brand ID
     * @return array Array of products from the specified brand
     */
    public function filter_products_by_brand($brand_id)
    {
        $brand_id = $this->db->real_escape_string($brand_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = '$brand_id'
                AND p.product_id IS NOT NULL
                ORDER BY p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * View single product with full details
     * @param int $id Product ID
     * @return array|null Product details or null if not found
     */
    public function view_single_product($id)
    {
        $id = $this->db->real_escape_string($id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = '$id'";
        $result = $this->db_fetch_one($sql);
        
        return $result;
    }

    // ===== ADDITIONAL USEFUL METHODS =====

    /**
     * Get featured products (newest products)
     * @param int $limit Number of products to return
     * @return array Array of featured products
     */
    public function get_featured_products($limit = 8)
    {
        $limit = (int)$limit;
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id IS NOT NULL
                ORDER BY p.product_id DESC
                LIMIT $limit";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Get products by price range
     * @param float $min_price Minimum price
     * @param float $max_price Maximum price
     * @return array Array of products in price range
     */
    public function get_products_by_price_range($min_price, $max_price)
    {
        $min_price = (float)$min_price;
        $max_price = (float)$max_price;
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_price >= $min_price 
                AND p.product_price <= $max_price
                AND p.product_id IS NOT NULL
                ORDER BY p.product_price ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Get related products (same category, different products)
     * @param int $product_id Current product ID
     * @param int $category_id Category ID
     * @param int $limit Number of related products to return
     * @return array Array of related products
     */
    public function get_related_products($product_id, $category_id, $limit = 4)
    {
        $product_id = (int)$product_id;
        $category_id = (int)$category_id;
        $limit = (int)$limit;
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = $category_id 
                AND p.product_id != $product_id
                AND p.product_id IS NOT NULL
                ORDER BY RAND()
                LIMIT $limit";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Get products with pagination for customer display
     * @param int $limit Number of products per page
     * @param int $offset Offset for pagination
     * @param string $sort Sort order (name_asc, name_desc, price_asc, price_desc, newest)
     * @return array Array of products with pagination
     */
    public function get_products_paginated_customer($limit, $offset, $sort = 'name_asc')
    {
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id IS NOT NULL";
        
        // Add sorting
        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY p.product_title ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY p.product_title DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY p.product_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.product_price DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY p.product_id DESC";
                break;
            default:
                $sql .= " ORDER BY p.product_title ASC";
                break;
        }
        
        $sql .= " LIMIT $limit OFFSET $offset";
        
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Get total count of products for pagination
     * @return int Total number of products
     */
    public function get_total_products_count()
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE product_id IS NOT NULL";
        $result = $this->db_fetch_one($sql);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Get products by multiple categories
     * @param array $category_ids Array of category IDs
     * @return array Array of products from specified categories
     */
    public function get_products_by_categories($category_ids)
    {
        if (empty($category_ids) || !is_array($category_ids)) {
            return array();
        }
        
        $category_ids = array_map('intval', $category_ids);
        $category_list = implode(',', $category_ids);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat IN ($category_list)
                AND p.product_id IS NOT NULL
                ORDER BY c.cat_name ASC, p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Get products by multiple brands
     * @param array $brand_ids Array of brand IDs
     * @return array Array of products from specified brands
     */
    public function get_products_by_brands($brand_ids)
    {
        if (empty($brand_ids) || !is_array($brand_ids)) {
            return array();
        }
        
        $brand_ids = array_map('intval', $brand_ids);
        $brand_list = implode(',', $brand_ids);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, c.cat_image, b.brand_image
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand IN ($brand_list)
                AND p.product_id IS NOT NULL
                ORDER BY b.brand_name ASC, p.product_title ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    /**
     * Get product statistics for dashboard
     * @return array Array of product statistics
     */
    public function get_product_statistics()
    {
        $stats = array();
        
        // Total products
        $sql = "SELECT COUNT(*) as total FROM products WHERE product_id IS NOT NULL";
        $result = $this->db_fetch_one($sql);
        $stats['total_products'] = $result ? (int)$result['total'] : 0;
        
        // Products by category
        $sql = "SELECT c.cat_name, COUNT(p.product_id) as count 
                FROM categories c 
                LEFT JOIN products p ON c.cat_id = p.product_cat 
                GROUP BY c.cat_id, c.cat_name 
                ORDER BY count DESC";
        $result = $this->db_fetch_all($sql);
        $stats['by_category'] = $result ? $result : array();
        
        // Products by brand
        $sql = "SELECT b.brand_name, COUNT(p.product_id) as count 
                FROM brands b 
                LEFT JOIN products p ON b.brand_id = p.product_brand 
                GROUP BY b.brand_id, b.brand_name 
                ORDER BY count DESC";
        $result = $this->db_fetch_all($sql);
        $stats['by_brand'] = $result ? $result : array();
        
        // Price range
        $sql = "SELECT MIN(product_price) as min_price, MAX(product_price) as max_price, AVG(product_price) as avg_price 
                FROM products WHERE product_id IS NOT NULL";
        $result = $this->db_fetch_one($sql);
        $stats['price_range'] = $result ? $result : array();
        
        return $stats;
}
?>
