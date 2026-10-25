<?php
/**
 * JeevanRakt - Navigation Bar Include
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$baseUrl = get_base_url();
$currentScript = basename($_SERVER['SCRIPT_NAME']);
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= $baseUrl ?>index.php">
            <img src="<?= $baseUrl ?>images/blood-drop.svg" alt="Drop Logo" width="30" height="36" class="me-2">
            <span>JeevanRakt</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'index.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>index.php">
                        <i class="fa-solid fa-house me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'search.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>search.php">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Find Donor
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'blood-stock.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>blood-stock.php">
                        <i class="fa-solid fa-vials me-1"></i> Blood Stock
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'request-blood.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>request-blood.php">
                        <i class="fa-solid fa-hand-holding-hand me-1"></i> Request Blood
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'register.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>register.php">
                        <i class="fa-solid fa-user-plus me-1"></i> Register as Donor
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'screening.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>screening.php">
                        <i class="fa-solid fa-clipboard-check me-1"></i> Health Screening
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'about.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>about.php">
                        <i class="fa-solid fa-circle-info me-1"></i> About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript == 'contact.php') ? 'active' : '' ?>" href="<?= $baseUrl ?>contact.php">
                        <i class="fa-solid fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?php if (is_admin()): ?>
                    <a href="<?= $baseUrl ?>admin/dashboard.php" class="btn btn-sm btn-warning fw-bold text-dark me-1">
                        <i class="fa-solid fa-gauge-high me-1"></i> Admin Panel
                    </a>
                    <a href="<?= $baseUrl ?>admin/logout.php" class="btn btn-sm btn-outline-light">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </a>
                <?php elseif (is_logged_in()): ?>
                    <span class="text-light me-2 small">
                        <i class="fa-solid fa-user me-1"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                    </span>
                    <a href="<?= $baseUrl ?>logout.php" class="btn btn-sm btn-outline-light">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="<?= $baseUrl ?>login.php" class="btn btn-sm btn-outline-light me-1">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                    </a>
                    <a href="<?= $baseUrl ?>admin/login.php" class="btn btn-sm btn-secondary opacity-75">
                        <i class="fa-solid fa-user-shield me-1"></i> Admin Portal
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
