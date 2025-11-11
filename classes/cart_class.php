<?php
require_once __DIR__ . '/../settings/db_class.php';

class cart_class extends db_connection {
    
    /**
     * Get user's IP address
     */
    private function get_ip_address() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Check if product already exists in cart
     * @param int $product_id
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array|false Returns cart item if exists, false otherwise
     */
    public function check_product_in_cart($product_id, $customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $product_id = $db->real_escape_string($product_id);
        
        if ($customer_id) {
            // Logged-in user: check by customer_id
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "SELECT * FROM cart WHERE p_id = '$product_id' AND c_id = '$customer_id'";
        } else {
            // Guest user: check by IP address
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "SELECT * FROM cart WHERE p_id = '$product_id' AND ip_add = '$ip_address' AND c_id IS NULL";
        }
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Add product to cart
     * If product already exists, increment quantity instead of duplicating
     * @param int $product_id
     * @param int $quantity
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array Returns success status and message
     */
    public function add_to_cart($product_id, $quantity = 1, $customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return [
                'success' => false,
                'message' => 'Database connection failed.'
            ];
        }
        
        $product_id = $db->real_escape_string($product_id);
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            return [
                'success' => false,
                'message' => 'Quantity must be greater than 0.'
            ];
        }
        
        // Check if product exists in cart
        $existing_item = $this->check_product_in_cart($product_id, $customer_id, $ip_address);
        
        if ($existing_item) {
            // Product exists, update quantity
            $new_quantity = $existing_item['qty'] + $quantity;
            return $this->update_cart_quantity($product_id, $new_quantity, $customer_id, $ip_address);
        }
        
        // Product doesn't exist, add new item
        if ($customer_id) {
            // Logged-in user
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "INSERT INTO cart (p_id, c_id, qty, ip_add) VALUES ('$product_id', '$customer_id', '$quantity', NULL)";
        } else {
            // Guest user
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "INSERT INTO cart (p_id, ip_add, qty, c_id) VALUES ('$product_id', '$ip_address', '$quantity', NULL)";
        }
        
        $result = $this->db_write_query($sql);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Product added to cart successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to add product to cart.'
            ];
        }
    }
    
    /**
     * Update quantity of a product in cart
     * @param int $product_id
     * @param int $quantity
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array Returns success status and message
     */
    public function update_cart_quantity($product_id, $quantity, $customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return [
                'success' => false,
                'message' => 'Database connection failed.'
            ];
        }
        
        $product_id = $db->real_escape_string($product_id);
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            // If quantity is 0 or less, remove item instead
            return $this->remove_from_cart($product_id, $customer_id, $ip_address);
        }
        
        if ($customer_id) {
            // Logged-in user
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "UPDATE cart SET qty = '$quantity' WHERE p_id = '$product_id' AND c_id = '$customer_id'";
        } else {
            // Guest user
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "UPDATE cart SET qty = '$quantity' WHERE p_id = '$product_id' AND ip_add = '$ip_address' AND c_id IS NULL";
        }
        
        $result = $this->db_write_query($sql);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Cart quantity updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update cart quantity.'
            ];
        }
    }
    
    /**
     * Remove product from cart
     * @param int $product_id
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array Returns success status and message
     */
    public function remove_from_cart($product_id, $customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return [
                'success' => false,
                'message' => 'Database connection failed.'
            ];
        }
        
        $product_id = $db->real_escape_string($product_id);
        
        if ($customer_id) {
            // Logged-in user
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "DELETE FROM cart WHERE p_id = '$product_id' AND c_id = '$customer_id'";
        } else {
            // Guest user
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "DELETE FROM cart WHERE p_id = '$product_id' AND ip_add = '$ip_address' AND c_id IS NULL";
        }
        
        $result = $this->db_write_query($sql);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Product removed from cart successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove product from cart.'
            ];
        }
    }
    
    /**
     * Get all cart items for a user
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array Returns array of cart items with product details
     */
    public function get_user_cart($customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return [];
        }
        
        if ($customer_id) {
            // Logged-in user
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_desc,
                           cat.cat_name, b.brand_name
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE c.c_id = '$customer_id'
                    ORDER BY c.p_id ASC";
        } else {
            // Guest user
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_desc,
                           cat.cat_name, b.brand_name
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE c.ip_add = '$ip_address' AND c.c_id IS NULL
                    ORDER BY c.p_id ASC";
        }
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : [];
    }
    
    /**
     * Empty cart for a user
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array Returns success status and message
     */
    public function empty_cart($customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return [
                'success' => false,
                'message' => 'Database connection failed.'
            ];
        }
        
        if ($customer_id) {
            // Logged-in user
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "DELETE FROM cart WHERE c_id = '$customer_id'";
        } else {
            // Guest user
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "DELETE FROM cart WHERE ip_add = '$ip_address' AND c_id IS NULL";
        }
        
        $result = $this->db_write_query($sql);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Cart emptied successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to empty cart.'
            ];
        }
    }
    
    /**
     * Get cart count (number of items)
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return int
     */
    public function get_cart_count($customer_id = null, $ip_address = null) {
        $db = $this->db_conn();
        if (!$db) {
            return 0;
        }
        
        if ($customer_id) {
            $customer_id = $db->real_escape_string($customer_id);
            $sql = "SELECT SUM(qty) as total FROM cart WHERE c_id = '$customer_id'";
        } else {
            if (!$ip_address) {
                $ip_address = $this->get_ip_address();
            }
            $ip_address = $db->real_escape_string($ip_address);
            $sql = "SELECT SUM(qty) as total FROM cart WHERE ip_add = '$ip_address' AND c_id IS NULL";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result && $result['total'] ? (int)$result['total'] : 0;
    }
    
    /**
     * Merge guest cart into user cart on login
     * @param string $ip_address
     * @param int $customer_id
     * @return array Returns success status and message
     */
    public function merge_guest_cart($ip_address, $customer_id) {
        $db = $this->db_conn();
        if (!$db) {
            return [
                'success' => false,
                'message' => 'Database connection failed.'
            ];
        }
        
        $ip_address = $db->real_escape_string($ip_address);
        $customer_id = $db->real_escape_string($customer_id);
        
        // Get guest cart items
        $guest_cart = $this->db_fetch_all("SELECT p_id, qty FROM cart WHERE ip_add = '$ip_address' AND c_id IS NULL");
        
        if (!$guest_cart || empty($guest_cart)) {
            return [
                'success' => true,
                'message' => 'No guest cart items to merge.'
            ];
        }
        
        $merged_count = 0;
        
        foreach ($guest_cart as $item) {
            $product_id = $item['p_id'];
            $quantity = $item['qty'];
            
            // Check if user already has this product in cart
            $existing = $this->check_product_in_cart($product_id, $customer_id);
            
            if ($existing) {
                // Update quantity (add guest quantity to existing)
                $new_quantity = $existing['qty'] + $quantity;
                $this->update_cart_quantity($product_id, $new_quantity, $customer_id);
            } else {
                // Add new item to user cart
                $this->add_to_cart($product_id, $quantity, $customer_id);
            }
            
            $merged_count++;
        }
        
        // Delete guest cart items
        $this->db_write_query("DELETE FROM cart WHERE ip_add = '$ip_address' AND c_id IS NULL");
        
        return [
            'success' => true,
            'message' => "Successfully merged $merged_count item(s) from guest cart."
        ];
    }
}
?>

