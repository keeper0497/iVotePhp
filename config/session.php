<?php
// Session configuration and initialization

// Configure session settings
ini_set('session.cookie_lifetime', 3600); // 1 hour
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

// Start session
session_start();

// Check if user is logged in and is admin
function checkAdminAuth() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: home.php');
        exit;
    }
}
?>