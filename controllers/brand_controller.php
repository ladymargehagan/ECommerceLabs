<?php
require_once __DIR__ . '/../classes/brand_class.php';

class brand_controller extends brand_class
{
    public function add_brand_ctr($kwargs)
    {
        $brand_name = $kwargs['brand_name'];
        $brand_image = $kwargs['brand_image'] ?? '';
        
        if (empty($brand_name)) {
            return array('success' => false, 'message' => 'Brand name is required');
        }
        
        // Check if brand name already exists
        if ($this->brand_name_exists($brand_name)) {
            return array('success' => false, 'message' => 'Brand name already exists');
        }
        
        $result = $this->add_brand($brand_name, $brand_image);
        
        if ($result) {
            return array('success' => true, 'message' => 'Brand added successfully', 'brand_id' => $result);
        } else {
            return array('success' => false, 'message' => 'Failed to add brand');
        }
    }
    
    public function get_brands_by_user_ctr($user_id)
    {
        // Ensure database connection
        if (!$this->db_connect()) {
            return array('success' => false, 'message' => 'Database connection failed');
        }
        
        $brands = $this->get_all_brands();
        
        // Ensure we return an array even if empty
        if (!is_array($brands)) {
            $brands = array();
        }
        
        return array('success' => true, 'data' => $brands);
    }
    
    public function get_all_brands_ctr()
    {
        $brands = $this->get_all_brands();
        return array('success' => true, 'data' => $brands);
    }
    
    public function get_brand_by_id_ctr($brand_id)
    {
        $brand = $this->get_brand_by_id($brand_id);
        
        if ($brand) {
            return array('success' => true, 'data' => $brand);
        } else {
            return array('success' => false, 'message' => 'Brand not found');
        }
    }
    
    public function update_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'];
        $brand_name = $kwargs['brand_name'];
        $brand_image = $kwargs['brand_image'] ?? '';
        
        if (empty($brand_name)) {
            return array('success' => false, 'message' => 'Brand name is required');
        }
        
        // Check if brand exists
        $brand = $this->get_brand_by_id($brand_id);
        if (!$brand) {
            return array('success' => false, 'message' => 'Brand not found');
        }
        
        // Check if new name already exists (excluding current brand)
        if ($this->brand_name_exists($brand_name, $brand_id)) {
            return array('success' => false, 'message' => 'Brand name already exists');
        }
        
        $result = $this->update_brand($brand_id, $brand_name, $brand_image);
        
        if ($result) {
            return array('success' => true, 'message' => 'Brand updated successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to update brand');
        }
    }
    
    public function delete_brand_ctr($brand_id)
    {
        if (empty($brand_id)) {
            return array('success' => false, 'message' => 'Brand ID is required');
        }

        if (!is_numeric($brand_id)) {
            return array('success' => false, 'message' => 'Invalid brand ID');
        }

        $brand_id = (int)$brand_id;
        
        // Check if brand exists
        $brand = $this->get_brand_by_id($brand_id);
        if (!$brand) {
            return array('success' => false, 'message' => 'Brand not found');
        }
        
        $result = $this->delete_brand($brand_id);
        
        if ($result) {
            return array('success' => true, 'message' => 'Brand deleted successfully');
        } else {
            return array('success' => false, 'message' => 'Cannot delete brand. It may be associated with products.');
        }
    }

    public function get_categories_ctr($user_id)
    {
        $categories = $this->get_categories_by_user($user_id);
        return array('success' => true, 'data' => $categories);
    }

    public function upload_image_ctr($file, $brand_id)
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
        
        // Create directory structure: uploads/u{user_id}/b{brand_id}/
        // Use absolute path based on controller file location (same as categories)
        $base_dir = dirname(dirname(__FILE__)); // Go from controllers/ to project root
        $upload_dir = "{$base_dir}/uploads/u{$user_id}/b{$brand_id}/";
        
        // Ensure directory exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                return array('success' => false, 'message' => 'Failed to create upload directory: ' . $upload_dir);
            }
        }
        
        // Generate filename with timestamp
        $timestamp = time();
        $filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $image_path = "uploads/u{$user_id}/b{$brand_id}/{$filename}";
            return array('success' => true, 'data' => $image_path);
        } else {
            return array('success' => false, 'message' => 'Failed to move uploaded file');
        }
    }
}
?>
