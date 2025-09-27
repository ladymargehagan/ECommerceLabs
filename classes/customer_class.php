<?php
require_once '../settings/db_class.php';

class Customer {
    private $db;
    
    public function __construct() {
        $this->db = new db_connection();
    }
    
    /**
     * Get customer by email address with password verification
     * @param string $email
     * @param string $password
     * @return array Returns array with success status, message, and data
     */
    public function get($email, $password) {
        // Sanitize inputs
        $email = trim($email);
        $password = trim($password);
        
        // Validate email format
        if (empty($email)) {
            return [
                'success' => false,
                'message' => 'Email address is required.',
                'data' => null
            ];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format.',
                'data' => null
            ];
        }
        
        // Validate password
        if (empty($password)) {
            return [
                'success' => false,
                'message' => 'Password is required.',
                'data' => null
            ];
        }
        
        // Query database for customer by email using prepared statement
        $sql = "SELECT customer_id, customer_name, customer_email, customer_pass, 
                       customer_country, customer_city, customer_contact, 
                       customer_image, user_role 
                FROM customer 
                WHERE customer_email = ?";
        
        // Get database connection
        $db_conn = $this->db->db_conn();
        if (!$db_conn) {
            return [
                'success' => false,
                'message' => 'Database connection failed.',
                'data' => null
            ];
        }
        
        $stmt = $db_conn->prepare($sql);
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database query preparation failed.',
                'data' => null
            ];
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();
        
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Customer not found with the provided email address.',
                'data' => null
            ];
        }
        
        // Verify password
        if (!password_verify($password, $customer['customer_pass'])) {
            return [
                'success' => false,
                'message' => 'Invalid password provided.',
                'data' => null
            ];
        }
        
        // Remove password from returned data for security
        unset($customer['customer_pass']);
        
        return [
            'success' => true,
            'message' => 'Customer login successful.',
            'data' => $customer
        ];
    }
}
?>
