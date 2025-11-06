<?php
require_once __DIR__ . '/../classes/product_class.php';

class product_controller extends product_class
{
    public function add_product_ctr($kwargs)
    {
        $product_cat = $kwargs['product_cat'];
        $product_brand = $kwargs['product_brand'];
        $product_title = $kwargs['product_title'];
        $product_price = $kwargs['product_price'];
        $product_desc = $kwargs['product_desc'];
        $product_image = $kwargs['product_image'];
        $product_keywords = $kwargs['product_keywords'];
        
        if (empty($product_title)) {
            return array('success' => false, 'message' => 'Product title is required');
        }
        
        if (empty($product_cat)) {
            return array('success' => false, 'message' => 'Product category is required');
        }
        
        if (empty($product_brand)) {
            return array('success' => false, 'message' => 'Product brand is required');
        }
        
        if (empty($product_price) || !is_numeric($product_price) || $product_price <= 0) {
            return array('success' => false, 'message' => 'Valid product price is required');
        }
        
        // Check if product title already exists
        if ($this->product_title_exists($product_title)) {
            return array('success' => false, 'message' => 'Product title already exists');
        }
        
        $result = $this->add_product($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);
        
        if ($result) {
            return array('success' => true, 'message' => 'Product added successfully', 'product_id' => $result);
        } else {
            return array('success' => false, 'message' => 'Failed to add product');
        }
    }
    
    public function get_all_products_ctr()
    {
        $products = $this->get_all_products();
        return array('success' => true, 'data' => $products);
    }
    
    public function get_product_by_id_ctr($product_id)
    {
        $product = $this->get_product_by_id($product_id);
        
        if ($product) {
            return array('success' => true, 'data' => $product);
        } else {
            return array('success' => false, 'message' => 'Product not found');
        }
    }
    
    public function update_product_ctr($kwargs)
    {
        $product_id = $kwargs['product_id'];
        $product_cat = $kwargs['product_cat'];
        $product_brand = $kwargs['product_brand'];
        $product_title = $kwargs['product_title'];
        $product_price = $kwargs['product_price'];
        $product_desc = $kwargs['product_desc'];
        $product_image = $kwargs['product_image'];
        $product_keywords = $kwargs['product_keywords'];
        
        if (empty($product_title)) {
            return array('success' => false, 'message' => 'Product title is required');
        }
        
        if (empty($product_cat)) {
            return array('success' => false, 'message' => 'Product category is required');
        }
        
        if (empty($product_brand)) {
            return array('success' => false, 'message' => 'Product brand is required');
        }
        
        if (empty($product_price) || !is_numeric($product_price) || $product_price <= 0) {
            return array('success' => false, 'message' => 'Valid product price is required');
        }
        
        // Check if product exists
        $product = $this->get_product_by_id($product_id);
        if (!$product) {
            return array('success' => false, 'message' => 'Product not found');
        }
        
        // Check if new title already exists (excluding current product)
        if ($this->product_title_exists($product_title, $product_id)) {
            return array('success' => false, 'message' => 'Product title already exists');
        }
        
        $result = $this->update_product($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);
        
        if ($result) {
            return array('success' => true, 'message' => 'Product updated successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to update product');
        }
    }
    
    public function delete_product_ctr($product_id)
    {
        // Check if product exists
        $product = $this->get_product_by_id($product_id);
        if (!$product) {
            return array('success' => false, 'message' => 'Product not found');
        }
        
        $result = $this->delete_product($product_id);
        
        if ($result) {
            return array('success' => true, 'message' => 'Product deleted successfully');
        } else {
            return array('success' => false, 'message' => 'Cannot delete product. It may be in cart or orders.');
        }
    }

    public function get_categories_ctr()
    {
        $categories = $this->get_categories();
        return array('success' => true, 'data' => $categories);
    }

    public function get_brands_ctr()
    {
        $brands = $this->get_brands();
        return array('success' => true, 'data' => $brands);
    }

    public function upload_image_ctr($file, $product_id)
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
        
        // Create directory structure: uploads/u{user_id}/p{product_id}/
        $upload_dir = "../uploads/u{$user_id}/p{$product_id}/";
        
        // Ensure parent directory exists first
        $parent_dir = "../uploads/u{$user_id}/";
        if (!is_dir($parent_dir)) {
            if (!mkdir($parent_dir, 0777, true)) {
                return array('success' => false, 'message' => 'Failed to create parent upload directory');
            }
        }
        
        // Ensure product directory exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                return array('success' => false, 'message' => 'Failed to create upload directory. Please check server permissions.');
            }
        }
        
        // Generate filename with timestamp
        $timestamp = time();
        $filename = "img_{$sanitizedName}_{$timestamp}.{$extension}";
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $image_path = "uploads/u{$user_id}/p{$product_id}/{$filename}";
            return array('success' => true, 'data' => $image_path);
        } else {
            return array('success' => false, 'message' => 'Failed to move uploaded file. Please check server permissions.');
        }
    }

    // Enhanced controller methods for customer-facing functionality
    
    public function view_all_products_ctr($limit = null, $offset = 0)
    {
        $products = $this->view_all_products($limit, $offset);
        $total_count = $this->get_products_count();
        
        return array(
            'success' => true, 
            'data' => $products,
            'total_count' => $total_count,
            'limit' => $limit,
            'offset' => $offset
        );
    }

    public function search_products_ctr($query, $limit = null, $offset = 0)
    {
        if (empty(trim($query))) {
            return array('success' => false, 'message' => 'Search query is required');
        }

        $products = $this->search_products($query, $limit, $offset);
        $total_count = $this->get_search_count($query);
        
        return array(
            'success' => true, 
            'data' => $products,
            'total_count' => $total_count,
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset
        );
    }

    public function filter_products_by_category_ctr($cat_id, $limit = null, $offset = 0)
    {
        if (empty($cat_id)) {
            return array('success' => false, 'message' => 'Category ID is required');
        }

        $products = $this->filter_products_by_category($cat_id, $limit, $offset);
        $total_count = $this->get_category_count($cat_id);
        
        return array(
            'success' => true, 
            'data' => $products,
            'total_count' => $total_count,
            'category_id' => $cat_id,
            'limit' => $limit,
            'offset' => $offset
        );
    }

    public function filter_products_by_brand_ctr($brand_id, $limit = null, $offset = 0)
    {
        if (empty($brand_id)) {
            return array('success' => false, 'message' => 'Brand ID is required');
        }

        $products = $this->filter_products_by_brand($brand_id, $limit, $offset);
        $total_count = $this->get_brand_count($brand_id);
        
        return array(
            'success' => true, 
            'data' => $products,
            'total_count' => $total_count,
            'brand_id' => $brand_id,
            'limit' => $limit,
            'offset' => $offset
        );
    }

    public function view_single_product_ctr($id)
    {
        if (empty($id)) {
            return array('success' => false, 'message' => 'Product ID is required');
        }

        $product = $this->view_single_product($id);
        
        if ($product) {
            return array('success' => true, 'data' => $product);
        } else {
            return array('success' => false, 'message' => 'Product not found');
        }
    }

    public function get_products_with_filters_ctr($category_id = null, $brand_id = null, $search_query = null, $limit = null, $offset = 0)
    {
        $products = $this->get_products_with_filters($category_id, $brand_id, $search_query, $limit, $offset);
        $total_count = $this->get_filtered_count($category_id, $brand_id, $search_query);
        
        return array(
            'success' => true, 
            'data' => $products,
            'total_count' => $total_count,
            'filters' => array(
                'category_id' => $category_id,
                'brand_id' => $brand_id,
                'search_query' => $search_query
            ),
            'limit' => $limit,
            'offset' => $offset
        );
    }

}
?>
