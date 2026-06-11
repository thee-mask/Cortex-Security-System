<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System - Portal Authentication</title>
    <style>
        :root {
            --bg-dark: #0f172a;
            --panel-bg: rgba(30, 41, 59, 0.7);
            --border-glass: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #3b82f6;
            --accent-danger: #ef4444;
            --input-bg: rgba(15, 23, 42, 0.6);
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-size: 24px;
            margin: 0;
            background: linear-gradient(to right, #3b82f6, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 8px 0 0 0;
        }

        form {
            display: grid;
            gap: 20px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group label {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
        }

        input {
            padding: 12px 16px;
            background: var(--input-bg);
            border: 1px solid var(--border-glass);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        button {
            background: var(--accent-primary);
            color: var(--text-main);
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .error-banner {
            background: rgba(239, 68, 68, 0.15);
            color: var(--accent-danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            text-align: center;
            font-weight: 500;
        }

        .toggle-container {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .toggle-link {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .toggle-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h2 id="form-title">Portal Authentication</h2>
            <p id="form-subtitle">Sign in to access the classroom administration dashboard.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'incorrect'): ?>
                <div class="error-banner">❌ Invalid credentials or account unavailable.</div>
            <?php elseif ($_GET['error'] === 'empty'): ?>
                <div class="error-banner">⚠️ All authentication fields are required.</div>
            <?php elseif ($_GET['error'] === 'weak'): ?>
                <div class="error-banner" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                    🔒 Weak Password! Registration requires at least 6 characters.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="process.php" method="POST">
            <input type="hidden" id="form-action" name="action" value="login">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" id="submit-btn">Secure Log In</button>
        </form>

        <div class="toggle-container">
            <span id="toggle-text">New user? </span>
            <a class="toggle-link" id="toggle-mode-btn" onclick="toggleAuthMode()">Create an Account</a>
        </div>
    </div>

    <script>
        function toggleAuthMode() {
            const currentAction = document.getElementById('form-action').value;
            const formTitle = document.getElementById('form-title');
            const formSubtitle = document.getElementById('form-subtitle');
            const submitBtn = document.getElementById('submit-btn');
            const toggleText = document.getElementById('toggle-text');
            const toggleModeBtn = document.getElementById('toggle-mode-btn');
            const formAction = document.getElementById('form-action');

            if (currentAction === 'login') {
                // Switch interface to Sign Up mode
                formAction.value = 'signup';
                formTitle.innerText = "Create an Account";
                formSubtitle.innerText = "Register your credentials to access the roster system.";
                submitBtn.innerText = "Register New Account";
                toggleText.innerText = "Already have an account? ";
                toggleModeBtn.innerText = "Login here";
            } else {
                // Switch interface back to Login mode
                formAction.value = 'login';
                formTitle.innerText = "Portal Authentication";
                formSubtitle.innerText = "Sign in to access the classroom administration dashboard.";
                submitBtn.innerText = "Secure Log In";
                toggleText.innerText = "New user? ";
                toggleModeBtn.innerText = "Create an Account";
            }
        }
    </script>
</body>
</html>