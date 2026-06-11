<?php
// 1. Link your central MySQLi connection file
require_once __DIR__ . '/includes/db.php';

// Capture form values sent via FormData from dashboard.php
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$admission = isset($_POST['admission']) ? trim($_POST['admission']) : null;
$course = isset($_POST['course']) ? trim($_POST['course']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : null;

// Stop execution if fields are completely blank
if (!$name || !$admission || !$course || !$status) {
    die("error_missing_fields");
}

// Automatically resolve your active database connection variable name from db.php
if (isset($pdo)) { $database = $pdo; }
elseif (isset($conn)) { $database = $conn; }
elseif (isset($db)) { $database = $db; }
else { die("Database connection variable not found inside includes/db.php"); }

try {
    // STEP 1: Safely prepare the check query
    $check_sql = "SELECT id FROM attendance WHERE admission = ?";
    $check_stmt = $database->prepare($check_sql);
    
    if (!$check_stmt) {
        // This catches if the table 'attendance' or column 'admission' doesn't exist
        die("Database Structural Error: " . $database->error);
    }
    
    $check_stmt->bind_param("s", $admission);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // STEP 2: Record exists! Update it
        $update_sql = "UPDATE attendance SET status = ?, name = ?, course = ? WHERE admission = ?";
        $update_stmt = $database->prepare($update_sql);
        if (!$update_stmt) { die("Update Prep Error: " . $database->error); }
        
        $update_stmt->bind_param("ssss", $status, $name, $course, $admission);
        $update_stmt->execute();
    } else {
        // STEP 3: Brand new record! Insert it
        $insert_sql = "INSERT INTO attendance (name, admission, course, status) VALUES (?, ?, ?, ?)";
        $insert_stmt = $database->prepare($insert_sql);
        if (!$insert_stmt) { die("Insert Prep Error: " . $database->error); }
        
        $insert_stmt->bind_param("ssss", $name, $admission, $course, $status);
        $insert_stmt->execute();
    }

    // Echo exactly success so dashboard.php knows everything worked perfectly
    echo "success";

} catch (Exception $e) {
    echo "Execution Error: " . $e->getMessage();
}
?>