<?php
require_once '../settings/db_class.php';

class product_class extends db_connection
{
    public function add_product($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords)
    {
        // Check if product title already exists
        $check_sql = "SELECT product_id FROM products WHERE product_title = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("s", $product_title);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisssss", $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);
        $result = $stmt->execute();
        
        if ($result) {
            // Return the ID of the inserted product
            $insert_id = $this->db->insert_id;
            $stmt->close();
            return $insert_id;
        }
        
        $stmt->close();
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
                WHERE p.product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        
        return $product;
    }

    public function update_product($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords)
    {
        // Check if product exists
        $check_sql = "SELECT product_id FROM products WHERE product_id = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_exists = $result->fetch_assoc();
        $stmt->close();
        
        if (!$product_exists) {
            return false;
        }

        // Check if new title already exists (excluding current product)
        $check_name_sql = "SELECT product_id FROM products WHERE product_title = ? AND product_id != ?";
        $stmt = $this->db->prepare($check_name_sql);
        $stmt->bind_param("si", $product_title, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $title_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($title_exists) {
            return false;
        }

        $sql = "UPDATE products SET 
                product_cat = ?, 
                product_brand = ?, 
                product_title = ?, 
                product_price = ?, 
                product_desc = ?, 
                product_image = ?, 
                product_keywords = ? 
                WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisssssi", $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $product_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function delete_product($product_id)
    {
        // Check if product exists
        $check_sql = "SELECT product_id FROM products WHERE product_id = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_exists = $result->fetch_assoc();
        $stmt->close();
        
        if (!$product_exists) {
            return false;
        }

        // Check if product is used in cart or orders
        $check_cart_sql = "SELECT p_id FROM cart WHERE p_id = ?";
        $stmt = $this->db->prepare($check_cart_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($cart_exists) {
            return false;
        }
        
        $check_orders_sql = "SELECT product_id FROM orderdetails WHERE product_id = ?";
        $stmt = $this->db->prepare($check_orders_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($order_exists) {
            return false;
        }

        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $result = $stmt->execute();
        $stmt->close();
        
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
        $sql = "SELECT product_id FROM products WHERE product_title = ?";
        if ($exclude_id) {
            $sql .= " AND product_id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($exclude_id) {
            $stmt->bind_param("si", $product_title, $exclude_id);
        } else {
            $stmt->bind_param("s", $product_title);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->fetch_assoc();
        $stmt->close();
        
        return $exists ? true : false;
    }

    // View all products (alias for get_all_products)
    public function view_all_products()
    {
        return $this->get_all_products();
    }

    // Search products by query
    public function search_products($query)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_title LIKE ? 
                OR p.product_desc LIKE ? 
                OR p.product_keywords LIKE ?
                ORDER BY p.product_title ASC";
        
        $search_term = "%{$query}%";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        
        return $products;
    }

    // Filter products by category
    public function filter_products_by_category($cat_id)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = ?
                ORDER BY p.product_title ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        
        return $products;
    }

    // Filter products by brand
    public function filter_products_by_brand($brand_id)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = ?
                ORDER BY p.product_title ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        
        return $products;
    }

    // View single product (alias for get_product_by_id)
    public function view_single_product($id)
    {
        return $this->get_product_by_id($id);
    }

}
?>
