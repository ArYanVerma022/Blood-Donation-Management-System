<?php
/**
 * JeevanRakt - Admin Manage Donors
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Manage Donors - Admin JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Handle Delete Request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmtDel = $pdo->prepare("DELETE FROM donors WHERE id = ?");
        $stmtDel->execute([$id]);
        set_flash_message('success', 'Donor record deleted successfully.');
    } catch (PDOException $e) {
        set_flash_message('danger', 'Error deleting donor: ' . $e->getMessage());
    }
    header('Location: donors.php');
    exit;
}

// Handle Update Status Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $donor_id = (int)$_POST['donor_id'];
    $new_status = sanitize_input($_POST['status']);
    try {
        $stmtUpd = $pdo->prepare("UPDATE donors SET status = ? WHERE id = ?");
        $stmtUpd->execute([$new_status, $donor_id]);
        set_flash_message('success', 'Donor availability status updated to ' . $new_status);
    } catch (PDOException $e) {
        set_flash_message('danger', 'Error updating donor status: ' . $e->getMessage());
    }
    header('Location: donors.php');
    exit;
}

// Search / Filter
$search = sanitize_input($_GET['search'] ?? '');
$blood_group = sanitize_input($_GET['blood_group'] ?? '');

$sql = "SELECT d.*, 
        (SELECT screening_result FROM donor_screening WHERE donor_id = d.id ORDER BY id DESC LIMIT 1) AS screening_result
        FROM donors d WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (d.name LIKE ? OR d.city LIKE ? OR d.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($blood_group)) {
    $sql .= " AND d.blood_group = ?";
    $params[] = $blood_group;
}

$sql .= " ORDER BY d.id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $donors = $stmt->fetchAll();
} catch (PDOException $e) {
    $donors = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-users text-primary me-2"></i> Manage Registered Donors
        </h2>
        <p class="text-muted mb-0">View, update status, and manage all blood donor profiles.</p>
    </div>
    <a href="../register.php" class="btn btn-blood btn-sm" target="_blank">
        <i class="fa-solid fa-plus me-1"></i> Register New Donor
    </a>
</div>

<!-- Search Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 bg-white">
        <form action="donors.php" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by name, city, or phone..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="blood_group" class="form-select">
                    <option value="">-- All Blood Groups --</option>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                        <option value="<?= $bg ?>" <?= ($blood_group == $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search me-1"></i> Search</button>
            </div>
            <div class="col-md-2">
                <a href="donors.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Donors List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor Name</th>
                        <th>Blood Group</th>
                        <th>Gender / Age</th>
                        <th>City & Phone</th>
                        <th>Last Donation</th>
                        <th>Status</th>
                        <th>Screening Result</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donors)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No donor records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($donors as $d): ?>
                            <tr>
                                <td>#<?= $d['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($d['name']) ?></td>
                                <td><span class="badge bg-danger fs-6"><?= htmlspecialchars($d['blood_group']) ?></span></td>
                                <td><?= htmlspecialchars($d['gender']) ?>, <?= (int)$d['age'] ?> Yrs</td>
                                <td>
                                    <?= htmlspecialchars($d['city']) ?><br>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($d['phone']) ?></small>
                                </td>
                                <td><?= $d['last_donation_date'] ? date('d M Y', strtotime($d['last_donation_date'])) : '<span class="text-muted">Never</span>' ?></td>
                                <td>
                                    <!-- Change Status Dropdown Form -->
                                    <form action="donors.php" method="POST" class="d-inline">
                                        <input type="hidden" name="donor_id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="Available" <?= ($d['status'] === 'Available') ? 'selected' : '' ?>>Available</option>
                                            <option value="Temporarily Unavailable" <?= ($d['status'] === 'Temporarily Unavailable') ? 'selected' : '' ?>>Temp Unavailable</option>
                                            <option value="Unavailable" <?= ($d['status'] === 'Unavailable') ? 'selected' : '' ?>>Unavailable</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php 
                                        $sc = $d['screening_result'] ?? 'None';
                                        if ($sc === 'Proceed to Screening') echo '<span class="badge bg-success">🟢 Proceed</span>';
                                        elseif ($sc === 'Medical Review Required') echo '<span class="badge bg-warning text-dark">🟡 Review</span>';
                                        elseif ($sc === 'Temporarily Deferred') echo '<span class="badge bg-danger">🔴 Deferred</span>';
                                        else echo '<span class="badge bg-light text-muted">Not Screened</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="donor-screening.php?donor_id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="View Screening Records">
                                        <i class="fa-solid fa-notes-medical"></i>
                                    </a>
                                    <a href="donors.php?action=delete&id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" title="Delete Donor">
                                        <i class="fa-solid fa-trash"></i>
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
