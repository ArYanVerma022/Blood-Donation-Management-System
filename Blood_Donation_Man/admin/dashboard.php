<?php
/**
 * JeevanRakt - Admin Dashboard
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Admin Dashboard - JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Fetch summary metrics
try {
    $stmtDonors = $pdo->query("SELECT COUNT(*) FROM donors");
    $totalDonors = $stmtDonors->fetchColumn();

    $stmtDonations = $pdo->query("SELECT SUM(quantity) FROM donations");
    $totalDonations = $stmtDonations->fetchColumn() ?: 0;

    $stmtStock = $pdo->query("SELECT SUM(quantity) FROM blood_stock");
    $totalStock = $stmtStock->fetchColumn() ?: 0;

    $stmtRequests = $pdo->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'Pending'");
    $pendingRequests = $stmtRequests->fetchColumn();

    // Recent Donors
    $stmtRecentDonors = $pdo->query("SELECT * FROM donors ORDER BY id DESC LIMIT 5");
    $recentDonors = $stmtRecentDonors->fetchAll();

    // Recent Donations
    $stmtRecentDonations = $pdo->query("SELECT dn.*, d.name AS donor_name FROM donations dn JOIN donors d ON dn.donor_id = d.id ORDER BY dn.id DESC LIMIT 5");
    $recentDonations = $stmtRecentDonations->fetchAll();

    // Pending Requests
    $stmtPendingReqs = $pdo->query("SELECT * FROM blood_requests WHERE status = 'Pending' ORDER BY id DESC LIMIT 5");
    $pendingReqs = $stmtPendingReqs->fetchAll();

    // Low Stock Alert Items
    $stmtLowStock = $pdo->query("SELECT * FROM blood_stock WHERE quantity <= 4 ORDER BY quantity ASC");
    $lowStockItems = $stmtLowStock->fetchAll();

} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>

<!-- Header Banner -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-gauge-high text-primary me-2"></i> Admin Control Dashboard
        </h2>
        <p class="text-muted mb-0">Overview of donors, stock levels, donations, and blood requests.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="donations.php?action=add" class="btn btn-blood btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Record Donation
        </a>
        <a href="reports.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-file-invoice me-1"></i> View Reports
        </a>
    </div>
</div>

<!-- Admin Action Quick Links Navigation Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2 px-3 bg-white rounded">
        <div class="d-flex flex-wrap gap-2">
            <a href="donors.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-users me-1"></i> Manage Donors</a>
            <a href="donor-screening.php" class="btn btn-sm btn-outline-info text-dark"><i class="fa-solid fa-clipboard-check me-1"></i> Health Screening Records</a>
            <a href="donations.php" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-hand-holding-droplet me-1"></i> Manage Donations</a>
            <a href="blood-stock.php" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-vials me-1"></i> Manage Blood Stock</a>
            <a href="requests.php" class="btn btn-sm btn-outline-warning text-dark"><i class="fa-solid fa-notes-medical me-1"></i> Manage Requests</a>
            <a href="reports.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-chart-pie me-1"></i> System Reports</a>
        </div>
    </div>
</div>

<!-- 4 Main Administrative Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card stat-donors">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Donors</div>
                    <div class="fs-2 fw-bold text-dark"><?= number_format($totalDonors) ?></div>
                </div>
                <div class="fs-1 text-primary opacity-50"><i class="fa-solid fa-users"></i></div>
            </div>
            <a href="donors.php" class="small text-decoration-none mt-2 d-inline-block">View All Donors &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-card stat-donations">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Donations</div>
                    <div class="fs-2 fw-bold text-dark"><?= number_format($totalDonations) ?></div>
                </div>
                <div class="fs-1 text-success opacity-50"><i class="fa-solid fa-hand-holding-droplet"></i></div>
            </div>
            <a href="donations.php" class="small text-decoration-none mt-2 d-inline-block">View History &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-card stat-stock">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Blood Units</div>
                    <div class="fs-2 fw-bold text-dark"><?= number_format($totalStock) ?></div>
                </div>
                <div class="fs-1 text-danger opacity-50"><i class="fa-solid fa-vials"></i></div>
            </div>
            <a href="blood-stock.php" class="small text-decoration-none mt-2 d-inline-block">Manage Inventory &rarr;</a>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-card stat-requests">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Requests</div>
                    <div class="fs-2 fw-bold text-dark"><?= number_format($pendingRequests) ?></div>
                </div>
                <div class="fs-1 text-warning opacity-50"><i class="fa-solid fa-clock"></i></div>
            </div>
            <a href="requests.php" class="small text-decoration-none mt-2 d-inline-block">Review Requests &rarr;</a>
        </div>
    </div>
</div>

<!-- Low Stock Warning Banner if any -->
<?php if (!empty($lowStockItems)): ?>
    <div class="alert alert-warning border-warning shadow-sm mb-4">
        <h5 class="fw-bold mb-2">
            <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Low Blood Stock Warning Alert!
        </h5>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <?php foreach ($lowStockItems as $lsi): ?>
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <?= htmlspecialchars($lsi['blood_group']) ?>: Only <?= (int)$lsi['quantity'] ?> Units Left
                </span>
            <?php endforeach; ?>
            <a href="blood-stock.php" class="btn btn-sm btn-dark ms-auto">Update Stock Units Now</a>
        </div>
    </div>
<?php endif; ?>

<!-- Dashboard Section Grids -->
<div class="row g-4 mb-4">
    
    <!-- Recent Donors -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i> Recent Donors</h5>
                <a href="donors.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Blood Group</th>
                            <th>City</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentDonors as $rd): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($rd['name']) ?></strong></td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars($rd['blood_group']) ?></span></td>
                                <td><?= htmlspecialchars($rd['city']) ?></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($rd['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Donations -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-hand-holding-droplet text-success me-2"></i> Recent Donations</h5>
                <a href="donations.php" class="btn btn-sm btn-outline-success">View History</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Donor</th>
                            <th>Group</th>
                            <th>Quantity</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentDonations as $rdn): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($rdn['donor_name']) ?></strong></td>
                                <td><span class="badge bg-danger"><?= htmlspecialchars($rdn['blood_group']) ?></span></td>
                                <td><?= (int)$rdn['quantity'] ?> Unit(s)</td>
                                <td><?= date('d M Y', strtotime($rdn['donation_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left text-warning me-2"></i> Pending Patient Blood Requests</h5>
                <a href="requests.php" class="btn btn-sm btn-outline-warning text-dark">Manage All Requests</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Requester</th>
                            <th>Blood Group</th>
                            <th>Qty Needed</th>
                            <th>Hospital & City</th>
                            <th>Phone</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingReqs)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-3">No pending blood requests.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pendingReqs as $pr): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($pr['requester_name']) ?></strong></td>
                                    <td><span class="badge bg-danger fs-6"><?= htmlspecialchars($pr['blood_group']) ?></span></td>
                                    <td><?= (int)$pr['quantity'] ?> Unit(s)</td>
                                    <td><?= htmlspecialchars($pr['hospital']) ?>, <?= htmlspecialchars($pr['city']) ?></td>
                                    <td><?= htmlspecialchars($pr['phone']) ?></td>
                                    <td><?= date('d M Y', strtotime($pr['request_date'])) ?></td>
                                    <td>
                                        <a href="requests.php?id=<?= $pr['id'] ?>&action=approve" class="btn btn-sm btn-success">Approve</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
