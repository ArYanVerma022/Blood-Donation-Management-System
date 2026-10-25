<?php
/**
 * JeevanRakt - Contact Page
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'Contact Us - JeevanRakt';
require_once __DIR__ . '/includes/header.php';

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c_name = sanitize_input($_POST['name'] ?? '');
    $c_email = sanitize_input($_POST['email'] ?? '');
    $c_message = sanitize_input($_POST['message'] ?? '');

    if (!empty($c_name) && !empty($c_email) && !empty($c_message)) {
        $successMsg = "Thank you $c_name! Your message has been received. Our team will get back to you shortly.";
    }
}
?>

<div class="row g-4 justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100 p-4">
            <h4 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-phone-volume text-blood-red me-2"></i> Blood Helpline & Contact
            </h4>
            <p class="text-secondary mb-4">
                Have questions about blood donation or need help registering as a donor? Get in touch with our team.
            </p>

            <div class="mb-3 d-flex align-items-start">
                <div class="bg-danger text-white rounded-circle p-2 me-3 fs-5" style="width: 40px; height: 40px; text-align: center;">
                    <i class="fa-solid fa-hospital"></i>
                </div>
                <div>
                    <strong class="d-block text-dark">JeevanRakt Blood Bank Center</strong>
                    <span class="text-muted small">Campus Health Center, College Campus</span>
                </div>
            </div>

            <div class="mb-3 d-flex align-items-start">
                <div class="bg-primary text-white rounded-circle p-2 me-3 fs-5" style="width: 40px; height: 40px; text-align: center;">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <strong class="d-block text-dark">24/7 Emergency Helpline</strong>
                    <span class="text-muted small">+91 98765 43210 / 1800-123-BLOOD</span>
                </div>
            </div>

            <div class="mb-4 d-flex align-items-start">
                <div class="bg-success text-white rounded-circle p-2 me-3 fs-5" style="width: 40px; height: 40px; text-align: center;">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <strong class="d-block text-dark">Project Email</strong>
                    <span class="text-muted small">support@jeevanrakt.org</span>
                </div>
            </div>

            <div class="alert alert-light border small mt-auto">
                <strong>Student Project Developer:</strong><br>
                Aryan Verma (Student ID: 241306064)
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4">
            <h4 class="fw-bold text-dark mb-3">Send Us a Message</h4>

            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Your Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Full Name">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label fw-semibold">Message / Inquiry</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Write your message here..."></textarea>
                </div>

                <button type="submit" class="btn btn-blood w-100 py-2">
                    <i class="fa-solid fa-paper-plane me-1"></i> Send Inquiry
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
