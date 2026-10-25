<?php
/**
 * JeevanRakt - Public Blood Stock & Availability Page
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Blood Stock Availability - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM blood_stock ORDER BY blood_group ASC");
    $stockList = $stmt->fetchAll();
} catch (PDOException $e) {
    $stockList = [];
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-vials text-blood-red me-2"></i> Blood Stock Availability
        </h2>
        <p class="text-muted">Real-time inventory levels of blood units across all standard blood groups.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="request-blood.php" class="btn btn-blood">
            <i class="fa-solid fa-hand-holding-medical me-1"></i> Submit Blood Request
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">Inventory Summary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Available Units</th>
                                <th>Stock Status</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockList as $stock): ?>
                                <?php $isLow = $stock['quantity'] <= 4; ?>
                                <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="blood-group-badge me-3" style="width: 42px; height: 42px; font-size: 1rem;">
                                                <?= htmlspecialchars($stock['blood_group']) ?>
                                            </div>
                                            <strong class="fs-5 text-dark"><?= htmlspecialchars($stock['blood_group']) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fs-4 fw-bold text-dark"><?= (int)$stock['quantity'] ?></span>
                                        <span class="text-muted small">Units (approx 350ml/unit)</span>
                                    </td>
                                    <td>
                                        <?php if ($isLow): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock (&le; 4 Units)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fa-solid fa-check-circle me-1"></i> Adequate Stock
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('d M Y, h:i A', strtotime($stock['updated_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Stock Information Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-circle-info text-info me-2"></i> Stock Management Policy
            </h5>
            <p class="text-secondary small mb-3">
                Blood stock units are automatically incremented whenever a successful donor donation is recorded by JeevanRakt administrators.
            </p>
            <hr>
            <h6 class="fw-bold text-dark">Emergency Blood Need?</h6>
            <p class="text-secondary small mb-3">
                If your required blood group shows low stock, you can register a formal patient request or search our available donor directory directly.
            </p>
            <div class="d-grid gap-2">
                <a href="request-blood.php" class="btn btn-outline-danger">Request Blood Unit</a>
                <a href="search.php" class="btn btn-outline-primary">Search Active Donors</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
