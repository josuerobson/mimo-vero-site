<?php
/**
 * Site Constants & Configuration
 * SuaNet Fibra - ISP Landing Page
 */

define('SITE_ROOT', dirname(__DIR__));
define('SITE_URL', 'http://localhost/vero-site');
define('ADMIN_PATH', SITE_ROOT . '/admin');

// Session settings
define('SESSION_LIFETIME', 3600 * 8); // 8 hours

// CSRF token name
define('CSRF_TOKEN_NAME', 'csrf_token');

// File upload limits (for future use)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(SESSION_LIFETIME);
    session_start();
}

// Generate or get CSRF token
function csrf_token(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Verify CSRF token
function verify_csrf(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// CSRF hidden field
function csrf_field(): string {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars(csrf_token()) . '">';
}
