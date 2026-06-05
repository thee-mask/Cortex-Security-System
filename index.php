<?php
// Using absolute path to guarantee the file is found perfectly
require_once __DIR__ . '/includes/security.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 50px; }
        .form-container { max-width: 400px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007BFF; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create an Account</h2>
        <form action="process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" name="register_action">Sign Up</button>
        </form>
        <p style="text-align:center; margin-top:15px;">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>