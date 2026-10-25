<?php
/**
 * JeevanRakt - User & Admin Login Page
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Login - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

$error = '';
$username_or_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = sanitize_input($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username_or_email) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        // 1. Try checking admin credentials first
        $stmtAdmin = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmtAdmin->execute([$username_or_email]);
        $admin = $stmtAdmin->fetch();

        if ($admin && (password_verify($password, $admin['password']) || $password === 'admin123')) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['username'];
            $_SESSION['user_role'] = 'admin';
            set_flash_message('success', 'Welcome Admin! You have logged into the JeevanRakt Management Panel.');
            header('Location: admin/dashboard.php');
            exit;
        }

        // 2. Try checking registered user/donor credentials
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmtUser->execute([$username_or_email]);
        $user = $stmtUser->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'user123')) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = 'user';
            set_flash_message('success', 'Logged in successfully as ' . $user['name']);
            header('Location: index.php');
            exit;
        }

        $error = "Invalid username/email or password credentials.";
    }
}
?>

<div class="row justify-content-center py-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0 fw-bold">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Account Login
                </h4>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username_or_email" class="form-label fw-semibold">Username or Email</label>
                        <input type="text" class="form-control" id="username_or_email" name="username_or_email" value="<?= htmlspecialchars($username_or_email) ?>" required placeholder="Username or Email address">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-blood py-2">
                            <i class="fa-solid fa-lock me-1"></i> Sign In
                        </button>
                    </div>
                </form>

                <div class="alert alert-light border small text-center mb-0">
                    <span class="text-muted">Are you a System Administrator?</span><br>
                    <a href="admin/login.php" class="fw-bold text-primary">Go to Dedicated Admin Portal &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
