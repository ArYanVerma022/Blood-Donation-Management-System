<?php
/**
 * JeevanRakt - Search & Find Donors Directory
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Search Blood Donors - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

$blood_group = sanitize_input($_GET['blood_group'] ?? '');
$city = sanitize_input($_GET['city'] ?? '');
$status = sanitize_input($_GET['status'] ?? 'Available');

// Build dynamic search SQL query
$sql = "SELECT d.*, 
        (SELECT screening_result FROM donor_screening WHERE donor_id = d.id ORDER BY id DESC LIMIT 1) AS screening_result
        FROM donors d 
        WHERE 1=1";

$params = [];

if (!empty($blood_group)) {
    $sql .= " AND d.blood_group = ?";
    $params[] = $blood_group;
}

if (!empty($city)) {
    $sql .= " AND d.city LIKE ?";
    $params[] = '%' . $city . '%';
}

if (!empty($status)) {
    $sql .= " AND d.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY d.id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $donors = $stmt->fetchAll();
} catch (PDOException $e) {
    $donors = [];
    $error = "Error querying donors: " . $e->getMessage();
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-magnifying-glass text-blood-red me-2"></i> Find Blood Donors
        </h2>
        <p class="text-muted">Search available blood donors by blood group, city, and status.</p>
    </div>
</div>

<!-- Search Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3 bg-white rounded">
        <form action="search.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="blood_group" class="form-label fw-semibold mb-1">Blood Group</label>
                <select name="blood_group" id="blood_group" class="form-select">
                    <option value="">-- All Blood Groups --</option>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                        <option value="<?= $bg ?>" <?= ($blood_group == $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="city" class="form-label fw-semibold mb-1">City / Location</label>
                <input type="text" name="city" id="city" class="form-control" placeholder="e.g. Delhi, Mumbai, Bangalore" value="<?= htmlspecialchars($city) ?>">
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold mb-1">Availability Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="Available" <?= ($status == 'Available') ? 'selected' : '' ?>>Available Only</option>
                    <option value="Temporarily Unavailable" <?= ($status == 'Temporarily Unavailable') ? 'selected' : '' ?>>Temporarily Unavailable</option>
                    <option value="Unavailable" <?= ($status == 'Unavailable') ? 'selected' : '' ?>>Unavailable</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-blood">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">
            SearchResults <span class="badge bg-secondary"><?= count($donors) ?> Donors Found</span>
        </h5>
        <?php if (!empty($blood_group) || !empty($city)): ?>
            <a href="search.php" class="btn btn-sm btn-outline-secondary">Reset Filters</a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (empty($donors)): ?>
            <div class="text-center py-5">
                <img src="images/blood-drop.svg" alt="Empty" width="48" opacity="0.4" class="mb-3">
                <h5 class="text-muted fw-bold">No Matching Donors Found</h5>
                <p class="text-secondary small mb-3">Try broadening your search city or selecting all blood groups.</p>
                <a href="request-blood.php" class="btn btn-blood btn-sm">Submit a Blood Request Instead</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Group</th>
                            <th>City</th>
                            <th>Last Donation</th>
                            <th>Status</th>
                            <th>Screening Status</th>
                            <th>Contact Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donors as $d): ?>
                            <tr>
                                <td class="fw-bold">
                                    <i class="fa-solid fa-user me-2 text-primary"></i>
                                    <?= htmlspecialchars($d['name']) ?>
                                    <div class="small text-muted font-monospace"><?= htmlspecialchars($d['gender']) ?>, <?= (int)$d['age'] ?> Yrs</div>
                                </td>
                                <td>
                                    <span class="badge bg-danger fs-6 px-3 py-2"><?= htmlspecialchars($d['blood_group']) ?></span>
                                </td>
                                <td>
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    <?= htmlspecialchars($d['city']) ?>
                                </td>
                                <td>
                                    <?= $d['last_donation_date'] ? date('d M Y', strtotime($d['last_donation_date'])) : '<span class="text-muted">Never / First Time</span>' ?>
                                </td>
                                <td>
                                    <?php if ($d['status'] === 'Available'): ?>
                                        <span class="badge bg-success">Available</span>
                                    <?php elseif ($d['status'] === 'Temporarily Unavailable'): ?>
                                        <span class="badge bg-warning text-dark">Temp Unavailable</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Unavailable</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $scResult = $d['screening_result'] ?? 'Not Screened';
                                        if ($scResult === 'Proceed to Screening') {
                                            echo '<span class="badge bg-success-subtle text-success border border-success">🟢 Proceed</span>';
                                        } elseif ($scResult === 'Medical Review Required') {
                                            echo '<span class="badge bg-warning-subtle text-dark border border-warning">🟡 Review Req</span>';
                                        } elseif ($scResult === 'Temporarily Deferred') {
                                            echo '<span class="badge bg-danger-subtle text-danger border border-danger">🔴 Deferred</span>';
                                        } else {
                                            echo '<span class="badge bg-light text-muted border">Pending</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($d['status'] === 'Available'): ?>
                                        <a href="tel:<?= htmlspecialchars($d['phone']) ?>" class="btn btn-sm btn-outline-success">
                                            <i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($d['phone']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Protected / Hidden</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
