<?php
session_start();
ob_start();

// Redirect unauthenticated users to login (except for customer-facing product actions)
if (!isset($_SESSION['user_id'])) {
    // Allow access to product_actions.php for customer-facing functionality
    $current_file = basename($_SERVER['PHP_SELF']);
    $is_product_action = ($current_file === 'product_actions.php');
    
    if (!$is_product_action) {
        // Simple path detection - if we're in a subdirectory, go up one level
        $current_dir = dirname($_SERVER['PHP_SELF']);
        $login_path = (strpos($current_dir, '/admin') !== false || 
                       strpos($current_dir, '/customer') !== false || 
                       strpos($current_dir, '/actions') !== false) 
                       ? '../login/login.php' : 'login/login.php';
        header("Location: $login_path");
        exit;
    }
}

function getUserID() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Enforce role-based access control
function checkRole($requiredRole) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $requiredRole) {
        // Simple path detection - if we're in a subdirectory, go up one level
        $current_dir = dirname($_SERVER['PHP_SELF']);
        $login_path = (strpos($current_dir, '/admin') !== false || 
                       strpos($current_dir, '/customer') !== false || 
                       strpos($current_dir, '/actions') !== false) 
                       ? '../login/login.php' : 'login/login.php';
        header("Location: $login_path");
        exit;
    }
}
?>
