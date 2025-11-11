<?php
require_once __DIR__ . '/../classes/cart_class.php';

class cart_controller {
    private $cart;
    
    public function __construct() {
        $this->cart = new cart_class();
    }
    
    /**
     * Add product to cart
     * @param array $params ['product_id', 'quantity', 'customer_id' (optional), 'ip_address' (optional)]
     * @return array
     */
    public function add_to_cart_ctr($params) {
        try {
            if (!is_array($params)) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters provided.'
                ];
            }
            
            $product_id = isset($params['product_id']) ? (int)$params['product_id'] : 0;
            $quantity = isset($params['quantity']) ? (int)$params['quantity'] : 1;
            $customer_id = isset($params['customer_id']) ? (int)$params['customer_id'] : null;
            $ip_address = isset($params['ip_address']) ? $params['ip_address'] : null;
            
            if ($product_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid product ID.'
                ];
            }
            
            if ($quantity <= 0) {
                return [
                    'success' => false,
                    'message' => 'Quantity must be greater than 0.'
                ];
            }
            
            return $this->cart->add_to_cart($product_id, $quantity, $customer_id, $ip_address);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update cart item quantity
     * @param int $product_id
     * @param int $quantity
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array
     */
    public function update_cart_item_ctr($product_id, $quantity, $customer_id = null, $ip_address = null) {
        try {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($product_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid product ID.'
                ];
            }
            
            return $this->cart->update_cart_quantity($product_id, $quantity, $customer_id, $ip_address);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Remove item from cart
     * @param int $product_id
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array
     */
    public function remove_from_cart_ctr($product_id, $customer_id = null, $ip_address = null) {
        try {
            $product_id = (int)$product_id;
            
            if ($product_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid product ID.'
                ];
            }
            
            return $this->cart->remove_from_cart($product_id, $customer_id, $ip_address);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get user cart
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array
     */
    public function get_user_cart_ctr($customer_id = null, $ip_address = null) {
        try {
            return $this->cart->get_user_cart($customer_id, $ip_address);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Empty cart
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return array
     */
    public function empty_cart_ctr($customer_id = null, $ip_address = null) {
        try {
            return $this->cart->empty_cart($customer_id, $ip_address);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get cart count
     * @param int|null $customer_id
     * @param string|null $ip_address
     * @return int
     */
    public function get_cart_count_ctr($customer_id = null, $ip_address = null) {
        try {
            return $this->cart->get_cart_count($customer_id, $ip_address);
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Merge guest cart into user cart
     * @param string $ip_address
     * @param int $customer_id
     * @return array
     */
    public function merge_guest_cart_ctr($ip_address, $customer_id) {
        try {
            return $this->cart->merge_guest_cart($ip_address, $customer_id);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
}
?>

