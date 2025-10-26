<?php
require_once '../classes/category_class.php';

class category_controller
{
    private $category_class;

    public function __construct()
    {
        $this->category_class = new category_class();
    }

    public function add_category_ctr($kwargs)
    {
        $cat_name = $kwargs['cat_name'];
        $created_by = $kwargs['created_by'];

        if (empty($cat_name)) {
            return array('success' => false, 'message' => 'Category name is required');
        }

        if (strlen($cat_name) > 100) {
            return array('success' => false, 'message' => 'Category name must be less than 100 characters');
        }

        if ($this->category_class->category_name_exists($cat_name)) {
            return array('success' => false, 'message' => 'Category name already exists');
        }

        $result = $this->category_class->add_category($cat_name, $created_by);
        
        if ($result) {
            return array('success' => true, 'message' => 'Category added successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to add category');
        }
    }

    public function get_categories_ctr($user_id)
    {
        $categories = $this->category_class->get_categories_by_user($user_id);
        
        if ($categories === false) {
            return array('success' => false, 'message' => 'Failed to fetch categories');
        }
        
        return array('success' => true, 'data' => $categories);
    }

    public function update_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'];
        $cat_name = $kwargs['cat_name'];
        $user_id = $kwargs['user_id'];

        if (empty($cat_id) || empty($cat_name)) {
            return array('success' => false, 'message' => 'Category ID and name are required');
        }

        if (strlen($cat_name) > 100) {
            return array('success' => false, 'message' => 'Category name must be less than 100 characters');
        }

        if ($this->category_class->category_name_exists($cat_name, $cat_id)) {
            return array('success' => false, 'message' => 'Category name already exists');
        }

        $result = $this->category_class->update_category($cat_id, $cat_name, $user_id);
        
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

        $result = $this->category_class->delete_category($cat_id, $user_id);
        
        if ($result) {
            return array('success' => true, 'message' => 'Category deleted successfully');
        } else {
            return array('success' => false, 'message' => 'Failed to delete category, category not found, or category is being used by products');
        }
    }
}
?>
