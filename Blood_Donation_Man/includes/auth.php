<?php
/**
 * JeevanRakt - Blood Donor Management System
 * Authentication & Session Helper
 * Developer: Aryan Verma (Student ID: 241306064)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if current session is an admin
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if current session is logged in (User or Admin)
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Enforce Admin-only page restriction
 */
function require_admin() {
    if (!is_admin()) {
        $_SESSION['flash_type'] = 'danger';
        $_SESSION['flash_message'] = 'Access restricted to administrators only. Please log in as Admin.';
        header('Location: ' . get_base_url() . 'admin/login.php');
        exit;
    }
}

/**
 * Enforce logged-in user requirement
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_type'] = 'warning';
        $_SESSION['flash_message'] = 'Please login to access this page.';
        header('Location: ' . get_base_url() . 'login.php');
        exit;
    }
}

/**
 * Get base URL dynamically based on script path
 */
function get_base_url() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    if (strpos($scriptName, '/admin/') !== false) {
        return '../';
    }
    return './';
}
