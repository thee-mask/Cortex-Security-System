<?php
require_once __DIR__ . '/includes/security.php';

// Erase data arrays and destroy file allocation structures
$_SESSION = array();
session_unset(); 
session_destroy(); 

$redirect_msg = "You have logged out successfully.";
if (isset($_GET['msg'])) {
    $redirect_msg = $_GET['msg'];
}

// Return safely to login gate
header("Location: login.php?msg=" . urlencode($redirect_msg));
exit();