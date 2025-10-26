<?php
session_start();
ob_start();

// Redirect unauthenticated users to login
if (!isset($_SESSION['user_id'])) {
    // Simple path detection - if we're in a subdirectory, go up one level
    $current_dir = dirname($_SERVER['PHP_SELF']);
    $login_path = (strpos($current_dir, '/admin') !== false || 
                   strpos($current_dir, '/customer') !== false || 
                   strpos($current_dir, '/actions') !== false) 
                   ? '../login/login.php' : 'login/login.php';
    header("Location: $login_path");
    exit;
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
