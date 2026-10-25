<?php
/**
 * JeevanRakt - Admin Manage Blood Stock
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Manage Blood Stock - Admin JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Handle Direct Stock Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $blood_group = sanitize_input($_POST['blood_group'] ?? '');
    $new_quantity = (int)($_POST['quantity'] ?? 0);

    if ($new_quantity < 0) {
        set_flash_message('danger', 'Stock quantity cannot be negative.');
    } else {
        try {
            $stmtUpd = $pdo->prepare("UPDATE blood_stock SET quantity = ? WHERE blood_group = ?");
            $stmtUpd->execute([$new_quantity, $blood_group]);
            set_flash_message('success', "Blood stock for $blood_group updated to $new_quantity units.");
        } catch (PDOException $e) {
            set_flash_message('danger', 'Error updating stock: ' . $e->getMessage());
        }
    }
    header('Location: blood-stock.php');
    exit;
}

// Fetch Inventory List
try {
    $stmt = $pdo->query("SELECT * FROM blood_stock ORDER BY blood_group ASC");
    $stockList = $stmt->fetchAll();
} catch (PDOException $e) {
    $stockList = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-vials text-danger me-2"></i> Manage Blood Stock Inventory
        </h2>
        <p class="text-muted mb-0">View real-time units and manually update stock levels when required.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">Current Blood Inventory Status</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Current Quantity</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Update Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockList as $st): ?>
                                <?php $isLow = $st['quantity'] <= 4; ?>
                                <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                                    <td>
                                        <span class="badge bg-danger fs-6 px-3 py-2"><?= htmlspecialchars($st['blood_group']) ?></span>
                                    </td>
                                    <td>
                                        <strong class="fs-5 text-dark"><?= (int)$st['quantity'] ?></strong> <small class="text-muted">Units</small>
                                    </td>
                                    <td>
                                        <?php if ($isLow): ?>
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Sufficient</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($st['updated_at'])) ?></td>
                                    <td>
                                        <!-- Inline Quick Update Form -->
                                        <form action="blood-stock.php" method="POST" class="d-flex gap-2 align-items-center">
                                            <input type="hidden" name="update_stock" value="1">
                                            <input type="hidden" name="blood_group" value="<?= $st['blood_group'] ?>">
                                            <input type="number" name="quantity" class="form-control form-control-sm" value="<?= (int)$st['quantity'] ?>" min="0" max="999" style="width: 75px;">
                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </form>
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
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-cat text-primary me-2"></i> Inventory Threshold Policy</h5>
            <p class="small text-muted mb-3">
                Blood groups with <strong>4 units or fewer</strong> are automatically flagged as <strong>Low Stock</strong> across the entire application and administrative dashboard.
            </p>
            <hr>
            <div class="alert alert-info small mb-0">
                <i class="fa-solid fa-lightbulb me-1"></i> <strong>Tip:</strong> Stock automatically increases when you add a new donor donation in <a href="donations.php">Manage Donations</a>, and automatically decreases when you complete a blood request in <a href="requests.php">Manage Requests</a>.
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
