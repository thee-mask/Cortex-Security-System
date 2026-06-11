<?php
// Start the session engine to read active user tokens
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 * the operator has not logged in successful
 */
if (!isset($_SESSION['username'])) {
    
    header("Location: login.php?error=unauthorized");
    exit();
}

if (isset($_SESSION['last_login_time'])) {
    $elapsed_time = time() - $_SESSION['last_login_time'];
    
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=expired");
        exit();
    }
}
// Update the activity timestamp to keep the session fresh for another 15 minutes
$_SESSION['last_login_time'] = time();
?>