<?php
/**
 * JeevanRakt - Blood Donor Management System
 * Database Configuration & Connection File
 * Developer: Aryan Verma (Student ID: 241306064)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'blood_donor_management';
$username = 'root';
$password = ''; // Default XAMPP MySQL password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // If database connection fails, provide a clear, helpful message
    die("<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24; border-radius: 8px;'>
        <h2>🩸 JeevanRakt - Database Connection Error</h2>
        <p>Could not connect to the database <strong>$dbname</strong> on <strong>$host</strong>.</p>
        <p><strong>Troubleshooting Steps:</strong></p>
        <ul>
            <li>Ensure MySQL service is running in your <strong>XAMPP Control Panel</strong>.</li>
            <li>Make sure you have created the database <code>$dbname</code> in phpMyAdmin.</li>
            <li>Import the <code>database/blood_donor_management.sql</code> file into phpMyAdmin.</li>
        </ul>
        <p><small>Error details: " . htmlspecialchars($e->getMessage()) . "</small></p>
    </div>");
}

/**
 * Utility function to sanitize user input for standard XSS prevention
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Flash Message System for clean feedback banners
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_type'] = $type; // e.g. 'success', 'danger', 'warning', 'info'
    $_SESSION['flash_message'] = $message;
}

function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        
        echo "<div class='alert alert-{$type} alert-dismissible fade show shadow-sm' role='alert'>
                " . htmlspecialchars($msg) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}
