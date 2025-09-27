<?php
require_once '../settings/db_class.php';

class Category extends db_connection {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a new category
     * @param string $cat_name Category name
     * @param int $user_id User ID who created the category
     * @return array Returns array with success status, message, and data
     */
    public function add($cat_name, $user_id) {
        // Sanitize inputs
        $cat_name = trim($cat_name);
        $user_id = (int)$user_id;
        
        // Validate category name
        if (empty($cat_name)) {
            return [
                'success' => false,
                'message' => 'Category name is required.',
                'data' => null
            ];
        }
        
        if (strlen($cat_name) > 100) {
            return [
                'success' => false,
                'message' => 'Category name must be 100 characters or less.',
                'data' => null
            ];
        }
        
        //Checking if category name already exists in database
        $check_sql = "SELECT cat_id FROM categories WHERE cat_name = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Category name already exists. Please choose a different name.',
                'data' => null
            ];
        }
        
        //Inserting new category into database
        $sql = "INSERT INTO categories (cat_name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $cat_name);
        
        if ($stmt->execute()) {
            $cat_id = $this->db->insert_id;
            return [
                'success' => true,
                'message' => 'Category added successfully.',
                'data' => [
                    'cat_id' => $cat_id,
                    'cat_name' => $cat_name
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to add category. Please try again.',
                'data' => null
            ];
        }
    }
    
    /**
     * Get all categories
     * @return array Returns array with success status, message, and data
     */
    public function get_all() {
        $sql = "SELECT cat_id, cat_name FROM categories ORDER BY cat_name ASC";
        $result = $this->db->query($sql);
        
        if ($result) {
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            return [
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data' => $categories
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to retrieve categories.',
                'data' => null
            ];
        }
    }
    
    /**
     * Get category by ID
     * @param int $cat_id Category ID
     * @return array Returns array with success status, message, and data
     */
    public function get_by_id($cat_id) {
        $cat_id = (int)$cat_id;
        
        $sql = "SELECT cat_id, cat_name FROM categories WHERE cat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $category = $result->fetch_assoc();
            return [
                'success' => true,
                'message' => 'Category retrieved successfully.',
                'data' => $category
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Category not found.',
                'data' => null
            ];
        }
    }
    
    /**
     * Update category
     * @param int $cat_id Category ID
     * @param string $cat_name New category name
     * @return array Returns array with success status, message, and data
     */
    public function update($cat_id, $cat_name) {
        // Sanitize inputs
        $cat_id = (int)$cat_id;
        $cat_name = trim($cat_name);
        
        //Validating category name
        if (empty($cat_name)) {
            return [
                'success' => false,
                'message' => 'Category name is required.',
                'data' => null
            ];
        }
        
        if (strlen($cat_name) > 100) {
            return [
                'success' => false,
                'message' => 'Category name must be 100 characters or less.',
                'data' => null
            ];
        }
        
        //Checking if category exists
        $check_sql = "SELECT cat_id FROM categories WHERE cat_id = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return [
                'success' => false,
                'message' => 'Category not found.',
                'data' => null
            ];
        }
        
        //Checking if new name already exists (excluding current category)
        $check_name_sql = "SELECT cat_id FROM categories WHERE cat_name = ? AND cat_id != ?";
        $stmt = $this->db->prepare($check_name_sql);
        $stmt->bind_param("si", $cat_name, $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Category name already exists. Please choose a different name.',
                'data' => null
            ];
        }
        
        //Updating category
        $sql = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $cat_name, $cat_id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => [
                    'cat_id' => $cat_id,
                    'cat_name' => $cat_name
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update category. Please try again.',
                'data' => null
            ];
        }
    }
    
    /**
     * Delete category
     * @param int $cat_id Category ID
     * @return array Returns array with success status, message, and data
     */
    public function delete($cat_id) {
        $cat_id = (int)$cat_id;
        
        //Checking if category exists
        $check_sql = "SELECT cat_id FROM categories WHERE cat_id = ?";
        $stmt = $this->db->prepare($check_sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return [
                'success' => false,
                'message' => 'Category not found.',
                'data' => null
            ];
        }
        
        //Checking if category is being used by any products
        $check_products_sql = "SELECT product_id FROM products WHERE product_cat = ? LIMIT 1";
        $stmt = $this->db->prepare($check_products_sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete category. It is being used by one or more products.',
                'data' => null
            ];
        }
        
        // Delete category
        $sql = "DELETE FROM categories WHERE cat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Category deleted successfully.',
                'data' => null
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to delete category. Please try again.',
                'data' => null
            ];
        }
    }
}
?>
