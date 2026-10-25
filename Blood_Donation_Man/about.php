<?php
/**
 * JeevanRakt - About Page
 * Developer: Aryan Verma (Student ID: 241306064)
 */
$pageTitle = 'About Us - JeevanRakt';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        
        <!-- Main Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3 text-center">
                <h3 class="mb-0 fw-bold">
                    <img src="images/blood-drop.svg" alt="Logo" width="28" height="32" class="me-2">
                    JeevanRakt – Blood Donor Management System
                </h3>
                <p class="mb-0 text-light opacity-85 small">College Web Development Project</p>
            </div>
            <div class="card-body p-4 p-md-5">

                <!-- Student Attribution Box -->
                <div class="light-blue-section border-primary p-4 mb-4 text-center text-md-start">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-user-graduate fs-1"></i>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4 class="fw-bold text-dark mb-1">Developer Credentials</h4>
                            <p class="mb-1"><strong>Student Name:</strong> Aryan Verma</p>
                            <p class="mb-1"><strong>Student ID:</strong> 241306064</p>
                            <p class="mb-0 text-muted"><strong>Course:</strong> BCA (Bachelor of Computer Applications)</p>
                        </div>
                    </div>
                </div>

                <h4 class="fw-bold text-dark mb-3">Project Objective</h4>
                <p class="text-secondary leading-relaxed">
                    <strong>JeevanRakt</strong> is a simple, functional, college-level web application designed to help small blood banks, non-profit organizations, and hospitals manage blood donors, donor health screening records, blood donations, real-time blood stock levels, and patient blood requests efficiently.
                </p>

                <h4 class="fw-bold text-dark mb-3 mt-4">Key System Features</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-white">
                            <h6 class="fw-bold text-primary"><i class="fa-solid fa-users me-2"></i> Donor Management</h6>
                            <p class="small text-muted mb-0">Register donors with complete personal details, location, blood group, availability status, and donation history.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-white">
                            <h6 class="fw-bold text-danger"><i class="fa-solid fa-heart-pulse me-2"></i> Pre-Donation Health Screening</h6>
                            <p class="small text-muted mb-0">Automated health questionnaire evaluating symptoms, medical history, procedures, and alcohol consumption to produce preliminary 🟢, 🟡, or 🔴 status results.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-white">
                            <h6 class="fw-bold text-success"><i class="fa-solid fa-boxes-stacked me-2"></i> Real-time Blood Inventory</h6>
                            <p class="small text-muted mb-0">Track blood unit quantities across all 8 major blood groups (A+, A-, B+, B-, AB+, AB-, O+, O-) with low-stock warnings.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-white">
                            <h6 class="fw-bold text-warning"><i class="fa-solid fa-hand-holding-hand me-2"></i> Blood Request System</h6>
                            <p class="small text-muted mb-0">Allow patients and hospitals to submit emergency blood requests and track fulfillment status.</p>
                        </div>
                    </div>
                </div>

                <h4 class="fw-bold text-dark mb-3">Technology Stack Used</h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><i class="fa-brands fa-php text-primary me-2"></i> <strong>PHP (PDO):</strong> Server-side logic, session authentication, and database operations.</li>
                    <li class="list-group-item"><i class="fa-solid fa-database text-warning me-2"></i> <strong>MySQL:</strong> Relational database storing admin, donor, screening, donation, stock, and request tables.</li>
                    <li class="list-group-item"><i class="fa-brands fa-bootstrap text-purple me-2" style="color: #6f42c1;"></i> <strong>HTML5, CSS3, & Bootstrap 5:</strong> Responsive user interface designed according to student project standards.</li>
                    <li class="list-group-item"><i class="fa-solid fa-server text-success me-2"></i> <strong>XAMPP:</strong> Local server development environment (Apache + MariaDB/MySQL).</li>
                </ul>

                <div class="alert alert-warning border-warning">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Educational Disclaimer:</strong> 
                    This project was developed by <strong>Aryan Verma (241306064)</strong> strictly for academic evaluation purposes as a BCA college project. It is not intended for commercial healthcare operations.
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
