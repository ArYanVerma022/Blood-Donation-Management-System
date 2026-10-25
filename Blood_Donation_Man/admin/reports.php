<?php
/**
 * JeevanRakt - Admin Reports & Summary Statistics
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Reports & Summary - Admin JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Gather statistics for reporting
try {
    // 1. Blood group metrics breakdown
    $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    $groupData = [];

    foreach ($bloodGroups as $bg) {
        $stmtD = $pdo->prepare("SELECT COUNT(*) FROM donors WHERE blood_group = ?");
        $stmtD->execute([$bg]);
        $donorCnt = $stmtD->fetchColumn();

        $stmtDn = $pdo->prepare("SELECT SUM(quantity) FROM donations WHERE blood_group = ?");
        $stmtDn->execute([$bg]);
        $donationsCnt = $stmtDn->fetchColumn() ?: 0;

        $stmtSt = $pdo->prepare("SELECT quantity FROM blood_stock WHERE blood_group = ?");
        $stmtSt->execute([$bg]);
        $stockCnt = $stmtSt->fetchColumn() ?: 0;

        $stmtReq = $pdo->prepare("SELECT COUNT(*) FROM blood_requests WHERE blood_group = ? AND status = 'Pending'");
        $stmtReq->execute([$bg]);
        $reqCnt = $stmtReq->fetchColumn();

        $groupData[] = [
            'blood_group' => $bg,
            'donors' => $donorCnt,
            'donations' => $donationsCnt,
            'stock' => $stockCnt,
            'pending_req' => $reqCnt
        ];
    }

    // Totals
    $totalDonors = array_sum(array_column($groupData, 'donors'));
    $totalDonations = array_sum(array_column($groupData, 'donations'));
    $totalStock = array_sum(array_column($groupData, 'stock'));
    $totalRequests = array_sum(array_column($groupData, 'pending_req'));

} catch (PDOException $e) {
    die("Database report error: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-chart-column text-primary me-2"></i> System Reports & Analytics
        </h2>
        <p class="text-muted mb-0">Comprehensive summary of blood group stock, donor registrations, and requests.</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm btn-print-hide">
        <i class="fa-solid fa-print me-1"></i> Print Report
    </button>
</div>

<!-- Print Report Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-primary mb-1">🩸 JeevanRakt Management Report</h4>
                <p class="mb-0 text-muted small">Generated on: <?= date('d F Y, h:i A') ?></p>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Developer Attribution</small>
                <strong>Aryan Verma (241306064)</strong>
            </div>
        </div>

        <!-- System Key Totals -->
        <div class="row g-3 my-2 text-center">
            <div class="col-3">
                <div class="p-3 bg-light rounded border">
                    <small class="text-uppercase text-muted fw-bold">Total Donors</small>
                    <div class="fs-3 fw-bold text-primary"><?= number_format($totalDonors) ?></div>
                </div>
            </div>
            <div class="col-3">
                <div class="p-3 bg-light rounded border">
                    <small class="text-uppercase text-muted fw-bold">Total Donations</small>
                    <div class="fs-3 fw-bold text-success"><?= number_format($totalDonations) ?></div>
                </div>
            </div>
            <div class="col-3">
                <div class="p-3 bg-light rounded border">
                    <small class="text-uppercase text-muted fw-bold">Current Stock</small>
                    <div class="fs-3 fw-bold text-danger"><?= number_format($totalStock) ?></div>
                </div>
            </div>
            <div class="col-3">
                <div class="p-3 bg-light rounded border">
                    <small class="text-uppercase text-muted fw-bold">Pending Requests</small>
                    <div class="fs-3 fw-bold text-warning"><?= number_format($totalRequests) ?></div>
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown Matrix -->
        <h5 class="fw-bold text-dark mt-4 mb-3">Blood Group Distribution Matrix</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle custom-table">
                <thead>
                    <tr class="table-dark">
                        <th>Blood Group</th>
                        <th>Registered Donors</th>
                        <th>Donations Recorded</th>
                        <th>Current Available Units</th>
                        <th>Pending Patient Requests</th>
                        <th>Stock Alert</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupData as $row): ?>
                        <tr>
                            <td class="fw-bold text-center fs-5">
                                <span class="badge bg-danger px-3 py-2"><?= $row['blood_group'] ?></span>
                            </td>
                            <td class="text-center fw-bold"><?= $row['donors'] ?></td>
                            <td class="text-center fw-bold"><?= $row['donations'] ?> Units</td>
                            <td class="text-center fw-bold fs-5 text-dark"><?= $row['stock'] ?> Units</td>
                            <td class="text-center fw-bold"><?= $row['pending_req'] ?></td>
                            <td class="text-center">
                                <?php if ($row['stock'] <= 4): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Adequate</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
