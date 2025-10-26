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
        
        return $result;
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

    public function upload_product_image($file, $product_id)
    {
        $upload_dir = '../images/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . $product_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return 'images/products/' . $filename;
        }
        
        return false;
    }
}
?>
