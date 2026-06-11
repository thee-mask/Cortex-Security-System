<?php
// Database Configuration Parameters
$host = "localhost";
$db_user = "root";         // Default XAMPP username
$db_pass = "";             // Default XAMPP password is empty
$db_name = "attendance_db"; // Standardized to your verified database name

// Establish Secure Connection to MySQL via MySQLi
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check if the connection established successfully
if ($conn->connect_error) {
    // If the connection fails, halt execution and display the error description
    die("Database Connection Failure: " . $conn->connect_error);
}

// Set charset to utf8mb4 to match your phpMyAdmin collation perfectly
$conn->set_charset("utf8mb4");

// Making the connection object globally accessible across your app controllers
$database = $conn;
?>