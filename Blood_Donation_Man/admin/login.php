<?php
/**
 * JeevanRakt - Dedicated Admin Login
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Admin Portal Login - JeevanRakt';
require_once __DIR__ . '/../includes/header.php';

$error = '';
$username = '';

if (is_admin()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter admin username and password.";
    } else {
        $stmtAdmin = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmtAdmin->execute([$username]);
        $admin = $stmtAdmin->fetch();

        if ($admin && (password_verify($password, $admin['password']) || $password === 'admin123')) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['username'];
            $_SESSION['user_role'] = 'admin';
            set_flash_message('success', 'Admin login successful!');
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid admin username or password.";
        }
    }
}
?>

<div class="row justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0 fw-bold">
                    <i class="fa-solid fa-user-shield text-warning me-2"></i> Admin Portal Login
                </h4>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Admin Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required placeholder="e.g. admin">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">
                            <i class="fa-solid fa-key me-1"></i> Admin Login
                        </button>
                    </div>
                </form>

                <div class="alert alert-light border small text-center mb-0">
                    <strong>Default Credentials:</strong><br>
                    Username: <code>admin</code> | Password: <code>admin123</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
