<?php
/**
 * JeevanRakt - Pre-Donation Health Screening Questionnaire
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Pre-Donation Health Screening - JeevanRakt';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/screening_engine.php';

$donor_id = isset($_GET['donor_id']) ? (int)$_GET['donor_id'] : 0;

// Fetch list of donors for selection if not pre-selected
$donorsList = [];
try {
    $stmtDonors = $pdo->query("SELECT id, name, blood_group, phone FROM donors ORDER BY id DESC LIMIT 50");
    $donorsList = $stmtDonors->fetchAll();
} catch (PDOException $e) {
    $donorsList = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_id = (int)($_POST['donor_id'] ?? 0);
    
    if ($donor_id <= 0) {
        $error = "Please select a registered donor before proceeding with screening.";
    } else {
        // Collect responses
        $formData = [
            'unwell' => $_POST['unwell'] ?? 'no',
            'fever' => $_POST['fever'] ?? 'no',
            'cold_flu' => $_POST['cold_flu'] ?? 'no',
            'infection' => $_POST['infection'] ?? 'no',
            'antibiotics' => $_POST['antibiotics'] ?? 'no',
            'hiv_history' => $_POST['hiv_history'] ?? 'no',
            'hepatitis_history' => $_POST['hepatitis_history'] ?? 'no',
            'bleeding_disorder' => $_POST['bleeding_disorder'] ?? 'no',
            'heart_condition' => $_POST['heart_condition'] ?? 'no',
            'diabetes' => $_POST['diabetes'] ?? 'no',
            'medical_condition' => $_POST['medical_condition'] ?? 'no',
            'medication' => $_POST['medication'] ?? 'no',
            'blood_thinner' => $_POST['blood_thinner'] ?? 'no',
            'hiv_meds' => $_POST['hiv_meds'] ?? 'no',
            'smoking' => $_POST['smoking'] ?? 'no',
            'tobacco' => $_POST['tobacco'] ?? 'no',
            'alcohol' => $_POST['alcohol'] ?? 'no',
            'recent_surgery' => $_POST['recent_surgery'] ?? 'no',
            'dental_procedure' => $_POST['dental_procedure'] ?? 'no',
            'tattoo' => $_POST['tattoo'] ?? 'no',
            'piercing' => $_POST['piercing'] ?? 'no',
            'blood_transfusion' => $_POST['blood_transfusion'] ?? 'no',
            'travel_history' => $_POST['travel_history'] ?? 'no',
            'infection_exposure' => $_POST['infection_exposure'] ?? 'no',
        ];

        // Process through Screening Engine
        $evalResult = evaluate_health_screening($formData);
        $resultText = $evalResult['result'];
        $reasonText = implode("\n", $evalResult['reasons']);

        try {
            $stmtInsert = $pdo->prepare("INSERT INTO donor_screening 
                (donor_id, unwell, fever, cold_flu, infection, antibiotics, hiv_history, hepatitis_history, bleeding_disorder, heart_condition, diabetes, medical_condition, medication, blood_thinner, hiv_meds, smoking, tobacco, alcohol, recent_surgery, dental_procedure, tattoo, piercing, blood_transfusion, travel_history, infection_exposure, screening_result, deferral_reason) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmtInsert->execute([
                $donor_id,
                $formData['unwell'], $formData['fever'], $formData['cold_flu'], $formData['infection'], $formData['antibiotics'],
                $formData['hiv_history'], $formData['hepatitis_history'], $formData['bleeding_disorder'], $formData['heart_condition'], $formData['diabetes'], $formData['medical_condition'],
                $formData['medication'], $formData['blood_thinner'], $formData['hiv_meds'],
                $formData['smoking'], $formData['tobacco'], $formData['alcohol'],
                $formData['recent_surgery'], $formData['dental_procedure'], $formData['tattoo'], $formData['piercing'], $formData['blood_transfusion'],
                $formData['travel_history'], $formData['infection_exposure'],
                $resultText, $reasonText
            ]);

            $screeningId = $pdo->lastInsertId();
            header("Location: screening-result.php?id=" . $screeningId);
            exit;
        } catch (PDOException $e) {
            $error = "Database Error while saving screening record: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center mb-4">
    <div class="col-lg-10">
        <!-- Safety & Disclaimer Banner -->
        <div class="alert alert-info border-info shadow-sm d-flex align-items-center mb-4">
            <i class="fa-solid fa-user-doctor fs-2 text-info me-3"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">🧑‍⚕️ Pre-Donation Health Screening</h5>
                <p class="mb-0 small">
                    This screening questionnaire provides a preliminary evaluation for donor safety.
                    <strong>Notice:</strong> This system does NOT replace a doctor or qualified blood-bank professional. Final eligibility must always be confirmed by donation center medical staff.
                </p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-clipboard-question me-2"></i> Donor Health Questionnaire
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="screening.php" method="POST">
                    
                    <!-- Select Donor -->
                    <div class="card bg-light border-0 p-3 mb-4">
                        <label for="donor_id" class="form-label fw-bold fs-6">
                            Select Donor <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-lg" id="donor_id" name="donor_id" required>
                            <option value="">-- Choose Registered Donor --</option>
                            <?php foreach ($donorsList as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= ($donor_id == $d['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['name']) ?> (Blood Group: <?= htmlspecialchars($d['blood_group']) ?> | Phone: <?= htmlspecialchars($d['phone']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted mt-1">Not registered yet? <a href="register.php">Click here to register first.</a></small>
                    </div>

                    <!-- Question Group A: Current Health -->
                    <div class="card mb-4 border border-light-subtle">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="fa-solid fa-temperature-three-quarters text-danger me-2"></i> Section A: Current Health & Symptoms
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">1. Are you currently feeling unwell?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="unwell" id="unwell_no" value="no" checked>
                                            <label class="form-check-label" for="unwell_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="unwell" id="unwell_yes" value="yes">
                                            <label class="form-check-label text-danger" for="unwell_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">2. Do you currently have fever?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fever" id="fever_no" value="no" checked>
                                            <label class="form-check-label" for="fever_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fever" id="fever_yes" value="yes">
                                            <label class="form-check-label text-danger" for="fever_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">3. Do you currently have a cold or flu-like illness?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cold_flu" id="cold_no" value="no" checked>
                                            <label class="form-check-label" for="cold_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cold_flu" id="cold_yes" value="yes">
                                            <label class="form-check-label text-danger" for="cold_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">4. Are you taking antibiotics for an infection?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="antibiotics" id="antibiotics_no" value="no" checked>
                                            <label class="form-check-label" for="antibiotics_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="antibiotics" id="antibiotics_yes" value="yes">
                                            <label class="form-check-label text-danger" for="antibiotics_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Group B: Medical History -->
                    <div class="card mb-4 border border-light-subtle">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="fa-solid fa-notes-medical text-primary me-2"></i> Section B: Medical History & Chronic Conditions
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">1. History of HIV / AIDS?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hiv_history" id="hiv_no" value="no" checked>
                                            <label class="form-check-label" for="hiv_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hiv_history" id="hiv_yes" value="yes">
                                            <label class="form-check-label text-warning" for="hiv_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">2. History of Hepatitis B or Hepatitis C?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hepatitis_history" id="hep_no" value="no" checked>
                                            <label class="form-check-label" for="hep_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hepatitis_history" id="hep_yes" value="yes">
                                            <label class="form-check-label text-warning" for="hep_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">3. Bleeding or clotting disorder?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bleeding_disorder" id="bleed_no" value="no" checked>
                                            <label class="form-check-label" for="bleed_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bleeding_disorder" id="bleed_yes" value="yes">
                                            <label class="form-check-label text-warning" for="bleed_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">4. Heart condition or uncontrolled diabetes?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="heart_condition" id="heart_no" value="no" checked>
                                            <label class="form-check-label" for="heart_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="heart_condition" id="heart_yes" value="yes">
                                            <label class="form-check-label text-warning" for="heart_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Group C: Alcohol & Substance -->
                    <div class="card mb-4 border border-light-subtle">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="fa-solid fa-wine-glass text-warning me-2"></i> Section C: Substance & Alcohol Screening
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">1. Consumed alcohol in the last 24 hours / under influence?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="alcohol" id="alc_no" value="no" checked>
                                            <label class="form-check-label" for="alc_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="alcohol" id="alc_yes" value="yes">
                                            <label class="form-check-label text-danger" for="alc_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">2. Do you currently smoke or use tobacco products?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="smoking" id="smoke_no" value="no" checked>
                                            <label class="form-check-label" for="smoke_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="smoking" id="smoke_yes" value="yes">
                                            <label class="form-check-label text-secondary" for="smoke_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Group D: Recent Procedures & Travel -->
                    <div class="card mb-4 border border-light-subtle">
                        <div class="card-header bg-light fw-bold text-dark">
                            <i class="fa-solid fa-syringe text-info me-2"></i> Section D: Recent Procedures & Travel
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">1. Recent surgery, tattoo, or piercing (last 6 months)?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tattoo" id="tat_no" value="no" checked>
                                            <label class="form-check-label" for="tat_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tattoo" id="tat_yes" value="yes">
                                            <label class="form-check-label text-warning" for="tat_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">2. Recent blood transfusion or major travel?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="travel_history" id="trv_no" value="no" checked>
                                            <label class="form-check-label" for="trv_no">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="travel_history" id="trv_yes" value="yes">
                                            <label class="form-check-label text-warning" for="trv_yes">Yes</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-blood btn-lg px-5">
                            <i class="fa-solid fa-calculator me-2"></i> Calculate Preliminary Screening Result
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
