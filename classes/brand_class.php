<?php
require_once '../settings/db_class.php';

class brand_class extends db_connection
{
    public function add_brand($brand_name)
    {
        // Check if brand name already exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        if ($this->db_fetch_one($check_sql)) {
            return false;
        }

        $sql = "INSERT INTO brands (brand_name) VALUES ('$brand_name')";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function get_all_brands()
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_brand_by_id($brand_id)
    {
        $sql = "SELECT * FROM brands WHERE brand_id = '$brand_id'";
        $result = $this->db_fetch_one($sql);
        
        return $result;
    }

    public function update_brand($brand_id, $brand_name)
    {
        // Check if brand exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_id = '$brand_id'";
        $brand_exists = $this->db_fetch_one($check_sql);
        
        if (!$brand_exists) {
            return false;
        }

        // Check if new name already exists (excluding current brand)
        $check_name_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND brand_id != '$brand_id'";
        if ($this->db_fetch_one($check_name_sql)) {
            return false;
        }

        $sql = "UPDATE brands SET brand_name = '$brand_name' WHERE brand_id = '$brand_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function delete_brand($brand_id)
    {
        // Check if brand exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_id = '$brand_id'";
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

    public function brand_name_exists($brand_name, $exclude_id = null)
    {
        $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        if ($exclude_id) {
            $sql .= " AND brand_id != '$exclude_id'";
        }
        return $this->db_fetch_one($sql) ? true : false;
    }
}
?>
