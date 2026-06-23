<?php
/**
 * Admin Authentication Check
 * SuaNet Fibra
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/site.php';

function is_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_auth(): void {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function get_admin_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id' => $_SESSION['admin_id'] ?? 0,
        'username' => $_SESSION['admin_username'] ?? '',
        'full_name' => $_SESSION['admin_full_name'] ?? 'Admin'
    ];
}

// Check session timeout
if (is_logged_in() && isset($_SESSION['admin_last_activity'])) {
    if (time() - $_SESSION['admin_last_activity'] > SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        header('Location: index.php?expired=1');
        exit;
    }
}
if (is_logged_in()) {
    $_SESSION['admin_last_activity'] = time();
}
