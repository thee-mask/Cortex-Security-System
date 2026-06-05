<?php
// Secure session configuration before execution
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1'); // Blocks JavaScript XSS access to session cookies
    ini_set('session.use_only_cookies', '1');
    session_start();
}

/**
 * Generates a secure cryptographic anti-CSRF token for forms
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validates the incoming form CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitizes user input data to prevent Cross-Site Scripting (XSS)
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}