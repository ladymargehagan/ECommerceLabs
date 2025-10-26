<?php
session_start();
ob_start();

// Redirect unauthenticated users to login
if (!isset($_SESSION['user_id'])) {
    // Determine the correct path based on current directory
    $login_path = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || 
                   strpos($_SERVER['REQUEST_URI'], '/customer/') !== false || 
                   strpos($_SERVER['REQUEST_URI'], '/actions/') !== false) 
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
        // Determine the correct path based on current directory
        $login_path = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || 
                       strpos($_SERVER['REQUEST_URI'], '/customer/') !== false || 
                       strpos($_SERVER['REQUEST_URI'], '/actions/') !== false) 
                       ? '../login/login.php' : 'login/login.php';
        header("Location: $login_path");
        exit;
    }
}
?>
