<?php
// includes/protect.php
// ===================

// Set secure session parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$current_page = basename($_SERVER['PHP_SELF']);

// Define public pages that don't require authentication
$public_pages = ['login.php', 'register.php'];

// If the user is on a public page, stop the protection script here
if (in_array($current_page, $public_pages)) {
    return;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: login.php");
    exit;
}

// Check if master key is set in the session
if (!isset($_SESSION['master_key'])) {
    header("Location: logout.php");
    exit;
}

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// CSRF validation for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = "Invalid CSRF token";
        header("Location: ../dashboard.php");
        exit;
    }
}
?>
