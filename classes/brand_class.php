<?php
require_once __DIR__ . '/../settings/db_class.php';

class brand_class extends db_connection
{
    public function add_brand($brand_name, $brand_image = '', $created_by = null)
    {
        // Check if brand name already exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        if ($this->db_fetch_one($check_sql)) {
            return false;
        }

        if ($created_by !== null) {
            $sql = "INSERT INTO brands (brand_name, brand_image, created_by) VALUES ('$brand_name', '$brand_image', '$created_by')";
        } else {
            $sql = "INSERT INTO brands (brand_name, brand_image) VALUES ('$brand_name', '$brand_image')";
        }
        $result = $this->db_write_query($sql);
        
        // Return the brand ID (last insert id)
        if ($result) {
            return mysqli_insert_id($this->db);
        }
        
        return false;
    }

    public function get_brands_by_user($user_id)
    {
        // Check if created_by column exists, if not fall back to all brands
        // Get brands created by user, or brands used in products created by user's categories
        $sql = "SELECT DISTINCT b.* FROM brands b 
                LEFT JOIN products p ON b.brand_id = p.product_brand 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                WHERE b.created_by = '$user_id' OR c.created_by = '$user_id'
                ORDER BY b.brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        // Fallback: if query fails (created_by column doesn't exist), get all brands
        if ($result === false) {
            $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
            $result = $this->db_fetch_all($sql);
        }
        
        return $result ? $result : array();
    }

    public function get_all_brands()
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $result = $this->db_fetch_all($sql);
        
        return $result ? $result : array();
    }

    public function get_brand_by_id($brand_id)
    {
        $sql = "SELECT * FROM brands WHERE brand_id = '$brand_id'";
        $result = $this->db_fetch_one($sql);
        
        return $result;
    }

    public function update_brand($brand_id, $brand_name, $brand_image = '')
    {
        // Check if brand exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_id = '$brand_id'";
        $brand_exists = $this->db_fetch_one($check_sql);
        
        if (!$brand_exists) {
            return false;
        }

        // Check if new name already exists (excluding current brand)
        $check_name_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND brand_id != '$brand_id'";
        if ($this->db_fetch_one($check_name_sql)) {
            return false;
        }

        $sql = "UPDATE brands SET brand_name = '$brand_name', brand_image = '$brand_image' WHERE brand_id = '$brand_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function delete_brand($brand_id)
    {
        // Ensure database connection
        if (!$this->db_connect()) {
            return false;
        }

        // Check if brand exists
        $check_sql = "SELECT brand_id FROM brands WHERE brand_id = '$brand_id'";
        $brand_exists = $this->db_fetch_one($check_sql);
        
        if (!$brand_exists) {
            return false;
        }

        // Check if brand is used in products
        $check_products_sql = "SELECT product_id FROM products WHERE product_brand = '$brand_id'";
        if ($this->db_fetch_one($check_products_sql)) {
            return false;
        }

        $sql = "DELETE FROM brands WHERE brand_id = '$brand_id'";
        $result = $this->db_write_query($sql);
        
        return $result;
    }

    public function get_brands_by_category_for_user($user_id)
    {
        // SIMPLE: Get all brands and organize by categories
        $all_brands = $this->get_all_brands();
        if (!$all_brands) {
            $all_brands = array();
        }
        
        // Get user's categories
        $user_categories = $this->get_categories_by_user($user_id);
        if (!$user_categories) {
            $user_categories = array();
        }
        
        // Get brands organized by categories through products
        $brands_by_category = array();
        if (count($user_categories) > 0) {
            $sql = "SELECT DISTINCT c.cat_id, c.cat_name, c.cat_image, 
                           b.brand_id, b.brand_name, b.brand_image
                    FROM categories c
                    INNER JOIN products p ON c.cat_id = p.product_cat
                    INNER JOIN brands b ON p.product_brand = b.brand_id
                    WHERE c.created_by = '$user_id'
                    ORDER BY c.cat_name ASC, b.brand_name ASC";
            $brands_by_category = $this->db_fetch_all($sql);
            if (!$brands_by_category) {
                $brands_by_category = array();
            }
        }
        
        // Track which brands are already in categories
        $brands_in_categories = array();
        foreach ($brands_by_category as $item) {
            if (isset($item['brand_id']) && $item['brand_id']) {
                $brands_in_categories[$item['brand_id']] = true;
            }
        }
        
        // Build result: brands with products (organized by category) + brands without products
        $result = $brands_by_category;
        
        // Add brands that aren't in any category yet
        foreach ($all_brands as $brand) {
            $brand_id = $brand['brand_id'];
            if (!isset($brands_in_categories[$brand_id])) {
                if (count($user_categories) > 0) {
                    $first_cat = $user_categories[0];
                    $result[] = array(
                        'cat_id' => $first_cat['cat_id'],
                        'cat_name' => $first_cat['cat_name'],
                        'cat_image' => isset($first_cat['cat_image']) ? $first_cat['cat_image'] : null,
                        'brand_id' => $brand_id,
                        'brand_name' => $brand['brand_name'],
                        'brand_image' => isset($brand['brand_image']) ? $brand['brand_image'] : null
                    );
                } else {
                    // No categories, show in "All Brands" section
                    $result[] = array(
                        'cat_id' => 0,
                        'cat_name' => 'All Brands',
                        'cat_image' => null,
                        'brand_id' => $brand_id,
                        'brand_name' => $brand['brand_name'],
                        'brand_image' => isset($brand['brand_image']) ? $brand['brand_image'] : null
                    );
                }
            }
        }
        
        return $result;
    }

    public function get_categories_by_user($user_id)
    {
        $sql = "SELECT * FROM categories WHERE created_by = '$user_id' ORDER BY cat_name ASC";
        $result = $this->db_fetch_all($sql);
        
        if ($result === false || empty($result)) {
            $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
            $result = $this->db_fetch_all($sql);
        }
        
        return $result ? $result : array();
    }

    public function brand_name_exists($brand_name, $exclude_id = null)
    {
        $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
        if ($exclude_id) {
            $sql .= " AND brand_id != '$exclude_id'";
        }
        return $this->db_fetch_one($sql) ? true : false;
    }
}
?>
