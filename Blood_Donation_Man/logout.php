<?php
/**
 * JeevanRakt - User Logout
 * Developer: Aryan Verma (Student ID: 241306064)
 */
require_once __DIR__ . '/config/database.php';

session_unset();
session_destroy();

session_start();
set_flash_message('info', 'You have been logged out successfully.');
header('Location: index.php');
exit;
