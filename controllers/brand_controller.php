<?php
require_once '../classes/brand_class.php';

class brand_controller extends brand_class
{
    public function add_brand_ctr($kwargs)
    {
        $brand_name = $kwargs['brand_name'];
        
        if (empty($brand_name)) {
            return array('success' => false, 'message' => 'Brand name is required');
        }
        
        // Check if brand name already exists
        if ($this->brand_name_exists($brand_name)) {
            return array('success' => false, 'message' => 'Brand name already exists');
        }
        
        $result = $this->add_brand($brand_name);
        
        if ($result) {
            return array('success' => true, 'message' => 'Brand added successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to add brand');
        }
    }
    
    public function get_brands_by_user_ctr($user_id)
    {
        $brands = $this->get_brands_by_user($user_id);
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
        
        $result = $this->update_brand($brand_id, $brand_name);
        
        if ($result) {
            return array('success' => true, 'message' => 'Brand updated successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to update brand');
        }
    }
    
    public function delete_brand_ctr($brand_id)
    {
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
}
?>
