<?php
session_start();
ob_start();

// Redirect unauthenticated users to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
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
        header("Location: ../login/login.php");
        exit;
    }
}
?>
