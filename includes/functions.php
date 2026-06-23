<?php
/**
 * Helper Functions
 * SuaNet Fibra - ISP Landing Page
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/site.php';

/**
 * Get a single setting value from the database
 */
function get_setting(string $key, string $default = ''): string {
    static $cache = [];
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $cache[$key] = $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        $cache[$key] = $default;
    }
    return $cache[$key];
}

/**
 * Get all settings grouped by group
 */
function get_all_settings(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM site_settings ORDER BY setting_group, id");
        $rows = $stmt->fetchAll();
        $cache = [];
        foreach ($rows as $row) {
            $cache[$row['setting_group']][] = $row;
        }
    } catch (Exception $e) {
        $cache = [];
    }
    return $cache;
}

/**
 * Get all active plans
 */
function get_plans(bool $active_only = true): array {
    try {
        $db = getDB();
        $sql = "SELECT * FROM plans";
        if ($active_only) {
            $sql .= " WHERE active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all active differentials
 */
function get_differentials(bool $active_only = true): array {
    try {
        $db = getDB();
        $sql = "SELECT * FROM differentials";
        if ($active_only) {
            $sql .= " WHERE active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Escape output for HTML
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if a setting is truthy
 */
function setting_enabled(string $key): bool {
    return in_array(get_setting($key), ['1', 'true', 'yes', 'on']);
}

/**
 * Format phone number for display
 */
function format_phone(string $phone): string {
    $digits = preg_replace('/\D/', '', $phone);
    if (strlen($digits) === 11) {
        return '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 5) . '-' . substr($digits, 7);
    }
    return $phone;
}

/**
 * Get current page URL
 */
function current_url(): string {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
           '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
