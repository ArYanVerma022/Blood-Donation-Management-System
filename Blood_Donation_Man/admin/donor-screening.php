<?php
/**
 * JeevanRakt - Admin Donor Health Screening Logs
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Donor Screening Records - Admin JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

$filter_donor_id = isset($_GET['donor_id']) ? (int)$_GET['donor_id'] : 0;

$sql = "SELECT s.*, d.name AS donor_name, d.blood_group, d.phone, d.city 
        FROM donor_screening s 
        JOIN donors d ON s.donor_id = d.id";
$params = [];

if ($filter_donor_id > 0) {
    $sql .= " WHERE s.donor_id = ?";
    $params[] = $filter_donor_id;
}

$sql .= " ORDER BY s.id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $screenings = $stmt->fetchAll();
} catch (PDOException $e) {
    $screenings = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-clipboard-check text-info me-2"></i> Donor Health Screening Log Records
        </h2>
        <p class="text-muted mb-0">Pre-donation health questionnaire responses and calculated screening results.</p>
    </div>
    <a href="donors.php" class="btn btn-outline-secondary btn-sm">
        &larr; Back to Donors List
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead>
                    <tr>
                        <th>Screening ID</th>
                        <th>Donor Name</th>
                        <th>Blood Group</th>
                        <th>Screening Result</th>
                        <th>Reasons / Notes</th>
                        <th>Date & Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($screenings)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No health screening records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($screenings as $sc): ?>
                            <tr>
                                <td>#<?= $sc['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($sc['donor_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($sc['city']) ?> | <?= htmlspecialchars($sc['phone']) ?></small>
                                </td>
                                <td><span class="badge bg-danger fs-6"><?= htmlspecialchars($sc['blood_group']) ?></span></td>
                                <td>
                                    <?php 
                                        $res = $sc['screening_result'];
                                        if ($res === 'Proceed to Screening') echo '<span class="badge bg-success">🟢 Proceed</span>';
                                        elseif ($res === 'Medical Review Required') echo '<span class="badge bg-warning text-dark">🟡 Medical Review</span>';
                                        else echo '<span class="badge bg-danger">🔴 Deferred</span>';
                                    ?>
                                </td>
                                <td class="small text-secondary" style="max-width: 300px;">
                                    <?= nl2br(htmlspecialchars($sc['deferral_reason'])) ?>
                                </td>
                                <td class="small"><?= date('d M Y, h:i A', strtotime($sc['screening_date'])) ?></td>
                                <td>
                                    <a href="../screening-result.php?id=<?= $sc['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-eye me-1"></i> View Result
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
