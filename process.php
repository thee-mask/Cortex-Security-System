<?php
// 1. Include security helper layers and the live database link
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register_action'])) {
    
    // 2. Verify CSRF Token integrity to protect against request forgery
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed. Security breach prevented.");
    }

    // 3. Sanitize input strings to prevent Cross-Site Scripting (XSS)
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; 

    // 4. Server-side Input Validation Rules
    if (strlen($username) < 4 || strlen($password) < 6) {
        die("Validation Error: Username must be at least 4 characters, Password must be at least 6 characters.");
    }

    // 5. One-Way Cryptographic Password Hashing (Bcrypt)
    $secure_hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 6. SQL Prepared Statement to safely insert records into the database
    $query = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameters ("ss" means two strings)
        mysqli_stmt_bind_param($stmt, "ss", $username, $secure_hashed_password);
        
        // Execute the database write operation
        if (mysqli_stmt_execute($stmt)) {
            // Clean, simplified UI layout response as requested
            echo "<div style='font-family:Arial, sans-serif; padding:30px; max-width:500px; margin:50px auto; background:#fff; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align: center;'>";
            echo "<h2 style='color:#28a745;'>Registration Successful!</h2>";
            echo "<p style='font-size: 16px; margin-bottom: 20px;'>Welcome, <strong>" . htmlspecialchars($username) . "</strong>.</p>";
            echo "<br><a href='login.php' style='display:inline-block; padding:10px 20px; background:#007BFF; color:#fff; text-decoration:none; border-radius:4px; font-weight: bold;'>Proceed to Secure Login</a>";
            echo "</div>";
        } else {
            // Handle error state if the username is already taken
            echo "<div style='font-family:Arial, sans-serif; padding:20px; max-width:500px; margin:50px auto; background:#f8d7da; color:#721c24; border-radius:8px; text-align: center;'>";
            echo "<h3>Registration Failed</h3>";
            echo "<p>The username <strong>" . htmlspecialchars($username) . "</strong> is already taken. Please try another.</p>";
            echo "<br><a href='index.php' style='color:#721c24; font-weight:bold;'>Go Back to Form</a>";
            echo "</div>";
        }

        // Close the statement structure safely
        mysqli_stmt_close($stmt);
    } else {
        echo "Database error: Unable to prepare the structural SQL statement.";
    }

    // Close the live server connection link
    mysqli_close($conn);
    
} else {
    // Force redirect back to entry form if accessed directly via URL parameters
    header("Location: index.php");
    exit();
}