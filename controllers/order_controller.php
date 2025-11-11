<?php
require_once __DIR__ . '/../classes/order_class.php';

class order_controller {
    private $order;
    
    public function __construct() {
        $this->order = new order_class();
    }
    
    /**
     * Create order
     * @param array $params ['customer_id', 'order_status' (optional)]
     * @return array Returns order_id on success
     */
    public function create_order_ctr($params) {
        try {
            if (!is_array($params)) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters provided.',
                    'order_id' => null
                ];
            }
            
            $customer_id = isset($params['customer_id']) ? (int)$params['customer_id'] : 0;
            $order_status = isset($params['order_status']) ? $params['order_status'] : 'pending';
            
            if ($customer_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid customer ID.',
                    'order_id' => null
                ];
            }
            
            $order_id = $this->order->create_order($customer_id, $order_status);
            
            if ($order_id) {
                return [
                    'success' => true,
                    'message' => 'Order created successfully.',
                    'order_id' => $order_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create order.',
                    'order_id' => null
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'order_id' => null
            ];
        }
    }
    
    /**
     * Add order details
     * @param array $params ['order_id', 'product_id', 'quantity']
     * @return array
     */
    public function add_order_details_ctr($params) {
        try {
            if (!is_array($params)) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters provided.'
                ];
            }
            
            $order_id = isset($params['order_id']) ? (int)$params['order_id'] : 0;
            $product_id = isset($params['product_id']) ? (int)$params['product_id'] : 0;
            $quantity = isset($params['quantity']) ? (int)$params['quantity'] : 0;
            
            if ($order_id <= 0 || $product_id <= 0 || $quantity <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters.'
                ];
            }
            
            $result = $this->order->add_order_details($order_id, $product_id, $quantity);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Order detail added successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add order detail.'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Record payment
     * @param array $params ['order_id', 'customer_id', 'amount', 'currency' (optional)]
     * @return array
     */
    public function record_payment_ctr($params) {
        try {
            if (!is_array($params)) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters provided.'
                ];
            }
            
            $order_id = isset($params['order_id']) ? (int)$params['order_id'] : 0;
            $customer_id = isset($params['customer_id']) ? (int)$params['customer_id'] : 0;
            $amount = isset($params['amount']) ? (double)$params['amount'] : 0;
            $currency = isset($params['currency']) ? $params['currency'] : 'USD';
            
            if ($order_id <= 0 || $customer_id <= 0 || $amount <= 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters.'
                ];
            }
            
            $result = $this->order->record_payment($order_id, $customer_id, $amount, $currency);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Payment recorded successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to record payment.'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get customer orders
     * @param int $customer_id
     * @return array
     */
    public function get_customer_orders_ctr($customer_id) {
        try {
            $customer_id = (int)$customer_id;
            if ($customer_id <= 0) {
                return [];
            }
            
            return $this->order->get_customer_orders($customer_id);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get complete order information
     * @param int $order_id
     * @return array|false
     */
    public function get_complete_order_ctr($order_id) {
        try {
            $order_id = (int)$order_id;
            if ($order_id <= 0) {
                return false;
            }
            
            return $this->order->get_complete_order($order_id);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get order by ID
     * @param int $order_id
     * @return array|false
     */
    public function get_order_by_id_ctr($order_id) {
        try {
            $order_id = (int)$order_id;
            if ($order_id <= 0) {
                return false;
            }
            
            return $this->order->get_order_by_id($order_id);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>

