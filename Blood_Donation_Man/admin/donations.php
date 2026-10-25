<?php
/**
 * JeevanRakt - Admin Manage Donations & Record New Donation
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Manage Donations - Admin JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Fetch Donors for Dropdown Selection
try {
    $stmtDonors = $pdo->query("SELECT id, name, blood_group, city, status FROM donors ORDER BY name ASC");
    $donorsList = $stmtDonors->fetchAll();
} catch (PDOException $e) {
    $donorsList = [];
}

// Handle Record New Donation Submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_donation'])) {
    $donor_id = (int)($_POST['donor_id'] ?? 0);
    $blood_group = sanitize_input($_POST['blood_group'] ?? '');
    $donation_date = sanitize_input($_POST['donation_date'] ?? date('Y-m-d'));
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($donor_id <= 0) $error = "Please select a valid donor.";
    if (empty($blood_group)) $error = "Blood Group is required.";
    if ($quantity <= 0) $error = "Quantity must be at least 1 unit.";

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            // 1. Insert Donation Record
            $stmtIns = $pdo->prepare("INSERT INTO donations (donor_id, donation_date, blood_group, quantity) VALUES (?, ?, ?, ?)");
            $stmtIns->execute([$donor_id, $donation_date, $blood_group, $quantity]);

            // 2. Update Blood Stock Inventory (+ Quantity)
            $stmtStock = $pdo->prepare("UPDATE blood_stock SET quantity = quantity + ? WHERE blood_group = ?");
            $stmtStock->execute([$quantity, $blood_group]);

            // 3. Update Donor's Last Donation Date
            $stmtDonorDate = $pdo->prepare("UPDATE donors SET last_donation_date = ? WHERE id = ?");
            $stmtDonorDate->execute([$donation_date, $donor_id]);

            $pdo->commit();

            set_flash_message('success', "Donation recorded successfully! Added $quantity unit(s) of $blood_group to stock.");
            header('Location: donations.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Transaction Failed: " . $e->getMessage();
        }
    }
}

// Handle Delete Donation Record
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Fetch donation info first to adjust stock
        $stmtFind = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
        $stmtFind->execute([$id]);
        $dn = $stmtFind->fetch();

        if ($dn) {
            $pdo->beginTransaction();
            // Deduct stock
            $stmtDeduct = $pdo->prepare("UPDATE blood_stock SET quantity = GREATEST(0, quantity - ?) WHERE blood_group = ?");
            $stmtDeduct->execute([$dn['quantity'], $dn['blood_group']]);

            // Delete donation row
            $stmtDel = $pdo->prepare("DELETE FROM donations WHERE id = ?");
            $stmtDel->execute([$id]);

            $pdo->commit();
            set_flash_message('success', 'Donation record deleted and stock adjusted.');
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        set_flash_message('danger', 'Error deleting record: ' . $e->getMessage());
    }
    header('Location: donations.php');
    exit;
}

// Fetch Donation History List
try {
    $stmtHistory = $pdo->query("SELECT dn.*, d.name AS donor_name, d.city, d.phone 
                                FROM donations dn 
                                JOIN donors d ON dn.donor_id = d.id 
                                ORDER BY dn.id DESC");
    $donationsHistory = $stmtHistory->fetchAll();
} catch (PDOException $e) {
    $donationsHistory = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-hand-holding-droplet text-success me-2"></i> Manage Blood Donations
        </h2>
        <p class="text-muted mb-0">Record new blood donations and view full donation history.</p>
    </div>
    <button class="btn btn-blood btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#recordDonationForm" aria-expanded="false" aria-controls="recordDonationForm">
        <i class="fa-solid fa-plus me-1"></i> Record New Donation
    </button>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger mb-4"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Collapsible Record Donation Form -->
<div class="collapse mb-4 <?= (isset($_GET['action']) && $_GET['action'] === 'add') ? 'show' : '' ?>" id="recordDonationForm">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Record New Donor Donation</h5>
        </div>
        <div class="card-body p-4">
            <form action="donations.php" method="POST">
                <input type="hidden" name="record_donation" value="1">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="donor_id" class="form-label fw-semibold">Select Registered Donor <span class="text-danger">*</span></label>
                        <select name="donor_id" id="donor_id" class="form-select" required>
                            <option value="">-- Choose Donor --</option>
                            <?php foreach ($donorsList as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['blood_group']) ?> | <?= htmlspecialchars($d['city']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="blood_group" class="form-label fw-semibold">Blood Group <span class="text-danger">*</span></label>
                        <select name="blood_group" id="blood_group" class="form-select" required>
                            <option value="">Select Group</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                                <option value="<?= $bg ?>"><?= $bg ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="quantity" class="form-label fw-semibold">Units (Qty) <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" max="5" value="1" required>
                    </div>

                    <div class="col-md-2">
                        <label for="donation_date" class="form-label fw-semibold">Donation Date <span class="text-danger">*</span></label>
                        <input type="date" name="donation_date" id="donation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Save Donation & Sync Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Donation History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Donation History Log</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead>
                    <tr>
                        <th>Donation ID</th>
                        <th>Donor Name</th>
                        <th>Blood Group</th>
                        <th>Quantity</th>
                        <th>Donation Date</th>
                        <th>Logged At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donationsHistory)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No donation history recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($donationsHistory as $dh): ?>
                            <tr>
                                <td>#<?= $dh['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($dh['donor_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($dh['city']) ?> | <?= htmlspecialchars($dh['phone']) ?></small>
                                </td>
                                <td><span class="badge bg-danger fs-6"><?= htmlspecialchars($dh['blood_group']) ?></span></td>
                                <td class="fw-bold text-dark"><?= (int)$dh['quantity'] ?> Unit(s)</td>
                                <td><?= date('d M Y', strtotime($dh['donation_date'])) ?></td>
                                <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($dh['created_at'])) ?></td>
                                <td>
                                    <a href="donations.php?action=delete&id=<?= $dh['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
