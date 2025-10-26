<?php
require_once '../settings/db_class.php';

class brand_class extends db_connection
{
    public function add_brand($brand_name, $brand_image = '')
    {
        // Check if brand name already exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_name = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("s", $brand_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        $sql = "INSERT INTO brands (brand_name, brand_image) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $brand_name, $brand_image);
        $result = $stmt->execute();
        
        if ($result) {
            // Return the ID of the inserted brand
            $insert_id = $this->db->insert_id;
            $stmt->close();
            return $insert_id;
        }
        
        $stmt->close();
        return false;
    }

    public function get_brands_by_user($user_id)
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_all_brands()
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_brand_by_id($brand_id)
    {
        $sql = "SELECT * FROM brands WHERE brand_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $brand = $result->fetch_assoc();
        $stmt->close();
        
        return $brand;
    }

    public function update_brand($brand_id, $brand_name, $brand_image = '')
    {
        // Check if brand exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_id = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $brand_exists = $result->fetch_assoc();
        $stmt->close();
        
        if (!$brand_exists) {
            return false;
        }

        // Check if new name already exists (excluding current brand)
        $check_name_sql = "SELECT brand_id FROM brands WHERE brand_name = ? AND brand_id != ?";
        $stmt = $this->db->prepare($check_name_sql);
        $stmt->bind_param("si", $brand_name, $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $name_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($name_exists) {
            return false;
        }

        $sql = "UPDATE brands SET brand_name = ?, brand_image = ? WHERE brand_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $brand_name, $brand_image, $brand_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function delete_brand($brand_id)
    {
        // Check if brand exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_id = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $brand_exists = $result->fetch_assoc();
        $stmt->close();
        
        if (!$brand_exists) {
            return false;
        }

        // Check if brand is used in products
        $check_products_sql = "SELECT product_id FROM products WHERE product_brand = ?";
        $stmt = $this->db->prepare($check_products_sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($product_exists) {
            return false;
        }

        $sql = "DELETE FROM brands WHERE brand_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    public function get_categories_by_user($user_id)
    {
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
        $sql = "SELECT brand_id FROM brands WHERE brand_name = ?";
        if ($exclude_id) {
            $sql .= " AND brand_id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($exclude_id) {
            $stmt->bind_param("si", $brand_name, $exclude_id);
        } else {
            $stmt->bind_param("s", $brand_name);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->fetch_assoc();
        $stmt->close();
        
        return $exists ? true : false;
    }
}
?>
