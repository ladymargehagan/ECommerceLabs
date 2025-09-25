session_start();

// for header redirection
ob_start();

// Returns true if a user session exists
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get current logged-in user ID
function getUserID() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current logged-in user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Returns true if the current user has administrative privileges
function isAdmin() {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    $role = $_SESSION['role'];
    return ($role === 1 || $role === '1' || $role === 'admin');
}
