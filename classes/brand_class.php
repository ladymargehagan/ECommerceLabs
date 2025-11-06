<?php
require_once __DIR__ . '/../settings/db_class.php';

class brand_class extends db_connection
{
    public function add_brand($brand_name, $brand_image = '')
    {
        // Ensure database connection is established
        if (!$this->db_connect()) {
            return false;
        }
        
        // Escape inputs to prevent SQL injection
        $brand_name = $this->db->real_escape_string($brand_name);
        $brand_image = $this->db->real_escape_string($brand_image);
        
        // Check if brand name already exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        if ($this->db_fetch_one($check_sql)) {
            return false;
        }

        $sql = "INSERT INTO brands (brand_name, brand_image) VALUES ('$brand_name', '$brand_image')";
        $result = $this->db_write_query($sql);
        
        if ($result) {
            // Return the inserted brand_id
            return $this->last_insert_id();
        }
        
        return false;
    }

    public function get_brands_by_user($user_id)
    {
        if (!$this->db_connect()) {
            return array();
        }
        $user_id = $this->db->real_escape_string($user_id);
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_all_brands()
    {
        if (!$this->db_connect()) {
            return array();
        }
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_brand_by_id($brand_id)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_id = $this->db->real_escape_string($brand_id);
        $sql = "SELECT * FROM brands WHERE brand_id = '$brand_id'";
        $result = $this->db_fetch_one($sql);
        
        return $result;
    }

    public function update_brand($brand_id, $brand_name, $brand_image = null)
    {
        // Ensure database connection is established
        if (!$this->db_connect()) {
            return false;
        }
        
        // Escape inputs
        $brand_id = $this->db->real_escape_string($brand_id);
        $brand_name = $this->db->real_escape_string($brand_name);
        
        // Check if brand exists and get current image
        $check_sql = "SELECT brand_image FROM brands WHERE brand_id = '$brand_id'";
        $brand_exists = $this->db_fetch_one($check_sql);
        
        if (!$brand_exists) {
            return false;
        }

        // Check if new name already exists (excluding current brand)
        $check_name_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND brand_id != '$brand_id'";
        if ($this->db_fetch_one($check_name_sql)) {
            return false;
        }

        // If no new image provided, keep the old one
        if ($brand_image === null || $brand_image === '') {
            $brand_image = $brand_exists['brand_image'];
        } else {
            $brand_image = $this->db->real_escape_string($brand_image);
        }

        $sql = "UPDATE brands SET brand_name = '$brand_name', brand_image = '$brand_image' WHERE brand_id = '$brand_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function delete_brand($brand_id)
    {
        // Ensure database connection
        if (!$this->db_connect()) {
            return false;
        }
        
        // Escape input
        $brand_id = $this->db->real_escape_string($brand_id);

        // Check if brand exists
        $check_sql = "SELECT brand_id, brand_image FROM brands WHERE brand_id = '$brand_id'";
        $brand_exists = $this->db_fetch_one($check_sql);
        
        if (!$brand_exists) {
            return false;
        }

        // Check if brand is used in products
        $check_products_sql = "SELECT product_id FROM products WHERE product_brand = '$brand_id'";
        if ($this->db_fetch_one($check_products_sql)) {
            return false;
        }

        $sql = "DELETE FROM brands WHERE brand_id = '$brand_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function get_categories_by_user($user_id)
    {
        if (!$this->db_connect()) {
            return array();
        }
        $user_id = $this->db->real_escape_string($user_id);
        $sql = "SELECT * FROM categories WHERE created_by = '$user_id' ORDER BY cat_name ASC";
        $result = $this->db_fetch_all($sql);
        
        if ($result === false || empty($result)) {
            $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
            $result = $this->db_fetch_all($sql);
        }
        
        return $result ? $result : array();
    }

    public function brand_name_exists($brand_name, $exclude_id = null)
    {
        if (!$this->db_connect()) {
            return false;
        }
        $brand_name = $this->db->real_escape_string($brand_name);
        $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        if ($exclude_id) {
            $exclude_id = $this->db->real_escape_string($exclude_id);
            $sql .= " AND brand_id != '$exclude_id'";
        }
        return $this->db_fetch_one($sql) ? true : false;
    }
}
?>