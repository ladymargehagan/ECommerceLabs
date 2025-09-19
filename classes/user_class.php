<?php

require_once '../settings/db_class.php';


// User class for handling customer accounts
 
class User extends db_connection
{
    private $user_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;
    private $country;
    private $city;
    private $image;

    public function __construct($user_id = null)
    {
        parent::db_connect();
        if ($user_id) {
            $this->user_id = $user_id;
            $this->loadUser();
        }
    }

    private function loadUser($user_id = null)
    {
        if ($user_id) {
            $this->user_id = $user_id;
        }
        if (!$this->user_id) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $this->name     = $result['customer_name'];
            $this->email    = $result['customer_email'];
            $this->password = $result['customer_pass'];
            $this->role     = $result['user_role'];
            $this->phone_number = $result['customer_contact'];
            $this->country  = $result['customer_country'];
            $this->city     = $result['customer_city'];
            $this->image    = $result['customer_image'];
        }
    }

    
    // Register a new user (customer)
   
    public function addUser($name, $email, $hashed_password, $phone_number, $role, $country, $city, $image = null)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, user_role, customer_country, customer_city, customer_image) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssisss", $name, $email, $hashed_password, $phone_number, $role, $country, $city, $image);

        if ($stmt->execute()) {
            return $this->db->insert_id; // return new user ID
        }
        return false;
    }

    
    // Check if email already exists
     
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    
    // Get user by email
    
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
