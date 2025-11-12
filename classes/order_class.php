<?php
require_once __DIR__ . '/../settings/db_class.php';

class order_class extends db_connection {
    
    /**
     * Create a new order
     * @param int $customer_id
     * @param string $order_status
     * @return int|false Returns order_id on success, false on failure
     */
    public function create_order($customer_id, $order_status = 'pending') {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $customer_id = $db->real_escape_string($customer_id);
        $order_status = $db->real_escape_string($order_status);

        // Generate unique invoice number (smaller format to fit database column)
        // Format: YYYYMMDDXXXX where XXXX is random 4 digits
        $invoice_no = (int)(date('Ymd') . rand(1000, 9999));
        $order_date = date('Y-m-d');
        
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                VALUES ('$customer_id', '$invoice_no', '$order_date', '$order_status')";
        
        // Need to ensure connection is active
        if (!$this->db_connect()) {
            return false;
        }
        
        $result = mysqli_query($this->db, $sql);
        
        if ($result) {
            // Get the last inserted ID from the database connection
            return mysqli_insert_id($this->db);
        }
        
        return false;
    }
    
    /**
     * Add order details (product items)
     * @param int $order_id
     * @param int $product_id
     * @param int $quantity
     * @return bool
     */
    public function add_order_details($order_id, $product_id, $quantity) {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $order_id = $db->real_escape_string($order_id);
        $product_id = $db->real_escape_string($product_id);
        $quantity = (int)$quantity;
        
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) 
                VALUES ('$order_id', '$product_id', '$quantity')";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Record payment
     * @param int $order_id
     * @param int $customer_id
     * @param double $amount
     * @param string $currency
     * @return bool
     */
    public function record_payment($order_id, $customer_id, $amount, $currency = 'USD') {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $order_id = $db->real_escape_string($order_id);
        $customer_id = $db->real_escape_string($customer_id);
        $amount = (double)$amount;
        $currency = $db->real_escape_string($currency);
        $payment_date = date('Y-m-d');
        
        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) 
                VALUES ('$amount', '$customer_id', '$order_id', '$currency', '$payment_date')";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Get order by ID
     * @param int $order_id
     * @return array|false
     */
    public function get_order_by_id($order_id) {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $order_id = $db->real_escape_string($order_id);
        
        $sql = "SELECT o.*, c.customer_name, c.customer_email 
                FROM orders o
                INNER JOIN customer c ON o.customer_id = c.customer_id
                WHERE o.order_id = '$order_id'";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get order details (products) for an order
     * @param int $order_id
     * @return array
     */
    public function get_order_details($order_id) {
        $db = $this->db_conn();
        if (!$db) {
            return [];
        }
        
        $order_id = $db->real_escape_string($order_id);
        
        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image, 
                       p.product_desc, cat.cat_name, b.brand_name
                FROM orderdetails od
                INNER JOIN products p ON od.product_id = p.product_id
                LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE od.order_id = '$order_id'
                ORDER BY od.product_id ASC";
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : [];
    }
    
    /**
     * Get payment information for an order
     * @param int $order_id
     * @return array|false
     */
    public function get_payment_by_order($order_id) {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $order_id = $db->real_escape_string($order_id);
        
        $sql = "SELECT * FROM payment WHERE order_id = '$order_id'";
        
        return $this->db_fetch_one($sql);
    }
    
    /**
     * Get all orders for a customer
     * @param int $customer_id
     * @return array
     */
    public function get_customer_orders($customer_id) {
        $db = $this->db_conn();
        if (!$db) {
            return [];
        }
        
        $customer_id = $db->real_escape_string($customer_id);
        
        $sql = "SELECT o.*, 
                       (SELECT SUM(od.qty * p.product_price) 
                        FROM orderdetails od 
                        INNER JOIN products p ON od.product_id = p.product_id 
                        WHERE od.order_id = o.order_id) as total_amount
                FROM orders o
                WHERE o.customer_id = '$customer_id'
                ORDER BY o.order_date DESC, o.order_id DESC";
        
        $result = $this->db_fetch_all($sql);
        return $result ? $result : [];
    }
    
    /**
     * Get complete order information (order + details + payment)
     * @param int $order_id
     * @return array|false
     */
    public function get_complete_order($order_id) {
        $order = $this->get_order_by_id($order_id);
        if (!$order) {
            return false;
        }
        
        $order['details'] = $this->get_order_details($order_id);
        $order['payment'] = $this->get_payment_by_order($order_id);
        
        // Calculate total
        $total = 0;
        foreach ($order['details'] as $detail) {
            $total += $detail['qty'] * $detail['product_price'];
        }
        $order['total'] = $total;
        
        return $order;
    }
    
    /**
     * Update order status
     * @param int $order_id
     * @param string $order_status
     * @return bool
     */
    public function update_order_status($order_id, $order_status) {
        $db = $this->db_conn();
        if (!$db) {
            return false;
        }
        
        $order_id = $db->real_escape_string($order_id);
        $order_status = $db->real_escape_string($order_status);
        
        $sql = "UPDATE orders SET order_status = '$order_status' WHERE order_id = '$order_id'";
        
        return $this->db_write_query($sql);
    }
}
?>

