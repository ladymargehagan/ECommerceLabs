<?php
require_once '../settings/db_class.php';

class category_class extends db_connection
{
    public function add_category($cat_name, $created_by, $cat_image = '')
    {
        $check_sql = "SELECT cat_id FROM categories WHERE cat_name = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        $sql = "INSERT INTO categories (cat_name, created_by, cat_image) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sis", $cat_name, $created_by, $cat_image);
        $result = $stmt->execute();
        
        if (!$result) {
            $sql = "INSERT INTO categories (cat_name, cat_image) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $cat_name, $cat_image);
            $result = $stmt->execute();
        }
        
        if ($result) {
            $insert_id = $this->db->insert_id;
            $stmt->close();
            return $insert_id;
        }
        
        $stmt->close();
        return false;
    }

    public function get_categories_by_user($user_id)
    {
        $sql = "SELECT * FROM categories WHERE created_by = ? ORDER BY cat_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        $stmt->close();
        
        if (empty($categories)) {
            $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
            $result = $this->db_fetch_all($sql);
            return $result ? $result : array();
        }
        
        return $categories;
    }

    public function get_category_by_id($cat_id, $user_id)
    {
        $sql = "SELECT * FROM categories WHERE cat_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        
        if (!$category) {
            $sql = "SELECT * FROM categories WHERE cat_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $category = $result->fetch_assoc();
            $stmt->close();
        }
        
        return $category;
    }

    public function update_category($cat_id, $cat_name, $user_id, $cat_image = '')
    {
        $check_sql = "SELECT cat_id FROM categories WHERE cat_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category_exists = $result->fetch_assoc();
        $stmt->close();
        
        if (!$category_exists) {
            $check_sql = "SELECT cat_id FROM categories WHERE cat_id = ?";
            $stmt = $this->db->prepare($check_sql);
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $category_exists = $result->fetch_assoc();
            $stmt->close();
        }
        
        if (!$category_exists) {
            return false;
        }

        $check_name_sql = "SELECT cat_id FROM categories WHERE cat_name = ? AND cat_id != ?";
        $stmt = $this->db->prepare($check_name_sql);
        $stmt->bind_param("si", $cat_name, $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $name_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($name_exists) {
            return false;
        }

        $sql = "UPDATE categories SET cat_name = ?, cat_image = ? WHERE cat_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssii", $cat_name, $cat_image, $cat_id, $user_id);
        $result = $stmt->execute();
        
        if (!$result) {
            $sql = "UPDATE categories SET cat_name = ?, cat_image = ? WHERE cat_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $cat_name, $cat_image, $cat_id);
            $result = $stmt->execute();
        }
        
        $stmt->close();
        return $result;
    }

    public function delete_category($cat_id, $user_id)
    {
        $check_sql = "SELECT cat_id FROM categories WHERE cat_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category_exists = $result->fetch_assoc();
        $stmt->close();
        
        if (!$category_exists) {
            $check_sql = "SELECT cat_id FROM categories WHERE cat_id = ?";
            $stmt = $this->db->prepare($check_sql);
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $category_exists = $result->fetch_assoc();
            $stmt->close();
        }
        
        if (!$category_exists) {
            return false;
        }

        $check_products_sql = "SELECT product_id FROM products WHERE product_cat = ?";
        $stmt = $this->db->prepare($check_products_sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_exists = $result->fetch_assoc();
        $stmt->close();
        
        if ($product_exists) {
            return false;
        }

        $sql = "DELETE FROM categories WHERE cat_id = ? AND created_by = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $cat_id, $user_id);
        $result = $stmt->execute();
        
        if (!$result) {
            $sql = "DELETE FROM categories WHERE cat_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $cat_id);
            $result = $stmt->execute();
        }
        
        $stmt->close();
        return $result;
    }

    public function category_name_exists($cat_name, $exclude_id = null)
    {
        $sql = "SELECT cat_id FROM categories WHERE cat_name = ?";
        if ($exclude_id) {
            $sql .= " AND cat_id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($exclude_id) {
            $stmt->bind_param("si", $cat_name, $exclude_id);
        } else {
            $stmt->bind_param("s", $cat_name);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->fetch_assoc();
        $stmt->close();
        
        return $exists ? true : false;
    }
}
?>
