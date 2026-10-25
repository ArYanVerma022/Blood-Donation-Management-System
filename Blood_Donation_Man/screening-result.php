<?php
/**
 * JeevanRakt - Preliminary Screening Result Display
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Screening Result - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<div class='alert alert-danger'>Invalid screening record requested.</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT s.*, d.name AS donor_name, d.blood_group, d.phone, d.city 
                           FROM donor_screening s 
                           JOIN donors d ON s.donor_id = d.id 
                           WHERE s.id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();

    if (!$record) {
        echo "<div class='alert alert-danger'>Screening record not found.</div>";
        require_once __DIR__ . '/includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error retrieving record: " . htmlspecialchars($e->getMessage()) . "</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$result = $record['screening_result'];
$reasons = explode("\n", $record['deferral_reason']);

// Styling & Badges based on result
if ($result === 'Proceed to Screening') {
    $cardHeaderBg = 'bg-success text-white';
    $icon = '🟢';
    $statusText = 'Proceed to Screening';
    $alertClass = 'alert-success';
    $mainMessage = 'You may proceed to the next stage of blood-bank screening. Final eligibility will be determined by qualified staff.';
} elseif ($result === 'Medical Review Required') {
    $cardHeaderBg = 'bg-warning text-dark';
    $icon = '🟡';
    $statusText = 'Medical Review Required';
    $alertClass = 'alert-warning';
    $mainMessage = 'Your answers require additional medical review. Please discuss them with blood-bank screening staff before donating.';
} else {
    $cardHeaderBg = 'bg-danger text-white';
    $icon = '🔴';
    $statusText = 'Temporarily Deferred';
    $alertClass = 'alert-danger';
    $mainMessage = 'You should not proceed with blood donation at this time based on the health questionnaire answers provided.';
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow">
            <div class="card-header <?= $cardHeaderBg ?> py-3 text-center">
                <div class="display-6 fw-bold"><?= $icon ?> <?= htmlspecialchars($statusText) ?></div>
                <div class="small opacity-85">Preliminary Pre-Donation Health Screening Result</div>
            </div>
            <div class="card-body p-4">

                <!-- Donor Meta Summary -->
                <div class="row bg-light rounded-3 p-3 mb-4 border">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <small class="text-muted d-block">Donor Name</small>
                        <strong class="fs-5"><?= htmlspecialchars($record['donor_name']) ?></strong>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <small class="text-muted d-block">Blood Group</small>
                        <span class="badge bg-danger fs-6"><?= htmlspecialchars($record['blood_group']) ?></span>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <small class="text-muted d-block">Screening Date</small>
                        <strong><?= date('d M Y, h:i A', strtotime($record['screening_date'])) ?></strong>
                    </div>
                </div>

                <!-- Primary Result Box -->
                <div class="alert <?= $alertClass ?> p-3 mb-4">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-circle-info me-2"></i> Preliminary Status Assessment</h5>
                    <p class="mb-0 fs-6"><?= htmlspecialchars($mainMessage) ?></p>
                </div>

                <!-- Reasons Breakdown -->
                <div class="mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2">
                        <i class="fa-solid fa-list-check me-2 text-primary"></i> Detailed Screening Notes & Reason(s):
                    </h5>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($reasons as $reason): ?>
                            <?php if (trim($reason) !== ''): ?>
                                <li class="list-group-item d-flex align-items-start px-0 py-2">
                                    <i class="fa-solid fa-angle-right text-muted me-2 mt-1"></i>
                                    <div><?= htmlspecialchars($reason) ?></div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Medical Disclaimer -->
                <div class="alert alert-secondary small mb-4">
                    <i class="fa-solid fa-shield-halved me-1"></i> <strong>Important Medical Disclaimer:</strong> 
                    This screening is an automated preliminary check based on user-entered questionnaire responses. It does not constitute a final medical diagnosis or doctor consultation. Final donation approval rests solely with trained medical personnel at the donation facility.
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center btn-print-hide">
                    <button onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-print me-1"></i> Print Screening Result
                    </button>
                    <div class="d-flex gap-2">
                        <a href="search.php" class="btn btn-outline-primary">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Search Donors
                        </a>
                        <a href="register.php" class="btn btn-blood">
                            <i class="fa-solid fa-plus me-1"></i> Register Another Donor
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
