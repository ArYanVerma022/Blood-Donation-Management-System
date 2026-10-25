<?php
/**
 * JeevanRakt - Admin Manage Blood Requests
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Manage Blood Requests - Admin JeevanRakt';
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Handle Status Updates
if (isset($_GET['id']) && isset($_GET['action'])) {
    $req_id = (int)$_GET['id'];
    $action = sanitize_input($_GET['action']);

    $statusMap = [
        'approve' => 'Approved',
        'reject' => 'Rejected',
        'complete' => 'Completed',
        'pending' => 'Pending'
    ];

    if (array_key_exists($action, $statusMap)) {
        $new_status = $statusMap[$action];

        try {
            // Fetch request record details
            $stmtFind = $pdo->prepare("SELECT * FROM blood_requests WHERE id = ?");
            $stmtFind->execute([$req_id]);
            $req = $stmtFind->fetch();

            if ($req) {
                // If completing request, verify and deduct blood stock quantity!
                if ($new_status === 'Completed' && $req['status'] !== 'Completed') {
                    $stmtStock = $pdo->prepare("SELECT quantity FROM blood_stock WHERE blood_group = ?");
                    $stmtStock->execute([$req['blood_group']]);
                    $availUnits = (int)$stmtStock->fetchColumn();

                    if ($availUnits < $req['quantity']) {
                        set_flash_message('danger', "Cannot complete request! Available stock for {$req['blood_group']} ($availUnits units) is less than requested quantity ({$req['quantity']} units).");
                        header('Location: requests.php');
                        exit;
                    }

                    $pdo->beginTransaction();

                    // Deduct inventory
                    $stmtDeduct = $pdo->prepare("UPDATE blood_stock SET quantity = quantity - ? WHERE blood_group = ?");
                    $stmtDeduct->execute([$req['quantity'], $req['blood_group']]);

                    // Update request status
                    $stmtUpd = $pdo->prepare("UPDATE blood_requests SET status = ? WHERE id = ?");
                    $stmtUpd->execute([$new_status, $req_id]);

                    $pdo->commit();
                    set_flash_message('success', "Blood Request #$req_id marked as Completed. Deducted {$req['quantity']} unit(s) of {$req['blood_group']} from stock.");
                } else {
                    $stmtUpd = $pdo->prepare("UPDATE blood_requests SET status = ? WHERE id = ?");
                    $stmtUpd->execute([$new_status, $req_id]);
                    set_flash_message('success', "Blood Request #$req_id updated to '$new_status'.");
                }
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            set_flash_message('danger', 'Error updating request: ' . $e->getMessage());
        }
    }
    header('Location: requests.php');
    exit;
}

// Handle Direct Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmtDel = $pdo->prepare("DELETE FROM blood_requests WHERE id = ?");
        $stmtDel->execute([$id]);
        set_flash_message('success', 'Blood Request record deleted.');
    } catch (PDOException $e) {
        set_flash_message('danger', 'Error deleting request: ' . $e->getMessage());
    }
    header('Location: requests.php');
    exit;
}

// Fetch Requests
$status_filter = sanitize_input($_GET['status'] ?? '');
$sql = "SELECT * FROM blood_requests WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $requestsList = $stmt->fetchAll();
} catch (PDOException $e) {
    $requestsList = [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-notes-medical text-warning me-2"></i> Manage Patient Blood Requests
        </h2>
        <p class="text-muted mb-0">Review emergency blood requests, approve orders, and complete fulfillment.</p>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2 bg-white">
        <div class="d-flex flex-wrap gap-2">
            <a href="requests.php" class="btn btn-sm <?= empty($status_filter) ? 'btn-primary' : 'btn-outline-secondary' ?>">All Requests</a>
            <a href="requests.php?status=Pending" class="btn btn-sm <?= ($status_filter === 'Pending') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' ?>">Pending</a>
            <a href="requests.php?status=Approved" class="btn btn-sm <?= ($status_filter === 'Approved') ? 'btn-success' : 'btn-outline-success' ?>">Approved</a>
            <a href="requests.php?status=Completed" class="btn btn-sm <?= ($status_filter === 'Completed') ? 'btn-info text-white' : 'btn-outline-info text-dark' ?>">Completed</a>
            <a href="requests.php?status=Rejected" class="btn btn-sm <?= ($status_filter === 'Rejected') ? 'btn-danger' : 'btn-outline-danger' ?>">Rejected</a>
        </div>
    </div>
</div>

<!-- Requests Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead>
                    <tr>
                        <th>Req ID</th>
                        <th>Requester</th>
                        <th>Group Needed</th>
                        <th>Qty</th>
                        <th>Hospital & Location</th>
                        <th>Urgency Reason</th>
                        <th>Req Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requestsList)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No blood request records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($requestsList as $r): ?>
                            <tr>
                                <td>#<?= $r['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($r['requester_name']) ?></strong><br>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($r['phone']) ?></small>
                                </td>
                                <td><span class="badge bg-danger fs-6"><?= htmlspecialchars($r['blood_group']) ?></span></td>
                                <td class="fw-bold"><?= (int)$r['quantity'] ?> Unit(s)</td>
                                <td>
                                    <strong><?= htmlspecialchars($r['hospital']) ?></strong><br>
                                    <span class="small text-muted"><?= htmlspecialchars($r['city']) ?></span>
                                </td>
                                <td class="small text-secondary" style="max-width: 250px;">
                                    <?= htmlspecialchars($r['reason']) ?>
                                </td>
                                <td class="small"><?= date('d M Y', strtotime($r['request_date'])) ?></td>
                                <td>
                                    <?php 
                                        $st = $r['status'];
                                        if ($st === 'Pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                                        elseif ($st === 'Approved') echo '<span class="badge bg-success">Approved</span>';
                                        elseif ($st === 'Completed') echo '<span class="badge bg-info text-white">Completed</span>';
                                        else echo '<span class="badge bg-danger">Rejected</span>';
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if ($r['status'] === 'Pending'): ?>
                                            <a href="requests.php?id=<?= $r['id'] ?>&action=approve" class="btn btn-success" title="Approve Request">Approve</a>
                                            <a href="requests.php?id=<?= $r['id'] ?>&action=reject" class="btn btn-outline-danger" title="Reject Request">Reject</a>
                                        <?php elseif ($r['status'] === 'Approved'): ?>
                                            <a href="requests.php?id=<?= $r['id'] ?>&action=complete" class="btn btn-info text-white" title="Mark Completed & Deduct Stock">Complete</a>
                                        <?php endif; ?>
                                        <a href="requests.php?id=<?= $r['id'] ?>&action=delete" class="btn btn-outline-secondary btn-delete-confirm" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                    </div>
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
