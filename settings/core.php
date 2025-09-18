// Settings/core.php
<?php
session_start();

// for header redirection
ob_start();

// check if user is logged in
if (!isset($_SESSION['user_id'])) {   // fixed: use 'user_id' consistently
    header("Location: ../login/login.php");
    exit;
}

/**
 * Get current logged-in user ID
 */
function getUserID() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current logged-in user role
 */
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Check if user has a specific role
 * Example: checkRole(1); // admin-only page
 */
function checkRole($requiredRole) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $requiredRole) {
        header("Location: ../login/login.php");
        exit;
    }
}
?>
