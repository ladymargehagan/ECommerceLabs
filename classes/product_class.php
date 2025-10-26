<?php
require_once __DIR__ . '/../settings/db_class.php';

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

    public function product_title_exists($product_title, $exclude_id = null)
    {
        $sql = "SELECT product_id FROM products WHERE product_title = '$product_title'";
        if ($exclude_id) {
            $sql .= " AND product_id != '$exclude_id'";
        }
        return $this->db_fetch_one($sql) ? true : false;
    }

    // Customer-facing methods for product display and search
    public function view_all_products($limit = null, $offset = 0)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : array();
    }

    public function search_products($query, $limit = null, $offset = 0)
    {
        $search_query = $this->db->real_escape_string($query);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_title LIKE '%$search_query%' 
                OR p.product_desc LIKE '%$search_query%' 
                OR p.product_keywords LIKE '%$search_query%'
                ORDER BY p.product_title ASC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : array();
    }

    public function filter_products_by_category($cat_id, $limit = null, $offset = 0)
    {
        $cat_id = $this->db->real_escape_string($cat_id);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = '$cat_id'
                ORDER BY p.product_title ASC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : array();
    }

    public function filter_products_by_brand($brand_id, $limit = null, $offset = 0)
    {
        $brand_id = $this->db->real_escape_string($brand_id);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = '$brand_id'
                ORDER BY p.product_title ASC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : array();
    }

    public function view_single_product($id)
    {
        $id = $this->db->real_escape_string($id);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = '$id'";
        $result = $this->db_fetch_one($sql);
        
        return $result;
    }

    public function get_products_count()
    {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['total'] : 0;
    }

    public function get_search_products_count($query)
    {
        $search_query = $this->db->real_escape_string($query);
        $sql = "SELECT COUNT(*) as total 
                FROM products p 
                WHERE p.product_title LIKE '%$search_query%' 
                OR p.product_desc LIKE '%$search_query%' 
                OR p.product_keywords LIKE '%$search_query%'";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['total'] : 0;
    }

    public function get_filtered_products_count($type, $id)
    {
        $id = $this->db->real_escape_string($id);
        if ($type === 'category') {
            $sql = "SELECT COUNT(*) as total FROM products WHERE product_cat = '$id'";
        } elseif ($type === 'brand') {
            $sql = "SELECT COUNT(*) as total FROM products WHERE product_brand = '$id'";
        } else {
            return 0;
        }
        
        $result = $this->db_fetch_one($sql);
        return $result ? $result['total'] : 0;
    }

}
?>
