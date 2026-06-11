<?php
session_start();
// 1. Link your central database connection configuration file
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Clean and capture user input variables securely
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $action   = isset($_POST['action']) ? trim($_POST['action']) : 'login';

    // Global Validation Guard: Stop empty form submittals
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    // 2. Automated Database Link Adapter
    // This dynamically identifies if your db.php file setup uses $pdo, $conn, or $db
    if (isset($pdo)) {
        $database = $pdo;
        $db_type = 'PDO';
    } elseif (isset($conn)) {
        $database = $conn;
        // Determine type based on the object instance style
        $db_type = ($conn instanceof PDO) ? 'PDO' : 'MySQLi';
    } elseif (isset($db)) {
        $database = $db;
        $db_type = ($db instanceof PDO) ? 'PDO' : 'MySQLi';
    } else {
        die("System Configuration Error: Active database connection instance reference variable not found.");
    }

    // ==========================================
    // LAYER A: USER REGISTRATION (SIGN UP MODE)
    // ==========================================
    if ($action === 'signup') {
        if (strlen($password) < 6) {
            header("Location: login.php?error=weak");
            exit();
        }

        // Generate secure cryptographic password hash
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        if ($db_type === 'PDO') {
            try {
                $stmt = $database->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->execute([':username' => $username, ':password' => $hashed_password]);
                
                $_SESSION['username'] = $username;
                $_SESSION['last_login_time'] = time();
                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                header("Location: login.php?error=incorrect");
                exit();
            }
        } else {
            // MySQLi Fallback Execution Strategy
            $stmt = $database->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("ss", $username, $hashed_password);
                if ($stmt->execute()) {
                    $_SESSION['username'] = $username;
                    $_SESSION['last_login_time'] = time();
                    header("Location: dashboard.php");
                    exit();
                }
            }
            header("Location: login.php?error=incorrect");
            exit();
        }
    } 
    
    // ==========================================
    // LAYER B: USER AUTHENTICATION (LOGIN MODE)
    // ==========================================
    else {
        $user = null;

        if ($db_type === 'PDO') {
            try {
                $stmt = $database->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                header("Location: login.php?error=incorrect");
                exit();
            }
        } else {
            // MySQLi Fallback Query Handling
            $stmt = $database->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            }
        }

        // Evaluate credentials match matrix
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['last_login_time'] = time();
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=incorrect");
            exit();
        }
    }
} else {
    header("Location: login.php");
    exit();
}
?>