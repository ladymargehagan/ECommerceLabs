<?php
require_once '../classes/product_class.php';

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

}
?>
