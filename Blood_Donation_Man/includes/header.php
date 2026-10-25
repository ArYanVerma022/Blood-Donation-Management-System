<?php
/**
 * JeevanRakt - Header Include
 * Developer: Aryan Verma (Student ID: 241306064)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

$baseUrl = get_base_url();
$pageTitle = $pageTitle ?? 'JeevanRakt - Blood Donor Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>css/style.css">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="py-4">
    <div class="container">
        <?php display_flash_message(); ?>
