<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';

if (isset($pdo)) { $database = $pdo; $db_type = 'PDO'; }
elseif (isset($conn)) { $database = $conn; $db_type = ($conn instanceof PDO) ? 'PDO' : 'MySQLi'; }
elseif (isset($db)) { $database = $db; $db_type = ($db instanceof PDO) ? 'PDO' : 'MySQLi'; }

$students = [];

try {
    if ($db_type === 'PDO') {
        $stmt = $database->query("SELECT name, admission, course, status FROM attendance ORDER BY id DESC");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $result = $database->query("SELECT name, admission, course, status FROM attendance ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    echo json_encode($students);
} catch (Exception $e) {
    echo json_encode([]);
}
?>