<?php
/**
 * JeevanRakt - Blood Request Submission Form
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Request Blood - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$requester_name = $blood_group = $quantity = $hospital = $city = $phone = $reason = $request_date = '';
$quantity = 1;
$request_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requester_name = sanitize_input($_POST['requester_name'] ?? '');
    $blood_group = sanitize_input($_POST['blood_group'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 1);
    $hospital = sanitize_input($_POST['hospital'] ?? '');
    $city = sanitize_input($_POST['city'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $reason = sanitize_input($_POST['reason'] ?? '');
    $request_date = sanitize_input($_POST['request_date'] ?? date('Y-m-d'));

    if (empty($requester_name)) $errors[] = "Requester Name is required.";
    if (empty($blood_group)) $errors[] = "Blood Group selection is required.";
    if ($quantity < 1 || $quantity > 10) $errors[] = "Quantity must be between 1 and 10 units.";
    if (empty($hospital)) $errors[] = "Hospital or Organization name is required.";
    if (empty($city)) $errors[] = "City is required.";
    if (empty($phone) || strlen($phone) < 10) $errors[] = "Valid contact phone number is required.";
    if (empty($reason)) $errors[] = "Please specify the medical reason for the request.";

    if (empty($errors)) {
        try {
            $stmtInsert = $pdo->prepare("INSERT INTO blood_requests 
                (requester_name, blood_group, quantity, hospital, city, phone, reason, request_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmtInsert->execute([$requester_name, $blood_group, $quantity, $hospital, $city, $phone, $reason, $request_date]);

            set_flash_message('success', 'Your blood request has been successfully submitted! An administrator will review your request shortly.');
            header('Location: request-blood.php?submitted=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <?php if (isset($_GET['submitted'])): ?>
            <div class="alert alert-success p-4 shadow-sm mb-4">
                <h4 class="fw-bold"><i class="fa-solid fa-circle-check text-success me-2"></i> Request Submitted Successfully</h4>
                <p class="mb-2">Your request for <strong><?= htmlspecialchars($blood_group ?? 'Blood') ?></strong> has been logged in JeevanRakt.</p>
                <p class="mb-0 small text-muted">You can also browse our available donors directory while your request is being reviewed.</p>
                <hr>
                <a href="search.php" class="btn btn-sm btn-success">Browse Donors Directory</a>
                <a href="request-blood.php" class="btn btn-sm btn-outline-secondary">Submit Another Request</a>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0 fw-bold">
                    <i class="fa-solid fa-hand-holding-medical me-2"></i> Patient Blood Request Form
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

                <form action="request-blood.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="requester_name" class="form-label fw-semibold">Requester / Patient Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="requester_name" name="requester_name" value="<?= htmlspecialchars($requester_name) ?>" placeholder="Full Name" required>
                        </div>

                        <div class="col-md-3">
                            <label for="blood_group" class="form-label fw-semibold">Blood Group Needed <span class="text-danger">*</span></label>
                            <select class="form-select" id="blood_group" name="blood_group" required>
                                <option value="">Select</option>
                                <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                                    <option value="<?= $bg ?>" <?= ($blood_group == $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="quantity" class="form-label fw-semibold">Quantity (Units) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="10" value="<?= htmlspecialchars($quantity) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="hospital" class="form-label fw-semibold">Hospital / Healthcare Facility <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hospital" name="hospital" value="<?= htmlspecialchars($hospital) ?>" placeholder="e.g. AIIMS Hospital, Max Healthcare" required>
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label fw-semibold">City / Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($city) ?>" placeholder="e.g. Delhi, Mumbai" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Contact Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="10-digit emergency number" required>
                        </div>

                        <div class="col-md-6">
                            <label for="request_date" class="form-label fw-semibold">Required By Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="request_date" name="request_date" value="<?= htmlspecialchars($request_date) ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="reason" class="form-label fw-semibold">Medical Reason / Urgency <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Briefly describe why blood is required (e.g. Surgery, Dengue treatment, Anemia emergency)" required><?= htmlspecialchars($reason) ?></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                        <button type="submit" class="btn btn-blood px-4">
                            <i class="fa-solid fa-paper-plane me-1"></i> Submit Blood Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
