<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

session_start();

$response = array();

// TODO: Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

// Check if controller file exists
if (!file_exists('../controllers/user_controller.php')) {
    $response['status'] = 'error';
    $response['message'] = 'Controller file not found';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

// Collect fields
$name         = trim($_POST['name']);
$email        = trim($_POST['email']);
$password     = $_POST['password'];
$phone_number = trim($_POST['phone_number']);
$role         = $_POST['role'] ?? 2;  // default to 2 (customer)
$country      = trim($_POST['country']);
$city         = trim($_POST['city']);

// Optional image upload (set null if not provided)
$image = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image = basename($_FILES['image']['name']);
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $image);
}

// --- VALIDATION --- //
// Required fields validation
if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($country) || empty($city)) {
    $response['status'] = 'error';
    $response['message'] = 'All required fields must be filled';
    echo json_encode($response);
    exit();
}

// Field length validation
if (strlen($name) > 100) {
    $response['status'] = 'error';
    $response['message'] = 'Name must be 100 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($email) > 50) {
    $response['status'] = 'error';
    $response['message'] = 'Email must be 50 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($phone_number) > 15) {
    $response['status'] = 'error';
    $response['message'] = 'Phone number must be 15 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($country) > 30) {
    $response['status'] = 'error';
    $response['message'] = 'Country must be 30 characters or less';
    echo json_encode($response);
    exit();
}

if (strlen($city) > 30) {
    $response['status'] = 'error';
    $response['message'] = 'City must be 30 characters or less';
    echo json_encode($response);
    exit();
}

// Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Validate phone number 
if (!preg_match('/^[0-9]{7,15}$/', $phone_number)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid phone number';
    echo json_encode($response);
    exit();
}

// Password hashing 
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// --- CALL CONTROLLER --- //
try {
    $user_id = register_user_ctr($name, $email, $hashed_password, $phone_number, $role, $country, $city, $image);

    if ($user_id) {
        $response['status'] = 'success';
        $response['message'] = 'Registered successfully. Please login.';
        $response['user_id'] = $user_id;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to register. Email may already exist.';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Registration error: ' . $e->getMessage();
}

echo json_encode($response);
