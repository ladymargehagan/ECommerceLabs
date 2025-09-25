// Settings/core.php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// for header redirection
ob_start();

// check if user is logged in
if (!isLoggedIn()) {   
    header("Location: ../login/login.php");
    exit;
}

// Get current logged-in user ID
function getUserID() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current logged-in user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Returns true if a session is active for a logged-in user
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Returns true if the current user has administrative privileges
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Check if user has a specific role
function checkRole($requiredRole) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $requiredRole) {
        header("Location: ../login/login.php");
        exit;
    }
}
?>
