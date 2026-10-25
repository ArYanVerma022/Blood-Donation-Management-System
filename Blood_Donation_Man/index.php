<?php
/**
 * JeevanRakt - Home Page
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'JeevanRakt - Blood Donor Management System';
require_once __DIR__ . '/includes/header.php';

// Fetch live system statistics
try {
    $stmtDonors = $pdo->query("SELECT COUNT(*) FROM donors WHERE status = 'Available'");
    $totalDonors = $stmtDonors->fetchColumn();

    $stmtDonations = $pdo->query("SELECT SUM(quantity) FROM donations");
    $totalDonations = $stmtDonations->fetchColumn() ?: 0;

    $stmtStock = $pdo->query("SELECT SUM(quantity) FROM blood_stock");
    $totalStock = $stmtStock->fetchColumn() ?: 0;

    $stmtRequests = $pdo->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'Pending'");
    $pendingRequests = $stmtRequests->fetchColumn();

    // Fetch blood group stock levels
    $stmtGroupStock = $pdo->query("SELECT blood_group, quantity FROM blood_stock ORDER BY blood_group ASC");
    $bloodGroupStock = $stmtGroupStock->fetchAll();
} catch (PDOException $e) {
    $totalDonors = $totalDonations = $totalStock = $pendingRequests = 0;
    $bloodGroupStock = [];
}
?>

<!-- Hero Section -->
<div class="hero-section text-center text-md-start mb-5 rounded-3 px-4 py-5 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <span class="badge bg-danger mb-2 px-3 py-2 text-uppercase tracking-wider">
                <i class="fa-solid fa-heart-pulse me-1"></i> Life Saving Initiative
            </span>
            <h1 class="display-5 fw-bold text-dark mb-3">
                🩸 Donate Blood, <span class="text-blood-red">Save Lives</span>
            </h1>
            <p class="lead text-secondary mb-4">
                JeevanRakt helps connect blood donors with people who need blood while maintaining basic donor and blood-stock records.
            </p>
            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                <a href="search.php" class="btn btn-blood btn-lg shadow-sm">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Find a Donor
                </a>
                <a href="register.php" class="btn btn-outline-blood btn-lg">
                    <i class="fa-solid fa-user-plus me-1"></i> Register as Donor
                </a>
                <a href="request-blood.php" class="btn btn-secondary btn-lg rounded-pill px-4">
                    <i class="fa-solid fa-hand-holding-medical me-1"></i> Request Blood
                </a>
            </div>
        </div>
        <div class="col-lg-5 text-center mt-4 mt-lg-0">
            <img src="images/hero-illustration.svg" alt="Blood Donation Hero" class="img-fluid rounded-3" style="max-height: 280px;">
        </div>
    </div>
</div>

<!-- Database Stats Section -->
<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-donors text-center">
            <div class="fs-1 fw-bold text-primary mb-1"><?= number_format($totalDonors) ?></div>
            <div class="text-uppercase text-muted fw-semibold small">Available Donors</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-donations text-center">
            <div class="fs-1 fw-bold text-success mb-1"><?= number_format($totalDonations) ?></div>
            <div class="text-uppercase text-muted fw-semibold small">Donations Recorded</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-stock text-center">
            <div class="fs-1 fw-bold text-danger mb-1"><?= number_format($totalStock) ?></div>
            <div class="text-uppercase text-muted fw-semibold small">Available Blood Units</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-requests text-center">
            <div class="fs-1 fw-bold text-warning mb-1"><?= number_format($pendingRequests) ?></div>
            <div class="text-uppercase text-muted fw-semibold small">Pending Requests</div>
        </div>
    </div>
</div>

<!-- Blood Group Availability Quick Grid -->
<div class="light-blue-section mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-vials text-blood-red me-2"></i> Live Blood Group Availability
            </h3>
            <p class="text-muted small mb-0">Current real-time stock units available in JeevanRakt database.</p>
        </div>
        <a href="blood-stock.php" class="btn btn-sm btn-outline-primary fw-semibold">View Full Details &rarr;</a>
    </div>

    <div class="row g-3">
        <?php foreach ($bloodGroupStock as $stock): ?>
            <?php 
                $isLow = $stock['quantity'] <= 3;
                $bgClass = $isLow ? 'border-warning bg-warning-subtle' : 'border-light bg-white';
            ?>
            <div class="col-6 col-sm-4 col-md-3">
                <div class="card <?= $bgClass ?> text-center p-3 blood-card h-100">
                    <div class="mx-auto mb-2 blood-group-badge">
                        <?= htmlspecialchars($stock['blood_group']) ?>
                    </div>
                    <div class="fs-5 fw-bold text-dark mb-0">
                        <?= htmlspecialchars($stock['quantity']) ?> <small class="fs-6 text-muted">Units</small>
                    </div>
                    <?php if ($isLow): ?>
                        <span class="badge bg-warning text-dark mt-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> Low Stock
                        </span>
                    <?php else: ?>
                        <span class="badge bg-success-subtle text-success mt-2">Available</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Information Section: Why Donate & Eligibility -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm p-4">
            <h4 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-circle-question text-primary me-2"></i> Why Donate Blood?
            </h4>
            <ul class="list-unstyled space-y-2 text-secondary">
                <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i> <strong>Save Up to 3 Lives:</strong> A single donation can help trauma patients, surgical patients, and anemia treatments.</li>
                <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i> <strong>Cardiovascular Health:</strong> Regular donation helps regulate iron levels in the bloodstream.</li>
                <li class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i> <strong>Free Health Screening:</strong> Pulse, blood pressure, and hemoglobin levels are checked prior to donation.</li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm p-4">
            <h4 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-user-shield text-blood-red me-2"></i> Basic Donor Guidelines
            </h4>
            <ul class="list-unstyled space-y-2 text-secondary">
                <li class="mb-2"><i class="fa-solid fa-angle-right text-danger me-2"></i> <strong>Age & Weight:</strong> Between 18–65 years of age with a minimum weight of 45 kg.</li>
                <li class="mb-2"><i class="fa-solid fa-angle-right text-danger me-2"></i> <strong>Frequency:</strong> Minimum gap of 3 months (90 days) between blood donations.</li>
                <li class="mb-2"><i class="fa-solid fa-angle-right text-danger me-2"></i> <strong>Pre-Donation Check:</strong> Stay well hydrated, eat a nutritious meal, and complete our screening questionnaire.</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
