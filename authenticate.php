<?php
// 1. Include security helper layers and the live database link
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login_action'])) {
    
    // 2. Validate Anti-CSRF Token integrity
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("Security Check Failed: Anti-CSRF Token Mismatch.");
    }

    // 3. Sanitize inputs
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];

    // 4. Secure SQL Prepared Statement to fetch the user matching the typed username
    $query = "SELECT id, username, password FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind the username parameter ("s" means string input)
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        // Execute the database read search operation
        mysqli_stmt_execute($stmt);
        
        // Get the query result set
        $result = mysqli_stmt_get_result($stmt);

        // Check if a matching user row was found in your database
        if ($row = mysqli_fetch_assoc($result)) {
            
            // 5. Dynamic Cryptographic Hash Verification Pattern
            // Compares the typed password against the secure bcrypt hash stored in the DB
            if (password_verify($password, $row['password'])) {
                
                // Prevent Session Fixation hijacking variants by cycling the active ID
                session_regenerate_id(true);
                
                // Establish active state session variables
                $_SESSION['username'] = $row['username'];
                $_SESSION['last_login_time'] = time();
                
                // Redirect straight into your secure dashboard system
                header("Location: dashboard.php");
                exit();
            }
        }
        
        // Close the statement structure safely
        mysqli_stmt_close($stmt);
    }

    // Close the live server connection link
    mysqli_close($conn);
    
    // Fail check fallback: Redirect back to login with a strict database validation error message
    header("Location: login.php?msg=Invalid database authentication credentials.");
    exit();

} else {
    header("Location: login.php");
    exit();
}