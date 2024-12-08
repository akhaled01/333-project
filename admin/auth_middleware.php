<?php
session_start();

function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Debug: Log session data
error_log('Session data: ' . print_r($_SESSION, true));
error_log('Is admin check result: ' . (isAdmin() ? 'true' : 'false'));
error_log('user_id set: ' . (isset($_SESSION['user_id']) ? 'true' : 'false'));
error_log('role set: ' . (isset($_SESSION['user_role']) ? 'true' : 'false'));
error_log('role value: ' . ($_SESSION['user_role'] ?? 'not set'));

// Check if user is logged in and is an admin
if (!isAdmin()) {
    http_response_code(403);
    
    $_SESSION['error'] = "Access denied. You need administrator privileges to access this page.";
    
    header('Location: /');
    exit();
}
?>
