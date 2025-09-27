<?php
require_once '../classes/category_class.php';

class CategoryController {
    private $category;
    
    public function __construct() {
        $this->category = new Category();
    }
    
    /**
     * Add category controller method
     * @param array $kwargs Array containing cat_name and user_id
     * @return array Returns array with success status, message, and data
     */
    public function add_category_ctr($kwargs) {
        // Validate required parameters
        if (!isset($kwargs['cat_name']) || !isset($kwargs['user_id'])) {
            return [
                'success' => false,
                'message' => 'Missing required parameters: cat_name and user_id.',
                'data' => null
            ];
        }
        
        //Calling the category class add method
        return $this->category->add($kwargs['cat_name'], $kwargs['user_id']);
    }
    
    /**
     * Get all categories controller method
     * @return array Returns array with success status, message, and data
     */
    public function get_all_categories_ctr() {
        return $this->category->get_all();
    }
    
    /**
     * Get category by ID controller method
     * @param int $cat_id Category ID
     * @return array Returns array with success status, message, and data
     */
    public function get_category_by_id_ctr($cat_id) {
        return $this->category->get_by_id($cat_id);
    }
    
    /**
     * Update category controller method
     * @param array $kwargs Array containing cat_id and cat_name
     * @return array Returns array with success status, message, and data
     */
    public function update_category_ctr($kwargs) {
        //Validating required parameters
        if (!isset($kwargs['cat_id']) || !isset($kwargs['cat_name'])) {
            return [
                'success' => false,
                'message' => 'Missing required parameters: cat_id and cat_name.',
                'data' => null
            ];
        }
        
        //Calling the category class update method
        return $this->category->update($kwargs['cat_id'], $kwargs['cat_name']);
    }
    
    /**
     * Delete category controller method
     * @param int $cat_id Category ID
     * @return array Returns array with success status, message, and data
     */
    public function delete_category_ctr($cat_id) {
        return $this->category->delete($cat_id);
    }
}
?>
