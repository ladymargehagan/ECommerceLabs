<?php
require_once '../settings/db_class.php';

class category_class extends db_connection
{
    public function add_category($cat_name, $created_by, $cat_image = '')
    {
        $check_sql = "SELECT cat_id FROM categories WHERE cat_name = '$cat_name'";
        if ($this->db_fetch_one($check_sql)) {
            return false;
        }

        $sql = "INSERT INTO categories (cat_name, created_by, cat_image) VALUES ('$cat_name', '$created_by', '$cat_image')";
        $result = $this->db_write_query($sql);
        
        if (!$result) {
            $sql = "INSERT INTO categories (cat_name, cat_image) VALUES ('$cat_name', '$cat_image')";
            $result = $this->db_write_query($sql);
        }
        
        if ($result) {
            // Return the ID of the inserted category
            return $this->db->insert_id;
        }
        
        return false;
    }

    public function get_categories_by_user($user_id)
    {
        $sql = "SELECT * FROM categories WHERE created_by = '$user_id' ORDER BY cat_name ASC";
        $result = $this->db_fetch_all($sql);
        
        if ($result === false || empty($result)) {
            $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
            $result = $this->db_fetch_all($sql);
        }
        
        return $result;
    }

    public function get_category_by_id($cat_id, $user_id)
    {
        $sql = "SELECT * FROM categories WHERE cat_id = '$cat_id' AND created_by = '$user_id'";
        $result = $this->db_fetch_one($sql);
        
        if (!$result) {
            $sql = "SELECT * FROM categories WHERE cat_id = '$cat_id'";
            $result = $this->db_fetch_one($sql);
        }
        
        return $result;
    }

    public function update_category($cat_id, $cat_name, $user_id, $cat_image = '')
    {
        $check_sql = "SELECT cat_id FROM categories WHERE cat_id = '$cat_id' AND created_by = '$user_id'";
        $category_exists = $this->db_fetch_one($check_sql);
        
        if (!$category_exists) {
            $check_sql = "SELECT cat_id FROM categories WHERE cat_id = '$cat_id'";
            $category_exists = $this->db_fetch_one($check_sql);
        }
        
        if (!$category_exists) {
            return false;
        }

        $check_name_sql = "SELECT cat_id FROM categories WHERE cat_name = '$cat_name' AND cat_id != '$cat_id'";
        if ($this->db_fetch_one($check_name_sql)) {
            return false;
        }

        $sql = "UPDATE categories SET cat_name = '$cat_name', cat_image = '$cat_image' WHERE cat_id = '$cat_id' AND created_by = '$user_id'";
        $result = $this->db_write_query($sql);
        
        if (!$result) {
            $sql = "UPDATE categories SET cat_name = '$cat_name', cat_image = '$cat_image' WHERE cat_id = '$cat_id'";
            $result = $this->db_write_query($sql);
        }
        
        return $result;
    }

    public function delete_category($cat_id, $user_id)
    {
        $check_sql = "SELECT cat_id FROM categories WHERE cat_id = '$cat_id' AND created_by = '$user_id'";
        $category_exists = $this->db_fetch_one($check_sql);
        
        if (!$category_exists) {
            $check_sql = "SELECT cat_id FROM categories WHERE cat_id = '$cat_id'";
            $category_exists = $this->db_fetch_one($check_sql);
        }
        
        if (!$category_exists) {
            return false;
        }

        $check_products_sql = "SELECT product_id FROM products WHERE product_cat = '$cat_id'";
        if ($this->db_fetch_one($check_products_sql)) {
            return false;
        }

        $sql = "DELETE FROM categories WHERE cat_id = '$cat_id' AND created_by = '$user_id'";
        $result = $this->db_write_query($sql);
        
        if (!$result) {
            $sql = "DELETE FROM categories WHERE cat_id = '$cat_id'";
            $result = $this->db_write_query($sql);
        }
        
        return $result;
    }

    public function category_name_exists($cat_name, $exclude_id = null)
    {
        $sql = "SELECT cat_id FROM categories WHERE cat_name = '$cat_name'";
        if ($exclude_id) {
            $sql .= " AND cat_id != '$exclude_id'";
        }
        return $this->db_fetch_one($sql) ? true : false;
    }
}
?>
