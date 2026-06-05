<?php
// Database configuration
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "studentdb";

// Connect to MySQL
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

// Check connection structural state
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>