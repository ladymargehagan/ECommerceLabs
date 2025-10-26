<?php
require_once '../classes/category_class.php';

class category_controller extends category_class
{

    public function add_category_ctr($kwargs)
    {
        $cat_name = $kwargs['cat_name'];
        $created_by = $kwargs['created_by'];
        $cat_image = $kwargs['cat_image'] ?? '';

        if (empty($cat_name)) {
            return array('success' => false, 'message' => 'Category name is required');
        }

        if (strlen($cat_name) > 100) {
            return array('success' => false, 'message' => 'Category name must be less than 100 characters');
        }

        if ($this->category_name_exists($cat_name)) {
            return array('success' => false, 'message' => 'Category name already exists');
        }

        $result = $this->add_category($cat_name, $created_by, $cat_image);
        
        if ($result) {
            return array('success' => true, 'message' => 'Category added successfully', 'category_id' => $result);
        } else {
            return array('success' => false, 'message' => 'Failed to add category');
        }
    }

    public function get_categories_ctr($user_id)
    {
        $categories = $this->get_categories_by_user($user_id);
        
        if ($categories === false) {
            return array('success' => false, 'message' => 'Failed to fetch categories');
        }
        
        return array('success' => true, 'data' => $categories);
    }
    
    public function get_category_by_id_ctr($cat_id, $user_id)
    {
        $category = $this->get_category_by_id($cat_id, $user_id);
        
        if ($category) {
            return array('success' => true, 'data' => $category);
        } else {
            return array('success' => false, 'message' => 'Category not found');
        }
    }

    public function update_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'];
        $cat_name = $kwargs['cat_name'];
        $user_id = $kwargs['user_id'];
        $cat_image = $kwargs['cat_image'] ?? '';

        if (empty($cat_id) || empty($cat_name)) {
            return array('success' => false, 'message' => 'Category ID and name are required');
        }

        if (strlen($cat_name) > 100) {
            return array('success' => false, 'message' => 'Category name must be less than 100 characters');
        }

        if ($this->category_name_exists($cat_name, $cat_id)) {
            return array('success' => false, 'message' => 'Category name already exists');
        }

        $result = $this->update_category($cat_id, $cat_name, $user_id, $cat_image);
        
        if ($result) {
            return array('success' => true, 'message' => 'Category updated successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to update category or category not found');
        }
    }

    public function delete_category_ctr($cat_id, $user_id)
    {
        if (empty($cat_id)) {
            return array('success' => false, 'message' => 'Category ID is required');
        }

        $result = $this->delete_category($cat_id, $user_id);
        
        if ($result) {
            return array('success' => true, 'message' => 'Category deleted successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to delete category, category not found, or category is being used by products');
        }
    }

    public function upload_image_ctr($file, $category_id)
    {
        if (!isset($_SESSION['user_id'])) {
            return array('success' => false, 'message' => 'User not logged in');
        }

        $user_id = $_SESSION['user_id'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return array('success' => false, 'message' => 'File upload error');
        }

        // Check file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            return array('success' => false, 'message' => 'File size too large. Maximum 5MB allowed.');
        }

        // Check file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $file_type = mime_content_type($file['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            return array('success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.');
        }

        // Process filename
        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        
        // Create directory structure: uploads/u{user_id}/c{category_id}/
        $upload_dir = "../uploads/u{$user_id}/c{$category_id}/";
        
        // Ensure directory exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                return array('success' => false, 'message' => 'Failed to create upload directory');
            }
        }
        
        // Generate filename with timestamp
        $timestamp = time();
        $filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $image_path = "uploads/u{$user_id}/c{$category_id}/{$filename}";
            return array('success' => true, 'data' => $image_path);
        } else {
            return array('success' => false, 'message' => 'Failed to move uploaded file');
        }
    }
}
?>
