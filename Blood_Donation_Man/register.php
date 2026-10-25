<?php
/**
 * JeevanRakt - Donor Registration Page
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Donor Registration - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$name = $gender = $age = $blood_group = $phone = $email = $city = $address = $last_donation_date = '';
$status = 'Available';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $gender = sanitize_input($_POST['gender'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $blood_group = sanitize_input($_POST['blood_group'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $city = sanitize_input($_POST['city'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $last_donation_date = !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : null;
    $status = sanitize_input($_POST['status'] ?? 'Available');

    // Validation Rules
    if (empty($name)) $errors[] = "Full Name is required.";
    if (empty($gender)) $errors[] = "Gender selection is required.";
    if ($age < 18 || $age > 65) $errors[] = "Donor age must be between 18 and 65 years.";
    if (empty($blood_group)) $errors[] = "Blood Group selection is required.";
    if (empty($phone) || strlen($phone) < 10) $errors[] = "A valid 10-digit phone number is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email address is required.";
    if (empty($city)) $errors[] = "City is required.";

    // Check Duplicate Phone or Email
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT id FROM donors WHERE phone = ? OR email = ?");
        $stmtCheck->execute([$phone, $email]);
        if ($stmtCheck->fetch()) {
            $errors[] = "A donor with this Phone Number or Email is already registered.";
        }
    }

    // Insert into Database
    if (empty($errors)) {
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO donors (name, gender, age, blood_group, phone, email, city, address, last_donation_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->execute([$name, $gender, $age, $blood_group, $phone, $email, $city, $address, $last_donation_date, $status]);
            $donorId = $pdo->lastInsertId();

            set_flash_message('success', 'Donor registration successful! Please complete your Pre-Donation Health Screening below.');
            header("Location: screening.php?donor_id=" . $donorId);
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0 fw-bold">
                    <i class="fa-solid fa-user-plus me-2"></i> Donor Registration Form
                </h4>
            </div>
            <div class="card-body p-4">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Aryan Verma" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="gender" class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select</option>
                                <option value="Male" <?= ($gender == 'Male') ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($gender == 'Female') ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($gender == 'Other') ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="age" class="form-label fw-semibold">Age (Years) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="age" name="age" min="18" max="65" value="<?= htmlspecialchars($age ?: '') ?>" placeholder="18-65" required>
                        </div>

                        <div class="col-md-4">
                            <label for="blood_group" class="form-label fw-semibold">Blood Group <span class="text-danger">*</span></label>
                            <select class="form-select" id="blood_group" name="blood_group" required>
                                <option value="">Select Group</option>
                                <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                                    <option value="<?= $bg ?>" <?= ($blood_group == $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="10-digit number" required>
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="name@example.com" required>
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($city) ?>" placeholder="e.g. Delhi, Mumbai" required>
                        </div>

                        <div class="col-md-6">
                            <label for="last_donation_date" class="form-label fw-semibold">Last Donation Date</label>
                            <input type="date" class="form-control" id="last_donation_date" name="last_donation_date" value="<?= htmlspecialchars($last_donation_date) ?>">
                            <small class="text-muted">Leave blank if this is your first time donating.</small>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Address Details</label>
                            <textarea class="form-control" id="address" name="address" rows="2" placeholder="Locality, street name, pincode"><?= htmlspecialchars($address) ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Initial Availability Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Available" <?= ($status == 'Available') ? 'selected' : '' ?>>Available</option>
                                <option value="Temporarily Unavailable" <?= ($status == 'Temporarily Unavailable') ? 'selected' : '' ?>>Temporarily Unavailable</option>
                                <option value="Unavailable" <?= ($status == 'Unavailable') ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-blood px-4">
                            Submit & Proceed to Health Screening &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
