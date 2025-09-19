<?php
require_once '../classes/customer_class.php';

class CustomerController {
    private $customer;
    
    public function __construct() {
        $this->customer = new Customer();
    }
    
    // Login customer controller method that accepts keyword arguments
    public function login_customer_ctr($kwargs) {
        try {
            // Validate input parameters
            if (!is_array($kwargs)) {
                return [
                    'success' => false,
                    'message' => 'Invalid parameters provided. Expected array.',
                    'data' => null
                ];
            }
            
            // Extract email and password from kwargs array
            $email = isset($kwargs['email']) ? trim($kwargs['email']) : '';
            $password = isset($kwargs['password']) ? trim($kwargs['password']) : '';
            
            // Validating required fields
            if (empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Email and password are required in the parameters.',
                    'data' => null
                ];
            }
            
            // Use the customer class get method to attempt login
            $result = $this->customer->get($email, $password);
            
            return $result;
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during customer login: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
?>
